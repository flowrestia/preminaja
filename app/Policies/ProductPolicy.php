<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('seller') || $user->hasRole('superadmin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->sellerProfile && $product->seller_id === $user->sellerProfile->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('seller')
            && $user->sellerProfile
            && $user->sellerProfile->isVerified()
            && $user->status->value === 'active';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->hasRole('seller')
            && $user->sellerProfile
            && $user->sellerProfile->isVerified()
            && $user->status->value === 'active'
            && $product->seller_id === $user->sellerProfile->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->hasRole('seller')
            && $user->sellerProfile
            && $user->sellerProfile->isVerified()
            && $user->status->value === 'active'
            && $product->seller_id === $user->sellerProfile->id;
    }
}
