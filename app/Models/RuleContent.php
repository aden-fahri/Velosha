<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleContent extends Model
{
    protected $fillable = [
        'header_eyebrow',
        'header_title',
        'header_description',
        'header_meta',
        'groups',
        'sanksi_items',
        'sanksi_note',
        'cta_title',
        'cta_desc',
        'cta_label',
        'cta_url',
    ];

    protected function casts(): array
    {
        return [
            'groups' => 'array',
            'sanksi_items' => 'array',
        ];
    }

    public static function current(): static
    {
        return static::query()->first() ?? new static(static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'header_eyebrow' => 'Regulasi',
            'header_title' => 'Aturan Balap & Kode Etik',
            'header_description' => 'Ketentuan ini berlaku untuk seluruh event yang Curasi Velosha. Dengan membuka halaman ini, Anda dianggap menyetujui seluruh poin yang tercantum.',
            'header_meta' => 'Berlaku Musim 2026',
            'groups' => [
                [
                    'title' => 'Kendaraan & Peralatan',
                    'icon' => 'bike',
                    'items' => [
                        ['label' => 'Jenis sepeda', 'status' => 'allowed', 'text' => 'Road bike dan mountain bike diperbolehkan. Ban minimal 25mm untuk kategori kompetitif. Aero bar dan pelek carbon tetap boleh dipakai.'],
                        ['label' => 'Rem working', 'status' => 'mandatory', 'text' => 'Rem harus berfungsi penuh di kedua roda. Race Comisaris mengecek di technical check H-1. Rem rusak berarti tidak boleh start.'],
                        ['label' => 'Timing chip', 'status' => 'mandatory', 'text' => 'Chip RFID terpasang di race number dan wajib dipakai selama balapan. Chip hilang atau ditukar berujung diskualifikasi.'],
                        ['label' => 'E-Bike', 'status' => 'forbidden', 'text' => 'E-bike tidak boleh dipakai pada semua kategori, termasuk Fun Ride. Ban motor listrik akan disita di start gate.'],
                    ],
                ],
                [
                    'title' => 'Etika & Sportif',
                    'icon' => 'heart-handshake',
                    'items' => [
                        ['label' => 'No drafting pendek', 'status' => 'forbidden', 'text' => 'Menempel sangat dekat di belakang peserta lain untuk memanfaatkan angin secara berlebihan dinilai sebagai drafting dan dilarang.'],
                        ['label' => 'Zona kontes', 'status' => 'forbidden', 'text' => 'Dilarang menyenggol, memblokir, atau menyeberang di garis finis. Pelanggaran berat berujung diskualifikasi.'],
                        ['label' => 'Fair racing', 'status' => 'allowed', 'text' => 'Kerja sama dalam tim dan mengalah demi keselamatan adalah hal yang dihargai.'],
                        ['label' => 'Toleransi', 'status' => 'allowed', 'text' => 'Rider boleh melewati di tikungan sempit selama tidak menimbulkan bahaya bagi rider lain.'],
                    ],
                ],
                [
                    'title' => 'Kelengkapan Wajib',
                    'icon' => 'clipboard-check',
                    'items' => [
                        ['label' => 'Helm', 'status' => 'mandatory', 'text' => 'Helm bersertifikat wajib dipakai dari start gate sampai garis finis. Helm terbuka akan diminta dilepas di gate.'],
                        ['label' => 'Race number', 'status' => 'mandatory', 'text' => 'Dipasang di dada dan belakang, harus terlihat jelas oleh marshal dan kamera timing.'],
                        ['label' => 'E-Ticket QR', 'status' => 'mandatory', 'text' => 'Ditunjukkan saat pengambilan race pack dan pengecekan di start gate.'],
                        ['label' => 'Kartu identitas', 'status' => 'mandatory', 'text' => 'Wajib untuk pengambilan race pack dan kondisi darurat. Nama harus cocok dengan data registrasi.'],
                    ],
                ],
                [
                    'title' => 'Cuaca, Keselamatan & Rescue',
                    'icon' => 'cloud-sun',
                    'items' => [
                        ['label' => 'Perubahan jadwal', 'status' => 'note', 'text' => 'Race dapat ditunda, dipersingkat, atau dibatalkan karena cuaca ekstrem. Keputusan diumumkan paling lambat H-1 pukul 18:00.'],
                        ['label' => 'Peringatan BMKG', 'status' => 'note', 'text' => 'Bila BMKG menetapkan status peringatan, race dibatalkan tanpa biaya registrasi.'],
                        ['label' => 'Tim medis', 'status' => 'allowed', 'text' => 'Ambulans dan paramedis tersedia di setiap feed zone dan titik rawan. Laporan kondisi ke marshal sangat diharapkan.'],
                        ['label' => 'Asuransi', 'status' => 'note', 'text' => 'Setiap peserta terlindungi asuransi accident sejak masuk race village hingga melintasi garis finis.'],
                    ],
                ],
            ],
            'sanksi_items' => [
                ['title' => 'Peringatan', 'text' => 'Dicatat di log race. Pelanggaran pertama umumnya tidak berujung diskualifikasi.', 'tone' => 'neutral'],
                ['title' => 'Penalti Waktu', 'text' => 'Penalti 1–5 menit ditambahkan, atau penempatan di belakang waktu tercatat pada tahap tersebut.', 'tone' => 'race'],
                ['title' => 'Diskualifikasi', 'text' => 'Hasil dihapus dari leaderboard, kehilangan medial, dan banned dari event berikutnya.', 'tone' => 'danger'],
            ],
            'sanksi_note' => 'Seluruh keputusan Race Comisaris bersifat final dan tidak dapat diganggu gugat dalam 24 jam.',
            'cta_title' => 'Sudah paham aturannya?',
            'cta_desc' => 'Lihat event yang tersedia dan pilih kategorimu.',
            'cta_label' => 'Lihat Semua Race',
            'cta_url' => '/events',
        ];
    }
}
