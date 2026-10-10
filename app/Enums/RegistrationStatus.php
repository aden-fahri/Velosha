<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CHECKED_IN = 'checked_in';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Lunas',
            self::CHECKED_IN => 'Checked-in (RPC)',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PAID => 'success',
            self::CHECKED_IN => 'info',
            self::CANCELLED => 'danger',
            self::PENDING => 'warning',
        };
    }

    public static function options(): array
    {
        return [
            self::PENDING->value => self::PENDING->label(),
            self::PAID->value => self::PAID->label(),
            self::CHECKED_IN->value => self::CHECKED_IN->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
        ];
    }
}
