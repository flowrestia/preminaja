<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'withdrawal_number',
        'seller_id',
        'amount',
        'fee',
        'net_amount',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'status',
        'proof_url',
        'notes',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'bank_account_number' => 'encrypted',
            'bank_account_holder' => 'encrypted',
            'status' => WithdrawalStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }
}
