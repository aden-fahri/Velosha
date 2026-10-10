# AGENTS.md

## Struktur Repository

Aplikasi Laravel berada di folder `Velosha/`. Semua perintah (artisan, composer, npm) harus dijalankan dari `Velosha/`, bukan dari root repository.

Dokumen PRD di folder ini (`PRD/`) adalah spesifikasi produk — banyak fitur di dalamnya **belum diimplementasikan**. Jangan menganggap PRD sebagai bukti fitur yang sudah ada; verifikasi dulu di `routes/web.php` dan `resources/views/`.

## Perintah Pengembangan

- `composer dev` — menjalankan artisan serve, queue worker, pail logs, dan Vite dev server secara konkuren
- `composer test` — membersihkan config cache lalu menjalankan `php artisan test`
- `composer setup` — install penuh: composer install, copy `.env`, key generate, migrate, npm install, npm build

Untuk view saja tanpa backend, `php artisan serve` sudah cukup — layout memuat Tailwind via CDN sehingga halaman tetap ter-render tanpa `npm run build`.

## Testing

PHPUnit dengan SQLite in-memory — bukan MySQL. `phpunit.xml` meng-override `DB_CONNECTION`, jadi test tidak pernah menyentuh database `velosha`.

- Semua test: `composer test`
- Satu file test: `php artisan test --filter=ExampleTest`
- Suite Unit saja: `php artisan test --testsuite=Unit`

Test otomatis saat ini mencakup 121 feature test komprehensif (398 assertions) di direktori `tests/Feature`.

## Database

**MySQL** (bukan SQLite) untuk dev dan production. Kredensial ada di `.env`: database `velosha`, user `root`, password kosong (default XAMPP).

- Server MySQL XAMPP dijalankan sebagai proses `mysqld`, bukan Windows service — cek dengan `Get-Process mysqld`. Jika tidak jalan, MySQL harus dinyalakan lewat XAMPP Control Panel.
- Buat database dari CLI: `& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS velosha CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`
- `phpunit.xml` **tetap** memakai SQLite in-memory. Jangan ubah ke MySQL — test harus tetap cepat dan tidak bergantung pada server DB yang jalan.
- Karena `phpunit.xml` meng-override `DB_CONNECTION`, test tidak pernah menyentuh database `velosha`.

## Konvensi Kode

- **Border radius (ketat):** komponen terluar gunakan `velo-box-1` (1px), elemen bersarang di dalamnya gunakan `velo-box-3` (3px). Dilarang memakai `rounded-full` atau bentuk kapsul pada tombol.
- **Warna: 4 WARNA INTI yang telah disetujui.** Amber Race `#DE7119` untuk aksi, Warm Gray `#DEE3E2` untuk latar `<body>`, Dark Teal `#116979` untuk teks dan panel gelap, Light Teal `#18B0B0` untuk dekoratif saja. Navbar dan kartu tetap putih (`bg-white`).
  - **Di atas permukaan amber terang (`race-400`/`race-300`) teks HARUS `teal-900` atau `race-900`, JANGAN `text-white`.** Putih di atas `#E58F42` hanya 2.52:1 dan tidak terbaca. Ini berlaku untuk hero, banner halaman, dan card berlatar amber.
  - **Tombol aksi terang:** `bg-race-300` + `text-race-900` (5.48:1) — lebih cerah dari `race-600`, tetap AA.
  - **Tombol solid gelap:** `bg-race-600` (`#B2590F`) + teks putih (4.85:1) — tetap dipakai di atas permukaan putih/gelap.
  - **`#18B0B0`/`teal-500` tidak boleh menjadi warna teks atau border teks** — hanya 2.67:1 di atas putih. Boleh untuk dot, garis, dan pattern grid.
  - Abu-abu Tailwind (`gray-*`), `blue-*`, `slate-*`, dan `amber-*` **dilarang** sebagai warna brand — digantikan warm gray dan dark teal. `emerald-*` dan `red-*` tetap dipakai karena perannya warna status universal.
  - Palet `navy-900` dan hex `#1E3A5F` juga sudah dihapus — jangan gunakan.
- **Logo:** `public/images/logo-velosha.jpg` — monogram resmi hasil **crop** dari file asli (nama "VELOSHA" di bawah sudah dihapus). Proporsi ±1.08:1, dipakai di navbar (`h-14`) dan footer (`h-20`). Jangan menambahkan teks brand di sebelah logo, dan jangan memuat file asli yang belum di-crop.
- **Font:** Inter (Google Fonts), fallback system-ui. Heading memakai weight 800-900.
- **Ikon:** Lucide via CDN, diinisialisasi dengan `lucide.createIcons()`. Format: `<i data-lucide="nama-ikon">`.
- **Tailwind:** dual setup — plugin Vite (`@tailwindcss/vite`) untuk build, plus script CDN sebagai fallback di layout. Tema kustom didefinisikan sebagai objek JS `tailwind.config` di dalam layout, bukan di file konfigurasi terpisah.
- **Teks harus ringkas.** Ini prinsip desain utama proyek (lihat PRD bagian "Editorial Impact" dan "Quiet Luxury Sports"): hindari paragraf panjang, jaga whitespace, biarkan tipografi dan foto yang berbicara. Copy Bahasa Indonesia yang singkat dan tegas.
- **Indent:** 4 spasi, UTF-8, line ending LF (lihat `.editorconfig`).
- **Bahasa:** seluruh teks UI dan view dalam Bahasa Indonesia, `lang="id"`.

## Alur Kerja Proyek

Proyek ini **frontend-first**. Fokus saat ini adalah designing dan tampilan — bukan logika backend. PRD `PRD-Velosha.md` bagian 7 memuat roadmap; Fase 1-2 (desain statis) adalah fase aktif, Fase 3+ (migration, model, Livewire logic) belum dimulai.

Implikasi praktis:
- Halaman di `routes/web.php` adalah closure yang hanya `return view(...)`. Jangan menambahkan controller, model, atau query database kecuali diminta.
- Data di dalam view (nama event, harga, tanggal, statistik) adalah **dummy hardcoded**. Jangan coba membacanya dari database.
- Widget chatbot AI di layout masih dummy — hanya toggle panel, tidak ada panggilan API.

## Catatan Arsitektur

- Semua route adalah closure di `routes/web.php` — belum ada controller selain `Controller.php` default.
- Single layout: `resources/views/layouts/app.blade.php`. Semua page meng-`@extends` layout ini dan menyetor `@section('content')`, dengan title lewat `@extends('layouts.app', ['title' => '...'])`.
- Halaman ada di `resources/views/pages/`. Admin dan user dashboard berada di direktori yang sama dengan halaman publik — tidak ada pemisahan direktori.
- Migrasi yang ada masih default Laravel (users, cache, jobs, sessions). Skema 8 tabel sesuai ERD di PRD belum dibuat.

## Gotchas

- Jangan jalankan artisan/composer/npm dari root `projek-kelas12/` — command akan gagal. Selalu gunakan `Velosha/` sebagai working directory.
- `laravel/pint` ada di `require-dev` tapi tidak dikonfigurasi: tidak ada `pint.json`, tidak ada composer script, tidak ada CI. Jargon "sudah ada Pint" tidak boleh dipakai saat memvalidasi kode.
- Belum ada CI, pre-commit hook, atau linter PHP lain. Verifikasi otomatis satu-satunya adalah test.
- `.env` tidak di-commit. Salin dari `.env.example` dan jalankan `php artisan key:generate` sebelum pemakaian pertama.
- Layout memanggil `@vite` hanya jika `public/build/manifest.json` atau `public/hot` ada — halaman tetap jalan tanpa build, tapi styling kustom di `resources/css/app.css` tidak akan termuat.
