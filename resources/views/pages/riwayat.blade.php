@extends('layouts.app', ['title' => 'Race History — Velosha'])
@use('App\Support\EventCardPresenter', 'P')

@section('content')
<x-page-header
    eyebrow="Arsip & Jejak Balap"
    title="Race History"
    description="Arsip resmi balapan yang telah diselenggarakan di kalender Velosha."
    meta="{{ $meta }}"
/>

<!-- Arsip Race Selesai -->
<section class="py-12 bg-white border-b border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="w-10 h-1 bg-race-500 velo-box-1 mb-3"></div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mb-2">Race Selesai</h2>
        <p class="text-xs text-velomutedlight mb-8">Balapan yang sudah digelar — arsip resmi musim Velosha.</p>

        @if ($archive->isEmpty())
        <div class="text-center py-12 border border-dashed border-veloborderstrong velo-box-1">
            <p class="text-sm text-velomutedlight mb-5">Belum ada balap yang selesai. Jadwal pertama menanti di kalender balap.</p>
            <a href="{{ route('events.index') }}" wire:navigate class="inline-flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold px-5 py-2.5 text-xs velo-box-1 shadow-sm transition">
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
                    <div class="flex items-center gap-2 text-[11px] text-velomuted mb-3">
                        <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                        <span>{{ P::tanggal($event->event_date) }}</span>
                    </div>
                    <h3 class="text-base font-extrabold text-teal-900 mb-1 group-hover:text-race-600 transition leading-snug">{{ $event->title }}</h3>
                    <div class="flex items-center gap-1.5 text-xs text-velomutedlight mb-5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 shrink-0"></i>
                        <span class="truncate">{{ $event->location_name }}</span>
                    </div>

                    <div class="mt-auto pt-4 border-t border-velosurface grid grid-cols-3 gap-2 text-center">
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-velomuted font-semibold">Jarak</div>
                            <div class="text-xs font-bold text-teal-900 mt-0.5">{{ P::distance($maxDistance) }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-velomuted font-semibold">Kelas</div>
                            <div class="text-xs font-bold text-teal-900 mt-0.5">{{ $event->categories->count() }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-velomuted font-semibold">Peserta</div>
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



@endsection
