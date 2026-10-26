@use('App\Support\EventCardPresenter', 'P')
@extends('layouts.app', ['title' => 'E-Ticket Resmi Peserta — Velosha'])

@section('content')
<div class="bg-velosurfacealt pb-12 min-h-[calc(100vh-140px)]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        @if ($registration === null || $registration->category === null || $registration->category->event === null)

            <!-- Belum ada pendaftaran -->
            <div class="text-center mb-8">
                <div class="w-12 h-12 bg-velosurface text-velomutedlight flex items-center justify-center velo-box-1 mx-auto mb-3 border border-veloborderstrong">
                    <i data-lucide="ticket" class="w-6 h-6"></i>
                </div>
                <span class="text-xs uppercase tracking-widest text-velomuted font-bold">Belum Ada E-Ticket</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mt-1">
                    Anda Belum Terdaftar pada Race Manapun.
                </h1>
                <p class="text-xs text-velomutedlight mt-2">
                    E-Ticket dan nomor BIB akan terbit otomatis setelah Anda menyelesaikan pendaftaran.
                </p>
            </div>

            <div class="bg-white border border-veloborder velo-box-1 p-8 text-center">
                <p class="text-xs text-velomutedlight mb-5">
                    Jelajahi kalender balap Velosha dan temukan kategori jarak yang sesuai dengan target Anda.
                </p>
                <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold px-6 py-3 text-xs velo-box-1 shadow-sm transition">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 mr-2"></i>
                    <span>Jelajahi Kalender Balap</span>
                </a>
            </div>

        @elseif (! $registration->isPaid())

            {{-- Header abu-abu + kontur topo horizontal --}}
            <x-page-header
                size="compact"
                width="max-w-3xl"
                eyebrow="Menunggu Pembayaran"
                title="Pendaftaran Anda Belum Lunas."
                description="Selesaikan pembayaran untuk menerbitkan E-Ticket dan nomor BIB resmi Anda."
                :meta="'REG-'.str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT)"
                :chips="[
                    ['icon' => 'flag', 'label' => $registration->category->event->title],
                    ['icon' => 'calendar', 'label' => P::tanggalLengkap($registration->category->event->event_date)],
                    ['icon' => 'map-pin', 'label' => $registration->category->event->location_name],
                ]"
            />

            <div class="bg-white border-2 border-race-400 velo-box-1 shadow-sm p-6 mt-6">
                <dl class="space-y-3 text-xs">
                    <div class="flex justify-between gap-4">
                        <dt class="text-velomutedlight">Nama Peserta</dt>
                        <dd class="font-semibold text-teal-900 text-right">{{ $registration->participant_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-velomutedlight">Kategori Jarak</dt>
                        <dd class="font-semibold text-teal-900 text-right">{{ $registration->category->name }} ({{ P::distance($registration->category->distance_km) }})</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-velomutedlight">Event</dt>
                        <dd class="font-semibold text-teal-900 text-right">{{ $registration->category->event->title }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-velomutedlight">Nomor Pendaftaran</dt>
                        <dd class="font-mono font-semibold text-teal-900 text-right">REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-velomutedlight">Status</dt>
                        <dd>
                            <span class="inline-block text-[10px] font-bold px-2 py-0.5 velo-box-3 bg-race-50 text-race-800 border border-race-300">PENDING</span>
                        </dd>
                    </div>
                </dl>

                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('checkout', ['event' => $registration->category->event->slug, 'category' => $registration->category->id]) }}" wire:navigate
                       class="flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] px-4 py-3 velo-box-1 text-xs font-semibold shadow-xs transition">
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

            <!-- Header abu-abu + kontur topo horizontal -->
            <x-page-header
                size="compact"
                width="max-w-3xl"
                eyebrow="{{ $registration->status === \App\Models\Registration::STATUS_CHECKED_IN ? 'Sudah Check-In di Race Pack Collection' : 'Pembayaran Terverifikasi Lunas' }}"
                title="Pendaftaran Berhasil! Sampai Jumpa di Pelataran Start."
                description="Simpan E-Ticket ini di ponsel Anda. Tunjukkan QR Code pada panitia Race Pack Collection untuk penukaran nomor dada dan RFID chip."
                :meta="'ORDER: #VLS-REG-'.str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT)"
                :chips="[
                    ['icon' => 'flag', 'label' => $event->title],
                    ['icon' => 'calendar', 'label' => P::tanggalLengkap($event->event_date)],
                    ['icon' => 'map-pin', 'label' => $event->location_name],
                    ['icon' => 'timer', 'label' => P::distance($category->distance_km).' · '.$category->name],
                ]"
            />

            <!-- Official E-Ticket Card -->
            <div class="bg-white border-2 border-teal-900 velo-box-1 shadow-md overflow-hidden relative">

                <!-- Top Header Ticket -->
                <div class="bg-[#0A353E] text-white p-6 sm:p-8 border-b border-teal-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center space-x-2.5 mb-2">
                            <img src="/images/logo-velosha.png" alt="Velosha" class="h-7 w-auto object-contain brightness-125" />
                            <span class="text-[10px] font-black tracking-widest uppercase bg-[var(--orange)] text-[var(--ink)] px-2 py-0.5 velo-box-3">OFFICIAL RACE PASS</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                            {{ $event->title }}
                        </h2>
                        <div class="text-xs text-teal-100/90 mt-1.5 flex items-center space-x-3 flex-wrap font-medium">
                            <span><i data-lucide="calendar" class="w-3.5 h-3.5 inline text-race-400"></i> {{ P::tanggalLengkap($event->event_date) }}</span>
                            @if ($waveTime !== '')
                            <span>&bull;</span>
                            <span>Start: {{ $waveTime }} WIB</span>
                            @endif
                            <span>&bull;</span>
                            <span><i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-race-400"></i> {{ $event->location_name }}</span>
                        </div>
                    </div>

                    <div class="text-left sm:text-right shrink-0">
                        <div class="text-[10px] font-mono text-teal-300 font-bold">REGISTRATION ID</div>
                        <div class="text-xs font-mono font-black text-white bg-teal-950 px-3 py-1.5 velo-box-3 border border-teal-800 mt-1">
                            #VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                </div>

                <!-- Ticket Core: BIB & QR Code Centerpiece -->
                <div class="p-6 sm:p-8 bg-white grid grid-cols-1 md:grid-cols-12 gap-6 items-center border-b border-dashed border-veloborder">

                    <!-- BIB Display (Left / 7 cols) -->
                    <div class="md:col-span-7 space-y-4">
                        <div>
                            <span class="text-[10px] uppercase tracking-wider font-extrabold text-velomuted block">Nomor BIB RFID Resmi</span>
                            <div class="text-4xl sm:text-5xl font-black tracking-tight text-teal-900 font-mono mt-1">
                                {{ $registration->bib_number }}
                            </div>
                        </div>

                        <!-- Participant Details Grid -->
                        <div class="grid grid-cols-2 gap-4 text-xs pt-2 border-t border-veloborder">
                            <div>
                                <span class="text-velomutedlight block text-[10px] uppercase font-bold">Nama Peserta</span>
                                <span class="font-extrabold text-teal-900 text-sm">{{ $registration->participant_name }}</span>
                            </div>
                            <div>
                                <span class="text-velomutedlight block text-[10px] uppercase font-bold">Kategori Jarak</span>
                                <span class="font-extrabold text-teal-900 text-sm">{{ $category->name }} ({{ P::distance($category->distance_km) }})</span>
                            </div>
                            <div>
                                <span class="text-velomutedlight block text-[10px] uppercase font-bold">Gelombang Start</span>
                                <span class="font-extrabold text-teal-900">{{ $waveName }}</span>
                            </div>
                            <div>
                                <span class="text-velomutedlight block text-[10px] uppercase font-bold">Golongan Darah</span>
                                <span class="font-bold text-red-700 bg-red-50 px-2 py-0.5 velo-box-3 border border-red-200">Gol. {{ $registration->blood_type }}</span>
                            </div>
                            <div>
                                <span class="text-velomutedlight block text-[10px] uppercase font-bold">Klub Sepeda</span>
                                <span class="font-bold text-teal-800">{{ $registration->club ?? 'Umum / Independen' }}</span>
                            </div>
                            <div>
                                <span class="text-velomutedlight block text-[10px] uppercase font-bold">Kontak Darurat</span>
                                <span class="font-bold text-teal-800">{{ $registration->emergency_contact_name }} ({{ $registration->emergency_contact_phone }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Box (Right / 5 cols) -->
                    <div class="md:col-span-5 flex flex-col items-center justify-center p-5 bg-[#f1f4f4] border border-veloborder velo-box-1 text-center shadow-xs">
                        <div class="p-3 bg-white border border-veloborderstrong velo-box-1 shadow-xs">
                            <img src="{{ \App\Support\QrCodeService::uri($qrData) }}"
                                 alt="QR Code Tiket Peserta"
                                 class="w-36 h-36">
                        </div>
                        <div class="text-[10px] font-mono text-teal-900 font-bold mt-3 bg-white px-2.5 py-1 velo-box-3 border border-veloborder">
                            SCAN CODE: {{ $scanCode }}
                        </div>
                        <div class="text-[10px] text-velomutedlight font-medium mt-1">
                            Tunjukkan saat penukaran Race Pack (RPC)
                        </div>
                    </div>

                </div>

                <!-- Ticket Footer Notes -->
                <div class="p-4 bg-[#f1f4f4] flex flex-col sm:flex-row sm:items-center justify-between text-xs text-velomutedlight gap-2">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                        <span>Status: <strong>{{ $registration->status === \App\Models\Registration::STATUS_CHECKED_IN ? 'Sudah Check-In' : 'Terverifikasi Lunas' }}</strong></span>
                    </div>
                    <div class="font-mono text-[11px] text-velomuted">
                        TERBIT: {{ $ticket?->created_at ? strtoupper($ticket->created_at->format('d M Y')) : '—' }} &middot; {{ $ticket?->created_at ? $ticket->created_at->format('H:i') : '—' }} WIB
                    </div>
                </div>

            </div>

            <!-- Action Buttons (Calendar, PDF, Share, Portal) -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 no-print">
                <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ rawurlencode('Velosha '.$event->title) }}&dates={{ $event->event_date->format('Ymd') }}T050000Z/{{ $event->event_date->format('Ymd') }}T130000Z&details={{ rawurlencode('Maraton Sepeda '.P::distance($category->distance_km).' Velosha. Nomor BIB: '.($registration->bib_number ?? '-')) }}&location={{ rawurlencode($event->location_name) }}"
                   target="_blank"
                   class="flex items-center justify-center px-4 py-3 bg-white hover:bg-[#f1f4f4] text-teal-900 border border-veloborderstrong velo-box-1 text-xs font-extrabold shadow-xs transition">
                    <i data-lucide="calendar" class="w-4 h-4 mr-2 text-teal-900"></i>
                    <span>Simpan di Google Calendar</span>
                </a>

                <button onclick="window.print()" class="flex items-center justify-center px-4 py-3 bg-white hover:bg-[#f1f4f4] text-teal-900 border border-veloborderstrong velo-box-1 text-xs font-extrabold shadow-xs transition">
                    <i data-lucide="printer" class="w-4 h-4 mr-2 text-teal-900"></i>
                    <span>Cetak E-Ticket (PDF)</span>
                </button>

                @if (Auth::user()?->isAdmin() || Auth::user()?->isPenyelenggara())
                <a href="/admin/registrations" class="flex items-center justify-center px-4 py-3 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] velo-box-1 text-xs font-extrabold shadow-xs transition">
                    <span>Kembali ke Panel Pengelola</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
                @else
                @php
                    $u = Auth::user();
                    $homeUrl = $u ? $u->home() : '/dashboard';
                    $homeLabel = $u?->isOperator() ? 'Antrian Helpdesk' : 'Buka Dashboard Peserta';
                @endphp
                <a href="{{ $homeUrl }}" class="flex items-center justify-center px-4 py-3 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] velo-box-1 text-xs font-extrabold shadow-xs transition">
                    <span>{{ $homeLabel }}</span>
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
