<?php

use App\Http\Controllers\DemoSocialController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
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

Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');

Route::get('/ranking', function () {
    return view('pages.ranking');
})->name('ranking');

Route::get('/help', function () {
    return view('pages.help');
})->name('help');

// 2. Daftar Event / Race Finder Catalog
Route::get('/events', [EventController::class, 'index'])->name('events.index');

// 3. Detail Event & Profil Rute
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// 4. Autentikasi: Login & Register Peserta (Laravel Breeze)
require __DIR__.'/auth.php';

// 4b. Social login (mode demo — Socialite asli menyusul)
Route::get('/auth/demo/{provider}', DemoSocialController::class)
    ->middleware('guest')
    ->name('auth.demo');

// 4c. Halaman login panel lama (bookmarked) → arahkan ke login utama
Route::get('/admin/login', fn () => redirect()->route('login'))
    ->name('admin.login.redirect');

// 5. Checkout & Formulir Pendaftaran (Livewire 3 langkah — wajib login peserta)
Route::get('/checkout', function (Request $request) {
    return view('pages.checkout', [
        'eventSlug' => (string) $request->query('event', ''),
        'categoryId' => $request->filled('category') ? (int) $request->query('category') : null,
    ]);
})->middleware(['auth', 'role:peserta'])->name('checkout');

// 6. Konfirmasi Pembayaran & E-Ticket Resmi (hanya pemilik pendaftaran / admin)
Route::get('/ticket/{registration?}', [TicketController::class, 'show'])
    ->middleware('auth')
    ->name('ticket.show');

// 7. Dashboard Peserta (Portal Rider — data & filter live via Livewire PortalDashboard)
Route::view('/dashboard', 'pages.dashboard')
    ->middleware(['auth', 'role:peserta'])
    ->name('dashboard');

// 8. Helpdesk Operator & Admin (Antrian Tiket)
Route::view('/helpdesk', 'pages.helpdesk.index')
    ->middleware(['auth', 'role:operator,admin'])
    ->name('helpdesk.index');
