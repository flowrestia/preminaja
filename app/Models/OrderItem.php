<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'variant_id',
        'seller_id',
        'quantity',
        'price',
        'subtotal',
        'platform_fee',
        'seller_earning',
        'escrow_release_at',
        'delivery_status',
        'credentials_payload',
        'delivered_at',
        'product_name_snapshot',
        'variant_name_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'seller_earning' => 'decimal:2',
            'escrow_release_at' => 'datetime',
            'quantity' => 'integer',
            'delivery_status' => DeliveryStatus::class,
            'credentials_payload' => 'encrypted',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function automatedCredential(): HasOne
    {
        return $this->hasOne(AutomatedCredential::class, 'order_item_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }
}
