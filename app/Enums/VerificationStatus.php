<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::APPROVED => 'Terverifikasi',
            self::REJECTED => 'Ditolak',
        };
    }
}
