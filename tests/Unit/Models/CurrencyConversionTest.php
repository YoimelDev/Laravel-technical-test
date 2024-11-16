<?php

namespace Tests\Unit\Models;

use App\Models\CurrencyConversion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user_relationship(): void
    {
        $user = User::factory()->create();
        $conversion = CurrencyConversion::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertTrue($conversion->user->is($user));
    }

    public function test_casts_decimal_fields(): void
    {
        $conversion = CurrencyConversion::factory()->create([
            'amount' => '100.50',
            'converted_amount' => '120.60',
            'rate' => '1.206'
        ]);

        $this->assertIsFloat($conversion->amount);
        $this->assertIsFloat($conversion->converted_amount);
        $this->assertIsFloat($conversion->rate);
    }

    public function test_casts_conversion_date(): void
    {
        $conversion = CurrencyConversion::factory()->create([
            'conversion_date' => now()
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $conversion->conversion_date);
    }
}
