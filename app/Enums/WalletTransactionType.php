<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case ORDER_SETTLEMENT = 'order_settlement';
    case ESCROW_HOLD = 'escrow_hold';
    case ESCROW_RELEASE = 'escrow_release';
    case PLATFORM_FEE = 'platform_fee';
    case WITHDRAWAL = 'withdrawal';
    case REFUND = 'refund';
    case REFUND_DEDUCTION = 'refund_deduction';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::ORDER_SETTLEMENT => 'Pencairan Pesanan',
            self::ESCROW_HOLD => 'Penahanan Dana Escrow',
            self::ESCROW_RELEASE => 'Pelepasan Dana Escrow',
            self::PLATFORM_FEE => 'Biaya Layanan Platform',
            self::WITHDRAWAL => 'Penarikan Saldo',
            self::REFUND => 'Pengembalian Dana (Refund)',
            self::REFUND_DEDUCTION => 'Pemotongan Refund Seller',
            self::ADJUSTMENT => 'Penyesuaian Saldo',
        };
    }
}
