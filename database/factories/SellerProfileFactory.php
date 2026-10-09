<?php

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SellerProfileFactory extends Factory
{
    protected $model = SellerProfile::class;

    public function definition(): array
    {
        $name = fake()->company();
        return [
            'user_id' => User::factory()->seller(),
            'store_name' => $name,
            'store_slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'bio' => fake()->paragraph(),
            'banner_url' => null,
            'bank_name' => 'BCA',
            'bank_account_number' => fake()->numerify('##########'),
            'bank_account_holder' => fake()->name(),
            'verification_status' => VerificationStatus::APPROVED,
            'verification_notes' => 'Toko telah diverifikasi resmi oleh admin preminaja.',
            'rating_avg' => 4.90,
            'rating_count' => 25,
        ];
    }
}
