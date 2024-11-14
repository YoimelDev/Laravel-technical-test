<?php

namespace App\Http\Requests\RoleChangeRequest;

use App\Enums\RoleChangeRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('process role requests');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in([RoleChangeRequestStatus::APPROVED->value, RoleChangeRequestStatus::REJECTED->value])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
