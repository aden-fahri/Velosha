<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPackageContent extends Model
{
    protected $fillable = [
        'eyebrow',
        'title',
        'description',
        'features',
        'benefits_title',
        'benefits',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'benefits' => 'array',
        ];
    }

    public static function current(): static
    {
        return static::query()->first() ?? new static(static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'eyebrow' => 'Isi Paket Tiket',
            'title' => 'Yang Kamu Dapat dari Satu Tiket',
            'description' => 'Beli satu tiket, dapat satu paket lengkap. Semua di bawah sudah termasuk dalam harga — tanpa biaya tambahan.',
            'features' => [
                ['icon' => 'cpu', 'title' => 'BIB & RFID Timing Chip', 'desc' => 'Nomor dada resmi dan chip pencatat waktu otomatis untuk hasil balapmu.'],
                ['icon' => 'award', 'title' => 'Medali Finisher Logam', 'desc' => 'Diraih di garis finish bagi peserta yang finis sebelum cut-off time.'],
                ['icon' => 'shield-plus', 'title' => 'Asuransi Kecelakaan', 'desc' => 'Perlindungan medis resmi berlaku selama race weekend.'],
                ['icon' => 'utensils', 'title' => 'Feed Zone & Finisher Meal', 'desc' => 'Empat pos hidrasi dan nutrisi di sepanjang rute, plus makan siang finisher.'],
            ],
            'benefits_title' => 'Benefit Lain dari Satu Tiket',
            'benefits' => [
                ['text' => 'E-Ticket PDF ber-QR dikirim ke email begitu pembayaran lunas'],
                ['text' => 'Race pack ditukar pakai QR: BIB + peniti, stiker RFID, buku panduan, voucher makan'],
                ['text' => 'Akses Rider Portal untuk cek status tiket dan notifikasi balap'],
                ['text' => 'Unduh file rute GPX siap pakai untuk Garmin / Wahoo'],
                ['text' => 'Jadwal balap bisa langsung disimpan ke Google Calendar'],
                ['text' => 'Waktu tempuhmu tercatat resmi dan tampil di halaman Ranking'],
            ],
        ];
    }
}
