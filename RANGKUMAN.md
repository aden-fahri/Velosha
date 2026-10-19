# RANGKUMAN PROYEK — VELOSHA

## 1. Gambaran Umum
Velosha adalah platform pendaftaran maraton sepeda / Gran Fondo Indonesia. Stack: Laravel 11, Livewire 3, Filament admin, Tailwind CSS (CDN + Vite), Alpine.js, SQLite (test) / MySQL (dev). Bahasa UI: Indonesia.

## 2. Struktur Folder (top-level)
- `app/` — Actions, Enums, Filament Resources, Http Controllers, Livewire, Models, Providers, Support
- `database/` — migrations, seeders, factories, sqlite
- `resources/views/` — layouts, pages, livewire, components, partials
- `resources/css`, `resources/js` — Tailwind entry + bootstrap
- `routes/` — web.php, auth.php, console.php
- `public/` — assets (images, build, fonts, js)
- `tests/Feature/` — 121 feature tests
- `PRD/` — dokumen spesifikasi

## 3. Style & Design System
- Border radius ketat: `velo-box-1` (1px) luar, `velo-box-3` (3px) dalam. Dilarang rounded-full/kapsul.
- 4 warna inti: Amber Race `#DE7119` (aksi), Warm Gray `#DEE3E2` (body), Dark Teal `#116979` (teks/panel), Light Teal `#18B0B0` (dekoratif). Navbar/kartu putih.
- Font Inter, heading 800-900. Ikon Lucide via CDN. Tailwind dual setup (Vite + CDN fallback).
- Teks ringkas, Bahasa Indonesia, `lang="id"`, indent 4 spasi, LF.

## 4. Alur / Flow
- Publik: Home, About, Rule, Riwayat, Ranking, Help, Events catalog, Event detail.
- Auth (Breeze): login/register, social demo.
- Peserta (role:peserta): Checkout 3 langkah (Livewire), Dashboard, Ticket/e-ticket.
- Admin: Filament panel (events, registrations, users, content CRUD: about/rule/help/footer/race-banner/ticket-package).
- Route closure di `routes/web.php`; controller dipakai untuk Event, Riwayat, Ticket.

## 5. Logic / Backend
- Models: Event, EventCategory, EventPhoto, Registration, Ticket, User, plus content models (About/Rule/Help/Footer/RaceBanner/TicketPackage).
- Actions: CancelRegistration, CompleteRegistration.
- Livewire: EventCatalog, RaceCards, Checkout (3 step), PortalDashboard, LiveRegistrations, MizuChatWidget.
- Filament: CRUD resource untuk semua entitas + widgets dashboard.
- Auth role middleware `Role`. DemoSocialController (demo mode).

## 6. Fitur Sudah Dibuat
- Katalog event + detail event (published).
- Checkout 3 langkah + medical form + review.
- Dashboard peserta (portal).
- E-ticket dengan QR (TicketController + QrCodeService).
- Riwayat balap (arsip event selesai).
- Admin Filament (CRUD konten & event & registrasi & user).
- Chatbot Mizu (widget, dummy/AI service).
- 121 feature tests (398 assertions).

## 7. Belum Dibuat / Belum Selesai
- Responsive: sebagian halaman belum optimal mobile.
- Animasi: belum ada transisi/motion yang lengkap (velo-reveal ada tapi terbatas).
- Video hero dihapus, kembali ke gambar.
- Social login asli (Socialite) masih demo.
- Midtrans payment integration belum live.
- Google Maps, Pusher real-time belum live.
- REST API 25 endpoint belum dibuat.
- High concurrency strategy belum diimplementasi.

## 8. Catatan
- Branch main up-to-date dengan origin/main; perubahan lokal (tests minor) belum di-commit.
- File screenshot "Desktop - 2.png" dan video hero sudah dihapus.
RANGKUMAN.md