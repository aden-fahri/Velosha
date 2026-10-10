@use('App\Support\EventCardPresenter', 'P')
@extends('layouts.app', ['title' => 'E-Ticket Resmi Peserta — Velosha'])

@section('content')
<div class="bg-velosurfacealt py-12 min-h-[calc(100vh-140px)]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        @if ($registration === null || $registration->category === null || $registration->category->event === null)

            <!-- Belum ada pendaftaran -->
            <div class="text-center mb-8">
                <div class="w-12 h-12 bg-velosurface text-[#556066] flex items-center justify-center velo-box-1 mx-auto mb-3 border border-veloborderstrong">
                    <i data-lucide="ticket" class="w-6 h-6"></i>
                </div>
                <span class="text-xs uppercase tracking-widest text-[#5f6a70] font-bold">Belum Ada E-Ticket</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mt-1">
                    Anda Belum Terdaftar pada Race Manapun.
                </h1>
                <p class="text-xs text-[#556066] mt-2">
                    E-Ticket dan nomor BIB akan terbit otomatis setelah Anda menyelesaikan pendaftaran.
                </p>
            </div>

            <div class="bg-white border border-veloborder velo-box-1 p-8 text-center">
                <p class="text-xs text-[#556066] mb-5">
                    Jelajahi kalender balap Velosha dan temukan kategori jarak yang sesuai dengan target Anda.
                </p>
                <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold px-6 py-3 text-xs velo-box-1 shadow-sm transition">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 mr-2"></i>
                    <span>Jelajahi Kalender Balap</span>
                </a>
            </div>

        @elseif (! $registration->isPaid())

            <!-- Menunggu pembayaran -->
            <div class="text-center mb-8">
                <div class="w-12 h-12 bg-amber-100 text-amber-800 flex items-center justify-center velo-box-1 mx-auto mb-3 border border-amber-300">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <span class="text-xs uppercase tracking-widest text-amber-800 font-bold">Menunggu Pembayaran</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mt-1">
                    Pendaftaran Anda Belum Lunas.
                </h1>
                <p class="text-xs text-[#556066] mt-2">
                    Selesaikan pembayaran untuk menerbitkan E-Ticket dan nomor BIB resmi Anda.
                </p>
            </div>

            <div class="bg-white border-2 border-amber-400 velo-box-1 shadow-sm p-6">
                <dl class="space-y-3 text-xs">
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#556066]">Nama Peserta</dt>
                        <dd class="font-semibold text-teal-900 text-right">{{ $registration->participant_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#556066]">Kategori Jarak</dt>
                        <dd class="font-semibold text-teal-900 text-right">{{ $registration->category->name }} ({{ P::distance($registration->category->distance_km) }})</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#556066]">Event</dt>
                        <dd class="font-semibold text-teal-900 text-right">{{ $registration->category->event->title }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#556066]">Nomor Pendaftaran</dt>
                        <dd class="font-mono font-semibold text-teal-900 text-right">REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#556066]">Status</dt>
                        <dd>
                            <span class="inline-block text-[10px] font-bold px-2 py-0.5 velo-box-3 bg-amber-50 text-amber-800 border border-amber-300">PENDING</span>
                        </dd>
                    </div>
                </dl>

                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('checkout', ['event' => $registration->category->event->slug, 'category' => $registration->category->id]) }}" wire:navigate
                       class="flex items-center justify-center bg-race-600 hover:bg-race-700 text-white px-4 py-3 velo-box-1 text-xs font-semibold shadow-xs transition">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 mr-2"></i>
                        <span>Bayar Sekarang</span>
                    </a>
                    <a href="{{ route('events.index') }}"
                       class="flex items-center justify-center bg-white hover:bg-velosurfacealt text-teal-800 border border-veloborderstrong px-4 py-3 velo-box-1 text-xs font-semibold shadow-xs transition">
                        Lihat Event Lain
                    </a>
                </div>
            </div>

        @else

            @php
                $category = $registration->category;
                $event = $category->event;
                $ticket = $registration->ticket;
                $waveParts = array_map('trim', explode(' - ', (string) $category->wave_start_time, 2));
                $waveName = $waveParts[0] !== '' ? $waveParts[0] : (string) $category->wave_start_time;
                $waveTime = $waveParts[1] ?? '';
                $qrData = $ticket?->qr_code_data ?? '';
                $bibSuffix = filled($registration->bib_number) ? substr((string) $registration->bib_number, -4) : '0000';
                $scanCode = strtoupper(substr(hash('sha256', $qrData !== '' ? $qrData : (string) $registration->id), 0, 4)).'-'.$bibSuffix.'-VLS';
            @endphp

            <!-- Success Banner -->
            <div class="text-center mb-8">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-800 flex items-center justify-center velo-box-1 mx-auto mb-3 border border-emerald-300 shadow-xs">
                    <i data-lucide="check" class="w-6 h-6 stroke-[3]"></i>
                </div>
                <span class="text-xs uppercase tracking-widest text-emerald-800 font-bold">
                    {{ $registration->status === \App\Models\Registration::STATUS_CHECKED_IN ? 'Sudah Check-In di Race Pack Collection' : 'Pembayaran Terverifikasi Lunas' }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mt-1">
                    Pendaftaran Berhasil! Sampai Jumpa di Pelataran Start.
                </h1>
                <p class="text-xs text-[#556066] mt-2">
                    Simpan E-Ticket ini di ponsel Anda. Tunjukkan QR Code pada panitia Race Pack Collection untuk penukaran nomor dada dan RFID chip.
                </p>
            </div>

            <!-- Official E-Ticket Card -->
            <div class="bg-white border-2 border-teal-800 velo-box-1 shadow-lg overflow-hidden relative">

                <!-- Top Header Ticket -->
                <div class="bg-[#2E343B] text-white p-6 border-b border-white/10 flex justify-between items-start">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="w-5 h-5 bg-race-500 text-white font-black text-xs flex items-center justify-center velo-box-1">V</span>
                            <span class="text-xs font-extrabold tracking-widest uppercase text-[#d1d5db]">VELOSHA OFFICIAL E-TICKET</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                            {{ $event->title }}
                        </h2>
                        <div class="text-xs text-[#d1d5db] mt-1 flex items-center space-x-3 flex-wrap">
                            <span>{{ P::tanggalLengkap($event->event_date) }}</span>
                            @if ($waveTime !== '')
                            <span>&bull;</span>
                            <span>Start: {{ $waveTime }}</span>
                            @endif
                            <span>&bull;</span>
                            <span>{{ $event->location_name }}</span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/40 velo-box-3">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5"></span>
                            {{ $registration->status === \App\Models\Registration::STATUS_CHECKED_IN ? 'CHECKED-IN' : 'CONFIRMED / PAID' }}
                        </span>
                        <div class="text-[10px] font-mono text-white/60 mt-2">ORDER: #VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>

                <!-- Ticket Core: BIB & QR Code Centerpiece -->
                <div class="p-6 sm:p-8 bg-white grid grid-cols-1 md:grid-cols-12 gap-6 items-center border-b border-dashed border-veloborderstrong relative">

                    <!-- BIB Display (Left / 7 cols) -->
                    <div class="md:col-span-7 space-y-4">
                        <div>
                            <span class="text-[11px] uppercase tracking-wider font-bold text-[#5f6a70] block">Nomor BIB Resmi</span>
                            <div class="text-4xl sm:text-5xl font-black tracking-tight text-teal-900 font-mono mt-1">
                                {{ $registration->bib_number }}
                            </div>
                        </div>

                        <!-- Participant Details Grid -->
                        <div class="grid grid-cols-2 gap-3 text-xs pt-2">
                            <div>
                                <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Nama Peserta</span>
                                <span class="font-bold text-teal-900 text-sm">{{ $registration->participant_name }}</span>
                            </div>
                            <div>
                                <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Kategori Jarak</span>
                                <span class="font-bold text-teal-900 text-sm">{{ $category->name }} ({{ P::distance($category->distance_km) }})</span>
                            </div>
                            <div>
                                <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Gelombang Start (Corral)</span>
                                <span class="font-bold text-teal-900">{{ $waveName }}</span>
                            </div>
                            <div>
                                <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Golongan Darah</span>
                                <span class="font-bold text-red-700 bg-red-50 px-1.5 py-0.5 velo-box-3 border border-red-200">Golongan {{ $registration->blood_type }}</span>
                            </div>
                            <div>
                                <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Klub Sepeda</span>
                                <span class="font-medium text-teal-700">{{ $registration->club ?? 'Umum / Independen' }}</span>
                            </div>
                            <div>
                                <span class="text-[#5f6a70] block text-[10px] uppercase font-semibold">Kontak Darurat</span>
                                <span class="font-medium text-teal-700">{{ $registration->emergency_contact_name }} ({{ $registration->emergency_contact_phone }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Box (Right / 5 cols) -->
                    <div class="md:col-span-5 flex flex-col items-center justify-center p-4 bg-velosurfacealt border border-veloborder velo-box-3 text-center">
                        <div class="p-2 bg-white border border-veloborderstrong velo-box-1 shadow-xs">
                            <!-- Dynamic QR Code generator for ticket verification -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ rawurlencode($qrData) }}"
                                 alt="QR Code Tiket Peserta"
                                 class="w-36 h-36">
                        </div>
                        <div class="text-[10px] font-mono text-[#556066] mt-2">
                            SCAN CODE: {{ $scanCode }}
                        </div>
                        <div class="text-[10px] text-[#5f6a70] mt-0.5">
                            Tunjukkan saat pengambilan Race Pack
                        </div>
                    </div>

                </div>

                <!-- Ticket Footer Notes -->
                <div class="p-4 bg-velosurfacealt flex flex-col sm:flex-row sm:items-center justify-between text-xs text-[#556066] gap-2">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="mail-check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Salinan PDF dikirim ke <strong>{{ $registration->user?->email ?? $event->user?->email }}</strong></span>
                    </div>
                    <div class="font-mono text-[11px] text-[#5f6a70]">
                        DIKELUARKAN: {{ $ticket?->created_at ? strtoupper($ticket->created_at->format('d M Y')) : '—' }} &middot; {{ $ticket?->created_at ? $ticket->created_at->format('H:i') : '—' }} WIB
                    </div>
                </div>

            </div>

            <!-- Action Buttons (Calendar, PDF, Share, Portal) -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 no-print">
                <!-- Google Calendar Direct URL API -->
                <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ rawurlencode('Velosha '.$event->title) }}&dates={{ $event->event_date->format('Ymd') }}T050000Z/{{ $event->event_date->format('Ymd') }}T130000Z&details={{ rawurlencode('Maraton Sepeda '.P::distance($category->distance_km).' Velosha. Nomor BIB: '.($registration->bib_number ?? '-')) }}&location={{ rawurlencode($event->location_name) }}"
                   target="_blank"
                   class="flex items-center justify-center px-4 py-3 bg-white hover:bg-velosurfacealt text-teal-800 border border-veloborderstrong velo-box-1 text-xs font-semibold shadow-xs transition">
                    <i data-lucide="calendar" class="w-4 h-4 mr-2 text-teal-900"></i>
                    <span>Simpan di Google Calendar</span>
                </a>

                <!-- Download PDF Button -->
                <button onclick="window.print()" class="flex items-center justify-center px-4 py-3 bg-white hover:bg-velosurfacealt text-teal-800 border border-veloborderstrong velo-box-1 text-xs font-semibold shadow-xs transition">
                    <i data-lucide="download" class="w-4 h-4 mr-2 text-teal-900"></i>
                    <span>Unduh E-Ticket (PDF)</span>
                </button>

                <!-- Dashboard / Panel Link -->
                @if (Auth::user()?->isAdmin() || Auth::user()?->isPenyelenggara())
                <a href="/admin/registrations" class="flex items-center justify-center px-4 py-3 bg-race-600 hover:bg-race-700 text-white velo-box-1 text-xs font-semibold shadow-xs transition">
                    <span>Kembali ke Panel Pengelola</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center px-4 py-3 bg-race-600 hover:bg-race-700 text-white velo-box-1 text-xs font-semibold shadow-xs transition">
                    <span>Buka Dashboard Peserta</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
                @endif
            </div>

        @endif

    </div>
</div>

<style>
@media print {
    header, footer, #veloChatWidget, .no-print {
        display: none !important;
    }
    body, .bg-velosurfacealt {
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endsection
