@extends('layouts.app', ['title' => 'Riwayat Balap — Velosha'])
@use('App\Support\EventCardPresenter', 'P')

@section('content')
<x-page-header
    eyebrow="Arsip & Jejak Balap"
    title="Riwayat Balap"
    description="Race yang sudah digelar di kalender Velosha dan jejak pendaftaranmu: status bayar, nomor BIB, dan e-ticket dalam satu tempat."
    meta="{{ $meta }}"
/>

<!-- Arsip Race Selesai -->
<section class="py-12 bg-white border-b border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="w-10 h-1 bg-race-500 velo-box-1 mb-3"></div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mb-2">Race Selesai</h2>
        <p class="text-xs text-[#556066] mb-8">Balapan yang sudah digelar — arsip resmi musim Velosha.</p>

        @if ($archive->isEmpty())
        <div class="text-center py-12 border border-dashed border-veloborderstrong velo-box-1">
            <p class="text-sm text-[#556066] mb-5">Belum ada balap yang selesai. Jadwal pertama menanti di kalender balap.</p>
            <a href="{{ route('events.index') }}" wire:navigate class="inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold px-5 py-2.5 text-xs velo-box-1 shadow-sm transition">
                Lihat Kalender Balap
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($archive as $event)
            @php
                $maxDistance = $event->categories->max('distance_km');
                $participantCount = (int) $event->categories->sum(fn ($c) => $c->registrations->count());
            @endphp
            <a href="{{ route('events.show', $event->slug) }}" wire:navigate class="group border border-veloborder velo-box-1 bg-white flex flex-col hover:border-teal-700 transition">
                <div class="h-1.5 bg-teal-800"></div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center gap-2 text-[11px] text-[#5f6a70] mb-3">
                        <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                        <span>{{ P::tanggal($event->event_date) }}</span>
                    </div>
                    <h3 class="text-base font-extrabold text-teal-900 mb-1 group-hover:text-race-600 transition leading-snug">{{ $event->title }}</h3>
                    <div class="flex items-center gap-1.5 text-xs text-[#556066] mb-5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 shrink-0"></i>
                        <span class="truncate">{{ $event->location_name }}</span>
                    </div>

                    <div class="mt-auto pt-4 border-t border-velosurface grid grid-cols-3 gap-2 text-center">
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-[#5f6a70] font-semibold">Jarak</div>
                            <div class="text-xs font-bold text-teal-900 mt-0.5">{{ P::distance($maxDistance) }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-[#5f6a70] font-semibold">Kelas</div>
                            <div class="text-xs font-bold text-teal-900 mt-0.5">{{ $event->categories->count() }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-[#5f6a70] font-semibold">Peserta</div>
                            <div class="text-xs font-bold text-teal-900 mt-0.5">{{ $participantCount }}</div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- Riwayat Pendaftaran Saya -->
<section class="py-12 bg-velosurfacealt border-b border-veloborder">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-3 mb-8">
            <div>
                <div class="w-10 h-1 bg-race-500 velo-box-1 mb-3"></div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight">Riwayat Pendaftaran Saya</h2>
            </div>
            <span class="text-xs text-[#556066] font-semibold">{{ auth()->check() ? 'Akun: '.auth()->user()->email : 'Tamu' }}</span>
        </div>

        @guest
        <div class="bg-white border border-veloborder velo-box-1 shadow-sm p-8 text-center">
            <div class="w-12 h-12 mx-auto mb-4 border border-veloborder velo-box-1 bg-velosurface flex items-center justify-center">
                <i data-lucide="user-round" class="w-6 h-6 text-teal-700"></i>
            </div>
            <h3 class="text-base font-extrabold text-teal-900 mb-2">Masuk untuk melihat riwayat Anda</h3>
            <p class="text-xs text-[#556066] mb-6 max-w-sm mx-auto">Status pendaftaran, nomor BIB, dan e-ticket tersimpan di akun pesertamu.</p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold px-5 py-2.5 text-xs velo-box-1 shadow-sm transition">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-white hover:bg-velosurface text-teal-900 font-semibold px-5 py-2.5 text-xs border border-veloborderstrong velo-box-1 transition">
                    Daftar Akun
                </a>
            </div>
        </div>
        @else
            @if ($myRegs->isEmpty())
            <div class="bg-white border border-veloborder velo-box-1 shadow-sm p-8 text-center">
                <p class="text-sm text-[#556066] mb-5">Belum ada pendaftaran. Mulai dari kalender balap untuk menerbitkan E-Ticket pertama Anda.</p>
                <a href="{{ route('events.index') }}" wire:navigate class="inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold px-5 py-2.5 text-xs velo-box-1 shadow-sm transition">
                    Jelajahi Kalender Balap
                </a>
            </div>
            @else
            @php
                $totalEvents = $myRegs->pluck('category.event_id')->filter()->unique()->count();
                $totalKm = $myRegs->groupBy('category.event_id')
                    ->map(fn ($regs) => (float) $regs->max('category.distance_km'))
                    ->sum();
                $totalBib = $myRegs->filter(fn ($r) => $r->bib_number !== null)->count();
            @endphp

            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white border border-veloborder velo-box-1 p-5 text-center">
                    <div class="text-2xl font-black text-teal-900">{{ $totalEvents }}</div>
                    <div class="text-[10px] uppercase tracking-wider text-[#5f6a70] font-bold mt-1">Event Diikuti</div>
                </div>
                <div class="bg-white border border-veloborder velo-box-1 p-5 text-center">
                    <div class="text-2xl font-black text-teal-900">{{ number_format($totalKm, 0, ',', '.') }}<span class="text-sm font-bold ml-0.5">KM</span></div>
                    <div class="text-[10px] uppercase tracking-wider text-[#5f6a70] font-bold mt-1">Total Jarak</div>
                </div>
                <div class="bg-white border border-veloborder velo-box-1 p-5 text-center">
                    <div class="text-2xl font-black text-teal-900">{{ $totalBib }}</div>
                    <div class="text-[10px] uppercase tracking-wider text-[#5f6a70] font-bold mt-1">BIB Terkumpul</div>
                </div>
            </div>

            <div class="space-y-3">
                @foreach ($myRegs as $registration)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border border-veloborderstrong velo-box-1 bg-white hover:bg-velosurfacealt transition">
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-teal-900 truncate">{{ $registration->category?->event?->title ?? 'Event Velosha' }}</div>
                        <div class="text-xs text-[#556066] mt-0.5 truncate">
                            {{ P::tanggal($registration->category?->event?->event_date) }}
                            &middot; {{ $registration->category?->name ?? '—' }}
                            &middot; BIB: <span class="font-mono">{{ $registration->bib_number ?? 'Belum terbit' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @if ($registration->isPaid())
                        <span class="text-[10px] font-bold px-2 py-1 velo-box-3 bg-emerald-50 text-emerald-800 border border-emerald-200">
                            @if ($registration->status === 'checked_in')
                            SUDAH CHECK-IN
                            @else
                            LUNAS
                            @endif
                        </span>
                        <a href="{{ route('ticket.show', $registration) }}" class="inline-flex items-center bg-race-600 hover:bg-race-700 text-white font-semibold px-3.5 py-2 text-xs velo-box-1 shadow-xs transition">
                            Lihat E-Ticket
                        </a>
                        @elseif ($registration->status === 'pending')
                        <span class="text-[10px] font-bold px-2 py-1 velo-box-3 bg-amber-50 text-amber-800 border border-amber-300">MENUNGGU BAYAR</span>
                        <a href="{{ route('checkout', ['event' => $registration->category?->event?->slug, 'category' => $registration->event_category_id]) }}" wire:navigate class="inline-flex items-center bg-race-600 hover:bg-race-700 text-white font-semibold px-3.5 py-2 text-xs velo-box-1 shadow-xs transition">
                            Bayar Sekarang
                        </a>
                        @else
                        <span class="text-[10px] font-bold px-2 py-1 velo-box-3 bg-red-50 text-red-700 border border-red-200">DIBATALKAN</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        @endguest
    </div>
</section>

@endsection
