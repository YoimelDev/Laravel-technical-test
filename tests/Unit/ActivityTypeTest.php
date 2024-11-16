<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Company;
use App\Models\ActivityType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_activity_type(): void
    {
        $activityType = ActivityType::factory()->create([
            'name' => 'Test Activity',
            'description' => 'Test Description'
        ]);

        $this->assertDatabaseHas('activity_types', [
            'name' => 'Test Activity',
            'description' => 'Test Description'
        ]);
    }

    public function test_can_update_activity_type(): void
    {
        $activityType = ActivityType::factory()->create();

        $activityType->update([
            'name' => 'Updated Activity',
            'description' => 'Updated Description'
        ]);

        $this->assertDatabaseHas('activity_types', [
            'id' => $activityType->id,
            'name' => 'Updated Activity',
            'description' => 'Updated Description'
        ]);
    }

    public function test_can_delete_activity_type(): void
    {
        $activityType = ActivityType::factory()->create();
        
        $activityType->delete();

        $this->assertDatabaseMissing('activity_types', [
            'id' => $activityType->id
        ]);
    }

    public function test_activity_type_can_have_companies(): void
    {
        $activityType = ActivityType::factory()->create();
        $company = Company::factory()->create();

        $activityType->companies()->attach($company->id);

        $this->assertTrue($activityType->companies->contains($company));
        $this->assertEquals(1, $activityType->companies->count());
    }
}
