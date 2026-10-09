<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case DISPUTED = 'disputed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PROCESSING => 'Diproses',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
            self::DISPUTED => 'Dalam Komplain / Bantuan',
        };
    }
}
