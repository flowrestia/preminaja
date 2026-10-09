<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $defaultPassword = env('SEED_DEFAULT_PASSWORD', 'password123');

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make($defaultPassword),
            'remember_token' => Str::random(10),
            'phone' => fake()->phoneNumber(),
            'status' => \App\Enums\UserStatus::ACTIVE,
            'last_login_at' => now(),
        ];
    }

    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::SUPERADMIN,
        ]);
    }

    public function seller(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::SELLER,
        ]);
    }

    public function buyer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::BUYER,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
