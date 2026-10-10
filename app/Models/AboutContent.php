<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    protected $fillable = [
        'header_eyebrow',
        'header_title',
        'header_description',
        'header_meta',
        'manifesto_title',
        'manifesto_body',
        'value_items',
        'cta_title',
        'cta_desc',
        'cta_label',
        'cta_url',
    ];

    protected function casts(): array
    {
        return [
            'value_items' => 'array',
        ];
    }

    public static function current(): static
    {
        return static::query()->first() ?? new static(static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'header_eyebrow' => 'Tentang Kami',
            'header_title' => 'Platform Registrasi Balap Sepeda Indonesia',
            'header_description' => 'Kami membangun infrastruktur terpadu untuk endurance ride di Indonesia — mulai dari kurasi rute, timing chip RFID, hingga Race Village hari-H.',
            'header_meta' => null,
            'manifesto_title' => 'Kami Menghidupkan Budaya Sepeda di Sini',
            'manifesto_body' => "Velosha lahir dari observasi sederhana: maraton sepeda di Indonesia masih bergantung pada spreadsheet, kotak doubtful, dan informasi yang tercecer di grup WhatsApp. Padahal peserta berhak atas pengalaman balapan yang layak.\n\nKarena itu kami membangun satu platform terpadu: katalog event terkurasi, sistem registrasi dengan validasi identitas, timing chip RFID, e-ticket QR, hingga Race Village yang nyaman. Satu akun, satu dashboard, semua kebutuhan riders di satu tempat.\n\nKami memilih presisi, bukan hiasan. Koordinat rute yang terukur, aturan yang jelas, dan hasil akhir yang bisa dipertanggungjawabkan.",
            'value_items' => [
                ['icon' => 'shield-check', 'title' => 'Transparan', 'desc' => 'Setiap hasil balapan terverifikasi RFID dan dapat diakses publik.'],
                ['icon' => 'gauge', 'title' => 'Terukur', 'desc' => 'Elevasi, gradien, dan cut-off dihitung dari data GPS asli, bukan estimasi.'],
                ['icon' => 'users', 'title' => 'Inklusif', 'desc' => 'Kategori mulai Fun Ride 25KM hingga Gran Fondo 180KM untuk semua level.'],
                ['icon' => 'heart-pulse', 'title' => 'Terawat', 'desc' => 'Tim medis dan ambulans di setiap feed zone, bukan opsional.'],
            ],
            'cta_title' => 'Siap mulai musim 2026?',
            'cta_desc' => 'Daftar sekali, kelola semua race Anda lewat satu portal.',
            'cta_label' => 'Lihat Semua Race',
            'cta_url' => '/events',
        ];
    }
}
