<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyConversionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'from_currency' => $this->faker->currencyCode(),
            'to_currency' => $this->faker->currencyCode(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'converted_amount' => $this->faker->randomFloat(2, 1, 1000),
            'rate' => $this->faker->randomFloat(4, 0.1, 10),
            'conversion_date' => $this->faker->dateTime(),
            'user_id' => null,
        ];
    }
}
