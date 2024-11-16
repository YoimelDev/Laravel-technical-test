<?php

namespace Tests\Feature;

use App\Enums\RoleType;
use App\Models\ActivityType;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ActivityTypeFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;
    private User $businessOwner;
    private User $basicUser;
    private Company $company;

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

        $this->company = Company::factory()->create([
            'user_id' => $this->admin->id
        ]);
    }

    public function test_admin_can_create_activity_type(): void
    {
        $data = [
            'type' => 'activity-types',
            'attributes' => [
                'name' => 'Test Activity',
                'description' => 'This is a test activity'
            ]
        ];

        $response = $this
        ->actingAs($this->admin)
        ->jsonApi()
        ->expects('activity-types')
        ->withData($data)
        ->includePaths('companies')
        ->post('/api/v1/activity-types');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attributes' => [
                        'name',
                        'description'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('activity_types', [
            'name' => 'Test Activity',
            'description' => 'This is a test activity'
        ]);
    }

    public function test_business_owner_can_create_activity_type(): void
    {
        $data = [
            'type' => 'activity-types',
            'attributes' => [
                'name' => 'Business Activity',
                'description' => 'Activity for business'
            ]
        ];

        $response = $this
            ->actingAs($this->businessOwner)
            ->jsonApi()
            ->expects('activity-types')
            ->withData($data)
            ->post('/api/v1/activity-types');

        $response->assertStatus(201);
    }

    public function test_basic_user_cannot_create_activity_type(): void
    {
        $data = [
            'type' => 'activity-types',
            'attributes' => [
                'name' => 'Basic Activity',
                'description' => 'Activity from basic user'
            ]
        ];

        $response = $this
            ->actingAs($this->basicUser)
            ->jsonApi()
            ->expects('activity-types')
            ->withData($data)
            ->post('/api/v1/activity-types');

        $response->assertStatus(403);
    }

    public function test_can_list_activity_types(): void
    {
        ActivityType::factory()->count(3)->create();

        $response = $this
            ->actingAs($this->basicUser)
            ->jsonApi()
            ->expects('activity-types')
            ->get('/api/v1/activity-types');

        $response->assertStatus(200);
    }

    public function test_can_show_single_activity_type(): void
    {
        $activityType = ActivityType::factory()->create();

        $response = $this
            ->actingAs($this->basicUser)
            ->jsonApi()
            ->expects('activity-types')
            ->get("/api/v1/activity-types/{$activityType->id}");

        $response->assertStatus(200);
    }

    public function test_admin_can_update_activity_type(): void
    {
        $activityType = ActivityType::factory()->create();

        $data = [
            'type' => 'activity-types',
            'id' => (string) $activityType->id,
            'attributes' => [
                'name' => 'Updated Activity',
                'description' => 'Updated description'
            ]
        ];

        $response = $this
            ->actingAs($this->admin)
            ->jsonApi()
            ->expects('activity-types')
            ->withData($data)
            ->patch("/api/v1/activity-types/{$activityType->id}");

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_activity_type(): void
    {
        $activityType = ActivityType::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->jsonApi()
            ->expects('activity-types')
            ->delete("/api/v1/activity-types/{$activityType->id}");

        $response->assertStatus(204);
    }

    public function test_cannot_create_activity_type_with_invalid_data(): void
    {
        $data = [
            'type' => 'activity-types',
            'attributes' => [
                'name' => '',
                'description' => 'Test description'
            ]
        ];

        $response = $this
            ->actingAs($this->admin)
            ->jsonApi()
            ->expects('activity-types')
            ->withData($data)
            ->post('/api/v1/activity-types');

        $response->assertStatus(422);
    }

    public function test_can_associate_activity_type_with_company(): void
    {
        $activityTypes = ActivityType::factory()->count(3)->create();
        $this->company->activityTypes()->attach($activityTypes);

        $data = $activityTypes->map(fn(ActivityType $activityType) => [
            'type' => 'activity-types',
            'id' => (string) $activityType->getRouteKey(),
        ])->all();

        $response = $this
            ->actingAs($this->admin)
            ->jsonApi()
            ->expects('activity-types')
            ->withData($data)
            ->post("/api/v1/companies/{$this->company->getRouteKey()}/relationships/activity-types");

        $response->assertStatus(204);
    }

    public function test_can_dissociate_activity_type_from_company(): void
    {
        $activityType = ActivityType::factory()->create();

        $data = [
            [
                'type' => 'activity-types',
                'id' => (string) $activityType->getRouteKey(),
            ]
        ];

        $response = $this
            ->actingAs($this->admin)
            ->jsonApi()
            ->expects('activity-types')
            ->withData($data)
            ->delete("/api/v1/companies/{$this->company->getRouteKey()}/relationships/activity-types");

        $response->assertStatus(204);
    }

    public function test_can_filter_activity_types(): void
    {
        $activityType1 = ActivityType::factory()->create(['name' => 'Technology']);
        $activityType2 = ActivityType::factory()->create(['name' => 'Marketing']);
        $activityType3 = ActivityType::factory()->create(['name' => 'Tech Support']);

        $response = $this
            ->actingAs($this->basicUser)
            ->jsonApi()
            ->expects('activity-types')
            ->filter(['name' => 'Technology,Tech Support'])
            ->get('/api/v1/activity-types');

        $response->assertStatus(200);
    }
}