<?php

namespace Tests\Feature;

use App\Enums\RoleChangeRequestStatus;
use App\Enums\RoleType;
use App\Models\RoleChangeRequest;
use App\Models\User;
use App\Notifications\RoleChangeProcessed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RoleChangeRequestFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $basicUser;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        
        $this->basicUser = User::factory()->create();
        $this->basicUser->assignRole(RoleType::BASIC->value);
        
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole(RoleType::ADMIN->value);
    }

    public function test_basic_user_can_request_role_change()
    {
        $requestData = [
            'requested_role' => RoleType::BUSINESS_OWNER->value,
            'reason' => 'I want to register my company',
        ];

        $response = $this->actingAs($this->basicUser)
            ->postJson('/api/v1/role-requests', $requestData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'requested_role',
                    'status',
                    'reason',
                ]
            ]);

        $this->assertDatabaseHas('role_change_requests', [
            'user_id' => $this->basicUser->id,
            'requested_role' => $requestData['requested_role'],
            'reason' => $requestData['reason'],
            'status' => RoleChangeRequestStatus::PENDING->value,
        ]);
    }

    public function test_user_cannot_request_invalid_role()
    {
        $requestData = [
            'requested_role' => 'invalid_role',
            'reason' => 'Test reason',
        ];

        $response = $this->actingAs($this->basicUser)
            ->postJson('/api/v1/role-requests', $requestData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['requested_role']);
    }

    public function test_admin_can_approve_role_request()
    {
        Notification::fake();

        $roleRequest = RoleChangeRequest::factory()->create([
            'user_id' => $this->basicUser->id,
            'requested_role' => RoleType::BUSINESS_OWNER->value,
            'status' => RoleChangeRequestStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/v1/role-requests/{$roleRequest->id}/process", [
                'status' => RoleChangeRequestStatus::APPROVED->value,
                'admin_notes' => 'Approved after verification',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('role_change_requests', [
            'id' => $roleRequest->id,
            'status' => RoleChangeRequestStatus::APPROVED->value,
            'processed_by' => $this->adminUser->id,
        ]);

        $this->basicUser->refresh();
        $this->assertTrue($this->basicUser->hasRole(RoleType::BUSINESS_OWNER->value));

        Notification::assertSentTo(
            $this->basicUser,
            RoleChangeProcessed::class
        );
    }

    public function test_admin_can_reject_role_request()
    {
        Notification::fake();

        $roleRequest = RoleChangeRequest::factory()->create([
            'user_id' => $this->basicUser->id,
            'requested_role' => RoleType::BUSINESS_OWNER->value,
            'status' => RoleChangeRequestStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/v1/role-requests/{$roleRequest->id}/process", [
                'status' => RoleChangeRequestStatus::REJECTED->value,
                'admin_notes' => 'Missing required documentation',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('role_change_requests', [
            'id' => $roleRequest->id,
            'status' => RoleChangeRequestStatus::REJECTED->value,
            'admin_notes' => 'Missing required documentation',
            'processed_by' => $this->adminUser->id,
        ]);

        $this->basicUser->refresh();
        $this->assertTrue($this->basicUser->hasRole(RoleType::BASIC->value));
        $this->assertFalse($this->basicUser->hasRole(RoleType::BUSINESS_OWNER->value));

        Notification::assertSentTo(
            $this->basicUser,
            RoleChangeProcessed::class
        );
    }

    public function test_non_admin_cannot_process_role_requests()
    {
        $roleRequest = RoleChangeRequest::factory()->create([
            'status' => RoleChangeRequestStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->basicUser)
            ->patchJson("/api/v1/role-requests/{$roleRequest->id}/process", [
                'status' => RoleChangeRequestStatus::APPROVED->value,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('role_change_requests', [
            'id' => $roleRequest->id,
            'status' => RoleChangeRequestStatus::PENDING->value,
        ]);
    }

    public function test_cannot_process_already_processed_request()
    {
        $roleRequest = RoleChangeRequest::factory()->create([
            'status' => RoleChangeRequestStatus::APPROVED->value,
            'processed_at' => now(),
            'processed_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/v1/role-requests/{$roleRequest->id}/process", [
                'status' => RoleChangeRequestStatus::REJECTED->value,
            ]);

        $response->assertStatus(422);
    }
}