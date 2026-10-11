# Product Requirements Document (PRD) — Desain & UI/UX
## Velosha: Platform Maraton & Balap Sepeda Indonesia

> **Dokumen:** Spesifikasi Desain Antarmuka, Sistem Desain, & Pengalaman Pengguna (UI/UX)  
> **Versi:** 2.1  
> **File:** `Velosha/PRD/PRD-Desain-UI-UX.md`  
> **Target Pengguna:** Peserta Balap Sepeda (*Cyclists*), Race Director, Panitia Penyelenggara (*Marshalls*)  

---

## 1. Filosofi Desain & Karakter Visual

Desain antarmuka **Velosha** mengusung konsep **"Architectural Precision & Quiet Luxury Sports"**. Berbeda dengan situs balap sepeda konvensional yang kerap menggunakan warna neon mencolok dan layout ramai, Velosha menggabungkan keanggunan editorial majalah fotografi dengan presisi geometris instrumen balap.

### Tiga Pilar Estetika:
1. **Geometric Sharpness (Ketajaman Geometris):** Struktur layout kotak dengan sudut tajam yang tegas (1px luar, 3px dalam), memberikan impresi profesional, terstruktur, dan disiplin tinggi layaknya dunia olahraga sepeda presisi (*aero engineering*).
2. **Editorial Impact (Kekuatan Visual Terkurasi):** Memanfaatkan fotografi aksi peleton asli beresolusi tinggi, ruang negatif (*whitespace*) yang lapang, dan tipografi yang bersih tanpa elemen visual yang bersaing.
3. **Ergonomic Data Density (Kepadatan Data Ergonomis):** Informasi esensial (jarak KM, elevasi tanjakan, batas cut-off time, titik pos hidrasi, dan nomor dada BIB) disajikan secara ringkas, jelas, dan mudah dipindai dalam hitungan detik.

---

## 2. Analisis & Inspirasi 3 Referensi Utama

```
                   ┌──────────────────────────────────────────────┐
                   │               VELOSHA UI/UX                  │
                   └──────────────────────┬───────────────────────┘
                                          │
         ┌────────────────────────────────┼────────────────────────────────┐
         ▼                                ▼                                ▼
┌─────────────────┐              ┌─────────────────┐              ┌─────────────────┐
│   GFNY World    │              │    NOMADICT     │              │     Airbnb      │
│  (Race Finder)  │              │ (Visual Impact) │              │  (Experiences)  │
├─────────────────┤              ├─────────────────┤              ├─────────────────┤
│• Data race rapi │              │• Foto aksi besar│              │• Category tabs  │
│• Peta rute & AS │              │• Warna tenang   │              │• Card ergonomis │
│• Penomoran BIB  │              │• Banyak spasi   │              │• Harga jelas    │
│• Profil elevasi │              │• Teks ringkas   │              │• Search simpel  │
└─────────────────┘              └─────────────────┘              └─────────────────┘
```

### 2.1 GFNY World (Race Finder)
- **Yang Diadopsi:**
  - Struktur data balap komprehensif: Lokasi kota, tanggal pelaksanaan, jarak kilometer resmi, profil tanjakan (*King of Mountain*), dan alokasi gelombang start (*wave corral*).
  - Peta interaktif rute sirkuit balap yang menampilkan posisi pos hidrasi (*aid stations/feed zones*).
  - Sistem penomoran dada resmi (BIB Number) yang dipersonalisasi per kategori lomba.
- **Yang Ditinggalkan:**
  - Warna hijau neon terang (`#99FF00`) dieliminasi karena menimbulkan kelelahan mata (*visual fatigue*).
  - Menu navigasi yang terlalu banyak (franchise, merchandise store, VIP club) disederhanakan menjadi navigasi inti yang terfokus.

### 2.2 NOMADICT
- **Yang Diadopsi:**
  - Gaya visual editorial: Menempatkan foto aksi peleton sepeda sebagai jangkar emosional (*emotional anchor*) utama halaman.
  - Palet warna "tenang" (*muted & grounded*) yang mengutamakan kontras hitam-putih murni dan aksen terukur.
  - Teks deskripsi yang padat dan langsung pada inti informasi, menghindari paragraf promosi yang bertele-tele.
- **Yang Ditinggalkan:**
  - Layout ala majalah/artikel blog dengan teks panjang yang tidak cocok untuk alur pendaftaran tiket cepat.
  - Sidebar widget dan banner promosi yang mengganggu fokus pengguna.

### 2.3 Airbnb Experiences
- **Yang Diadopsi:**
  - Bar kategori horizontal (*Category Tabs*) di atas katalog yang memungkinkan pengguna berganti filter jarak dalam satu klik.
  - Anatomi kartu event: Foto proporsional di atas → chip status sisa kuota → tanggal & lokasi → judul event → chip spesifikasi (elevasi & COT) → harga pendaftaran transparan di sudut bawah.
  - Bar pencarian ergonomis dengan input teks minimalis dan filter lokasi/waktu.
- **Yang Ditinggalkan:**
  - Sistem penilaian bintang multi-aspek (komunikasi, lokasi, dll) yang tidak relevan untuk balap sepeda berlisensi resmi.
  - Mekanisme *infinite scroll* yang melelahkan diganti dengan paginasi angka yang rapi dan terukur.

---

## 3. Sistem Desain (Design Tokens)

### 3.1 Border Radius Keras (Strict Geometric Radius)
Aturan border radius merupakan identitas visual terpenting aplikasi Velosha:
| Level Komponen | Nilai Radius | Class CSS Utility | Contoh Elemen |
|---|---|---|---|
| **Outer / Primary** | `1px` | `rounded-[1px]` / `.velo-box-1` | Container Card terluar, Tombol Utama & Sekunder, Input Form, Navbar, Modal Pop-up, Dropdown Frame |
| **Inner / Nested** | `3px` | `rounded-[3px]` / `.velo-box-3` | Badge Kategori, Tag Jarak (130 KM), Chip Spesifikasi, Thumbnail Foto di dalam Galeri, Progress Bar |
| **Anti-Pola (DILARANG)** | `9999px` / `full` | `rounded-full` | **Dilarang keras pada tombol**. Tombol berbentuk kapsul merusak karakter arsitektural dan ketajaman desain. |

*Catatan pengecualian: Bentuk lingkaran sempurna hanya diizinkan untuk avatar pengguna (opsional) dan indikator status dot kecil (misal: titik hijau online).*

### 3.2 Palet Warna (Light Mode — Oranye + Abu-abu)

Aplikasi memakai **empat warna inti yang sudah disetujui**: Amber Race, Warm Gray, Dark Teal, dan Light Teal. Dark Teal bertindak sebagai warna tekstual utama, sedangkan Amber Race menjadi warna aksi. Light Teal **hanya dekoratif** dan tidak boleh dipakai untuk teks.

```
PUTIH NAVBAR        WARM GRAY          DARK TEAL          LIGHT TEAL         AMBER RACE
#FFFFFF             #DEE3E2            #116979            #18B0B0            #DE7119
[Permukaan Kartu]   [Latar Aplikasi]   [Teks & Panel Gelap] [Dekoratif Murni] [Warna Aksi]
```

### Peran Warna

| Peran | Nilai HEX | Token Tailwind | Karakter & Penggunaan |
|---|---|---|---|
| **Latar Aplikasi** | `#DEE3E2` | `velosurface` | Warm gray seluruh `<body>`. Memisahkan kartu putih dari halaman. |
| **Permukaan Kartu** | `#FFFFFF` | `bg-white` | Navbar, navbar, dan semua kartu. Wajib putih agar kontras dengan warm gray. |
| **Permukaan Alt** | `#F1F4F4` | `velosurfacealt` | Section bergantian, legend, tabel info. |
| **Border Halus** | `#D5D9D8` | `veloborder` | Garis pemisah 1px kartu/navbar. |
| **Border Kuat** | `#B8BFBE` | `veloborderstrong` | Border saat input aktif atau card terpilih. |
| **Dark Teal (WARNA TEKS UTAMA)** | `#116979` | `teal-700` | Judul, heading, panel gelap, chatbot, label ink. **6.32:1** di atas putih, **4.88:1** di atas warm gray. |
| **Ink Gelap** | `#0A353E` – `#0E7C7E` | `teal-900` – `teal-600` | Judul besar dan angka statistik yang butuh kontras maksimum. |
| **Teks Sekunder** | `#556066` / `#5F6A70` | `velomutedlight` / `velomuted` | Metadata, deskripsi, caption, dan teks muted. |
| **Light Teal (DEKORATIF SAJA)** | `#18B0B0` | `teal-500` | **Dilarang untuk teks.** Hanya garis, dot, grid pattern, dan garis chart. Di atas putih hanya **2.67:1**. |
| **Light Teal Lembut** | `#DBEFEF` / `#F0F7F7` | — | Fill chart dan permukaan aksen yang menenangkan. |
| **Amber Race (Warna Aksi)** | `#DE7119` | `race-500` | Aksen signature: border kartu pilihan, checkbox/radio aktif, aksen dekoratif. **Bukan** latar tombol solid. |
| **Amber Solid (TOMBOL)** | `#B2590F` | `race-600` | **Latar semua tombol solid + teks putih.** **4.85:1** — memenuhi WCAG AA. |
| **Amber Hover** | `#A0520C` | `race-700` | State hover tombol solid. |
| **Amber Ink** | `#5D320C` | `race-900` | Teks oranye di atas permukaan terang. |
| **Amber Tint** | `#FDF3E9` | `race-50` | Latar lembut untuk badge beraksen amber. |
| **Overlay Gelap** | `#0A1F24` | — | Overlay auth dan hero overlay foto. |
| **Success / Paid** | `#065F46` / `#ECFDF5` | `velogreen` / `emerald-*` | Hijau botol tua untuk "LUNAS", "TERVERIFIKASI", sisa slot aman. |
| **Warning / Pending** | `#A0520C` / `#FDF3E9` | `race-700` / `race-50` | Status "MENUNGGU PEMBAYARAN" dan slot hampir habis. |
| **Emergency / Medical** | `#991B1B` / `#FEF2F2` | `red-*` | Kontak darurat, golongan darah, peringatan kritis. |
### Catatan Aksesibilitas Kontras

Empat aturan yang **wajib** dijaga saat menambah komponen baru:

1. **Di atas permukaan amber terang (`race-400` / `race-300`) teks memakai `teal-900` atau `race-900`, bukan `text-white`.** Putih di atas `#E58F42` hanya **2.52:1**. `teal-900` di atas `#E58F42` = **5.24:1**, `race-900` di atas `#EFA96A` = **5.48:1**. Ini berlaku untuk hero, banner halaman, dan card berlatar amber.
2. **Tombol ada dua resep, pilih sesuai latar.** Di atas putih/gelap: `bg-race-600` + teks putih (4.85:1). Di atas amber terang: `bg-race-300` + `text-race-900` (5.48:1) — lebih cerah dan tetap AA.
3. **`#18B0B0` tidak pernah menjadi warna teks atau border teks.** Hanya bentuk dekoratif kecil karena 2.67:1 di atas putih. Di atas `teal-900` baru mencapai 4.95:1.
4. **Teks di atas warm gray memakai dark teal**, bukan abu-abu medium. Body teks minimum `#556066` (4.98:1); teks kecil di atas `#DEE3E2` sebaiknya `teal-700` atau lebih gelap.

### Pola Permukaan Amber

Permukaan amber terang dipakai untuk **hero** dan **banner halaman** — dua area terbesar di halaman. Ini yang membuat Amber terasa "lebih terang" tanpa mengorbankan kontras teks:

```
Permukaan : bg-race-400  (#E58F42 golden amber)
Teks      : text-teal-900 (#0A353E)   5.24:1
Aksen     : bg-teal-900 / bg-teal-700  (bar, chip)
CTA       : bg-teal-700 + teks putih    6.32:1
CTA alt   : bg-race-300/70 + text-race-900
Border    : border-race-500
```

### Pallete yang Dihapus

> **Deep Navy `#1E3A5F`** dan **Abu-abu Tailwind (`gray-*`)** sudah dihapus dari sistem desain. Abu-abu digantikan **warm gray** untuk permukaan dan **dark teal** untuk teks. `blue-*`, `slate-*`, dan `amber-*` Tailwind juga tidak lagi dipakai sebagai warna brand — hanya `emerald-*` dan `red-*` yang dipertahankan karena perannya sebagai warna status universal.


### 3.3 Logo Resmi

Logo aplikasi memakai **file gambar** `public/images/logo-velosha.jpg`. File ini adalah **hasil crop** dari logo asli: bagian nama "VELOSHA" di bawah monogram telah dihapus, sehingga yang tampil hanya lambang (monogram + roda gigi) dengan proporsi ±1.08:1.

Aturan penggunaan:
- Jangan menambahkan teks "VELOSHA" atau tagline di sebelah logo — nama sudah menyatu di dalam lambang bila diperlukan.
- Jangan memuat file asli yang belum di-crop (`projek-kelas12/LogoVelosha.jpg`).
- Ukuran: navbar `h-14` (56px), footer `h-20` (80px), selalu dengan `object-contain`.
- Logo bersifat monokrom gelap; tidak boleh diberi warna oranye atau filter.

### 3.4 Kepadatan Teks (Copy Density)

Prinsip "Editorial Impact" dan "Quiet Luxury Sports" menuntut **teks yang ringkas**:

- Hindari paragraf deskriptif panjang. Maksimal 1–2 kalimat per blok.
- Biarkan foto, tipografi besar, dan whitespace yang membawa pesan.
- Copy Bahasa Indonesia yang tegas dan singkat — bukan bahasa pemasaran yang berputar-putar.
- Label tombol dan chip sebaiknya satu kata atau satu angka.

### 3.5 Tipografi & Skala Teks
Menggunakan **Inter** dari Google Fonts yang dirancang khusus untuk keterbacaan tinggi di layar komputer maupun ponsel pintar:

| Tingkatan Teks | Ukuran (px / rem) | Weight | Letter Spacing | Penerapan |
|---|---|---|---|---|
| **Display Hero (H1)** | 48px - 60px (3rem - 3.75rem) | 800 - 900 | `-0.025em` | Judul utama Hero Landing Page |
| **Page Title (H1)** | 30px - 36px (1.875rem - 2.25rem) | 800 | `-0.02em` | Judul halaman Katalog, Detail, Dashboard |
| **Section Title (H2)**| 22px - 26px (1.375rem - 1.625rem) | 700 | `-0.015em` | Judul section rute, fasilitas, galeri |
| **Card Heading (H3)** | 16px - 18px (1rem - 1.125rem) | 700 | `0em` | Judul event pada card dan modal |
| **Body Standard** | 14px (0.875rem) | 400 - 500 | `0em` | Teks paragraf, input form, rincian biaya |
| **Caption / Meta** | 12px (0.75rem) | 500 - 600 | `+0.01em` | Tanggal, lokasi, label form, breadcrumb |
| **Badge / Overline** | 10px - 11px (0.625rem - 0.6875rem)| 700 - 800 | `+0.05em` | Tag Kategori (GRAN FONDO), status kuota |
| **Monospace Data** | 14px - 40px (Font Monospace) | 700 - 900 | `+0.02em` | Nomor BIB (`VLS-GF-0488`), Order ID, Waktu |

### 3.6 Sistem Bayangan (Subtle Shadows)
- **Card Default:** `box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05)` (hampir tidak terlihat, menjaga kebersihan halaman).
- **Card Hover:** `box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08)` (sedikit terangkat halus saat kursor mengarah).
- **Modal / Floating Panel:** `box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15)`.

---

## 4. Rincian Layout 9 Halaman Utama

### 4.1 Master Layout & Shell Navigasi
- **Header / Navbar Sticky:**
  - Latar belakang putih dengan *backdrop blur* 95% (`bg-white/95`), border bawah 1px `#D5D9D8` (`veloborder`), tinggi 80px (`h-20`).
  - Kiri: Logo resmi `logo-velosha.jpg` (tinggi 56px, `h-14`, `object-contain`).
  - Tengah: Menu navigasi ringkas (*Beranda, Race, Detail, Rute*). Link aktif ditandai **amber** (`text-race-500` + `bg-race-50` + `border-b-2 border-race-500`); link non-aktif `text-[#4b555b]` dengan **hover dark teal** (`hover:text-teal-700 hover:bg-teal-50`).
  - Kanan: Tombol akses cepat *"Tiket"* dan tombol primary amber *"Masuk"* (`bg-race-600`).
  - Bebas dari banner pengumuman atas bertingkat agar tidak menyerupai feed media sosial.
- **Footer Minimalis:**
  - Latar `#F1F4F4` (`velosurfacealt`) dengan border atas `veloborder`, teks `#4b555b`; judul kolom memakai **dark teal**.
  - 4 kolom ringkas: Logo (tinggi 80px) + tagline, Kategori, Bantuan, dan grid metode pembayaran.
  - Subfooter 2 baris: copyright dan link Privasi / Ketentuan.

### 4.2 Halaman 1: Beranda (Landing Page)
- **Hero Section:**
  - Visual: Menggunakan foto peleton maraton asli pebalap dengan nomor dada resmi (`/images/hero-peloton.jpg`).
  - Gradien: Gelap di sisi kiri untuk keterbacaan teks judul putih, transparan di sisi kanan agar aksi peleton sepeda dan warna jersey oranye balap tampak hidup.
  - Judul: *"Taklukkan Rute Endurance Nusantara."* yang padat dan berani.
  - Subjudul: Kalimat tunggal elegan tanpa badge bertumpuk.
  - Tombol Aksi: Tombol oranye balap *"Eksplorasi Race"* dan tombol kaca transparan *"Sorotan: Borobudur 130KM"*.
  - Strip Metrik: 4 kolom ringkas dengan angka tebal (12 Race, 8.500+ Cyclist, 1.820 KM, 100% RFID).
- **Category Tabs (Airbnb Style):**
  - Tab kategori horizontal yang dapat digeser di mobile: *Semua Race, Gran Fondo (120KM+), Medio Fondo (60-90KM), Fun Ride, King of Mountain (KOM)*.
- **Grid Race Pilihan:**
  - Card 3 kolom dengan foto tajam ber-radius 1px, badge kategori bersarang 3px di kiri atas, dan indikator sisa slot di kanan bawah foto.
  - Chip spesifikasi: Kotak abu-abu kecil 3px menampilkan Elevasi (+1.450m), Cut-Off Time (7 Jam), dan Titik Pos Hidrasi (4 Titik).
  - Harga ditampilkan transparan di sudut kiri bawah kartu.
- **Peta Rute Interaktif (GFNY Style):**
  - Embed Google Maps rute resmi Borobudur loop 130KM.
  - Di samping peta: Kartu narasi 3 titik pos krusial (Start Gate Candi, Feed Zone KM 35 & 72, dan Sektor Tanjakan Menoreh).
  - Di bawah peta: Visualisasi profil elevasi tanjakan dengan grafik garis SVG.
- **Teaser Video YouTube 16:9:**
  - Frame video dokumentasi berasio 16:9 bersudut kotak 1px tanpa rounded berlebihan.
- **Fasilitas Termasuk:**
  - 4 kartu fasilitas: RFID Timing Chip, Medali Finisher Logam, Asuransi Kecelakaan, Recovery Lunch.
- **FAQ:**
  - Akordeon kotak 1px menjawab pertanyaan seputar kualifikasi sepeda (larangan aero bar), pengambilan race pack, dan metode bayar.

### 4.3 Halaman 2: Katalog Event (Race Finder)
- **Header Halaman Bersih (Clean Solid Header):**
  - Menggunakan warna solid **Dark Teal** (`#116979` / `bg-teal-700`) tanpa gambar latar belakang untuk menjaga kejelasan teks dan ketenangan visual.
  - Menghilangkan badge bertumpuk (seperti *"Race Finder & Kalender"*) demi estetika minimalis dan fokus langsung pada judul *"Jelajahi Maraton Sepeda Indonesia"*.
- **Bar Pencarian & Filter Multi-Parameter:**
  - 4 kolom kontrol sejajar: Input pencarian teks kota, dropdown kategori jarak, dropdown bulan lomba, dan pengurutan (*sorting*).
- **Indikator Filter Aktif:**
  - Tag chip 3px dengan tombol silang (x) untuk menghapus filter satu per satu, serta tombol *"Reset Semua"*.
- **Grid 6 Event Terkurasi:**
  - Borobudur Gran Fondo (130 KM), Bromo Summit KOM (95 KM), Bali Sunrise Fondo (75 KM), Lombok Mandalika Fondo (142 KM), Bandung Highland Classic (110 KM), dan Jogja Royal Heritage (45 KM).
- **Paginasi Geometris:**
  - Tombol halaman kotak 1px bernomor tegas dengan penanda halaman aktif berwarna dark teal/amber.

### 4.4 Halaman 3: Detail Event & Profil Rute
- **Header Bersih Tanpa Breadcrumb:**
  - Solid **Dark Teal** (`#116979` / `bg-teal-700`) tanpa gambar background. Breadcrumb berulang (*Beranda / Daftar Event / Gran Fondo Borobudur*) telah dihilangkan agar layout langsung menyajikan judul utama dan waktu/lokasi balap secara tegas dan bersih.
- **Galeri Foto Editorial (Airbnb 1+4 Grid):**
  - 1 foto besar utama di kiri (mengisi 2 kolom) + 4 foto pendukung di kanan dalam grid 2x2.
- **Strip Spesifikasi Utama:**
  - 4 kotak spesifikasi horizontal: Jarak Tempuh, Total Elevasi, Cut-off Time, Jumlah Aid Station.
- **Peta Sirkuit & Pos Hidrasi:**
  - Google Maps layar lebar dilengkapi tombol unduh file GPX untuk cyclocomputer Garmin/Wahoo.
  - Legenda warna 4 titik aid station: KM 35 (Hydration), KM 72 (Energy Gel), KM 98 (KOM Summit), KM 115 (Final Push).
- **Grafik Profil Elevasi Tanjakan:**
  - Grafik profil kontur tanjakan dengan indikator gradien maksimal (16.4%) dan penanda titik KOM di puncak 920m ASL.
- **Jadwal Race Weekend:**
  - Timeline kartu: Hari Jumat-Sabtu (Pengambilan Race Pack & Bike Expo) dan Hari Minggu (Race Day, pelepasan wave, dan podium).
- **Sticky Booking Sidebar:**
  - Card melayang di sisi kanan yang tetap terlihat saat scroll: Pilihan jarak radio card, sisa slot, checklist fasilitas, dan tombol *"Lanjut ke Formulir Pendaftaran"*.

### 4.5 Halaman 4: Login & Registrasi Peserta
- **Split Layout 50:50:**
  - Sisi kiri: Foto aksi pebalap dalam nuansa Dark Teal (`bg-teal-900`) dengan kutipan inspiratif dan jaminan keamanan data UCI Compliant.
  - Sisi kanan: Form autentikasi interaktif.
- **Social Login Terintegrasi:**
  - 3 tombol kotak bersusun ke bawah: **Google**, **Facebook**, dan **TikTok** lengkap dengan logo vektor SVG resmi.
- **Formulir Bersih:**
  - Input email dan kata sandi dengan garis border 1px tipis, checkbox "Ingat Sesi", dan link lupa sandi.
- **Switcher Cepat:**
  - Toggle tab di header form untuk berpindah dari "Masuk" ke "Daftar Akun Baru" tanpa reload halaman.

### 4.6 Halaman 5: Formulir Pendaftaran & Checkout
- **Header Solid Bersih:**
  - Solid Dark Teal (`#116979`) yang ringkas tanpa jejak breadcrumb.
- **Progress Bar 3-Langkah:**
  - Indikator langkah geometris: (1) Kategori Race → (2) Data Diri & Medis → (3) Pembayaran Midtrans.
- **Data Pribadi Sesuai KTP:**
  - Nama lengkap, NIK KTP / Paspor, tanggal lahir, jenis kelamin, golongan darah, email konfirmasi, dan nomor WhatsApp.
- **Kontak Darurat Medis (Safety Priority):**
  - Nama kontak darurat, hubungan kekeluargaan, nomor telepon darurat, dan riwayat alergi obat/makanan untuk tim paramedis.
- **Ringkasan Pembayaran & Simulasi Midtrans:**
  - Card ringkasan biaya di sisi kanan. Tombol bayar akan membuka modal simulasi **Midtrans Snap** dengan pilihan instan QRIS dan Virtual Account.

### 4.7 Halaman 6: Konfirmasi Pembayaran & E-Ticket
- **Banner Sukses:**
  - Simbol centang hijau tebal berkotak 1px dengan pesan *"Pembayaran Terverifikasi Lunas"*.
- **Card E-Ticket Presisi Tinggi:**
  - Header berlatar solid Dark Teal (`#116979`) dengan aksen badge Amber (`#DE7119`) dan border `border-teal-800`.
  - **Nomor BIB Besar:** Ditampilkan dengan font monospace raksasa (`VLS-GF-0488`).
  - **QR Code Unik:** Kode QR beresolusi tajam untuk dipindai panitia di Race Village.
  - Metadata lengkap: Wave corral start, golongan darah darurat, dan klub sepeda terdaftar.
- **Tombol Aksi Cepat:**
  - Tombol *"Simpan di Google Calendar"*, tombol *"Unduh E-Ticket (PDF)"*, dan tombol navigasi ke dashboard.

### 4.8 Halaman 7: Dashboard Peserta (Rider Portal)
- **Header Profil Rider:**
  - Kartu profil pesepeda dengan avatar inisial geometris, status *Verified Rider*, dan klub sepeda.
  - 4 metrik pribadi: Tiket terkonfirmasi, total KM endurance, status pembayaran 100% lunas, dan nomor BIB aktif.
- **Daftar Tiket Saya:**
  - Tiket lunas bertanda border tebal navy dan status hijau botol.
  - Tiket pending bertanda peringatan kuning dengan tombol bayar instan.
- **Panel Notifikasi Balap Real-time:**
  - Log pengumuman race pack collection dan pembaruan rute GPX.

### 4.9 Halaman 8: Dashboard Admin (Race Director Console)
- **Sub-Navbar Penyelenggara:**
  - Indikator stasiun marshall aktif, tombol akses scanner QR, dan tombol ekspor CSV peserta.
- **4 Metrik Eksekutif:**
  - Total Peserta (2.480), Pendapatan Tiket (Rp 1,24 Miliar), Event Aktif (4 Race), dan BIB Terbit Hari Ini (+84).
- **Tabel CRUD Event Balap:**
  - Nama race, tanggal, kategori jarak, progress bar keterisian kuota, status publikasi, dan aksi Edit/Hapus.
- **Tabel Alokasi BIB & Status Check-in:**
  - Daftar nomor BIB, nama peserta, kontak darurat, status pembayaran Midtrans, dan status check-in fisik.

### 4.10 Halaman 9: Scanner QR Validasi Hari-H (Admin Check-in)
- **Kamera Scanner Reticle:**
  - Frame kamera pemindai hitam dengan garis laser bidik merah beranimasi dan switch kamera depan/belakang.
  - Input teks pencarian manual BIB jika QR ponsel peserta retak atau baterai habis.
- **Kartu Hasil Validasi:**
  - Menampilkan status *"TIKET VALID & LUNAS"*, nomor BIB besar, nama lengkap, NIK, wave start, dan golongan darah.
- **Checklist Fisik Marshall:**
  - 3 kotak centang wajib sebelum peserta meninggalkan meja registrasi: (1) Nomor BIB fisik + peniti, (2) Stiker RFID seatpost, (3) Buku panduan & voucher makan siang.

### 4.11 Floating AI Cycling Assistant — "Tanya Mizu"
- **Widget Melayang Pojok Kanan Bawah:**
  - Tombol pemicu berlabel *"Tanya Mizu"* (bukan capsule, `velo-box-1`).
  - Membuka popover chat responsif (radius luar 1px, bubble chat dalam 3px).
  - Terhubung ke **Google Gemini API** (Free Tier, `gemini-3.1-flash-lite`) untuk menjawab konsultasi pendaftaran, Race Pack, cut-off time, dan regulasi sepeda.
  - Basis pengetahuan disusun dari konten situs di database (Help Center, Rule Center, Paket Tiket, About) sehingga otomatis sinkron dengan pembaruan oleh pengelola.
  - Widget hanya tampil ketika `GEMINI_API_KEY` terisi pada `.env`.
  - Dibuka untuk seluruh pengunjung (tanpa login), dengan pembatasan jumlah pesan per sesi agar kuota free tier tetap terkendali.

---

## 5. Standar Responsivitas & Aksesibilitas (A11y)

### 5.1 Breakpoints Responsif
- **Mobile (`< 640px`):** Layout 1 kolom penuh, navigasi hamburger minimalis, floating chatbot mengecil menjadi ikon saja, tabel CRUD dapat digulir horizontal (*overflow-x-auto*).
- **Tablet (`640px - 1024px`):** Grid kartu 2 kolom, hero section proporsional, galeri foto detail menyesuaikan.
- **Desktop (`> 1024px`):** Grid 3 kolom, sticky booking sidebar aktif, split-screen auth 50:50, tampilan dashboard admin penuh.

### 5.2 Standar Aksesibilitas (WCAG 2.1 Level AA)
- Rasio kontras teks minimum 4.5:1 untuk teks biasa dan 3:1 untuk teks besar terhadap latar belakang putih.
- Seluruh elemen tombol dan link memiliki status fokus (*focus-visible*) yang jelas tanpa merusak estetika tajam.
- Form input memiliki label eksplisit untuk pembaca layar (*screen readers*).
- Tombol aksi memiliki atribut `aria-label` yang deskriptif.
