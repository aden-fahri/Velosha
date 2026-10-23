# desain-revisi.md — Velosha: Revisi Desain "Kartun / Screen-Print"

Status: **desain disetujui pemilik** (mockup "Velosha — Mockup Semua Halaman", 12 board).
Dokumen ini adalah sumber kebenaran tunggal untuk redesign. Jika bertentangan dengan `PRD/AGENTS.md`, dokumen ini yang menang (AGENTS.md harus diperbarui, lihat §17).

---

## 1. Ringkasan arah

| Sebelum | Sesudah |
|---|---|
| Arsitektural, border-radius 1px/3px, teal + oranye, latar abu bertekstur topo | Kartun screen-print: garis tinta tebal, bayangan keras, halftone, latar krem hangat |
| Ikon + kartu generik, hero foto peloton | Hero ilustrasi pesepeda kartun (PNG transparan) keluar dari panel bertakik |
| Tanpa sistem animasi terpadu | Sistem gerak CSS-only, semua dibungkus `prefers-reduced-motion: no-preference` |

**Keputusan final dari pemilik (tidak boleh diubah agen):**
1. MySQL 100%. Midtrans ditunda (butuh API), jangan sentuh alur pembayaran.
2. Aturan radius 1px/3px **dihapus**. Gaya kartun disetujui.
3. Oranye `#DE7119` dipertahankan. Biru/teal diganti total (palet di §3).
4. Badge lingkaran di hero **dihapus**.
5. Slider media kiri-bawah hero: **maksimal 3 gambar**.
6. Takik panel hero ada di **ATAS**; hanya **roda depan** yang keluar dari outline.
7. Teks hero kiri minimalis: 1 judul, 1 paragraf pendek, 1 tombol. Tanpa badge, tanpa statistik, tanpa ikon dekoratif.
8. Halaman detail event: **jangan ubah layout** (instruksi lama dari pemilik) — hanya ganti token/komponen visual (warna, border, font, tombol). Struktur section tetap, termasuk galeri, YouTube, peta, itinerary, aid station, spesifikasi, booking box, role modal, dan sticky CTA mobile.

---

## 2. Prinsip desain

1. **Tinta dulu**: semua elemen interaktif punya kontur `--ink` 2.5–3px dan bayangan keras (offset, tanpa blur).
2. **Kejujuran fungsi**: dekorasi tidak boleh menggantikan informasi. Tidak ada angka/statistik karangan.
3. **Satu aksen per layar**: oranye untuk CTA utama; mustard/pine/berry hanya untuk status & penanda.
4. **Foto tetap dipakai** (poster event, galeri) tapi dibingkai gaya kartun (border tinta, bayangan keras, sudut 14px). Fallback bila tidak ada foto: ilustrasi (§9), bukan `hero-peloton.jpg` kosong.
5. **Gerak bermakna**: masuk, hover, status. Tidak ada gerak yang berjalan terus-menerus kecuali hero (bob ringan, speed lines) dan marquee, keduanya berhenti saat reduced-motion.

---

## 3. Token (sumber: `velosha.css` mockup → pindahkan ke `resources/css/app.css` `@theme`)

### 3.1 Warna

| Token | Hex | Pakai |
|---|---|---|
| `--ink` | `#10282E` | teks utama, kontur, bayangan |
| `--paper` | `#FBF4E6` | latar halaman |
| `--paper2` | `#F3E7CF` | latar section alternatif |
| `--butter` | `#F8E3B5` | panel hero, highlight lembut |
| `--card` | `#FFFDF8` | permukaan kartu |
| `--orange` | `#DE7119` | CTA utama, aksen merek |
| `--orange-d` | `#B2590F` | hover/aksen teks besar |
| `--orange-l` | `#F6B777` | latar lembut oranye |
| `--mustard` | `#F4BE3F` | penanda, highlight |
| `--pine` / `--pine-l` | `#1F6F4A` / `#D5EBD9` | sukses, "Lunas", "Terbuka" |
| `--berry` / `--berry-l` | `#9B3D5A` / `#F2D3DC` | "Mendatang", aksen sekunder |
| `--red` / `--red-l` | `#C23B22` / `#F8D9D2` | error, "Penuh" |
| `--mute` | `#5B6B6F` | teks sekunder |
| `--line` | `#E2D3B3` | garis tipis dalam kartu |

Hapus seluruh ramp `teal-*`, `velomuted*`, `velosurface*`, `veloborder*` dan kelas `velo-box-1/3`, `.card-outer`, `.card-inner`, `.btn-primary/.btn-secondary` lama setelah semua halaman bermigrasi (grep harus 0 hasil).

### 3.2 Kontras (sudah dihitung WCAG; agen WAJIB patuhi)

Lolos (≥4.5:1 teks normal): ink/paper 14.05, ink/card 15.13, **ink/orange 4.75**, ink/mustard 9.0, ink/butter 12.19, ink/orange-l 8.76, white/pine 6.12, white/berry 6.54, mute/paper 5.08, mute/card 5.47, mute/paper2 4.54, orange-d/card 4.77, pine/pine-l 4.87, paper/ink 14.05, mustard/ink 9.0, orange/ink 4.75.

**Gagal — dilarang untuk teks kecil (<18.66px bold / <24px):**
- `white` di atas `orange` = **3.24** → teks di tombol oranye **harus `--ink`**, bukan putih.
- `orange-d` di atas `paper` = 4.43, di atas `butter` = 3.85, di atas `paper2` = 3.96 → untuk teks kecil pakai `--ink` atau gelapkan token teks ke `#9A4B0A` (agen verifikasi ≥4.5 dengan skrip).
- `mute` di atas `butter` = 4.41 → gunakan `--ink` di atas butter.
- `red` di atas `red-l` = 4.02 → teks status merah pakai `--ink` atau `#A02F1A` (verifikasi).

Tambahkan tes otomatis (skrip atau test) yang menghitung pasangan token yang dipakai dan gagal bila <4.5 (teks normal) / <3 (UI besar, kontur).

### 3.3 Tipografi
- Heading: **Bricolage Grotesque** (variable). Body: **Inter**. Angka/BIB/kode: **JetBrains Mono**.
- **Self-host** via `@fontsource-variable/bricolage-grotesque`, `@fontsource/inter`, `@fontsource/jetbrains-mono` (hapus Google Fonts `<link>` di `layouts/app.blade.php`; subset latin, `font-display: swap`, preload 1 file heading + 1 body).
- Skala (desktop → mobile): h1 72→44, h2 44→34, h3 24→20, body 17→16, kecil 14. Gunakan `clamp()`; token fluid yang sudah ada di app.css boleh dipertahankan.
- Line-height heading 1.05–1.15, body 1.6. Tracking heading −0.01em.

### 3.4 Bentuk
- Kontur: 2.5px (tombol, input, chip) / 3px (kartu, panel, nav).
- Radius: chip 6, filter/input 8, tombol 10, kartu 14, panel besar 16–18. **Teks pendek jangan di kontainer berbentuk pil** (permintaan pemilik: jangan terlalu bulat bila teks sedikit).
- Bayangan keras: tombol 4px, kartu 5px, panel hero 14px (blok amber offset). Warna `--ink`.
- Halftone: titik `#E3BE78` di atas butter (pola SVG/CSS radial-gradient, bukan gambar raster).

---

## 4. Komponen

| Komponen | Spesifikasi |
|---|---|
| `.nav` | tinggi 76px, border-bottom 3px ink, sticky, latar paper. Logo resmi `logo-velosha.png` (bukan ikon roda placeholder mockup). 7 item nav (lihat §6.1). Mobile (<900px): hamburger → panel penuh, link tinggi 52px. |
| `.btn` | tinggi 52px, border 2.5px ink, radius 10, bayangan 4px; hover: translate(2px,2px) + bayangan 2px; active: translate(4px,4px) + tanpa bayangan; fokus: outline 3px `--mustard` offset 2px. Varian: primary (oranye, teks ink), ink (latar ink, teks paper), ghost (transparan). `.btn-sm` 44px. |
| `.card` | latar card, border 3px, radius 14, bayangan 5px; hover (jika klikabel): naik 3px. |
| `.chip` | radius 6, uppercase 12px, border 2px; varian status: Terbuka=pine, Mendatang=berry, Penuh=red, Selesai=mute. |
| `.filter` | tinggi 44, radius 8, bisa dipilih (aria-pressed). |
| `.input` | tinggi 52, radius 8, border 2.5px; fokus: bayangan mustard 3px; error: border red + pesan di bawah dengan ikon (tidak hanya warna). |
| `details.faq` | kontur tinta, ikon +/− berputar, konten dianimasikan `grid-template-rows 0fr→1fr`. |
| `.band` | marquee info; berhenti saat reduced-motion & saat hover/fokus. |
| `.numo` | angka outline besar untuk langkah/urutan (dekoratif, `aria-hidden`). |
| Badge status pendaftaran | Lunas=pine, Menunggu Bayar=mustard, Check-in=ink, Dibatalkan=red — **selalu ada teks**, bukan warna saja. |

Target sentuh minimal 44×44px untuk semua kontrol.

---

## 5. Sistem gerak

Semua di dalam `@media (prefers-reduced-motion: no-preference)`; di luar itu konten langsung tampil (tanpa opacity 0).

| Animasi | Spek |
|---|---|
| Reveal on scroll | fade + translateY(24px→0), 0.6s `cubic-bezier(.2,.7,.2,1)`, stagger 60ms antar anak (maks 6). Pertahankan `.velo-reveal` + IntersectionObserver yang ada. |
| Hero ride-in | pesepeda masuk dari kiri (translateX −120%→0) 1.1s ease-out sekali; setelah itu bob ±6px 3.2s infinite; speed lines muncul di belakang saat masuk lalu memudar. Roda depan melewati garis outline (z-index di atas panel). |
| Slider hero | maks 3 slide, crossfade 0.5s, autoplay 5s (berhenti saat hover/fokus/tab tersembunyi), titik navigasi + tombol prev/next dengan label aksesibel. |
| Hover tombol/kartu | translate + bayangan menyusut, 120ms. |
| Marquee `.band` | 40s linear infinite, `animation-play-state: paused` saat hover. |
| Transisi halaman | **ganti** efek blur + korden topo lama (`velo-nav-*`) dengan fade 150ms saja. Hapus `topo.svg` dari body. |
| Modal | scale .96→1 + fade 180ms. |
| FAQ | grid-rows 0fr→1fr 200ms. |

Aturan: hanya animasikan `transform`, `opacity`, `clip-path`. Tidak ada `filter: blur` animasi, tidak ada animasi `width/height/top/left`. Tidak ada library animasi tambahan.

---

## 6. Responsif

Breakpoint: 640 (sm), 900 (nav collapse), 1024 (lg), 1280 (xl). Target uji: **360, 390, 768, 1024, 1440**.

- Tanpa overflow horizontal di semua lebar (tes otomatis Playwright: `document.documentElement.scrollWidth <= innerWidth`).
- Gutter 16px (mobile), 24px (≥640), maks lebar konten 1200px.
- Grid kartu event: 1 kolom <640, 2 kolom ≥640, 3 kolom ≥1024.
- Hero mobile (≤900px): urutan teks → tombol → panel+pesepeda (skala 0.6, takik tetap) → slider (swipeable, 1 slide terlihat). Judul 44px.
- Tabel/daftar data (ranking, riwayat) → kartu bertumpuk di mobile.
- Sticky CTA bawah di detail event tetap (sudah ada), padding bawah halaman agar tidak menutupi konten.
- Gambar: `srcset` + `sizes`, `loading="lazy"` (kecuali hero/LCP), `width/height` eksplisit agar CLS = 0.

### 6.1 Navigasi (7 item)
Beranda · Kalender · Ranking · Tentang · Regulasi · Bantuan · (Masuk / Portal saya). Tab aktif = garis bawah mustard 3px. Hapus item yang tidak ada rutenya.

---

## 7. Spesifikasi per halaman

### 7.1 Beranda (`pages/home.blade.php`)
**Hero** (stage 1200×660 desktop):
- Panel: SVG viewBox `-2 -2 580 445`, path
  `M226,0 H540 Q576,0 576,36 V405 Q576,441 540,441 H36 Q0,441 0,405 V136 Q0,100 36,100 H154 Q190,100 190,64 V36 Q190,0 226,0 Z`
  isi `#F8E3B5` + halftone `#E3BE78`, stroke 3px ink. Blok amber `#DE7119` offset +14/+14 di belakang (stroke ink 3px).
- Pesepeda: PNG transparan 2048² (`rider_final_2048.webp` + AVIF + fallback PNG; 1024 untuk mobile), tampil 640×640 di left 610 / top −40; roda depan melewati outline panel.
- Kiri-atas: h1 "Siap gowes sampai finis?" (72px, garis bawah squiggle SVG mustard), paragraf 1–2 kalimat, 1 tombol "Cari event" → `events.index`.
- Kiri-bawah: slider media 480×220, maks 3 gambar, dikelola admin (§11).
- **Dihapus**: badge lingkaran, statistik hero, ikon dekoratif, teks "AI-slop".

**Section berikutnya (urutan):**
1. Marquee `.band` (info ringkas: tanggal event terdekat dari data nyata; bila tidak ada event, sembunyikan band).
2. **Pendaftaran Terbuka** dan **Mendatang** — kartu event dengan **banner/poster** (permintaan pemilik: harus pakai banner). Kartu: poster rasio 16:9 dalam bingkai kartun, chip status, judul, tanggal, lokasi, harga mulai dari, tombol "Lihat detail". Radius kartu 14; chip 6 (tidak bulat).
3. Paket tiket (`TicketPackageContent`): kartu dengan `.numo`.
4. **Informasi & Regulasi** (dirombak lebih bagus): grid kartu bernomor `.numo` (01–04) ringkasan regulasi dari `RuleContent`, tiap kartu: ikon lucide dalam `.sq`, judul, 1–2 kalimat, tautan "Baca selengkapnya" → `/rule#anchor`. Bukan daftar teks panjang.
5. Banner pesepeda `.bk` (ilustrasi) sebagai pemisah menuju footer + CTA "Cari event".

### 7.2 Kalender / Katalog (`EventCatalog`)
Param URL **dipertahankan**: `q`, `type` (default `all`), `month`, `sort` (default `date-asc`). Header halaman `.pagehead` (judul + deskripsi). Bar filter: input cari, `.filter` per tipe (Gran Fondo, Medio Fondo, Fun Ride, Climb), bulan, urutan. Chip filter aktif dengan tombol hapus (`clearFilter`). State kosong: ilustrasi + tombol "Reset filter". Loading: skeleton kartu (`wire:loading`).

### 7.3 Detail event
Layout **tidak berubah**. Terapkan token baru ke: `specs-strip`, `itinerary`, `aid-stations`, `gallery`, `booking-box`, `role-modal`, sticky CTA mobile (baris 94 `event-detail.blade.php`), YouTube embed (lazy, bingkai kartun), peta. Mockup tidak menampilkan galeri/YouTube/peta — **tetap pertahankan & restyle**, jangan dihapus.

### 7.4 Checkout (3 langkah Livewire)
Stepper bernomor di atas (`.numo`), `order-summary` sticky di kolom kanan (desktop) / akordeon di bawah (mobile). Opsi pilihan `.seg .opt{flex:1;min-width:0}` (hindari overflow). Validasi inline. **Teks "Biaya Layanan Midtrans" di ringkasan**: karena Midtrans belum aktif, tampilkan label netral "Biaya Layanan" — jangan ubah logika harga. Jangan ubah `simulatePaid`/guard pembayaran. Kontak darurat: label "Asuransi &amp; Tim Medis" harus dirender benar (lihat §14).

### 7.5 Login / Register / Auth (`auth-shell`)
Dua kolom desktop: kiri ilustrasi pesepeda + tagline pendek, kanan form kartu. Mobile: hanya form. Tombol sosial (Google/Facebook/TikTok) saat ini mengarah ke `/auth/demo/*` → **tampilkan hanya bila `config('app.demo_login')` true**; di produksi sembunyikan seluruh blok sosial + pemisah "atau".

### 7.6 Portal / Dashboard (`/dashboard`)
Sapaan, kartu ringkasan pendaftaran aktif, daftar riwayat (kartu bertumpuk di mobile), badge status (§4), tombol "E-Ticket" bila lunas, "Lanjutkan bayar" bila menunggu. State kosong dengan ilustrasi + CTA ke kalender.

### 7.7 E-Ticket (`ticket.blade.php`)
Tiket bergaya karcis: kiri info (nama, event, kategori, tanggal, lokasi), kanan potongan perforasi (garis putus tinta) dengan BIB `JetBrains Mono` besar + QR. Aksi: tambah ke Google Calendar, Cetak PDF, bagikan, ke Portal. **Tombol menuju `/admin/registrations` (baris ~238) harus disembunyikan untuk non-staf** (`@can`/cek role) — jangan tampil ke peserta. Gaya cetak: `@media print` tanpa bayangan/latar, kontras penuh.

### 7.8 Ranking
Podium 1–2–3 ilustratif (kartu bertingkat) + tabel. Data masih hardcoded sampai Fase 11 → beri label kecil "Data contoh" agar jujur; jangan klaim data nyata.

### 7.9 Tentang (`AboutContent`)
Hero kecil `.pagehead`, cerita, nilai (kartu bernomor), tim. Perbaiki typo "kotak doubtful" di konten default (§14).

### 7.10 Regulasi (`RuleContent`)
Daftar isi sticky (desktop) + section bernomor, `details.faq` untuk poin rinci. Anchor per section. Perbaiki double-escape `&amp;` (§14).

### 7.11 Bantuan (`HelpContent`)
Kartu kontak (3: WhatsApp, Email, Tanya Mizu — dari `contacts`), FAQ per grup (`faq_groups`) dengan `details.faq`, CTA bawah (`cta_*`). Kartu "Tanya Mizu" hanya tampil bila `MizuAiService::isConfigured()`.

### 7.12 Halaman turunan
Riwayat, register & varian auth (lupa/reset sandi, verifikasi), 403/404/500 (ilustrasi pesepeda tersesat/ban kempes + tombol kembali), halaman tanpa nav (`$nav=false`). Semua pakai token & komponen §3–4.

---

## 8. Footer
Latar ink, teks paper (kontras 14:1), kolom tautan, logo resmi, strip halftone di atas. Tautan harus punya rute nyata.

---

## 9. Aset & ilustrasi

| Aset | Sumber | Catatan |
|---|---|---|
| Pesepeda hero | `rider_final_2048.webp` (285KB), `rider_final_1024.webp` (128KB), `rider_final_2048.avif` (193KB), master `rider_final.png` | simpan di `public/images/hero/`; `<picture>` AVIF→WebP→PNG; atribut `width=2048 height=2048`; `fetchpriority="high"`, tanpa lazy |
| Ilustrasi fallback (kartu event tanpa foto, empty state, 404) | SVG inline sederhana 2–3 varian | tidak memakai `hero-peloton.jpg` |
| Logo | `logo-velosha.png` resmi | bukan ikon roda mockup |
| `hero-peloton.jpg` | **jangan dihapus** | masih dipakai `EventCardPresenter` sebagai fallback sampai ilustrasi fallback menggantikannya; hapus hanya setelah presenter diubah + tes lulus |

Ikon: lucide self-hosted (`public/js/lucide.min.js`) tetap; ikon dalam `.sq` (kotak 44px, border 2.5px).

---

## 10. Alur Livewire & Alpine
Tidak ada perubahan perilaku. `wire:navigate`, `RaceCards` (tab gran-fondo/medio-fondo/fun-ride/climb), `EventCatalog`, checkout steps, Mizu widget dipertahankan. Setelah morph Livewire, `lucide.createIcons()` tetap dipanggil (hook sudah ada di layout).

---

## 11. Data & admin (satu-satunya perubahan backend)

**Tabel baru `hero_slides`** (migration MySQL):
`id, image_path, alt (string 160), caption (nullable string 80), sort (unsigned tinyint default 0), is_active (bool default true), timestamps`.
- Validasi: **maksimal 3 baris aktif** (validasi di Filament resource + guard di model/observer; tes unit).
- Filament Resource `HeroSlideResource` (FileUpload `image/*`, maks 2MB, resize ke 960×440 WebP via Intervention bila sudah ada, kalau belum simpan apa adanya), reorderable, toggle aktif.
- Kompatibilitas: bila `hero_slides` kosong, slider menampilkan data `RaceBannerContent`/`EventPhoto` yang ada (maks 3) atau disembunyikan — tidak boleh error.
- `ProductionAdminSeeder`/seeder produksi **tidak** membuat slide demo.

Tidak ada perubahan lain pada skema, model sensitif (kolom terenkripsi), counter BIB, atau Filament admin lain.

---

## 12. Aksesibilitas
- Kontras sesuai §3.2. Fokus terlihat di semua kontrol (outline mustard 3px + offset).
- Hero: gambar pesepeda `alt=""` jika dekoratif, slider memakai `role="region" aria-roledescription="carousel"` + label; tombol prev/next & titik berlabel; autoplay berhenti saat fokus/hover.
- Status tidak hanya warna. Form: `label` terhubung, `aria-describedby` untuk error, `aria-live="polite"` untuk notifikasi Livewire.
- Skip link "Lewati ke konten". Hierarki heading tunggal h1 per halaman.
- Reduced-motion terhormati (§5). Marquee dapat dijeda.

## 13. Anggaran performa
LCP ≤ 2.5s (4G lambat, mobile), CLS ≤ 0.05, INP ≤ 200ms, JS halaman publik ≤ 150KB gz (di luar lucide), CSS ≤ 60KB gz, font ≤ 4 file woff2 (~120KB total), gambar hero ≤ 300KB. Lighthouse mobile target: Performance ≥ 90, A11y ≥ 95, Best Practices ≥ 95. Jalankan Lighthouse sungguhan dan laporkan angka (sebelumnya belum pernah dijalankan).

## 14. Bug & temuan yang harus diperbaiki sekalian
1. **`&amp;` double-escape** di halaman Rule (konten dari DB dirender dengan `{{ }}` sehingga entity tampil literal). Perbaiki di sumber konten/seeder (simpan `&` biasa) dan pastikan view memakai `{{ }}`; tambahkan tes render. Cek juga `order-summary` & `step-2-medical` (`&amp;` di Blade statis itu benar, jangan diubah).
2. Typo "kotak doubtful" pada default `AboutContent` → perbaiki kalimat.
3. Label "Biaya Layanan Midtrans" → "Biaya Layanan" (Midtrans belum aktif).
4. Tombol admin di E-Ticket tampil ke peserta → batasi ke staf.
5. Tombol sosial demo → hanya bila `demo_login`.
6. `PRD/AGENTS.md` usang (§17).
7. Ranking hardcoded → label "Data contoh".

## 15. QA & matriks uji
- Suite PHPUnit yang ada (158 + concurrency + ConfigGuard) **harus tetap hijau** pada setiap commit. Tidak boleh menghapus/menurunkan tes keamanan.
- Tambah: tes render tiap halaman publik (200 + string kunci), tes `hero_slides` maks 3, tes tombol admin tak tampil untuk peserta, tes sosial login tersembunyi saat `demo_login=false`, tes kontras token.
- Playwright (Chromium tersedia): screenshot 360/390/768/1024/1440 untuk 12 halaman, cek overflow horizontal = 0, cek `prefers-reduced-motion: reduce` (tidak ada elemen opacity 0 tersisa, tidak ada animasi jalan).
- Bandingkan dengan mockup; simpan screenshot di `docs/redesign/` (bukan di `public/`).

## 16. Urutan kerja (fase, tiap fase = 1 commit/PR kecil)
- **F0** Token + font self-host + base layout + komponen dasar (nav/footer/btn/card/chip/input) + hapus Google Fonts. Halaman lama masih jalan.
- **F1** Beranda + hero + `hero_slides` + resource Filament.
- **F2** Kalender + kartu event + RaceCards.
- **F3** Detail event (restyle saja).
- **F4** Checkout + auth.
- **F5** Portal + Ticket + Riwayat.
- **F6** Ranking + Tentang + Regulasi + Bantuan + 403/404/500.
- **F7** Animasi final + hapus kode/CSS lama + audit kontras + Lighthouse + dokumentasi.

## 17. Pembaruan dokumentasi
Perbarui `PRD/AGENTS.md`: hapus aturan 1px/3px, "4 warna", klaim SQLite/CDN; tulis token baru, MySQL 100%, font self-host, aturan reduced-motion, rujuk dokumen ini.

## 18. Default yang bisa di-veto pemilik
1. Foto tetap dipakai dalam bingkai kartun; fallback ilustrasi.
2. Logo resmi menggantikan ikon roda mockup.
3. Nav 7 item.
4. Tabel baru `hero_slides` + resource Filament.
5. Ranking tetap hardcoded sampai Fase 11, diberi label "Data contoh".
6. `hero.md` lama dinyatakan usang (digantikan dokumen ini).

## 19. Definition of done
Semua 12 halaman cocok mockup di 5 lebar; nol overflow; kontras lolos; reduced-motion lolos; Lighthouse sesuai §13 dengan angka dilaporkan; suite penuh hijau; grep kelas lama = 0; AGENTS.md diperbarui; tidak ada perubahan di alur pembayaran/keamanan.
