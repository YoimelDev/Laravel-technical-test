<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CurrencyConversion>
 */
class CurrencyConversionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'currency_from' => $this->faker->currencyCode,
            'currency_to' => $this->faker->currencyCode,
            'rate' => $this->faker->randomFloat(4, 0, 100),
        ];
    }
}
