<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tripay_reference',
        'merchant_ref',
        'payment_method',
        'amount',
        'fee',
        'total_amount',
        'status',
        'checkout_url',
        'qr_url',
        'pay_code',
        'tripay_response',
        'paid_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'tripay_response' => 'array',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPaid(): bool
    {
        return strtoupper($this->status) === 'PAID';
    }
}
