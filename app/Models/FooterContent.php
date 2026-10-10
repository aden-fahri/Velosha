<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterContent extends Model
{
    protected $fillable = [
        'brand_description',
        'kategori_title',
        'kategori_links',
        'bantuan_title',
        'bantuan_links',
        'payment_title',
        'payment_methods',
        'copyright',
        'bottom_links',
    ];

    protected function casts(): array
    {
        return [
            'kategori_links' => 'array',
            'bantuan_links' => 'array',
            'payment_methods' => 'array',
            'bottom_links' => 'array',
        ];
    }

    public static function current(): static
    {
        return static::query()->first() ?? new static(static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'brand_description' => 'Registrasi maraton, gran fondo, dan endurance ride di Indonesia.',
            'kategori_title' => 'Kategori',
            'kategori_links' => [
                ['label' => 'Gran Fondo', 'url' => '/events?type=gran-fondo'],
                ['label' => 'Medio Fondo', 'url' => '/events?type=medio-fondo'],
                ['label' => 'Fun Ride', 'url' => '/events?type=fun-ride'],
                ['label' => 'KOM Climb', 'url' => '/events?type=climb'],
            ],
            'bantuan_title' => 'Bantuan',
            'bantuan_links' => [
                ['label' => 'Kalender Balap', 'url' => '/events'],
                ['label' => 'Cek E-Ticket', 'url' => '/ticket'],
                ['label' => 'Portal Peserta', 'url' => '/dashboard'],
                ['label' => 'Panel Pengelola', 'url' => '/admin'],
            ],
            'payment_title' => 'Pembayaran',
            'payment_methods' => [
                ['label' => 'QRIS'],
                ['label' => 'Virtual Account'],
                ['label' => 'Kartu Kredit'],
                ['label' => 'VA Manual'],
            ],
            'copyright' => '© 2026 Velosha. Seluruh hak cipta dilindungi.',
            'bottom_links' => [
                ['label' => 'Privasi', 'url' => '/rule'],
                ['label' => 'Ketentuan', 'url' => '/rule'],
            ],
        ];
    }
}
