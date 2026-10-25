<?php

namespace Database\Seeders;

use App\Enums\Helpdesk\TicketCategory;
use App\Models\ReplyTemplate;
use Illuminate\Database\Seeder;

/**
 * Seeder idempoten untuk template balasan cepat helpdesk.
 * Aman dijalankan di lingkungan produksi maupun lokal.
 */
class ReplyTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'title' => 'Konfirmasi Pengiriman E-Ticket QR',
                'category' => TicketCategory::TIKET_QR,
                'body' => 'Halo {{reporter_name}}, terima kasih telah menghubungi tim Velosha Helpdesk mengenai tiket {{ticket_id}}. E-ticket resmi beserta kode QR dan detail nomor BIB dapat Anda unduh langsung pada menu Tiket & BIB di dashboard portal rider Anda. Jika kendala masih berlanjut, mohon lampirkan tangkapan layar transaksi Anda.',
                'keywords' => 'tiket, qr, barcode, unduh, download, bib, scan',
                'keyword_triggers' => ['tiket', 'qr', 'barcode', 'unduh', 'download', 'bib', 'scan'],
                'is_active' => true,
            ],
            [
                'title' => 'Instruksi Status dan Verifikasi Pembayaran',
                'category' => TicketCategory::PEMBAYARAN,
                'body' => 'Halo {{reporter_name}}, kami menerima tiket {{ticket_id}} terkait kategori {{ticket_category}}. Pembayaran via Virtual Account dan QRIS diproses secara otomatis dalam 5-15 menit. Pastikan nomor tagihan sesuai nominal unik. Jika pembayaran belum terverifikasi setelah 30 menit, silakan balas pesan ini dengan nomor referensi transaksi bank Anda.',
                'keywords' => 'bayar, pembayaran, qris, va, transfer, invoice, lunas, saldo',
                'keyword_triggers' => ['bayar', 'pembayaran', 'qris', 'va', 'transfer', 'invoice', 'lunas', 'saldo'],
                'is_active' => true,
            ],
            [
                'title' => 'Penyelesaian Masalah Akses dan Profil Akun',
                'category' => TicketCategory::AKUN,
                'body' => 'Halo {{reporter_name}}, perihal tiket {{ticket_id}} terkait {{ticket_category}}, Anda dapat memperbarui data diri atau mengatur ulang kata sandi melalui menu Profil di portal rider. Untuk kendala pergantian email terdaftar, mohon lampirkan foto identitas diri resmi untuk verifikasi keamanan.',
                'keywords' => 'akun, password, login, email, profil, reset, lupa, sandi',
                'keyword_triggers' => ['akun', 'password', 'login', 'email', 'profil', 'reset', 'lupa', 'sandi'],
                'is_active' => true,
            ],
            [
                'title' => 'Informasi Lokasi dan Pengambilan Race Pack',
                'category' => TicketCategory::LOKASI,
                'body' => 'Halo {{reporter_name}}, terkait pertanyaan {{ticket_category}} pada tiket {{ticket_id}}, pengambilan race pack (Race Expo) diadakan di venue utama sesuai jadwal yang tercantum pada halaman detail event. Harap membawa e-ticket QR resmi dan identitas diri saat penukaran fisik.',
                'keywords' => 'lokasi, venue, rute, expo, racepack, ambil, tempat, gpx, peta',
                'keyword_triggers' => ['lokasi', 'venue', 'rute', 'expo', 'racepack', 'ambil', 'tempat', 'gpx', 'peta'],
                'is_active' => true,
            ],
            [
                'title' => 'Tanggapan Standar Penerimaan Laporan Bantuan',
                'category' => TicketCategory::LAINNYA,
                'body' => 'Halo {{reporter_name}}, tiket {{ticket_id}} Anda telah kami terima dan masuk ke antrean operator Velosha. Tim kami akan segera meninjau uraian kendala Anda dan memberikan panduan lebih lanjut sesuai target waktu layanan kami.',
                'keywords' => 'bantuan, tanya, info, jadwal, kendala, komplain, lainnya',
                'keyword_triggers' => ['bantuan', 'tanya', 'info', 'jadwal', 'kendala', 'komplain', 'lainnya'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $data) {
            ReplyTemplate::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
