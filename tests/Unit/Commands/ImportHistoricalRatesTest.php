<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use App\Models\HistoricalRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ImportHistoricalRatesTest extends TestCase
{
    use RefreshDatabase;

    private string $validXmlResponse = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gesmes:Envelope xmlns:gesmes="http://www.gesmes.org/xml/2002-08-01" xmlns="http://www.ecb.int/vocabulary/2002-08-01/eurofxref">
    <gesmes:subject>Reference rates</gesmes:subject>
    <gesmes:Sender>
        <gesmes:name>European Central Bank</gesmes:name>
    </gesmes:Sender>
    <Cube>
        <Cube time="2024-01-10">
            <Cube currency="USD" rate="1.0953"/>
            <Cube currency="JPY" rate="159.03"/>
            <Cube currency="GBP" rate="0.86075"/>
        </Cube>
        <Cube time="2024-01-09">
            <Cube currency="USD" rate="1.0921"/>
            <Cube currency="JPY" rate="157.89"/>
            <Cube currency="GBP" rate="0.85995"/>
        </Cube>
    </Cube>
</gesmes:Envelope>
XML;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    public function test_command_imports_historical_rates_successfully(): void
    {
        Http::fake([
            'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml' => Http::response($this->validXmlResponse, 200)
        ]);

        $this->artisan('currency:import-historical')
            ->assertSuccessful()
            ->expectsOutput('Iniciando importación de tipos de cambio históricos...')
            ->expectsOutput('Procesados: 6 registros')
            ->expectsOutput('Nuevos registros: 6');

        $this->assertDatabaseCount('historical_rates', 6);
        
        $this->assertDatabaseHas('historical_rates', [
            'currency' => 'USD',
            'rate' => 1.0953,
            'rate_date' => '2024-01-10'
        ]);
    }

    public function test_command_handles_duplicate_records(): void
    {
        Http::fake([
            'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml' => Http::response($this->validXmlResponse, 200)
        ]);

        $this->artisan('currency:import-historical')->assertSuccessful();

        Http::fake([
            'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml' => Http::response($this->validXmlResponse, 200)
        ]);

        $this->artisan('currency:import-historical')
            ->assertSuccessful()
            ->expectsOutput('Procesados: 6 registros')
            ->expectsOutput('Nuevos registros: 0');

        $this->assertDatabaseCount('historical_rates', 6);
    }

    public function test_command_handles_api_error(): void
    {
        Http::fake([
            'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml' => Http::response('Error', 500)
        ]);

        $this->artisan('currency:import-historical')
            ->assertFailed()
            ->expectsOutput('Error durante la importación: String could not be parsed as XML');
    }

    public function test_command_handles_invalid_xml(): void
    {
        Http::fake([
            'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml' => Http::response('<?xml version="1.0"?><invalid>data</invalid>', 200)
        ]);

        $this->artisan('currency:import-historical')
            ->assertFailed()
            ->expectsOutputToContain('Error durante la importación:');
    }

    public function test_command_handles_empty_xml(): void
    {
        $emptyXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gesmes:Envelope xmlns:gesmes="http://www.gesmes.org/xml/2002-08-01" xmlns="http://www.ecb.int/vocabulary/2002-08-01/eurofxref">
    <Cube>
    </Cube>
</gesmes:Envelope>
XML;

        Http::fake([
            'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml' => Http::response($emptyXml, 200)
        ]);

        $this->artisan('currency:import-historical')
            ->assertSuccessful()
            ->expectsOutput('Procesados: 0 registros')
            ->expectsOutput('Nuevos registros: 0');

        $this->assertDatabaseCount('historical_rates', 0);
    }

    public function test_command_processes_rates_with_different_precisions(): void
    {
        $xmlWithDifferentPrecisions = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gesmes:Envelope xmlns:gesmes="http://www.gesmes.org/xml/2002-08-01" xmlns="http://www.ecb.int/vocabulary/2002-08-01/eurofxref">
    <Cube>
        <Cube time="2024-01-10">
            <Cube currency="USD" rate="1.0"/>
            <Cube currency="JPY" rate="159.123456"/>
            <Cube currency="GBP" rate="0.86075123"/>
        </Cube>
    </Cube>
</gesmes:Envelope>
XML;

        Http::fake([
            'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml' => Http::response($xmlWithDifferentPrecisions, 200)
        ]);

        $this->artisan('currency:import-historical')->assertSuccessful();

        $this->assertDatabaseHas('historical_rates', [
            'currency' => 'USD',
            'rate' => 1.0
        ]);

        $jpyRate = HistoricalRate::where('currency', 'JPY')->first();
        $this->assertEquals(159.123456, $jpyRate->rate);

        $gbpRate = HistoricalRate::where('currency', 'GBP')->first();
        $this->assertEquals(0.86075123, $gbpRate->rate);
    }

    public function test_command_respects_unique_constraint(): void
    {
        HistoricalRate::create([
            'rate_date' => '2024-01-10',
            'currency' => 'USD',
            'rate' => 1.0953
        ]);

        Http::fake([
            'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml' => Http::response($this->validXmlResponse, 200)
        ]);

        $this->artisan('currency:import-historical')->assertSuccessful();

        $this->assertEquals(
            1,
            HistoricalRate::where('rate_date', '2024-01-10')
                ->where('currency', 'USD')
                ->count()
        );
    }
}