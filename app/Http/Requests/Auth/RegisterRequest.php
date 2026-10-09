<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['buyer', 'seller'])],
            'phone' => ['nullable', 'string', 'max:25'],

            // Additional fields required if role === 'seller'
            'store_name' => [Rule::requiredIf(fn () => $this->input('role') === UserRole::SELLER->value), 'nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'bank_name' => ['nullable', 'string', 'max:50'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:100'],
        ];
    }
}
