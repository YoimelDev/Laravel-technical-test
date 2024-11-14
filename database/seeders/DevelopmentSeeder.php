<?php

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\User;
use App\Models\Company;
use App\Models\ActivityType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin
        $superAdmin = User::create([
            'name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole(RoleType::ADMIN->value);

        // Create some activity types
        $activityTypes = [
            ['name' => 'Technology', 'description' => 'Technology and IT services'],
            ['name' => 'Consulting', 'description' => 'Business consulting services'],
            ['name' => 'Manufacturing', 'description' => 'Manufacturing and production'],
            ['name' => 'Retail', 'description' => 'Retail and commerce'],
        ];

        foreach ($activityTypes as $type) {
            ActivityType::create($type);
        }

        // Create some test users with companies if needed
        if (app()->environment('local')) {
            User::factory(10)
                ->create()
                ->each(function ($user) {
                    $user->assignRole(RoleType::BASIC->value);
                });

            User::factory(5)
                ->has(
                    Company::factory()
                        ->hasAttached(
                            ActivityType::inRandomOrder()->limit(rand(1, 3))->get(),
                        )
                )
                ->create()
                ->each(function ($user) {
                    $user->assignRole(RoleType::BUSINESS_OWNER->value);
                });
        }
    }
}
