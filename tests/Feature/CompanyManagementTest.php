<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Models\User;
use App\Models\Company;
use App\Models\ActivityType;
use App\Enums\RoleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $businessOwner;
    private User $basicUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->admin = User::factory()->create();
        $this->admin->assignRole(RoleType::ADMIN->value);

        $this->businessOwner = User::factory()->create();
        $this->businessOwner->assignRole(RoleType::BUSINESS_OWNER->value);

        $this->basicUser = User::factory()->create();
        $this->basicUser->assignRole(RoleType::BASIC->value);
    }

    public function test_admin_can_view_companies_list(): void
    {
        Company::factory(3)->create();

        $response = $this->actingAs($this->admin)
            ->get('/api/v1/companies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'document_number',
                            'document_type',
                            'status',
                        ],
                    ]
                ],
            ]);
    }

    public function test_business_owner_can_create_company(): void
    {
        $activityTypes = ActivityType::factory(2)->create();
        
        $companyData = [
            'type' => 'companies',
            'attributes' => [
                'name' => 'Test Company',
                'document_number' => '12345678',
                'document_type' => 'dni',
                'status' => 'active'
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'users',
                        'id' => (string) $this->businessOwner->id
                    ]
                ],
                'activityTypes' => [
                    'data' => $activityTypes->map(fn($type) => [
                        'type' => 'activity-types',
                        'id' => (string) $type->getRouteKey()
                    ])->all()
                ]
            ]
        ];

        $response = $this
            ->actingAs($this->businessOwner)
            ->jsonApi()
            ->expects('companies')
            ->withData($companyData)
            ->includePaths('activityTypes')
            ->post('/api/v1/companies');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'document_number',
                        'document_type',
                        'status'
                    ]
                ]
            ]);
    }

    public function test_basic_user_cannot_create_company(): void
    {
        $activityTypes = ActivityType::factory(2)->create();

        $companyData = [
            'type' => 'companies',
            'attributes' => [
                'name' => 'Test Company',
                'document_number' => '12345678',
                'document_type' => 'dni',
                'status' => 'active'
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'users',
                        'id' => (string) $this->basicUser->id
                    ]
                ],
                'activityTypes' => [
                    'data' => $activityTypes->map(fn($type) => [
                        'type' => 'activity-types',
                        'id' => (string) $type->getRouteKey()
                    ])->all()
                ]
            ]
        ];

        $response = $this
            ->actingAs($this->basicUser)
            ->jsonApi()
            ->expects('companies')
            ->withData($companyData)
            ->includePaths('activityTypes')
            ->post('/api/v1/companies');

        $response->assertStatus(403);
    }

    public function test_admin_can_update_company(): void
    {
        $company = Company::factory()->create();

        $companyData = [
            'type' => 'companies',
            'id' => (string) $company->getRouteKey(),
            'attributes' => [
                'name' => 'Updated Company',
                'document_number' => '87654321',
                'document_type' => DocumentType::DNI,
                'status' => 'inactive'
            ],
        ];

        $response = $this
            ->actingAs($this->admin)
            ->jsonApi()
            ->expects('companies')
            ->includePaths('activityTypes')
            ->withData($companyData)
            ->patch("/api/v1/companies/{$company->getRouteKey()}");

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company',
            'document_number' => '87654321',
            'document_type' => DocumentType::DNI,
            'status' => 'inactive'
        ]);
    }

    public function test_admin_can_delete_company(): void
    {
        $company = Company::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->jsonApi()
            ->delete("/api/v1/companies/{$company->getRouteKey()}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id
        ]);
    }
}
