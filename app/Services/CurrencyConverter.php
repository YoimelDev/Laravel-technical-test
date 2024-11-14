<?php
// app/Services/CurrencyConverter.php
namespace App\Services;

use App\Models\CurrencyConversion;
use App\Models\HistoricalRate;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class CurrencyConverter
{
    public function convert(string $from, string $to, float $amount, ?User $user = null): array
    {
        // Buscar conversión reciente en caché
        $cachedConversion = CurrencyConversion::where([
            'from_currency' => $from,
            'to_currency' => $to,
        ])
            ->where('created_at', '>=', now()->subHour())
            ->latest()
            ->first();

        if ($cachedConversion) {
            return [
                'amount' => $amount,
                'converted_amount' => $amount * $cachedConversion->rate,
                'rate' => $cachedConversion->rate,
                'from' => $from,
                'to' => $to,
                'cached' => true,
            ];
        }

        // Realizar conversión usando API externa
        $response = Http::get(config('services.fixer.url') . '/latest', [
            'access_key' => config('services.fixer.key'),
            'base' => $from,
            'symbols' => $to,
        ])->throw()->json();

        $rate = $response['rates'][$to];
        $convertedAmount = $amount * $rate;

        // Guardar conversión en base de datos
        CurrencyConversion::create([
            'from_currency' => $from,
            'to_currency' => $to,
            'amount' => $amount,
            'converted_amount' => $convertedAmount,
            'rate' => $rate,
            'user_id' => $user?->id,
            'conversion_date' => now(),
        ]);

        return [
            'amount' => $amount,
            'converted_amount' => $convertedAmount,
            'rate' => $rate,
            'from' => $from,
            'to' => $to,
            'cached' => false,
        ];
    }

    public function getHistory(?string $currency = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = HistoricalRate::query();

        if ($currency) {
            $query->where('currency', $currency);
        }

        if ($fromDate) {
            $query->where('rate_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('rate_date', '<=', $toDate);
        }

        return $query->orderBy('rate_date')->get()->toArray();
    }
}