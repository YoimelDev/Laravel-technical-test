<?php

namespace App\Http\Requests\RoleChangeRequest;

use App\Enums\RoleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requested_role' => ['required', 'string', Rule::in([
                RoleType::BUSINESS_OWNER->value,
                RoleType::ADMIN->value
            ])],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
