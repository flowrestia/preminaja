<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'bio',
        'banner_url',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'verification_status',
        'verification_notes',
        'rating_avg',
        'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'bank_account_number' => 'encrypted',
            'bank_account_holder' => 'encrypted',
            'verification_status' => VerificationStatus::class,
            'rating_avg' => 'decimal:2',
            'rating_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'seller_id');
    }

    public function balance(): HasOne
    {
        return $this->hasOne(SellerBalance::class, 'seller_id');
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'seller_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'seller_id');
    }

    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::APPROVED;
    }
}
