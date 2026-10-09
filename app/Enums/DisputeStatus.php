<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case OPEN = 'open';
    case INVESTIGATING = 'investigating';
    case RESOLVED_BUYER = 'resolved_buyer';
    case RESOLVED_SELLER = 'resolved_seller';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Komplain Diajukan',
            self::INVESTIGATING => 'Dalam Investigasi Mediasi',
            self::RESOLVED_BUYER => 'Selesai - Refund ke Pembeli',
            self::RESOLVED_SELLER => 'Selesai - Dana Diteruskan ke Seller',
            self::CLOSED => 'Ditutup',
        };
    }
}
