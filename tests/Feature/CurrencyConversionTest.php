<?php

namespace Tests\Feature;

use App\Enums\RoleType;
use App\Models\CurrencyConversion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyConversionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        
        $this->user = User::factory()->create();
        $this->user->assignRole(RoleType::BUSINESS_OWNER->value);

        Http::preventStrayRequests();

        Cache::flush();
    }

    public function test_authenticated_user_can_convert_currency()
    {
        Http::fake([
            config('services.fixer.url') . '/latest*' =>  Http::response([
                'success' => true,
                'timestamp' => time(),
                'base' => 'EUR',
                'date' => date('Y-m-d'),
                'rates' => [
                    'USD' => 1.1,
                ],
            ], 200),
        ]);
        
        $conversionData = [
            'from' => 'EUR',
            'to' => 'USD',
            'amount' => 100
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/currency/convert', $conversionData);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'amount',
                    'converted_amount',
                    'rate',
                    'from',
                    'to',
                    'cached',
                ]
            ]);

        $this->assertDatabaseHas('currency_conversions', [
            'from_currency' => 'EUR',
            'to_currency' => 'USD',
            'amount' => 100.0,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_conversion_uses_cached_rate_when_available()
    {
        CurrencyConversion::create([
            'from_currency' => 'EUR',
            'to_currency' => 'USD',
            'amount' => 1.0,
            'converted_amount' => 1.10,
            'rate' => 1.10,
            'user_id' => $this->user->id,
            'conversion_date' => now(),
        ]);

        $conversionData = [
            'from' => 'EUR',
            'to' => 'USD',
            'amount' => 100
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/currency/convert', $conversionData);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'cached' => true,
                    'rate' => 1.10,
                ]
            ]);

        Http::assertNothingSent();
    }

    public function test_user_can_view_conversion_history()
    {
        CurrencyConversion::create([
            'from_currency' => 'EUR',
            'to_currency' => 'USD',
            'amount' => 100.0,
            'converted_amount' => 110.0,
            'rate' => 1.10,
            'user_id' => $this->user->id,
            'conversion_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/currency/history');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'currency',
                        'rate',
                        'rate_date'
                    ]
                ]
            ]);
    }

    public function test_user_can_filter_conversion_history()
    {
        $currency = 'USD';
        $fromDate = now()->subWeek()->toDateString();
        $toDate = now()->toDateString();

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/currency/history?currency={$currency}&from_date={$fromDate}&to_date={$toDate}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'currency',
                        'rate',
                        'rate_date'
                    ]
                ]
            ]);
    }

    public function test_validates_conversion_input()
    {
        $invalidData = [
            'from' => 'INVALID',
            'to' => 'USD',
            'amount' => 'not_a_number'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/currency/convert', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['from', 'amount']);
    }

    public function test_unauthenticated_user_cannot_access_conversion()
    {
        $response = $this->postJson('/api/v1/currency/convert', [
            'from' => 'EUR',
            'to' => 'USD',
            'amount' => 100
        ]);

        $response->assertUnauthorized();
    }

    public function test_handles_external_api_failure()
    {
        Http::fake([
            config('services.fixer.url') . '/latest*' => Http::response([
                'success' => false,
                'error' => ['info' => 'API error']
            ], 500)
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/currency/convert', [
                'from' => 'EUR',
                'to' => 'USD',
                'amount' => 100
            ]);

        $response->assertStatus(500);
    }
}