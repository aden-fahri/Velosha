<?php

namespace App\Enums;

enum EventStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case PUBLISHED = 'published';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Menunggu Moderasi',
            self::PUBLISHED => 'Published',
            self::REJECTED => 'Ditolak',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PUBLISHED => 'success',
            self::SUBMITTED => 'warning',
            self::REJECTED => 'danger',
            self::COMPLETED => 'info',
            self::CANCELLED => 'danger',
            self::DRAFT => 'gray',
        };
    }

    public static function options(): array
    {
        return [
            self::DRAFT->value => self::DRAFT->label(),
            self::SUBMITTED->value => self::SUBMITTED->label(),
            self::PUBLISHED->value => self::PUBLISHED->label(),
            self::REJECTED->value => self::REJECTED->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
        ];
    }
}
