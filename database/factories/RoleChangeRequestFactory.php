<?php

namespace Database\Factories;

use App\Enums\RoleChangeRequestStatus;
use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoleChangeRequest>
 */
class RoleChangeRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'requested_role' => fake()->randomElement(RoleType::cases()),
            'status' => fake()->randomElement(RoleChangeRequestStatus::cases()),
            'reason' => fake()->sentence(),
            'admin_notes' => fake()->optional()->sentence(),
            'processed_at' => fake()->optional()->dateTime(),
            'processed_by' => fake()->optional()->randomElement([null, User::factory()]),
        ];
    }
}
