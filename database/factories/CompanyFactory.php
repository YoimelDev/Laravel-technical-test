<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'document_number' => $this->faker->unique()->randomNumber(8),
            'document_type' => 'dni',
            'status' => 'active',
            'user_id' => $this->faker->randomNumber(),
        ];
    }
}
