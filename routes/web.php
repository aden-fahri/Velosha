<?php

use App\Http\Controllers\DemoSocialController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page / Beranda
Route::get('/', function () {
    return view('pages.home');
})->name('home');

// 1b. Halaman Informasi (Navigasi Utama)
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/rule', function () {
    return view('pages.rule');
})->name('rule');

Route::get('/help', function () {
    return view('pages.help');
})->name('help');

// 2. Daftar Event / Race Finder Catalog
Route::get('/events', [EventController::class, 'index'])->name('events.index');

// 3. Detail Event & Profil Rute
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// 4. Autentikasi: Login & Register Peserta (Laravel Breeze)
Route::redirect('/admin/login', '/login');
require __DIR__.'/auth.php';

// 4b. Social login (mode demo — Socialite asli menyusul)
Route::get('/auth/demo/{provider}', DemoSocialController::class)
    ->middleware('guest')
    ->name('auth.demo');

// 4c. Google OAuth 2.0 Resmi (menggunakan API Google dari .env)
Route::get('/auth/google', [App\Http\Controllers\GoogleAuthController::class, 'redirect'])
    ->middleware('guest')
    ->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleAuthController::class, 'callback'])
    ->middleware('guest')
    ->name('auth.google.callback');

// 5. Checkout & Formulir Pendaftaran (Livewire 3 langkah — peserta & penyelenggara)
Route::get('/checkout', function (Request $request) {
    return view('pages.checkout', [
        'eventSlug' => (string) $request->query('event', ''),
        'categoryId' => $request->filled('category') ? (int) $request->query('category') : null,
    ]);
})->middleware(['auth', 'role:peserta,penyelenggara'])->name('checkout');

// 5b. Ruang Tunggu Antrean Pendaftaran (Waiting Room)
Route::get('/waiting-room', function (Request $request) {
    return view('pages.waiting-room', [
        'eventSlug' => (string) $request->query('event', ''),
        'categoryId' => $request->filled('category') ? (int) $request->query('category') : null,
    ]);
})->middleware(['auth', 'role:peserta,penyelenggara'])->name('waiting-room');

// 6. Konfirmasi Pembayaran & E-Ticket Resmi (hanya pemilik pendaftaran / admin)
Route::get('/ticket/{registration?}', [TicketController::class, 'show'])
    ->middleware('auth')
    ->name('ticket.show');

Route::get('/ticket/{registration?}/download', [TicketController::class, 'download'])
    ->middleware('auth')
    ->name('ticket.download');

Route::get('/ticket/{registration?}/bib', [TicketController::class, 'bib'])
    ->middleware('auth')
    ->name('ticket.bib');

// 7. Dashboard Peserta (Portal Rider — data & filter live via Livewire PortalDashboard)
Route::view('/dashboard', 'pages.dashboard')
    ->middleware(['auth', 'role:peserta,penyelenggara'])
    ->name('dashboard');

// 7b. Helpdesk Pelapor (Peserta, Penyelenggara, Operator, Admin)
Route::view('/dashboard/bantuan/buat', 'pages.helpdesk.create')
    ->middleware(['auth', 'role:peserta,penyelenggara,operator,admin'])
    ->name('helpdesk.create-ticket');

Route::get('/dashboard/bantuan/{ticket?}', function ($ticket = null) {
    return view('pages.helpdesk.my-tickets', ['ticket' => $ticket]);
})->middleware(['auth', 'role:peserta,penyelenggara,operator,admin'])->name('helpdesk.my-tickets');

// 8. Helpdesk Operator & Admin (Antrian Tiket & Detail Khusus Operator/Admin)
Route::view('/helpdesk', 'pages.helpdesk.index')
    ->middleware(['auth', 'role:operator,admin'])
    ->name('helpdesk.index');

Route::view('/operator/helpdesk', 'pages.helpdesk.index')
    ->middleware(['auth', 'role:operator,admin']);

Route::get('/helpdesk/tickets/{ticket}', function ($ticket) {
    return view('pages.helpdesk.detail', ['ticketId' => (int) $ticket]);
})->middleware(['auth', 'role:operator,admin'])
  ->name('helpdesk.detail');

Route::get('/operator/helpdesk/tickets/{ticket}', function ($ticket) {
    return view('pages.helpdesk.detail', ['ticketId' => (int) $ticket]);
})->middleware(['auth', 'role:operator,admin']);

// 9. Helpdesk Admin Analitik (Khusus Administrator)
Route::view('/helpdesk/analitik', 'pages.helpdesk.analytics')
    ->middleware(['auth', 'role:admin'])
    ->name('helpdesk.analytics');

// 10. Pendaftaran Komunitas / Organisasi Penyelenggara (SaaS)
Route::get('/organisasi/daftar', [App\Http\Controllers\OrganizationController::class, 'create'])
    ->middleware('auth')
    ->name('organizations.create');

Route::post('/organisasi/daftar', [App\Http\Controllers\OrganizationController::class, 'store'])
    ->middleware('auth')
    ->name('organizations.store');

Route::get('/organisasi/dasbor', function () {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
    $org = $user?->organizations()->first() ?? $user?->ownedOrganizations()->first();
    if ($org) {
        return redirect()->route('organizations.show', ['organization' => $org->slug, 'tab' => 'overview']);
    }
    if ($user && $user->isPeserta()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('organizations.create')->with('info', 'Silakan daftarkan komunitas atau EO Anda terlebih dahulu.');
})->middleware('auth')->name('organizer.dashboard');

// 10a. Hub Dasbor Penyelenggara & Buat Event (SaaS)
Route::get('/organisasi/{organization:slug}', [App\Http\Controllers\OrganizationController::class, 'show'])
    ->middleware('auth')
    ->name('organizations.show');

Route::put('/organisasi/{organization:slug}', [App\Http\Controllers\OrganizationController::class, 'update'])
    ->middleware('auth')
    ->name('organizations.update');

Route::get('/organisasi/{organization:slug}/events/create', [App\Http\Controllers\OrganizationController::class, 'createEvent'])
    ->middleware('auth')
    ->name('organizations.events.create');

Route::post('/organisasi/{organization:slug}/events/create', [App\Http\Controllers\OrganizationController::class, 'storeEvent'])
    ->middleware('auth')
    ->name('organizations.events.store');

Route::get('/organisasi/{organization:slug}/events/{event:slug}/edit', [App\Http\Controllers\OrganizationController::class, 'editEvent'])
    ->middleware('auth')
    ->name('organizations.events.edit');

Route::put('/organisasi/{organization:slug}/events/{event:slug}', [App\Http\Controllers\OrganizationController::class, 'updateEvent'])
    ->middleware('auth')
    ->name('organizations.events.update');

// 10a-2. Gate Scanner Hari-H Balap (Check-in RPC)
Route::get('/organisasi/{organization:slug}/events/{event:slug}/scanner', [App\Http\Controllers\EventCheckInController::class, 'index'])
    ->middleware('auth')
    ->name('organizations.events.scanner');

Route::post('/organisasi/{organization:slug}/events/{event:slug}/scanner/verify', [App\Http\Controllers\EventCheckInController::class, 'verify'])
    ->middleware('auth')
    ->name('organizations.events.scanner.verify');

// 10b. Manajemen Paket Kuota Event Penyelenggara (SaaS)
Route::get('/paket', [App\Http\Controllers\OrganizationQuotaController::class, 'publicPackages'])
    ->name('packages.index');

Route::get('/organisasi/{organization:slug}/kuota', [App\Http\Controllers\OrganizationQuotaController::class, 'index'])
    ->middleware('auth')
    ->name('organizations.quota');

Route::post('/organisasi/{organization:slug}/kuota/beli', [App\Http\Controllers\OrganizationQuotaController::class, 'purchase'])
    ->middleware('auth')
    ->name('organizations.quota.purchase');

// 10c. Verifikasi Penyelenggara & Dokumen Legalitas (SaaS)
Route::get('/organisasi/{organization:slug}/verifikasi', [App\Http\Controllers\OrganizationController::class, 'verification'])
    ->middleware('auth')
    ->name('organizations.verification');

Route::post('/organisasi/{organization:slug}/verifikasi', [App\Http\Controllers\OrganizationController::class, 'submitVerification'])
    ->middleware('auth')
    ->name('organizations.verification.submit');

Route::get('/organisasi/{organization:slug}/dokumen-verifikasi', [App\Http\Controllers\OrganizationController::class, 'downloadDocument'])
    ->middleware('auth')
    ->name('organizations.document.download');

// 10d. Keuangan Penyelenggara & Pencairan Dana / Payout (SaaS)
Route::get('/organisasi/{organization:slug}/keuangan', [App\Http\Controllers\OrganizationFinanceController::class, 'index'])
    ->middleware('auth')
    ->name('organizations.finance');

Route::post('/organisasi/{organization:slug}/keuangan/rekening', [App\Http\Controllers\OrganizationFinanceController::class, 'storeAccount'])
    ->middleware('auth')
    ->name('organizations.finance.account.store');

Route::post('/organisasi/{organization:slug}/keuangan/payout', [App\Http\Controllers\OrganizationFinanceController::class, 'requestPayout'])
    ->middleware('auth')
    ->name('organizations.finance.payout');


// 11. Midtrans Webhook (Pembayaran Tiket Race & Kuota SaaS)
Route::post('/payment/midtrans-webhook', [App\Http\Controllers\PaymentWebhookController::class, 'handle'])
    ->name('payment.midtrans.webhook');