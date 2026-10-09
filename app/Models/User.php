<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_url',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Booted method to keep users.role column synchronized with Spatie hasRole
     * and revoke Sanctum tokens when user status is changed to blocked.
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            $isBlocked = ($user->status === UserStatus::BLOCKED) || ($user->status === 'blocked');
            if ($isBlocked) {
                $user->tokens()->delete();
            }
        });

        static::saved(function (User $user) {
            // When users.role is changed, ensure the corresponding Spatie role is assigned
            if ($user->wasChanged('role') || ! $user->roles()->exists()) {
                $roleName = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;
                if (! empty($roleName) && ! $user->hasRole($roleName)) {
                    $user->syncRoles([$roleName]);
                }
            }

            // Revoke all tokens if status is blocked
            $isBlocked = ($user->status === UserStatus::BLOCKED) || ($user->status === 'blocked');
            if ($isBlocked) {
                $user->tokens()->delete();
            }
        });
    }

    /**
     * Determine if user can access a Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->isSuperAdmin();
        }

        return false;
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'buyer_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === UserStatus::BLOCKED;
    }

    /**
     * Spatie role check is the ultimate source of truth.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::SUPERADMIN->value);
    }

    public function isSeller(): bool
    {
        return $this->hasRole(UserRole::SELLER->value);
    }

    public function isBuyer(): bool
    {
        return $this->hasRole(UserRole::BUYER->value);
    }
}
