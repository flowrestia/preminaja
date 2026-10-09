<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'balance',
        'pending_balance',
        'total_withdrawn',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'pending_balance' => 'decimal:2',
            'total_withdrawn' => 'decimal:2',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }
}
