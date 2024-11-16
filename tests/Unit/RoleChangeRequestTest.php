<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\RoleChangeRequest;
use App\Enums\RoleChangeRequestStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleChangeRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_role_change_request(): void
    {
        $request = RoleChangeRequest::factory()->create();
        
        $this->assertInstanceOf(RoleChangeRequest::class, $request);
        $this->assertNotNull($request->user_id);
        $this->assertNotNull($request->requested_role);
    }

    public function test_belongs_to_user_relationship(): void
    {
        $request = RoleChangeRequest::factory()->create();
        
        $this->assertInstanceOf(User::class, $request->user);
    }

    public function test_belongs_to_processor_relationship(): void
    {
        $request = RoleChangeRequest::factory()
            ->create(['processed_by' => User::factory()->create()->id]);
        
        $this->assertInstanceOf(User::class, $request->processor);
    }

    public function test_status_is_cast_to_enum(): void
    {
        $request = RoleChangeRequest::factory()->create();
        
        $this->assertInstanceOf(RoleChangeRequestStatus::class, $request->status);
    }

    public function test_processed_at_is_cast_to_datetime(): void
    {
        $request = RoleChangeRequest::factory()->create([
            'processed_at' => now()
        ]);
        
        $this->assertInstanceOf(\DateTime::class, $request->processed_at);
    }
}
