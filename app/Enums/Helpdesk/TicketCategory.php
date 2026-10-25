<?php

namespace App\Enums\Helpdesk;

/**
 * Kategori tiket bantuan helpdesk.
 */
enum TicketCategory: string
{
    case TIKET_QR = 'Tiket/QR';
    case PEMBAYARAN = 'Pembayaran';
    case AKUN = 'Akun';
    case LOKASI = 'Lokasi';
    case LAINNYA = 'Lainnya';

    /**
     * Mengembalikan seluruh nilai string enum kategori.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
