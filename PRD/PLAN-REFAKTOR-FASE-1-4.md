# Roadmap & Rencana Refaktorisasi Terpadu Velosha (Fase 1 – 4)
## Platform Maraton & Balap Sepeda Indonesia

> **Dokumen:** Rencana Kerja Teknis, Restrukturisasi Arsitektur, Penyelesaian Bug, & Standarisasi Livewire  
> **Target Status:** Bug-free, High-Concurrency Safe, Clean Code, & Sesuai PRD  
> **Lokasi File:** `Velosha/PRD/PLAN-REFAKTOR-FASE-1-4.md`  
> **Referensi Desain & UX:** [`PRD-Desain-UI-UX.md`](PRD-Desain-UI-UX.md) (Design Tokens, Palet, Border Radius, Tipografi, 9 Layout Halaman)  
> **Referensi Fitur & DB:** [`PRD-Fitur-Database.md`](PRD-Fitur-Database.md) (Fitur 1–10, ERD Basis Data, Third-Party Integrations)  
> **Referensi Kriteria:** [`PRD-Alur-dan-Penilaian.md`](PRD-Alur-dan-Penilaian.md) (12 Kriteria Penilaian Portofolio & High Concurrency)  
> **Aturan Pengembang:** [`AGENTS.md`](AGENTS.md) (Aturan Desain Ketat & Konvensi Proyek)  

---

## 📌 Ringkasan Eksekutif

Codebase Velosha saat ini memiliki 121 automated test yang lulus (*passed* — 398 assertions), namun memiliki **utang teknis (technical debt)** dan kebutuhan penyelarasan visual yang signifikan:
1. **Concurrency & Race Condition:** ~~Belum ada mekanisme penguncian kuota~~ ✅ *Sudah diselesaikan di Fase 1 (Action Class atomik + `lockForUpdate`).*
2. **God Views & God Classes:** File Blade berukuran di atas 22KB–35KB (`checkout.blade.php`, `app.blade.php`, `event-detail.blade.php`) menggabungkan logika form 3 langkah, layout, CSS inline, dan script Vanilla JS imperatif dalam satu file.
3. **Vanilla JS vs Livewire Anti-Pattern:** Banyak manipulasi DOM manual (`document.getElementById`, classList toggle, event listener IIFE) yang rentan rusak (*flicker* atau *broken state*) saat Livewire me-render ulang DOM.
4. **Inkonsistensi Token Visual:** Masih terdapat arbitrary colors (`bg-[#2E343B]`, `text-[#4b555b]`, utility Tailwind bawaan `gray-*`/`blue-*`) yang melanggar aturan 4 warna inti dan panduan kontras aksesibilitas WCAG AA.
5. **Fitur PRD Berstatus Dummy/Placeholder:** Chatbot AI asisten balap masih 100% teks HTML statis, QR Code mengandalkan domain API publik pihak ketiga (`api.qrserver.com`) yang tidak dapat berjalan offline, dan belum ada Gate Scanner untuk hari-H balap.

Rencana ini dibagi menjadi **4 Fase Berurutan** agar setiap langkah terukur, terisolasi, dan tidak merusak fitur yang sudah berjalan.

---

## 🎨 Pedoman Desain Visual & Standar UI/UX Velosha (Single Source of Truth)

Sebelum memulai perombakan file tampilan pada Fase 2 dan 3, seluruh perubahan antarmuka **wajib tunduk pada 5 aturan arsitektural visual berikut**:

### 1. Aturan Border Radius Keras (Strict Geometric Radius)
| Level Komponen | Nilai Radius | Class CSS Utility | Contoh Elemen |
|---|---|---|---|
| **Outer / Primary** | `1px` | `rounded-[1px]` / `.velo-box-1` | Container Card terluar, Tombol Utama & Sekunder, Input Form, Navbar, Modal Pop-up, Dropdown Frame, Frame Video |
| **Inner / Nested** | `3px` | `rounded-[3px]` / `.velo-box-3` | Badge Kategori, Tag Jarak (130 KM), Chip Spesifikasi (Elevasi/COT), Thumbnail Foto di dalam Galeri, Progress Bar, Bubble Chat AI |
| **Anti-Pola (DILARANG)** | `9999px` / `full` | `rounded-full` | **Dilarang keras pada tombol**. Tombol berbentuk kapsul merusak karakter arsitektural dan ketajaman desain. |

> [!NOTE]
> Pengecualian bentuk lingkaran sempurna (`rounded-full`) hanya diizinkan untuk avatar inisial pesepeda dan indikator dot status kecil (misal: titik hijau live).

### 2. Sistem 4 Warna Inti & Kontras Aksesibilitas WCAG AA
Aplikasi memakai **empat warna inti yang telah disetujui**:
```
PUTIH NAVBAR        WARM GRAY          DARK TEAL          LIGHT TEAL         AMBER RACE
#FFFFFF             #DEE3E2            #116979            #18B0B0            #DE7119
[Permukaan Kartu]   [Latar Aplikasi]   [Teks & Panel]     [Dekoratif Murni]  [Warna Aksi]
```

| Peran | Nilai HEX | Token Tailwind | Aturan & Penggunaan |
|---|---|---|---|
| **Latar Aplikasi** | `#DEE3E2` | `bg-velosurface` | Warm gray seluruh `<body>`. Memisahkan kartu putih dari halaman. |
| **Permukaan Kartu** | `#FFFFFF` | `bg-white` | Navbar, kartu event, panel form. Wajib putih agar kontras dengan warm gray. |
| **Permukaan Alt** | `#F1F4F4` | `bg-velosurfacealt` | Footer, section bergantian, strip metrik, tabel info. |
| **Border Halus** | `#D5D9D8` | `border-veloborder` | Garis pemisah 1px kartu, navbar, divider. |
| **Border Kuat** | `#B8BFBE` | `border-veloborderstrong` | Border saat input aktif, card terpilih, atau hover. |
| **Dark Teal (Teks Utama)** | `#116979` | `text-teal-700` | Judul, heading, label ink, chatbot. **6.32:1** di atas putih. |
| **Dark Teal Solid (Header/Hero)** | `#0A353E` | `bg-teal-900` / `text-teal-900` | Header solid katalog/detail/checkout, teks kontras tinggi, angka statistik. |
| **Light Teal (DEKORATIF MURNI)** | `#18B0B0` | `teal-500` | **DILARANG UNTUK TEKS.** Hanya garis, dot, grid pattern, garis chart SVG. (Hanya 2.67:1 di atas putih). |
| **Amber Solid (TOMBOL UTAMA)** | `#B2590F` | `bg-race-600` | **Latar tombol solid + teks putih.** Kontras **4.85:1** (memenuhi WCAG AA). |
| **Amber Hover** | `#A0520C` | `hover:bg-race-700` | State hover tombol solid utama. |
| **Amber Race (Aksen Signature)** | `#DE7119` | `race-500` | Border radio kartu pilihan, checkbox aktif, aksen dekoratif garis bawah. |
| **Amber Ink** | `#5D320C` | `text-race-900` | Teks oranye di atas permukaan terang (`race-300`/`race-50`). |
| **Amber Tint** | `#FDF3E9` | `bg-race-50` | Latar badge kategori dan chip aksen amber. |
| **Tombol di atas Amber Terang** | `#EFA96A` | `bg-race-300` + `text-race-900` | Khusus di atas hero amber: **5.48:1** (kontras aman). |
| **Status Sukses / Paid** | `#065F46` / `#ECFDF5` | `text-velogreen` / `bg-emerald-50` | Status "LUNAS", "TERVERIFIKASI", sisa slot aman. |
| **Status Warning / Pending** | `#A0520C` / `#FDF3E9` | `text-race-700` / `bg-race-50` | Status "MENUNGGU PEMBAYARAN", slot hampir habis. |
| **Status Emergency / Medis** | `#991B1B` / `#FEF2F2` | `text-red-700` / `bg-red-50` | Kontak darurat, riwayat alergi, tombol batal. |

> [!WARNING]
> **Larangan Palet Warna:** Abu-abu Tailwind (`gray-*`, `slate-*`, `zinc-*`), `blue-*`, `amber-*` bawaan, dan warna `navy-900` (`#1E3A5F`) **DILARANG DIGUNAKAN**. Seluruh styling harus merujuk pada token warm gray, dark teal, dan amber race di atas.

### 3. Logo Resmi Aplikasi
- **File Asset:** `public/images/logo-velosha.png` atau `public/images/logo-velosha.jpg` (monogram lambang hasil crop resmi, proporsi ±1.08:1).
- **Ukuran:** Navbar `h-14` (56px), Footer `h-20` (80px), selalu menggunakan `object-contain`.
- **Aturan:** Dilarang menambahkan teks brand "VELOSHA" di sebelah logo (lambang berdiri sendiri), dan dilarang memberi filter oranye/warna lain pada logo.

### 4. Tipografi & Kepadatan Teks (Editorial Impact)
- **Font Family:** `Inter` (Google Fonts), fallback `system-ui, sans-serif`.
- **Heading:** Weight 700–900, tracking rapat (`tracking-tight` / `-0.025em`).
- **Data Monospace:** Font monospace bold untuk nomor BIB (`VLS-GF-0488`), Order ID, dan waktu COT.
- **Copy Density:** Hindari paragraf panjang. Maksimal 1–2 kalimat tegas per blok. Biarkan visual aksi peleton, whitespace lapang, dan tipografi tajam yang menyampaikan informasi.

---

## ✅ FASE 1: Integritas Data, Concurrency, & Core Logic Refactoring — SELESAI
> **Fokus:** Menghilangkan potensi race condition, duplikasi kode (DRY), bug kalkulasi, serta runtime deprecation PHP 8.1+.  
> **Status:** ✅ Selesai — 121 feature test passed (398 assertions), naik dari 120 test (setelah pembersihan ExampleTest boilerplate).

### 1.1 Penerapan PHP 8.2 Backed Enums ✅

**Masalah:** Konstanta string tersebar di berbagai model, rawan typo.  
**Solusi:** Backed Enum tipe-ketat.

| Enum | Lokasi File | Isi |
|---|---|---|
| `RegistrationStatus` | `app/Enums/RegistrationStatus.php` | `pending`, `paid`, `checked_in`, `cancelled` + `label()`, `color()`, `options()` |
| `EventStatus` | `app/Enums/EventStatus.php` | `draft`, `published`, `completed`, `cancelled` |
| `EventType` | `app/Enums/EventType.php` | `gran_fondo`, `medio_fondo`, `fun_ride`, `climb` |
| `UserRole` | `app/Enums/UserRole.php` | `peserta`, `penyelenggara`, `admin` |
| `BloodType` | `app/Enums/BloodType.php` | `A`, `B`, `AB`, `O` |
| `Gender` | `app/Enums/Gender.php` | `male`, `female` |

**Model yang diupdate:** Konstanta `STATUS_*` / `ROLE_*` di `Registration.php`, `Event.php`, `User.php` sekarang mereferensi Enum.

### 1.2 Ekstraksi Action Class Atomik ✅

**Masalah:** Logika "bayar → BIB → tiket" duplikat di Checkout (Livewire) dan Filament Admin.  
**Solusi:** Satu Action class tunggal dengan database locking.

| Action | Lokasi File | Peran |
|---|---|---|
| `CompleteRegistrationAction` | `app/Actions/CompleteRegistrationAction.php` | Transaksi atomik: lock → cek kuota → BIB → status `paid` → increment → tiket |
| `CancelRegistrationAction` | `app/Actions/CancelRegistrationAction.php` | Pembatalan: kurangi `registered_count`, ubah status `cancelled` |

**Alur `CompleteRegistrationAction::execute()`:**
1. `DB::transaction` + `lockForUpdate()` pada baris `Registration` dan `EventCategory`.
2. Idempotent: jika sudah `PAID` → return tanpa error.
3. Verifikasi kuota (`$category->remaining > 0`).
4. Alokasi BIB format `VLS-{KODE}-{0001}` via `Registration::generateBibNumber()`.
5. Update status → `paid`, `increment('registered_count')`.
6. Terbitkan tiket via `Ticket::issueFor($registration)`.

**Penerapan:**
- `Checkout::simulatePaid()` → memanggil `CompleteRegistrationAction`.
- `RegistrationsTable` aksi "Tandai Lunas" → memanggil `CompleteRegistrationAction`.
- `EditRegistration` → proteksi agar admin tidak ubah status `paid` manual tanpa alokasi BIB.

### 1.3 Penyelesaian Bug Runtime & Deprecation PHP 8.1+ ✅

| File | Bug | Perbaikan |
|---|---|---|
| `resources/views/pages/ticket.blade.php` | `strtoupper(null)` → deprecation warning PHP 8.1 | Null-safe operator `?->` + fallback |
| `resources/views/pages/ticket.blade.php` | `$scanCode` crash jika BIB belum ada | Pengecekan `blank()` + fallback aman |
| `resources/views/pages/event-detail.blade.php` | `Carbon::parse("")` mengambil `now()` | Pengecekan `if ($event->start_time)` sebelum parse |
| `resources/views/pages/event-detail.blade.php` | `$photos[5]` crash jika foto < 6 | `$photos->get(5)` → return null |
| `resources/views/pages/event-detail.blade.php` | Deskripsi RichEditor double-escaped | `{!! !!}` + class `prose` |
| `app/Support/EventCardPresenter.php` | `Str::limit()` memotong tag HTML terbuka | `strip_tags()` sebelum `Str::limit()` |

### 1.4 Optimasi Query & Pencegahan N+1 ✅

| File | Perubahan |
|---|---|
| `routes/web.php` | Rute `/ticket/{registration?}`: eager-load `category.event.photos` + `ticket` |
| `app/Livewire/EventCatalog.php` | `monthOptions()`: optimasi query bulan via distinct berindeks |

### 1.5 Test Baru ✅

| File | Test Case |
|---|---|
| `tests/Feature/CompleteRegistrationActionTest.php` | ① Berhasil alokasi BIB & tiket |
| | ② Idempotent jika sudah paid |
| | ③ Cancel & throw jika kuota habis |

**Hasil:** `Tests: 121 passed (398 assertions) — Duration: 51.46s` (100% feature test substantif)

---

## 🏗️ FASE 2: Restrukturisasi File, Dekomposisi God Views & Clean Architecture
> **Fokus:** Mengurangi baris kode per file (target < 10KB / < 180 baris), memecah file raksasa menjadi komponen modular, menegakkan aturan desain visual di setiap view, dan memindahkan logika dari closure route ke dedicated controller.  
> **Status:** ⏳ Menunggu pengerjaan.

### 2.1 Dekomposisi God View `livewire/checkout.blade.php` (~35KB / 484 baris)

**Masalah:** Satu file Blade menampung form 3 langkah, order summary, dan payment modal. File terlalu panjang dan sulit di-maintain.  
**Solusi:** Pecah menjadi 5 sub-views modular via `@include('livewire.checkout.*')`.

```
resources/views/livewire/
└── checkout/
    ├── step-1-category.blade.php    # Langkah 1: Radio card pilihan kategori jarak
    ├── step-2-medical.blade.php     # Langkah 2: Data identitas KTP & kontak darurat medis
    ├── step-3-review.blade.php      # Langkah 3: Ringkasan periksa data sebelum pembayaran
    ├── order-summary.blade.php      # Panel sticky rincian biaya & countdown
    └── payment-modal.blade.php      # Modal simulasi Midtrans Snap & petunjuk QRIS/VA
```

#### Spesifikasi Visual & Komponen Sub-View Checkout:
1. **`step-1-category.blade.php`**:
   - Container card: `bg-white border border-veloborder rounded-[1px] p-6`.
   - Radio Card Pilihan Kategori: Border `border-veloborder`, ketika dipilih aktif `border-race-500 ring-1 ring-race-500 bg-race-50/20`.
   - Badge Kuota: `rounded-[3px]` (`velo-box-3`) berlatar `bg-emerald-50 text-emerald-800` jika > 10 slot; `bg-race-50 text-race-800` jika slot menipis.
   - Harga: Font monospace tebal warna `text-teal-900`.
2. **`step-2-medical.blade.php`**:
   - Input Form: `rounded-[1px]` (`velo-box-1`), border `border-veloborder`, focus `border-teal-700 ring-1 ring-teal-700 text-teal-900`.
   - Card Kontak Medis Darurat: Beraksen border halus merah `border-red-200 bg-red-50/30 p-4 rounded-[1px]` dengan label peringatan safety darurat.
   - Dropdown Golongan Darah & Gender: Memakai options dari Enum PHP 8.2 (`BloodType` & `Gender`).
3. **`step-3-review.blade.php`**:
   - Tabel review 2 kolom geometris dengan border halus `border-veloborder`.
   - Kotak konfirmasi syarat & ketentuan dengan checkbox custom `rounded-[1px] text-race-600`.
4. **`order-summary.blade.php`**:
   - Panel ringkasan biaya sticky: `rounded-[1px] bg-white border border-veloborder p-6 shadow-sm`.
   - Tombol Bayar Utama: `bg-race-600 text-white rounded-[1px] hover:bg-race-700 font-bold py-3.5 w-full text-center tracking-wide`.
5. **`payment-modal.blade.php`**:
   - Modal container: `rounded-[1px] bg-white p-6 shadow-2xl border border-veloborder`.
   - Tab pembayaran simulasi Snap: Pilihan tab QRIS (SVG lokal) dan Bank Virtual Account.

### 2.2 Dekomposisi Master Layout `layouts/app.blade.php` (~22KB / 473 baris)

**Masalah:** Layout utama menampung navbar sticky, mobile drawer, footer 4 kolom, dan 140 baris CSS inline (`@keyframes`, scrollbar, korden transisi).  
**Solusi:** Ekstrak komponen layout ke folder `components/layout/` dan pindahkan seluruh CSS inline ke `resources/css/app.css`.

| Komponen / File | Lokasi Target | Spesifikasi Visual & Peran |
|---|---|---|
| Navbar Desktop & Mobile | `resources/views/components/layout/navbar.blade.php` | • Tinggi 80px (`h-20`), `bg-white/95 backdrop-blur-xl border-b border-veloborder`.<br>• Logo `h-14` (`public/images/logo-velosha.png`) tanpa teks merk tambahan.<br>• Link aktif: `text-race-500 bg-race-50 border-b-2 border-race-500`.<br>• Link biasa: `text-[#4b555b] hover:text-teal-700 hover:bg-teal-50`.<br>• Tombol Masuk: `bg-race-600 text-white rounded-[1px] hover:bg-race-700`. |
| Footer Minimalis | `resources/views/components/layout/footer.blade.php` | • Latar `bg-velosurfacealt` (`#F1F4F4`), border atas `border-veloborder`.<br>• Logo `h-20` (`object-contain`).<br>• Judul 4 kolom memakai `text-teal-900 font-bold`.<br>• Grid metode pembayaran & copyright resmi. |
| Migrasi CSS Kustom | `resources/css/app.css` | • Pindahkan `@keyframes veloModalEnter`, `veloPageLeave`, `veloPageEnter`, `veloCurtainIn/Out` dari `<style>` inline layout.<br>• Pindahkan styling scrollbar warm gray (`#dee3e2`, thumb `#b8bfbe`). |

### 2.3 Dekomposisi Halaman Detail `pages/event-detail.blade.php` (~25KB / 428 baris)

**Masalah:** Halaman event detail menampung galeri Airbnb 1+4, itinerary balap, peta pos hidrasi, sticky booking box, dan modal otorisasi dalam satu file raksasa.  
**Solusi:** Ekstrak ke dalam partials modular di `resources/views/pages/event-detail/partials/`.

| Partial | Lokasi Target | Spesifikasi Desain & Peran |
|---|---|---|
| Header Solid Clean | Inline di `event-detail.blade.php` | Solid **Dark Teal** (`bg-teal-700 text-white`) tanpa background image peleton buram dan tanpa breadcrumb bertumpuk (sesuai PRD §4.4). |
| Galeri Foto | `.../partials/gallery.blade.php` | Format Airbnb 1+4 grid: 1 foto utama besar di kiri (2 kolom) + 4 foto thumbnail di kanan. Seluruh sudut foto bersudut `rounded-[3px]` (`velo-box-3`). |
| Itinerary Weekend | `.../partials/itinerary.blade.php` | Timeline balap: Race Pack Collection (Jumat-Sabtu) & Race Day (Minggu) dengan chip waktu `rounded-[3px]`. |
| Peta & Aid Stations | `.../partials/aid-stations.blade.php` | Embed peta Google Maps + Legenda 4 pos hidrasi (KM 35, KM 72, KM 98 KOM, KM 115) + tombol unduh file GPX Garmin/Wahoo (`rounded-[1px]`). |
| Profil Elevasi | `.../partials/elevation-chart.blade.php` | Grafik garis SVG profil kontur tanjakan vertikal dengan penanda puncak KOM (+920m ASL). |
| Sticky Booking Box | `.../partials/booking-box.blade.php` | Card melayang `bg-white border border-veloborder rounded-[1px] p-6 shadow-md`: Radio card kategori tiket, status sisa kuota, dan tombol CTA utama `bg-race-600 text-white rounded-[1px]`. |
| Role Modal Warning | `.../partials/role-modal.blade.php` | Popover peringatan untuk pengguna login dengan peran non-peserta (`penyelenggara`/`admin`), radius `rounded-[1px]`. |

### 2.4 Refaktorisasi Route Closure ke Dedicated Controllers

**Masalah:** `routes/web.php` menampung query Eloquent langsung di closure callback rute.  
**Solusi:** Pindahkan ke controller dedicated sehingga rute hanya mendefinisikan URL, middleware, dan nama.

| Controller | Lokasi Target | Method & Tanggung Jawab |
|---|---|---|
| `EventController` | `app/Http/Controllers/EventController.php` | • `index()`: Mengarahkan ke halaman katalog balap.<br>• `show(Event $event)`: Mengambil data event, kategori aktif, foto rute, dan menampilkan `pages.event-detail`. |
| `RiwayatController` | `app/Http/Controllers/RiwayatController.php` | • `index()`: Menampilkan arsip riwayat event balap masa lalu. |
| `TicketController` | `app/Http/Controllers/TicketController.php` | • `show(?Registration $registration)`: Eager-load tiket, kategori, event, dan foto; menampilkan `pages.ticket`. |

---

## 🎨 FASE 3: Standarisasi Livewire & Alpine.js, UI/UX Token & QR Code Mandiri
> **Fokus:** Menghapus manipulasi DOM manual Vanilla JS, menerapkan standar interaktivitas deklaratif Alpine.js, menyeragamkan token warna sesuai PRD, dan memastikan QR Code berjalan offline tanpa dependensi pihak ketiga.  
> **Status:** ⏳ Menunggu pengerjaan.

### 3.1 Migrasi Script Vanilla JS ke Alpine.js Deklaratif

**Masalah:** Banyak script IIFE Vanilla JS yang memanipulasi DOM secara imperatif (`document.getElementById`, manual click event, direct class toggle). Script ini rawan *broken* saat Livewire me-render ulang Virtual DOM.  
**Solusi:** Ganti seluruh manipulasi DOM dengan state deklaratif Alpine.js (`x-data`, `x-model`, `x-show`, `x-transition`).

| Area Interaktivitas | Implementasi Lama (Vanilla JS) | Solusi Baru (Alpine.js) |
|---|---|---|
| **Sinkronisasi Kategori (Event Detail)** | Script IIFE `syncCta()` + manipulasi atribut `href` manual | `x-data="{ selectedCategory: '{{ $defaultCategory->id }}' }"` + binding URL CTA dinamis |
| **Modal Role Warning** | Fungsi global `openRoleWarning()` di `window` + toggle class `.hidden` | `x-data="{ openRoleWarning: false }"` + directive `x-show` dengan transisi fade-in |
| **Navigasi Menu Mobile** | `document.getElementById('mobileMenu')` + toggle classList | `x-data="{ mobileOpen: false }"` pada navbar shell |
| **Chatbot Widget Toggler** | Class list toggle imperatif di layout | `x-data="{ chatOpen: false }"` dengan transisi popover halus |
| **Flash Message Auto-Dismiss** | `setTimeout` memanipulasi element style manual | `x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"` |

### 3.2 Generator QR Code Mandiri & Lokal (Offline-Ready)

**Masalah:** E-Ticket saat ini menampilkan gambar QR Code via URL eksternal:  
`<img src="https://api.qrserver.com/v1/create-qr-code/?data=...">`.  
Jika peserta berada di lokasi balap terpencil (*remote race venue*) atau ponsel dalam mode pesawat tanpa sinyal internet, QR Code gagal termuat dan peserta tidak dapat melakukan registrasi ulang di Race Village.  
**Solusi:** Implementasikan generator QR Code internal berbasis SVG (menggunakan package lokal seperti `chillerlan/php-qrcode` atau helper renderer SVG internal).

```php
// Helper atau Service lokal:
$svgQrCode = QrCodeService::generateSvg($registration->verification_payload);
```

**Spesifikasi Tampilan E-Ticket:**
- Render langsung sebagai inline `<svg>` beresolusi vektor tajam tanpa request HTTP ke luar server.
- Teruji dapat dirender dan dicetak sempurna dalam kondisi offline 100%.

### 3.3 Penyelarasan Menyeluruh Token Warna & Desain PRD

Audit seluruh file Blade untuk menghapus kode warna yang melanggar sistem desain:

| Pelanggaran (Ditemukan di Codebase) | Standar Perbaikan Sesuai PRD |
|---|---|
| `bg-[#2E343B]` (abu-abu tua sembarangan) | Ganti ke `bg-teal-900` (`#0A353E`) atau `bg-teal-700` (`#116979`) |
| `text-[#4b555b]` / `text-[#556066]` | Gunakan semantic token `text-velomuted` / `text-velomutedlight` |
| `bg-gray-100` / `bg-slate-50` | Gunakan `bg-velosurfacealt` (`#F1F4F4`) |
| `border-gray-200` / `border-slate-300` | Gunakan `border-veloborder` (`#D5D9D8`) |
| Tombol `rounded-full` (kapsul) | Ubah menjadi `rounded-[1px]` (`velo-box-1`) |
| Teks putih di atas background oranye terang | Ganti menjadi `text-teal-900` atau `text-race-900` (agar kontras memenuhi 5.24:1 WCAG AA) |

### 3.4 Halaman Error Kustom Bersuara Brand (404, 500, 403)

Buat halaman error kustom yang elegan dan konsisten dengan layout aplikasi:
- `resources/views/errors/404.blade.php`: Halaman "Rute Tidak Ditemukan" dengan ilustrasi geometris minimalis dan tombol kembali ke katalog `bg-race-600 text-white rounded-[1px]`.
- `resources/views/errors/500.blade.php`: Halaman "Gangguan Sirkuit Balap" dengan instruksi muat ulang ramah pengguna.
- `resources/views/errors/403.blade.php`: Halaman "Akses Jalur Khusus Dibatasi" untuk proteksi otorisasi peran.

---

## ⚡ FASE 4: Implementasi Fitur Lanjutan PRD
> **Fokus:** Mengubah fitur yang sebelumnya berstatus dummy/placeholder menjadi fungsional penuh sesuai spesifikasi PRD (Fitur 5, 6, 8, 10).  
> **Status:** ⏳ Menunggu pengerjaan.

### 4.1 Fitur 10: Asisten AI Cycling Strategist (Livewire Component + Service Layer)

**Masalah:** Widget chatbot AI di sudut kanan bawah saat ini hanya berisi teks HTML statis tanpa responsivitas.  
**Solusi:** Bangun komponen Livewire interaktif `app/Livewire/ChatbotWidget.php` yang terhubung ke service asisten balap.

#### Arsitektur Komponen:
1. **`app/Services/AiChatService.php`**:
   - Menghubungkan aplikasi ke AI API (Groq Llama 3 / Google Gemini Flash API via HTTP client).
   - Menyediakan **Engine Fallback Cerdas:** Jika API Key belum terkonfigurasi di `.env`, service secara otomatis beralih ke *Velosha Rulebook Engine* internal untuk menjawab konsultasi balap seputar:
     - Cut-off time & elevasi Borobudur Gran Fondo vs Bromo KOM.
     - Regulasi sepeda UCI (larangan aero bar pada peloton maraton).
     - Rekomendasi rasio gear (50-34T compact crank untuk tanjakan curam).
     - Lokasi dan strategi nutrisi di 4 Aid Station.
2. **`resources/views/livewire/chatbot-widget.blade.php`**:
   - Trigger button melayang di pojok kanan bawah: `bg-race-600 text-white h-12 w-12 rounded-[1px] shadow-lg flex items-center justify-center hover:bg-race-700`.
   - Popover Chat: `rounded-[1px] bg-white border border-veloborder shadow-2xl w-80 sm:w-96`.
   - Bubble Pesan: `rounded-[3px] p-3 text-xs` (`bg-teal-50 text-teal-900 border border-teal-100` untuk asisten AI; `bg-race-600 text-white` untuk cyclist).

### 4.2 Fitur 6: Gate Scanner QR Code Hari-H Balap (Admin & Penyelenggara)

**Masalah:** Belum ada mekanisme validasi tiket fisik saat hari pelaksanaan balap (*Race Pack Collection & Race Village check-in*).  
**Solusi:** Bangun antarmuka Gate Scanner reaktif untuk akun ber-role `penyelenggara` dan `admin`.

#### Spesifikasi Fitur Scanner:
- **Lokasi Rute:** `/admin/scan` (terintegrasi di panel Penyelenggara/Admin).
- **Komponen Livewire:** `app/Livewire/GateScanner.php` + `resources/views/livewire/gate-scanner.blade.php`.
- **Antarmuka Scanner (Heads-Up Display):**
  - Frame kamera hitam geometris dengan garis bidik laser merah beranimasi (`velo-box-1`).
  - Input teks pencarian manual nomor BIB jika ponsel peserta kehabisan daya atau layar retak.
- **Logika Verifikasi Atomik:**
  - Parse kode QR: `VELOSHA-VERIFY-{HASH}-{BIB}`.
  - Cek database:
    - ✅ **Tiket Valid & Lunas:** Ubah status menjadi `checked_in`, catat timestamp `checked_in_at`, tampilkan kartu hijau: nama lengkap, nomor BIB besar monospace, kategori race, dan golongan darah.
    - ⚠️ **Tiket Sudah Pernah Check-in:** Tampilkan kartu merah peringatan: *"Tiket sudah pernah digunakan pada pukul [waktu]"* untuk mencegah duplikasi nomor BIB di lintasan balap.
    - ❌ **Tiket Belum Lunas / Batal:** Tampilkan peringatan pembayaran belum selesai.
- **Checklist Fisik Marshall (3 Poin Verifikasi):**
  - Checkbox interaktif sebelum peserta meninggalkan meja: (1) Penyerahan BIB fisik + peniti, (2) Pemasangan stiker RFID di seatpost sepeda, (3) Paket suplemen & kupon makan.

### 4.3 Fitur 5: Midtrans Notification Webhook Controller

**Masalah:** Pembayaran saat ini disimulasikan dari browser client, belum ada endpoint server-to-server webhook resmi dari Midtrans payment gateway.  
**Solusi:** Buat dedicated controller webhook dengan verifikasi signature kriptografis.

#### Arsitektur Webhook:
- **Endpoint:** `POST /api/midtrans/notification` (didaftarkan di `bootstrap/app.php` agar dikecualikan dari verifikasi CSRF token).
- **Controller:** `app/Http/Controllers/Webhook/MidtransNotificationController.php`.
- **Verifikasi Keamanan:**
  - Validasi SHA-512 Signature Key: `hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey)`.
- **Transisi Status Idempotent:**
  - Status `settlement` atau `capture` (accept) → jalankan `CompleteRegistrationAction::execute()`.
  - Status `expire`, `cancel`, `deny` → jalankan `CancelRegistrationAction::execute()`.

### 4.4 Fitur 8 & Ekspor: E-Ticket PDF Siap Cetak & Ekspor CSV Pendaftar

**Masalah:** Peserta belum dapat mengunduh salinan tiket resmi dalam bentuk PDF siap cetak, dan Race Director belum memiliki fitur ekspor data peserta.  
**Solusi:** Implementasi download PDF e-ticket, ekspor CSV, dan tombol integrasi kalender.

| Fitur | Deskripsi & Spesifikasi Teknis |
|---|---|
| **E-Ticket PDF Siap Cetak** | Unduh PDF resmi ukuran A4 berdesain paspor balap (monokrom elegan, logo resmi, barcode/QR SVG lokal, nomor BIB raksasa, ringkasan kontak medis darurat, dan instruksi penukaran). |
| **Ekspor CSV Pendaftar** | Aksi "Ekspor CSV" pada tabel registrasi Filament untuk Race Director (berisi: Nomor BIB, Nama Lengkap, Kategori, NIK, No WA, Kontak Darurat, Golongan Darah, Status Bayar). |
| **Simpan di Google Calendar** | Tautan generator URL Google Calendar otomatis dengan tanggal pelaksanaan balap, lokasi venue Candi Borobudur, dan instruksi briefing. |
| **Web Share API** | Tombol berbagi profil balap ke media sosial atau salin tautan rute. |

---

## 📊 Matriks Status Pelaksanaan & Validasi

| Fase | Target Utama | Status | Indikator Keberhasilan | Estimasi Risiko |
|---|---|:---:|---|:---:|
| **Fase 1** | Integritas Data, Concurrency, & Enums | ✅ Selesai | 121 feature test PASS (398 assertions), Action class atomik teruji, database locking aman | Rendah |
| **Fase 2** | Dekomposisi God Views & Clean Architecture | ⏳ Siap | File Blade < 10KB per modul, layout terpisah, route closure bersih ke controller | Rendah |
| **Fase 3** | Standarisasi Alpine.js, UI Tokens, & QR Mandiri | ⏳ Siap | Nol Vanilla JS imperatif, QR Code inline SVG berfungsi offline, 100% konsisten warna PRD | Rendah |
| **Fase 4** | Fitur PRD: AI Aide, Gate Scanner, Webhook | ⏳ Siap | Chatbot AI responsif + rulebook fallback, Gate Scanner check-in valid, webhook aman | Sedang |

---

## 🎯 Panduan Validasi & Pengujian Berkala

Setiap kali menyelesaikan tahapan pengerjaan pada fase berikutnya, eksekusi pengujian berikut secara bertahap:

1. **Automated Testing Suite:**
   ```bash
   composer test
   ```
   *Target:* Seluruh 121 test (dan test-test baru) tetap berwarna hijau (`PASS`) tanpa ada assertion yang gagal.

2. **Validasi Tampilan Frontend di Browser:**
   ```bash
   php artisan serve
   ```
   *Periksa secara visual di browser:*
   - **Header & Navbar:** Ketinggian konsisten 80px, logo monogram `h-14` tanpa teks redundan, link aktif bergaris amber.
   - **Katalog & Detail:** Header solid dark teal (`bg-teal-700`) bersih tanpa breadcrumb berlebih, kartu event bersudut 1px dan chip 3px.
   - **Form Checkout:** Transisi 3 langkah lancar tanpa page reload, kartu kontak darurat merah lembut, modal pembayaran rapi.
   - **E-Ticket:** QR Code SVG tampil tajam dan dapat dipindai instan tanpa koneksi internet luar.
