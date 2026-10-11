@extends('layouts.app', ['title' => 'Velosha — Platform Maraton & Gran Fondo Indonesia'])

@section('content')
@php
    $featured = \App\Models\Event::published()->upcoming()->with('categories')->orderBy('event_date')->first();
@endphp

<!-- Hero Section (Foto peleton cerah + kabut lingkaran putih, veil gelap terarah) -->
<section class="velo-reveal relative bg-[#0A1F24] text-white overflow-hidden border-b border-teal-900">
    <div class="absolute inset-0 z-0">
        <img src="/images/hero-peloton.jpg"
             alt="Peloton Maraton Sepeda"
             class="w-full h-full object-cover object-[center_35%] filter brightness-100 contrast-110 saturate-[1.15]">

        <!-- Veil desktop: kiri gelap untuk kontras teks, kanan foto bersih -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#0A1F24]/92 via-[#0A1F24]/52 to-transparent sm:block hidden"></div>
        <!-- Veil mobile: bertingkat, foto tetap terlihat -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A1F24]/95 via-[#0A1F24]/75 to-[#0A1F24]/50 sm:hidden"></div>
        <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-[#0A1F24] via-[#0A1F24]/70 to-transparent"></div>

        <!-- Kabut lingkaran putih (3 layer, alpha 20% per layer sudah baked di PNG) -->
        <img src="/images/ellipse-9.png" alt="" aria-hidden="true"
             class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none">
        <img src="/images/ellipse-10.png" alt="" aria-hidden="true"
             class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none">
        <img src="/images/ellipse-11.png" alt="" aria-hidden="true"
             class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-32">
        <div class="max-w-2xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-[1.08] mb-4">
                Taklukkan Rute Endurance Nusantara.
            </h1>
            <p class="text-base sm:text-lg text-white/80 font-normal leading-relaxed mb-8 max-w-lg">
                Kalender resmi maraton sepeda, Gran Fondo, dan race endurance berstandar timing chip RFID.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="/events" wire:navigate class="inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold px-6 py-3.5 text-xs tracking-wider uppercase velo-box-1 shadow-sm transition">
                    <span>Eksplorasi Race</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
                <a href="{{ $featured ? route('events.show', $featured->slug) : route('events.index') }}" wire:navigate class="inline-flex items-center justify-center bg-white/10 hover:bg-white/20 text-white font-semibold px-6 py-3.5 text-xs tracking-wider uppercase border border-white/30 backdrop-blur-sm velo-box-1 transition">
                    <span>{{ $featured ? 'Sorotan: ' . \Illuminate\Support\Str::limit($featured->title, 30) : 'Lihat Jadwal Race' }}</span>
                </a>
            </div>
        </div>

        <!-- Metrics -->
        <div class="mt-16 pt-6 border-t border-white/15 grid grid-cols-4 gap-3 sm:gap-6 text-white max-w-3xl">
            <div>
                <div class="text-lg sm:text-2xl lg:text-3xl font-black tracking-tight">12</div>
                <div class="text-[10px] sm:text-[11px] leading-tight text-white/70 uppercase tracking-wider mt-0.5">Race Resmi</div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl lg:text-3xl font-black tracking-tight">8.500+</div>
                <div class="text-[10px] sm:text-[11px] leading-tight text-white/70 uppercase tracking-wider mt-0.5">Cyclist Terdaftar</div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl lg:text-3xl font-black tracking-tight">1.820</div>
                <div class="text-[10px] sm:text-[11px] leading-tight text-white/70 uppercase tracking-wider mt-0.5">KM Total Rute</div>
            </div>
            <div>
                <div class="text-lg sm:text-2xl lg:text-3xl font-black tracking-tight">100%</div>
                <div class="text-[10px] sm:text-[11px] leading-tight text-white/70 uppercase tracking-wider mt-0.5">RFID &amp; Tim Medis</div>
            </div>
        </div>
    </div>
</section>

<!-- Category Tabs + Featured Races: filter in-place via Livewire -->
<div class="velo-reveal" style="transition-delay: 80ms">
    @livewire('race-cards')
</div>

<!-- Isi Paket Tiket -->
@php
    $paket = \App\Models\TicketPackageContent::current();
    $paketImages = [
        'cpu' => 'paket/bib-rfid.png',
        'award' => 'paket/medali.png',
        'shield-plus' => 'paket/asuransi.png',
        'utensils' => 'paket/feedzone.png',
    ];
    $paketAccents = ['bg-teal-900', 'bg-race-500', 'bg-teal-700', 'bg-race-600'];
@endphp
<section class="velo-reveal py-16 bg-velosurfacealt border-b border-veloborder" style="transition-delay: 240ms">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div class="max-w-2xl">
                <div class="w-10 h-1 bg-race-500 velo-box-1 mb-3"></div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight">{{ $paket->title }}</h2>
                <p class="text-sm text-velomutedlight mt-2 leading-relaxed">Satu tiket, empat fasilitas resmi yang melengkapi Anda di setiap race.</p>
            </div>
        </div>

        {{-- Kartu fitur: grid 2 kolom mobile, 4 kolom desktop, aksen atas berwarna --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach (($paket->features ?? []) as $i => $f)
            <div class="relative bg-white border border-veloborder velo-box-1 overflow-hidden group hover:shadow-xl hover:border-teal-700 hover:-translate-y-1 transition-all duration-300">
                <div class="h-1.5 {{ $paketAccents[$i % count($paketAccents)] }} w-full" aria-hidden="true"></div>
                <div class="p-6">
                    {{-- Numbered badge --}}
                    <div class="flex items-center justify-between mb-4">
                        @if (isset($paketImages[$f['icon'] ?? '']))
                            <img src="{{ asset('images/' . $paketImages[$f['icon']]) }}"
                                 alt="{{ $f['title'] ?? '' }}" width="80" height="80" loading="lazy"
                                 class="w-16 h-16 object-contain">
                        @else
                            <div class="w-14 h-14 bg-velosurface text-teal-700 border border-veloborder velo-box-1 flex items-center justify-center">
                                <i data-lucide="{{ $f['icon'] ?? 'check' }}" class="w-6 h-6"></i>
                            </div>
                        @endif
                        <span class="text-4xl font-black text-velosurface select-none">0{{ $i + 1 }}</span>
                    </div>
                    <h4 class="text-sm font-bold text-teal-900 mb-2 group-hover:text-race-600 transition leading-snug">{!! $f['title'] ?? '' !!}</h4>
                    <p class="text-xs text-velomutedlight leading-relaxed">{!! $f['desc'] ?? '' !!}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Benefit: header gelap, grid 3 kolom, fasilitas resmi --}}
        <div class="mt-6 bg-teal-900 velo-box-1 overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-3.5 border-b border-white/10">
                <i data-lucide="sparkles" class="w-4 h-4 text-race-400"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">{{ $paket->benefits_title }}</h3>
            </div>
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 px-6 py-5">
                @foreach (($paket->benefits ?? []) as $b)
                <li class="flex items-start gap-2.5 text-xs text-teal-100/85 py-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5 text-race-400 shrink-0 mt-0.5"></i>
                    <span>{!! $b['text'] ?? '' !!}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
<!-- Panduan & Pusat Informasi Balap (About, Rule, History, Ranking, Help) -->
<section class="velo-reveal py-16 bg-velosurfacealt border-b border-veloborder" style="transition-delay: 280ms">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <div class="w-10 h-1 bg-race-500 velo-box-1 mb-3"></div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight">Informasi &amp; Regulasi Peserta</h2>
            <p class="text-sm text-velomutedlight mt-2 leading-relaxed">Pusat panduan resmi untuk mendukung pengalaman balap Anda: dari regulasi teknis hingga arsip pencapaian.</p>
        </div>

        {{-- Asymmetric Hub Layout: Left Dark Hero Box (5 cols) + Right 2x2 Grid (7 cols) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

            {{-- Kiri: Help Center Featured Dark Box --}}
            <div class="lg:col-span-5 bg-teal-900 text-white velo-box-1 p-8 flex flex-col justify-between relative overflow-hidden shadow-sm">
                <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                    <i data-lucide="life-buoy" class="w-56 h-56 text-white"></i>
                </div>
                <div class="relative z-10 space-y-4">
                    <div class="inline-flex items-center gap-2 bg-race-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 velo-box-3 tracking-wider">
                        <i data-lucide="headphones" class="w-3.5 h-3.5"></i>
                        Pusat Bantuan 24/7
                    </div>
                    <h3 class="text-2xl font-extrabold text-white tracking-tight">Help Center &amp; Layanan Peserta</h3>
                    <p class="text-xs text-teal-100/80 leading-relaxed">
                        Dapatkan jawaban cepat seputar tata cara pembayaran, prosedur penukaran Race Pack (RPC), pergantian nomor BIB, asuransi keselamatan, dan informasi darurat.
                    </p>
                </div>
                <div class="relative z-10 pt-8 mt-6 border-t border-white/10 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <a href="/help" wire:navigate class="flex-1 inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-extrabold text-xs px-5 py-3 velo-box-1 shadow-xs transition">
                        <span>Buka Pusat Bantuan</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
            </div>

            {{-- Kanan: 2x2 Horizontal Guide Cards --}}
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $infoCards = [
                        ['url' => '/about', 'cta' => 'Pelajari', 'icon' => 'info', 'title' => 'Tentang Velosha', 'desc' => 'Platform kurasi resmi maraton sepeda &amp; Gran Fondo Indonesia berstandar RFID.', 'bg' => 'bg-teal-50 text-teal-800'],
                        ['url' => '/rule', 'cta' => 'Aturan Race', 'icon' => 'shield-check', 'title' => 'Aturan & Kode Etik', 'desc' => 'Regulasi keselamatan helm, etika peloton, penalti, dan sanksi diskualifikasi.', 'bg' => 'bg-race-50 text-race-700'],
                        ['url' => '/riwayat', 'cta' => 'Lihat History', 'icon' => 'history', 'title' => 'Arsip Race History', 'desc' => 'Arsip balapan yang diselenggarakan lengkap dengan jumlah rider dan data kelas.', 'bg' => 'bg-teal-50 text-teal-800'],
                        ['url' => '/ranking', 'cta' => 'Leaderboard', 'icon' => 'trophy', 'title' => 'Ranking & Podium', 'desc' => 'Catatan waktu resmi per checkpoint RFID dan daftar podium juara per kategori.', 'bg' => 'bg-race-50 text-race-700'],
                    ];
                @endphp
                @foreach ($infoCards as $c)
                <a href="{{ $c['url'] }}" wire:navigate class="group bg-white border border-veloborder velo-box-1 p-5 flex flex-col justify-between hover:border-teal-700 hover:-translate-y-1 transition-all duration-200 shadow-xs">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 {{ $c['bg'] }} velo-box-3 border border-veloborder flex items-center justify-center">
                                <i data-lucide="{{ $c['icon'] }}" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[11px] font-extrabold text-teal-800 group-hover:text-race-600 transition flex items-center gap-1">
                                {{ $c['cta'] }}
                                <i data-lucide="arrow-right" class="w-3 h-3 group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </div>
                        <h3 class="text-sm font-extrabold text-teal-900 group-hover:text-race-600 transition">{{ $c['title'] }}</h3>
                        <p class="text-xs text-velomutedlight leading-relaxed">{!! $c['desc'] !!}</p>
                    </div>
                </a>
                @endforeach
            </div>

        </div>
    </div>
</section>

@endsection