# RANGKUMAN LENGKAP PROYEK VELOSHA

Dokumen ini merupakan rangkuman menyeluruh (*comprehensive product & technical summary*) mengenai platform **Velosha**, mencakup tumpukan teknologi (*technology stack*), identitas dan filosofi desain (*design system*), peta alur aplikasi dari sisi pengguna maupun penyelenggara, status implementasi saat ini, serta keunggulan kompetitif utama platform.

---

## 1. Tech Stack (Teknologi yang Digunakan)

Velosha dibangun di atas fondasi teknologi modern *TALL Stack-aligned* yang dirancang untuk performa tinggi, kemudahan pemeliharaan (*maintainability*), dan pengalaman pengguna (*user experience*) yang interaktif serta responsif:

| Lapisan / Komponen | Teknologi | Keterangan & Penggunaan |
| :--- | :--- | :--- |
| **Backend Framework** | **Laravel 11.x (PHP 8.2+)** | Mengelola *routing*, *business logic*, Eloquent ORM, migrasi database, middleware berbasis peran (*RBAC*), autentikasi, serta API endpoint. |
| **Reactivity / Frontend State** | **Livewire 3** | Menghadirkan antarmuka dinamis (*Single Page Application feel*) tanpa perlu API terpisah. Digunakan pada katalog race (`event-catalog`), proses multi-step checkout pendaftaran, filter live, tiket helpdesk interaktif, dan widget asisten AI. |
| **Client Interaction** | **Alpine.js (v3)** | Menangani micro-interactions di sisi peramban: *accordion*, *client-side instant search* FAQ & glosarium, modal pop-up, *sticky navigation scroll-spy*, dan *smooth transitions*. |
| **CSS & Design Engine** | **Tailwind CSS v3 + Scoped Project Variables** | Tata letak utilitas modular yang diperkaya dengan sistem token CSS kustom untuk neobrutalisme / screen-print vintage style. |
| **Ikonografi & Aset Visual** | **Lucide Icons & Native Scalable Vectors (SVG)** | Menggunakan set ikon Lucide teroptimasi dan ornamen vektor geometris native neobrutalisme dengan *aspect ratio lock*. |
| **Kecerdasan Buatan (AI)** | **Google Gemini 2.5 Flash / Flash Lite (Mizu AI Engine)** | Asisten AI virtual "Mizu" yang tersemat langsung untuk menjawab pertanyaan peserta seputar regulasi balap, jadwal event, alur pengambilan race pack, hingga status tiket aduan secara *real-time*. |
| **Database & Penyimpanan** | **MySQL / MariaDB** | Database relasional untuk menyimpan tabel pengguna, event, kategori race, pendaftaran (*registrations*), transaksi tiket pembayaran, bib RFID, serta tiket helpdesk. |

---

## 2. Style & Design System (Filosofi & Bahasa Desain)

Gaya visual Velosha mengusung tema **Retro-Modern Cycling Neobrutalism (Screen-Print & Comic-Editorial Style)**. Gaya ini menggabungkan ketegasan desain neobrutalisme modern dengan nuansa balap sepeda klasik vintage (*Gran Fondo / Giro / Tour heritage*).

### A. Palet Warna Resmi (Color Tokens)
- **Ink (`#10282E` / `var(--ink)`)**: Warna tinta utama untuk teks, border tegas, bayangan tajam (*hard offset shadow*), dan elemen struktural.
- **Butter / Amber (`#F8E3B5` / `var(--butter)`)**: Latar belakang banner hero dan page header yang hangat, nyaman di mata, dan konsisten di seluruh halaman utama.
- **Orange / Racing Terracotta (`#DE7119` / `var(--orange)`)**: Aksen energi, tombol aksi utama (*Call-to-Action*), lencana penanda, serta hover chevron navigasi.
- **Cream / Warm Canvas (`#FFFDF8` / `var(--card)`)**: Latar kartu konten dan panel ringkasan accordion untuk menjaga keterbacaan (*readability*).
- **Page Background (`#FBF4E6` / `var(--paper)`)**: Latar kanvas utama situs.
- **Pine Green (`#1F6F4A`)**: Indikator status sukses, kelengkapan wajib, dan lencana sanksi ringan.
- **Mustard Gold (`#F4BE3F`)**: Lencana informasi/anjuran dan fokus ring aksesibilitas keyboard.

### B. Karakteristik Visual Utama
1. **Borders & Shadows**: Border solid tegas `2.5px` hingga `3.5px solid #10282E` dengan *hard drop shadow* tanpa blur (`3px 3px 0 #10282E` hingga `8px 8px 0 #10282E`).
2. **Tipografi**:
   - **Headings**: `Bricolage Grotesque` (Bobot 800/900) – menghadirkan tipografi tajam, kuat, dan bergaya cetak poster balap sepeda.
   - **Body Text**: `Inter` (Bobot 400, 500, 600, 700) – menjamin tingkat keterbacaan tinggi pada rincian teknis, peraturan, dan instruksi transaksi.
3. **Ornamen Geometris Tanpa Distorsi**: Lingkaran, persegi berotasi, dan poligon geometris dengan *aspect-ratio lock* yang tidak pernah terdistorsi atau menimpa teks bacaan.
4. **Watermark Tipografis**: Bayangan tipografis berukuran besar (seperti kata `VELOSHA` di latar belakang footer) dengan transparansi rendah (`opacity: 0.022`) sebagai elemen tekstur elegan.
5. **Aksesibilitas & Standar**:
   - Target sentuh (*tap target*) mobile selalu minimal $\ge 44\text{ px}$.
   - Ukuran teks utama $\ge 15\text{ px}$.
   - *Focus ring* keyboard standar: `3px solid #F4BE3F` dengan offset `3px`.
   - Menghormati pengaturan pengguna `@media (prefers-reduced-motion: reduce)`.

---

## 3. Peta Alur Aplikasi Saat Ini (Current Application Flow)

Aplikasi Velosha saat ini telah mencakup ekosistem lengkap dari eksplorasi publik, pendaftaran, sistem tiket, hingga portal layanan mandiri peserta:

```
[1. Beranda / Landing Page] 
       │
       ├──> [2. Kalender Event (/events)] ──> Filter Kategori (Gran Fondo / Medio Fondo / Fun Ride / KOM)
       │          │
       │          └──> [3. Detail Race (/events/{slug})] ──> Profil Elevasi, Rute GPX & Kuota
       │                     │
       │                     └──> [4. Multi-Step Checkout (/checkout)] ──> Pilih Kelas, Isi Identitas & Asuransi
       │                                │
       │                                └──> [5. Invoice / E-Ticket (/ticket/{id})] ──> QR Code, Nomor BIB & RFID
       │
       ├──> [6. Pusat Bantuan (/help)] ──> Pencarian Instan Alpine.js, FAQ Terkategori & Glosarium 14 Istilah
       │          │
       │          └──> [7. Buat Tiket Bantuan (/dashboard/bantuan/buat)] ──> Aduan Kendala Pendaftaran/BIB
       │
       ├──> [8. Regulasi & Kode Etik (/rule)] ──> Sticky Scroll-Spy Nav, Standar Perlengkapan & Seksi Sanksi
       │
       ├──> [9. Tentang Velosha (/about)] ──> Manifesto Komunitas, Nilai Platform & Spesifikasi Teknis
       │
       └──> [10. Portal Dashboard Rider (/dashboard)] ──> Manajemen Profil, Tiket Saya & Status Helpdesk
```

### Rincian Setiap Alur:
1. **Eksplorasi Publik & Katalog Race**:
   - Pengunjung dapat melihat balapan unggulan di halaman beranda atau menjelajahi katalog interaktif `/events`.
   - Tersedia filter kategori dinamis via Livewire (*Gran Fondo, Medio Fondo, Fun Ride, KOM Climb*) dan pencarian nama event, rute, atau kota.
2. **Pendaftaran & Multi-Step Checkout**:
   - Peserta masuk/mendaftar akun Velosha.
   - Mengisi formulir verifikasi identitas (NIK, kontak darurat, golongan darah) dan ukuran jersey.
   - Memilih metode pembayaran instan (QRIS, Virtual Account BCA, Mandiri, BRI, BNI, Kartu Kredit).
3. **Penerbitan E-Ticket & Race Pack Readiness**:
   - Begitu transaksi berhasil terverifikasi otomatis, sistem menerbitkan halaman E-Ticket resmi `/ticket/{id}` lengkap dengan kode QR terenkripsi, alokasi nomor BIB unik, dan kesiapan timing chip RFID.
4. **Pusat Bantuan & Asisten Mizu**:
   - Halaman `/help` dilengkapi fitur pencarian *client-side* instan tanpa reload, 4 kelompok FAQ utama (Registrasi, Pembayaran, Race Pack, Hasil), serta glosarium istilah balap (*Gran Fondo, Drafting, Wave Start, COT, GPX*).
   - Asisten cerdas **Tanya Mizu** siap membantu 24/7 di sudut kanan bawah.
   - Jika jawaban tidak ditemukan, pengguna langsung diarahkan membuat tiket bantuan di Portal Peserta (`/dashboard/bantuan/buat`).
5. **Regulasi Balap & Sticky Scroll-Spy**:
   - Halaman `/rule` mengorganisasi aturan balap ke dalam 4 kategori besar (Kendaraan, Etika, Kelengkapan, Cuaca) dan tabel sanksi resmi (Peringatan, Penalti Waktu, Diskualifikasi) dengan bar navigasi *sticky* yang mendeteksi posisi baca pengguna (*Intersection Observer*).

---

## 4. Keunggulan Platform Velosha (Core Advantages)

1. **Satu Ekosistem Terpadu untuk Endurance Ride**:
   - Mengintegrasikan seluruh siklus event: publikasi jadwal, pendaftaran online, penerbitan nomor BIB otomatis, hingga panduan teknis hari-H dalam satu platform mandiri.
2. **Performa Sangat Ringan & Cepat (Zero-Lag UI)**:
   - Kombinasi Laravel 11 dan Livewire 3 meminimalkan beban bundle JavaScript berat (tanpa overhead framework SPA eksternal seperti React/Vue bundler kompleks), menghasilkan waktu muat halaman *sub-second*.
3. **Pencarian Cerdas & Aksesibilitas Teruji**:
   - Pencarian bantuan dan regulasi berjalan instan di memori peramban (*client-side filtering*), menyaring pertanyaan maupun jawaban sekaligus membuka item yang relevan secara otomatis.
   - Antarmuka dirancang memenuhi standar aksesibilitas keyboard dan *reduced-motion*.
4. **Identitas Visual Kuat & Berkarakter**:
   - Tidak menggunakan template generik; bahasa desain *Cartoon / Screen-Print Neobrutalism* memberikan diferensiasi visual yang sangat kuat di antara platform pendaftaran olahraga di Indonesia.
5. **Dukungan Asisten AI Cerdas Terintegrasi (Mizu AI)**:
   - Dukungan bot AI kontekstual yang dilatih khusus memahami aturan lomba sepeda, memangkas beban kerja tim customer service secara signifikan.
6. **Desain Mobile-First Adaptif**:
   - Seluruh halaman, mulai dari header, kartu kontak, grid aturan, hingga footer teroptimasi sempurna untuk layar ponsel pintar ($390\text{ px}$) tanpa pergeseran atau *horizontal scrollbar* yang mengganggu.

