<?php

namespace App\Enums;

enum UserRole: string
{
    case PESERTA = 'peserta';
    case PENYELENGGARA = 'penyelenggara';
    case OPERATOR = 'operator';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::PESERTA => 'Peserta (Cyclist)',
            self::PENYELENGGARA => 'Penyelenggara (Race Director)',
            self::OPERATOR => 'Operator Helpdesk',
            self::ADMIN => 'Administrator',
        };
    }

    public static function options(): array
    {
        return [
            self::PESERTA->value => self::PESERTA->label(),
            self::PENYELENGGARA->value => self::PENYELENGGARA->label(),
            self::OPERATOR->value => self::OPERATOR->label(),
            self::ADMIN->value => self::ADMIN->label(),
        ];
    }
}
