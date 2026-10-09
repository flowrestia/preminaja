<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new buyer account.
     */
    public function registerBuyer(array $data): User
    {
        $user = new User([
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'status' => UserStatus::ACTIVE,
            'phone' => $data['phone'] ?? null,
        ]);
        $user->role = UserRole::BUYER;
        $user->save();

        $user->assignRole(UserRole::BUYER->value);

        event(new Registered($user));

        return $user;
    }

    /**
     * Authenticate user credentials and verify account status.
     * Throws generic error message to avoid email enumeration.
     */
    public function authenticate(string $email, string $password): User
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.'],
            ]);
        }

        if ($user->status === UserStatus::BLOCKED) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda telah dinonaktifkan atau diblokir oleh administrator.'],
            ]);
        }

        $user->update(['last_login_at' => now()]);

        return $user;
    }
}
