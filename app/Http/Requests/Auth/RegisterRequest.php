<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'contact_information' => ['nullable', 'array'],
            'contact_information.phone' => ['nullable', 'string', 'max:20'],
            'contact_information.address' => ['nullable', 'string', 'max:255'],
            'contact_information.city' => ['nullable', 'string', 'max:100'],
            'contact_information.country' => ['nullable', 'string', 'max:100'],
            'contact_information.postal_code' => ['nullable', 'string', 'max:20'],
        ];
    }
}
