<?php

namespace App\Models;

use App\Enums\CredentialStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomatedCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'credential_data',
        'instructions',
        'status',
        'order_item_id',
    ];

    protected function casts(): array
    {
        return [
            'credential_data' => 'encrypted',
            'status' => CredentialStatus::class,
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
