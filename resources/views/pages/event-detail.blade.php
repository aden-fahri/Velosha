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
<!-- Banner Screenprint Neobrutalist dengan Pesepeda Menikung (EventDetail.dc.html) -->
<section style="position: relative; min-height: 380px; border-bottom: 3px solid #10282E; overflow: hidden; background: #F6B777; display: flex; align-items: flex-end; padding-bottom: 36px">
    <svg viewBox="0 0 1440 380" preserveAspectRatio="xMidYMid slice" width="100%" height="100%" aria-hidden="true" style="position: absolute; inset: 0">
        <rect width="1440" height="380" fill="#F6B777"></rect>
        <circle cx="1130" cy="120" r="64" fill="#FBF4E6" stroke="#10282E" stroke-width="4"></circle>
        <path d="M720 270 L820 130 L920 270Z M880 270 L1010 90 L1140 270Z M1100 270 L1200 150 L1300 270Z" fill="#B2590F" stroke="#10282E" stroke-width="4" stroke-linejoin="round"></path>
        <path d="M0 250 Q300 190 600 240 T1200 230 T1440 220 V380 H0Z" fill="#7DB88A" stroke="#10282E" stroke-width="4"></path>
        <path d="M0 310 Q360 270 720 300 T1440 290 V380 H0Z" fill="#1F6F4A" stroke="#10282E" stroke-width="4"></path>
    </svg>
    <img class="band-art" src="/images/mockup/miring.png" alt="Pesepeda berjersey oranye melaju miring menikung" style="height: 330px; right: 90px; bottom: 0">
    <div class="wrap" style="position: relative; z-index: 3; width: 100%">
        <div style="display: flex; gap: 10px; flex-wrap: wrap">
            <span class="chip o">{{ $event->type_label }}</span>
            <span class="chip">{{ P::distance($maxDistance) }}</span>
            <span class="chip m">{{ $eventDate->isFuture() ? ($totalRemaining > 0 ? 'Pendaftaran dibuka' : 'Slot penuh') : 'Event selesai' }}</span>
        </div>
        <h1 class="h2" style="font-size: 56px; margin-top: 16px; max-width: 760px; line-height: 1.05">
            {{ $event->title }}
        </h1>
        <div style="display: flex; gap: 24px; margin-top: 14px; font-weight: 700; font-size: 15px; flex-wrap: wrap; color: #10282E">
            <span>{{ P::tanggalLengkap($eventDate) }}</span>
            <span>{{ $event->location_name }}</span>
            <span>Start {{ $startTime }} WIB</span>
        </div>
    </div>
</section>

<!-- Photo Gallery Grid (1 large + up to 4 small grid) -->
@include('pages.event-detail.partials.gallery')

<!-- Main Detail Content & Sticky Checkout Sidebar -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

        <!-- Left: Course Overview, Maps & Itinerary (8 Cols) -->
        <div class="lg:col-span-8 space-y-12">

            <!-- Quick Spec Highlights Strip -->
            @include('pages.event-detail.partials.specs-strip')

            <!-- Description -->
            <div class="bg-white border border-veloborder velo-box-1 p-6 sm:p-8 space-y-4 shadow-xs">
                <h2 class="text-xl font-extrabold text-teal-900 tracking-tight mb-2 border-b border-veloborder pb-3">Tentang Rute &amp; Pengalaman</h2>
                <div class="text-sm text-velomutedlight leading-relaxed space-y-4">
                    @if(strip_tags((string) $event->description) !== (string) $event->description)
                        <div class="prose max-w-none text-sm text-velomutedlight leading-relaxed [&_img]:max-w-full [&_img]:h-auto [&_img]:my-4 [&_img]:velo-box-3">
                            {!! $event->description !!}
                        </div>
                    @else
                        {!! nl2br(e($event->description)) !!}
                    @endif
                </div>
            </div>

            @if ($event->youtube_video_id)
            <!-- Official Video -->
            <div>
                <h2 class="text-xl font-extrabold text-teal-900 tracking-tight mb-4">Video Resmi Event</h2>
                <div class="border border-veloborderstrong velo-box-1 overflow-hidden">
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
