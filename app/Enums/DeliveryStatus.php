<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case PENDING = 'pending';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pengiriman',
            self::DELIVERED => 'Terkirim',
            self::FAILED => 'Gagal Terkirim',
        };
    }
}
