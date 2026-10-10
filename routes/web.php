<?php

use App\Http\Controllers\DemoSocialController;
use App\Models\Event;
use App\Models\Registration;
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

Route::get('/riwayat', function () {
    $today = now()->toDateString();

    $archive = Event::query()
        ->where('status', '!=', Event::STATUS_DRAFT)
        ->where('status', '!=', Event::STATUS_CANCELLED)
        ->where(function ($query) use ($today) {
            $query->whereDate('event_date', '<', $today)
                ->orWhere('status', Event::STATUS_COMPLETED);
        })
        ->with('categories.registrations')
        ->orderByDesc('event_date')
        ->get();

    $myRegs = auth()->user()?->registrations()
        ->with(['category.event', 'ticket'])
        ->latest()
        ->get() ?? collect();

    return view('pages.riwayat', [
        'archive' => $archive,
        'myRegs' => $myRegs,
        'meta' => $archive->count().' Race Selesai · Musim 2026–2027',
    ]);
})->name('riwayat');

Route::get('/ranking', function () {
    return view('pages.ranking');
})->name('ranking');

Route::get('/help', function () {
    return view('pages.help');
})->name('help');

// 2. Daftar Event / Race Finder Catalog
Route::get('/events', function () {
    return view('pages.events');
})->name('events.index');

// 3. Detail Event & Profil Rute
Route::get('/events/{slug}', function ($slug) {
    $event = Event::query()
        ->published()
        ->with(['categories', 'photos'])
        ->where('slug', $slug)
        ->firstOrFail();

    return view('pages.event-detail', compact('event'));
})->name('events.show');

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
Route::get('/ticket/{registration?}', function (?Registration $registration = null) {
    $user = auth()->user();

    if ($registration instanceof Registration) {
        abort_unless(
            $registration->user_id === $user->id
            || $user->isAdmin()
            || ($user->isPenyelenggara() && $registration->category?->event?->user_id === $user->id),
            403
        );
        $registration->loadMissing(['category.event.photos', 'ticket', 'user']);
    } else {
        $registration = $user->registrations()->with(['category.event.photos', 'ticket', 'user'])->latest()->first();
    }

    return view('pages.ticket', ['registration' => $registration]);
})->middleware('auth')->name('ticket.show');

// 7. Dashboard Peserta (Portal Rider — data & filter live via Livewire PortalDashboard)
Route::view('/dashboard', 'pages.dashboard')
    ->middleware(['auth', 'role:peserta'])
    ->name('dashboard');
