<?php

namespace App\Console\Commands;

use App\Models\HistoricalRate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class ImportHistoricalRates extends Command
{
    protected $signature = 'currency:import-historical';
    protected $description = 'Import historical currency rates from ECB';

    private const ECB_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml';

    public function handle(): int
    {
        $this->info('Iniciando importación de tipos de cambio históricos...');

        try {
            $response = Http::get(self::ECB_URL);
            $xml = new SimpleXMLElement($response->body());
            
            $totalProcessed = 0;
            $totalNew = 0;

            foreach ($xml->Cube->Cube as $dayData) {
                $date = Carbon::parse((string) $dayData['time']);
                
                foreach ($dayData->Cube as $rateData) {
                    $currency = (string) $rateData['currency'];
                    $rate = (float) $rateData['rate'];
                    
                    $totalProcessed++;
                    
                    // Intentar insertar solo si no existe
                    $created = HistoricalRate::firstOrCreate(
                        [
                            'rate_date' => $date,
                            'currency' => $currency,
                        ],
                        [
                            'rate' => $rate,
                        ]
                    );

                    if ($created->wasRecentlyCreated) {
                        $totalNew++;
                    }
                }
            }

            $this->info("Procesados: {$totalProcessed} registros");
            $this->info("Nuevos registros: {$totalNew}");
            
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error durante la importación: ' . $e->getMessage());
            Log::error('Error importando tipos de cambio históricos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return self::FAILURE;
        }
    }
}