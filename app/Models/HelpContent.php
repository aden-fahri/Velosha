<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpContent extends Model
{
    protected $fillable = [
        'header_eyebrow',
        'header_title',
        'header_description',
        'header_meta',
        'contacts',
        'faq_groups',
        'cta_title',
        'cta_desc',
        'cta_label',
        'cta_url',
    ];

    protected function casts(): array
    {
        return [
            'contacts' => 'array',
            'faq_groups' => 'array',
        ];
    }

    public static function current(): static
    {
        return static::query()->first() ?? new static(static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'header_eyebrow' => 'Pusat Bantuan',
            'header_title' => 'Help Center',
            'header_description' => 'Semua yang perlu kamu tahu soal registrasi, pembayaran, pengambilan race pack, dan persiapan hari-H. Kalau jawabannya tidak ada di sini, hubungi WhatsApp kami.',
            'header_meta' => 'Diperbarui · 30 September 2026',
            'contacts' => [
                ['icon' => 'message-circle', 'name' => 'WhatsApp', 'info' => 'Respon 09.00–18.00 WIB', 'value' => '+62 812 3456 7890'],
                ['icon' => 'mail', 'name' => 'Email', 'info' => 'Balasan maksimal 1×24 jam', 'value' => 'cs@velosha.id'],
                ['icon' => 'help-circle', 'name' => 'Tanya Aide', 'info' => 'Panel di kanan bawah setiap halaman', 'value' => 'Mode pratinjau'],
            ],
            'faq_groups' => [
                [
                    'title' => 'Registrasi & Akun',
                    'items' => [
                        ['q' => 'Bagaimana cara mendaftar?', 'a' => 'Klik Lihat Semua Race di navigasi, pilih event, lalu tekan Daftar. Pilih kelas yang tersedia lalu lengkapi formulir identitas dan pembayaran.'],
                        ['q' => 'Boleh daftar lebih dari satu event?', 'a' => 'Boleh. Satu akun dapat mendaftar beberapa event sekaligus selama jadwal dan kelas tidak bertabrakan.'],
                        ['q' => 'Bagaimana cara mengganti data peserta?', 'a' => 'Buka Dashboard Peserta, pilih Detail Peserta, lalu tekan Edit. Perubahan nama atau NIK tidak dapat dilakukan setelah race pack diambil.'],
                    ],
                ],
                [
                    'title' => 'Pembayaran & Refund',
                    'items' => [
                        ['q' => 'Metode pembayaran apa saja yang diterima?', 'a' => 'QRIS, Virtual Account, Kartu Kredit, dan Virtual Account manual. Semua pembayaran terverifikasi otomatis dalam maksimal 15 menit.'],
                        ['q' => 'Kalau pembayaran gagal bagaimana?', 'a' => 'Status berubah menjadi Menunggu Pembayaran. Kamu bisa membayar ulang dari halaman E-Ticket dengan tombol Bayar Lagi.'],
                        ['q' => 'Apakah ada kebijakan refund?', 'a' => 'Pengembalian penuh tersedia sampai H-7. Setelah itu refund sesuai kebijakan organizer yang tertera di halaman detail event.'],
                    ],
                ],
                [
                    'title' => 'Race Pack & Hari-H',
                    'items' => [
                        ['q' => 'Kapan dan di mana pengambilan race pack?', 'a' => 'H-2 dan H-1 di Race Village. Bawa E-Ticket QR dan kartu identitas asli yang nama registrasinya sama.'],
                        ['q' => 'Apa saja isi race pack?', 'a' => 'Race number dengan timing chip RFID, bib penanda kategori, technical pouch, dan goodie bag yang berbeda tiap event.'],
                        ['q' => 'Jam berapa technical check?', 'a' => 'Technical check dibuka H-1 pukul 09.00 dan ditutup pukul 17.00. Peserta yang absen tidak boleh start.'],
                    ],
                ],
                [
                    'title' => 'Hasil & Sertifikat',
                    'items' => [
                        ['q' => 'Kapan hasil balapan dipublikasikan?', 'a' => 'Hasil provisional diumumkan maksimal 24 jam setelah finish dan menjadi final setelah verifikasi Race Comisaris.'],
                        ['q' => 'Bagaimana cara melihat ranking?', 'a' => 'Buka halaman Ranking, pilih event, lalu lihat leaderboard lengkap beserta waktu dan kecepatan rata-rata.'],
                        ['q' => 'Apakah sertifikat digital tersedia?', 'a' => 'Ya. Sertifikat dengan QR verifikasi dikirim ke email maksimal 7 hari setelah result final.'],
                    ],
                ],
                [
                    'title' => 'Istilah Sepeda',
                    'items' => [
                        ['q' => 'Apa itu Gran Fondo?', 'a' => 'Balap jarak jauh (umumnya 120KM ke atas) dengan kontrol waktu di tiap pos. Fokusnya menyelesaikan rute dalam batas waktu, bukan hanya kecepatan.'],
                        ['q' => 'Apa bedanya Medio Fondo dan Fun Ride?', 'a' => 'Medio Fondo adalah kelas jarak menengah (sekitar 60–100KM) dengan kontrol waktu ringan. Fun Ride jarak pendek santai tanpa kejaran waktu, cocok untuk pemula.'],
                        ['q' => 'Apa itu KOM?', 'a' => 'King of the Mountain — penghargaan untuk pencatat waktu tercepat di tanjakan (segmen menanjak) tertentu, misalnya KOM Puncak Menoreh.'],
                        ['q' => 'Apa itu Cut-Off Time (COT)?', 'a' => 'Batas waktu maksimal untuk menyelesaikan rute atau sampai di pos tertentu. Peserta yang melewati COT diminta berhenti balapan demi keselamatan.'],
                        ['q' => 'Apa itu Wave Start?', 'a' => 'Sistem start bergelombang: peserta dilepas bertahap per kelompok (Wave 1, Wave 2, dst.) untuk menghindari penumpukan di garis start.'],
                        ['q' => 'Apa itu BIB?', 'a' => 'Nomor start yang dipasang di sepeda dan baju. Wajib terlihat jelas sepanjang balapan agar panitia bisa mengenali peserta.'],
                        ['q' => 'Apa isi Race Pack?', 'a' => 'Paket yang diambil menjelang hari-H: nomor start, timing chip RFID, technical pouch, dan goodie bag. Detail isi berbeda tiap event.'],
                        ['q' => 'Apa itu Pos Hidrasi / Aid Station (AS)?', 'a' => 'Pos istirahat berkala di rute (AS 1, AS 2, dst.) tempat mengisi air minum, gel energi, dan cek kesehatan singkat.'],
                        ['q' => 'Apa itu Feed Zone?', 'a' => 'Pos istirahat utama yang lebih besar dari aid station biasa, biasanya menyediakan makanan ringan, mekanis sepeda, dan tim medis.'],
                        ['q' => 'Apa itu Drafting?', 'a' => 'Teknik bersepeda menempel di belakang peserta lain untuk menghemat tenaga dari hambatan angin. Umum di balap, tapi aturannya bisa berbeda tiap kelas.'],
                        ['q' => 'Apa itu file GPX?', 'a' => 'Berkas rute digital berisi jejak GPS. Diunduh lalu dibuka di aplikasi navigasi sepeda agar tidak salah belok di hari balapan.'],
                        ['q' => 'Apa itu Elevasi / Kenaikan Elevasi?', 'a' => 'Total ketinggian yang didaki sepanjang rute (satuan meter, ditulis +m). Angka lebih besar berarti tanjakan lebih menantang.'],
                        ['q' => 'Apa itu RFID Timing Chip?', 'a' => 'Chip di race number yang otomatis mencatat waktu melewati garis start, tiap pos, dan finish. Hasilnya jadi dasar ranking resmi.'],
                        ['q' => 'Apa artinya "Podium"?', 'a' => 'Upacara pemberian penghargaan untuk peserta tercepat per kelas; juga menyebut peringkat teratas di tabel hasil (misalnya "finisher podium").'],
                    ],
                ],
            ],
            'cta_title' => 'Masih ada yang mengganjal?',
            'cta_desc' => 'Tim kami siap membantu setiap hari kerja, 09.00–18.00 WIB.',
            'cta_label' => 'Lihat Semua Race',
            'cta_url' => '/events',
        ];
    }
}
