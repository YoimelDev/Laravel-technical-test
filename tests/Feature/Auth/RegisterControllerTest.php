<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        
        Notification::fake();
    }

    public function test_can_register_new_user(): void
    {
        $userData = [
            'name' => 'Test User',
            'last_name' => 'Test Last',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'contact_information' => [
                'phone' => '123456789',
                'address' => 'Test Address',
                'city' => 'Test City',
                'country' => 'Test Country',
                'postal_code' => '12345'
            ]
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'roles'
                    ],
                    'token'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);

        $this->assertDatabaseHas('contact_information', [
            'phone' => '123456789'
        ]);

        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($user, WelcomeNotification::class);
    }

    public function test_validates_registration_data(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'last_name']);
    }

    public function test_validates_unique_email(): void
    {
        $existingUser = User::factory()->create();

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'last_name' => 'Test Last',
            'email' => $existingUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}