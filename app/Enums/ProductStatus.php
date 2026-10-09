<?php

namespace App\Enums;

enum ProductStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case PUBLISHED = 'published';
    case REJECTED = 'rejected';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_REVIEW => 'Menunggu Moderasi',
            self::PUBLISHED => 'Dipublikasikan',
            self::REJECTED => 'Ditolak',
            self::INACTIVE => 'Nonaktif',
            self::ARCHIVED => 'Diarsipkan',
        };
    }
}
