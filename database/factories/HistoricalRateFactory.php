<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoricalRate>
 */
class HistoricalRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rate_date' => $this->faker->date(),
            'currency' => $this->faker->currencyCode,
            'rate' => $this->faker->randomFloat(8, 0, 100),
        ];
    }
}
