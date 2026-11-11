@extends('layouts.app', ['title' => $event->title . ' — Detail Race & Registrasi'])
@use('App\Support\EventCardPresenter', 'P')

@section('content')
@php
    $categories = $event->categories;
    $photos = $event->photos;
    $maxDistance = $categories->max('distance_km');
    $maxElevation = $categories->max('elevation_gain_m');
    $maxCutOff = $categories->max('cut_off_hours');
    $lowestPrice = $categories->min('price');
    $totalRemaining = (int) $categories->sum(fn ($category) => $category->remaining);
    $startTime = $event->start_time ? substr((string) $event->start_time, 0, 5) : '05:45';
    $cutoffEnd = ($event->start_time && $maxCutOff !== null)
        ? \Illuminate\Support\Carbon::parse((string) $event->start_time)->addHours((float) $maxCutOff)->format('H:i')
        : '—';
    $eventDate = \Illuminate\Support\Carbon::parse($event->event_date);
    $rpcStart = $eventDate->copy()->subDays(2);
    $rpcEnd = $eventDate->copy()->subDays(1);
    $monthShort = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $rpcLabel = $rpcStart->format('j') . '-' . $rpcEnd->format('j') . ' ' . $monthShort[(int) $rpcEnd->format('n')];
    $raceLabel = $eventDate->format('j') . ' ' . $monthShort[(int) $eventDate->format('n')];
@endphp
@if ($event->status !== \App\Models\Event::STATUS_PUBLISHED)
<div class="bg-[var(--mustard)] border-b-[2.5px] border-[var(--ink)] text-[var(--ink)] px-4 py-2.5 text-center text-xs font-black shadow-xs">
    Mode Pratinjau Panitia &mdash; Event ini berstatus <strong>{{ strtoupper(str_replace('_', ' ', $event->status)) }}</strong> dan belum dibuka untuk pendaftaran umum.
</div>
@endif
<!-- Page Header: Konsisten dengan Kalender, Tentang, Regulasi, & Bantuan -->
<section class="relative overflow-hidden" style="background: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink, #10282E); padding: 52px 0;">
    {{-- Elemen Dekorasi Geometris Neobrutalism di Sisi Kanan --}}
    <svg aria-hidden="true" style="position:absolute; top:0; right:0; width:520px; max-width:45%; height:100%; z-index:1; pointer-events:none;" viewBox="0 0 520 280">
        <g stroke="#10282E" stroke-width="3" stroke-linejoin="round">
            <circle cx="90" cy="65" r="9" fill="#DE7119"></circle>
            <rect x="160" y="145" width="20" height="20" rx="3" fill="#1F6F4A" transform="rotate(20 170 155)"></rect>
            <path d="M250 215 l12 -20 l12 20z" fill="#F4BE3F"></path>
            <circle cx="230" cy="55" r="7" fill="#9B3D5A"></circle>
            <rect x="320" y="115" width="18" height="18" rx="3" fill="#F4BE3F" transform="rotate(-15 329 124)"></rect>
            <path d="M420 65 l12 -20 l12 20z" fill="#DE7119"></path>
            <circle cx="475" cy="165" r="8" fill="#1F6F4A"></circle>
        </g>
    </svg>

    <div class="wrap relative z-10">
        <div class="flex items-center gap-2.5 mb-3.5 flex-wrap">
            <span class="chip font-bold bg-[var(--orange)] text-white border border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                {{ $event->type_label }}
            </span>
            <span class="chip font-bold bg-white text-[var(--ink)] border border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                {{ P::distance($maxDistance) }}
            </span>
            <span class="chip font-bold bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                {{ $eventDate->isFuture() ? ($totalRemaining > 0 ? 'Pendaftaran Dibuka' : 'Slot Penuh') : 'Event Selesai' }}
            </span>
        </div>

        <h1 class="font-heading font-black text-4xl sm:text-5xl md:text-6xl text-[var(--ink)] leading-[1.08] tracking-tight mb-4 max-w-3xl">
            {{ $event->title }}
        </h1>

        <div class="flex items-center gap-4 sm:gap-6 text-sm sm:text-base font-bold text-[var(--ink)]/90 flex-wrap mt-2">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[var(--orange)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>{{ P::tanggalLengkap($eventDate) }}</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[var(--pine)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>{{ $event->location_name }}</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[var(--ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span>Start {{ $startTime }} WIB</span>
            </span>
        </div>
    </div>
</section>

<!-- Photo Gallery Grid (1 large + up to 4 small grid) -->
@include('pages.event-detail.partials.gallery')

<!-- Main Detail Content & Sticky Checkout Sidebar -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

        <!-- Left: Course Overview, Maps & Itinerary (8 Cols) -->
        <div class="lg:col-span-8 min-w-0 space-y-12">

            <!-- Quick Spec Highlights Strip -->
            @include('pages.event-detail.partials.specs-strip')

            <!-- Description -->
            <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-2xl p-6 sm:p-8 space-y-4 shadow-[4px_4px_0_var(--ink)]">
                <h2 class="font-heading font-black text-2xl text-[var(--ink)] tracking-tight mb-2 border-b-2 border-[var(--ink)]/15 pb-3">Tentang Rute &amp; Pengalaman</h2>
                <div class="text-sm text-[var(--ink)]/85 leading-relaxed space-y-4 font-medium">
                    @if(strip_tags((string) $event->description) !== (string) $event->description)
                        <div class="prose max-w-none text-sm text-[var(--ink)]/85 leading-relaxed [&_img]:max-w-full [&_img]:h-auto [&_img]:my-4 [&_img]:rounded-xl [&_img]:border-2 [&_img]:border-[var(--ink)]">
                            {!! $event->description !!}
                        </div>
                    @else
                        {!! nl2br(e($event->description)) !!}
                    @endif
                </div>
            </div>

            @if ($event->youtube_video_id)
            <!-- Official Video -->
            <div class="space-y-4">
                <h2 class="font-heading font-black text-2xl text-[var(--ink)] tracking-tight">Video Resmi Event</h2>
                <div class="border-[2.5px] border-[var(--ink)] rounded-2xl shadow-[4px_4px_0_var(--ink)] overflow-hidden bg-black">
                    <div class="aspect-video w-full">
                        <iframe src="https://www.youtube.com/embed/{{ $event->youtube_video_id }}"
                                class="w-full h-full" allowfullscreen loading="lazy"
                                title="Video {{ $event->title }}"></iframe>
                    </div>
                </div>
            </div>
            @endif

            <!-- Interactive GPS Map & Course Profile -->
            @include('pages.event-detail.partials.aid-stations')

            <!-- Race Weekend Schedule / Timeline -->
            @include('pages.event-detail.partials.itinerary')

        </div>

        <!-- Right: Sticky Booking Box (4 Cols) -->
        @include('pages.event-detail.partials.booking-box')

    </div>
</section>

<!-- Sticky CTA (HP only) -->
<div class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-veloborder px-4 py-3 flex items-center justify-between gap-3" style="padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));">
    <div>
        <div class="text-[10px] uppercase font-bold tracking-wider text-velomuted">Harga mulai</div>
        <div class="text-lg font-extrabold text-teal-900">Rp {{ $lowestPrice === null ? '—' : \App\Support\EventCardPresenter::angka((float) $lowestPrice) }}</div>
    </div>
    <a href="#daftar" class="inline-flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold px-5 py-3 text-xs tracking-wider uppercase velo-box-1 shadow-sm transition min-h-[44px]">
        Daftar Sekarang
    </a>
</div>

@endsection
