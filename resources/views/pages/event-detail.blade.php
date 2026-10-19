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
{{-- Header abu-abu + kontur topo horizontal (seragam dengan halaman lain) --}}
<x-page-header
    size="compact"
    eyebrow="Detail Balap Resmi"
    title="{{ $event->title }}"
    :meta="$eventDate->isFuture() ? ($totalRemaining > 0 ? 'Registrasi Dibuka · '.$event->type_label : 'Slot Penuh · '.$event->type_label) : 'Event Selesai · '.$event->type_label"
    :chips="array_values(array_filter([
        ['icon' => 'calendar', 'label' => P::tanggalLengkap($eventDate)],
        ['icon' => 'clock', 'label' => 'Start '.$startTime.' WIB'],
        $event->location_name ? ['icon' => 'map-pin', 'label' => $event->location_name] : null,
        ['icon' => 'route', 'label' => P::distance($maxDistance)],
    ]))"
/>

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
    <a href="#daftar" class="inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold px-5 py-3 text-xs tracking-wider uppercase velo-box-1 shadow-sm transition min-h-[44px]">
        Daftar Sekarang
    </a>
</div>

@endsection
