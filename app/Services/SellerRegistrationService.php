<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Models\SellerBalance;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SellerRegistrationService
{
    /**
     * Register a new seller account with pending verification profile.
     */
    public function registerSeller(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = new User([
                'name' => $data['name'],
                'email' => strtolower(trim($data['email'])),
                'password' => Hash::make($data['password']),
                'status' => UserStatus::ACTIVE,
                'phone' => $data['phone'] ?? null,
            ]);
            $user->role = UserRole::SELLER;
            $user->save();

            $user->assignRole(UserRole::SELLER->value);

            // Generate unique store slug
            $baseSlug = Str::slug($data['store_name']);
            $slug = $baseSlug;
            $counter = 1;
            while (SellerProfile::where('store_slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . (++$counter);
            }

            $profile = SellerProfile::create([
                'user_id' => $user->id,
                'store_name' => $data['store_name'],
                'store_slug' => $slug,
                'bio' => $data['bio'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_account_holder' => $data['bank_account_holder'] ?? null,
                'verification_status' => VerificationStatus::PENDING,
                'verification_notes' => 'Pendaftaran baru, menunggu peninjauan tim operasional PreminAja.',
                'rating_avg' => 0.00,
                'rating_count' => 0,
            ]);

            // Initialize 0 balance wallet for seller
            SellerBalance::create([
                'seller_id' => $profile->id,
                'balance' => 0.00,
                'pending_balance' => 0.00,
                'total_withdrawn' => 0.00,
            ]);

            event(new Registered($user));

            return $user;
        });
    }
}
