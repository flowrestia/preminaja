<?php

namespace App\Models;

use App\Enums\DeliveryType;
use App\Enums\ProductStatus;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'slug',
        'description',
        'delivery_type',
        'status',
        'rejection_reason',
        'featured_image',
        'rating_avg',
        'rating_count',
        'sold_count',
    ];

    protected function casts(): array
    {
        return [
            'delivery_type' => DeliveryType::class,
            'status' => ProductStatus::class,
            'rating_avg' => 'decimal:2',
            'rating_count' => 'integer',
            'sold_count' => 'integer',
        ];
    }

    /**
     * Scope produk yang layak tampil di etalase katalog:
     * 1. Status produk published
     * 2. Profil seller approved
     * 3. Akun user seller active (tidak diblokir)
     */
    public function scopeVisibleInCatalog(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::PUBLISHED)
            ->whereHas('seller', function (Builder $sellerQuery) {
                $sellerQuery->where('verification_status', VerificationStatus::APPROVED)
                    ->whereHas('user', function (Builder $userQuery) {
                        $userQuery->where('status', UserStatus::ACTIVE);
                    });
            });
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function minPrice(): ?float
    {
        return (float) $this->variants()->where('is_active', true)->min('price');
    }
}
