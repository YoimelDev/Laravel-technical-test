<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleChangeRequest\ProcessRequest;
use App\Http\Requests\RoleChangeRequest\StoreRequest;
use App\Models\RoleChangeRequest;
use App\Notifications\RoleChangeProcessed;
use App\Enums\RoleChangeRequestStatus;
use Illuminate\Http\JsonResponse;

class RoleChangeRequestController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        $roleRequest = RoleChangeRequest::create([
            'user_id' => $request->user()->id,
            'requested_role' => $request->requested_role,
            'reason' => $request->reason,
            'status' => RoleChangeRequestStatus::PENDING->value,
        ]);

        return response()->json(['data' => $roleRequest], 201);
    }

    public function process(ProcessRequest $request, RoleChangeRequest $roleChangeRequest): JsonResponse
    {
        if ($roleChangeRequest->status !== RoleChangeRequestStatus::PENDING) {
            return response()->json(['error' => 'This request has already been processed'], 422);
        }

        $roleChangeRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        if ($request->status === RoleChangeRequestStatus::APPROVED->value) {
            $roleChangeRequest->user->syncRoles([$roleChangeRequest->requested_role]);
        }

        $roleChangeRequest->user->notify(new RoleChangeProcessed($roleChangeRequest));

        return response()->json(['data' => $roleChangeRequest]);
    }
}