<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ContactInformation;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_contact_information(): void
    {
        $user = User::factory()->create();
        
        $contactInfo = ContactInformation::factory()->create([
            'phone' => '123456789',
            'address' => 'Test Street',
            'city' => 'Test City',
            'country' => 'Test Country',
            'postal_code' => '12345',
            'contactable_id' => $user->id,
            'contactable_type' => User::class
        ]);

        $this->assertDatabaseHas('contact_information', [
            'phone' => '123456789',
            'address' => 'Test Street',
            'contactable_id' => $user->id
        ]);
    }

    public function test_can_associate_with_user(): void
    {
        $user = User::factory()->create();
        
        $contactInfo = ContactInformation::factory()->create([
            'contactable_id' => $user->id,
            'contactable_type' => User::class
        ]);

        $this->assertTrue($user->contactInformation->is($contactInfo));
    }

    public function test_can_associate_with_company(): void
    {
        $company = Company::factory()->create();
        
        $contactInfo = ContactInformation::factory()->create([
            'contactable_id' => $company->id,
            'contactable_type' => Company::class
        ]);

        $this->assertTrue($company->contactInformation->is($contactInfo));
    }

    public function test_contactable_relationship(): void
    {
        $user = User::factory()->create();
        $contactInfo = ContactInformation::factory()->create([
            'contactable_id' => $user->id,
            'contactable_type' => User::class
        ]);

        $this->assertInstanceOf(User::class, $contactInfo->contactable);
    }

    public function test_fill_attributes(): void 
    {
        $contactInfo = new ContactInformation();
        $data = [
            'phone' => '123456789',
            'address' => 'Test Street',
            'city' => 'Test City',
            'country' => 'Test Country',
            'postal_code' => '12345'
        ];

        $contactInfo->fill($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $contactInfo->$key);
        }
    }
}