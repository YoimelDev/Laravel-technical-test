<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_have_contact_information()
    {
        $user = User::factory()->create();
        
        $contactInfo = [
            'phone' => '123456789',
            'address' => 'Test Street 123',
            'city' => 'Test City',
            'country' => 'Test Country',
            'postal_code' => '12345'
        ];
        
        $user->contactInformation()->create($contactInfo);
        
        $this->assertDatabaseHas('contact_information', $contactInfo);
        $this->assertEquals($contactInfo['phone'], $user->contactInformation->phone);
    }

    public function test_can_own_only_one_company()
    {
        $user = User::factory()->create();
        
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);
        
        $this->assertTrue($user->company->is($company));
        $this->assertCount(1, $user->company()->get());
    }
}