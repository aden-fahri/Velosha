@extends('layouts.app', ['title' => 'Velosha — Platform Maraton & Gran Fondo Indonesia'])

@section('content')

<!-- INTRO LOADING OVERLAY (Full-screen, tanpa navigasi, sekali per sesi) -->
<x-intro-loading />

<!-- HERO SECTION (Screen-Print Neobrutalist Stage dengan Animasi 1:1 Mockup) -->
<section id="hero-section"
         style="position: relative; min-height: 760px; background: var(--paper); overflow: hidden; border-bottom: 3px solid var(--ink)"
         x-data="{
            off: false,
            replay() {
                this.off = true;
                setTimeout(() => this.off = false, 90);
            }
         }">

    <!-- Tombol Putar Ulang Animasi (Desktop Only) -->
    <div class="hidden lg:block absolute top-4 right-6 z-20">
        <button type="button"
                id="replay-hero"
                class="btn btn-ghost btn-sm"
                @click="replay()"
                aria-label="Putar ulang animasi hero">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M4 12a8 8 0 1 0 3-6.2"></path><path d="M4 4v5h5"></path></svg>
            <span>Putar ulang</span>
        </button>
    </div>

    <div class="stage" :class="off ? 'off' : 'on'">

        <!-- 1. Judul Hero (Order 1 di Mobile, Kiri Atas di Desktop) -->
        <div class="hero-title-block">
            <h1 class="a-rise" style="animation-delay: .2s; font-size: 72px; line-height: .98; font-weight: 800; letter-spacing: -0.04em; color: var(--ink); margin: 0">
                Siap gowes<br>
                sampai <span class="squig">finis?<svg viewBox="0 0 100 14" preserveAspectRatio="none" aria-hidden="true"><path d="M2 8 Q14 1 26 8 T50 8 T74 8 T98 8" fill="none" stroke="#DE7119" stroke-width="5" stroke-linecap="round" vector-effect="non-scaling-stroke"></path></svg></span>
            </h1>
        </div>

        <!-- 2. Pesepeda & Background Panel Berlekuk Oranye/Krem (Order 2 di Mobile, Kanan di Desktop) -->
        <div class="hero-rider-col" id="hero-rider-container">
            <!-- Panel Berlekuk SVG (Blok Oranye di Belakang) -->
            <svg class="a-amber" width="580" height="445" viewBox="-2 -2 580 445" aria-hidden="true">
                <path d="M226,0 H540 Q576,0 576,36 V405 Q576,441 540,441 H36 Q0,441 0,405 V136 Q0,100 36,100 H154 Q190,100 190,64 V36 Q190,0 226,0 Z"
                      fill="#DE7119" stroke="#10282E" stroke-width="3" stroke-linejoin="round"></path>
            </svg>

            <!-- Panel Berlekuk SVG (Krem Bertitik Halus di Depan) -->
            <svg class="a-panel" id="hero-panel" width="580" height="445" viewBox="-2 -2 580 445" aria-hidden="true">
                <defs>
                    <clipPath id="panelShape">
                        <path d="M226,0 H540 Q576,0 576,36 V405 Q576,441 540,441 H36 Q0,441 0,405 V136 Q0,100 36,100 H154 Q190,100 190,64 V36 Q190,0 226,0 Z"></path>
                    </clipPath>
                    <pattern id="dots" width="12" height="12" patternUnits="userSpaceOnUse">
                        <circle cx="6" cy="6" r="1.9" fill="#E3BE78"></circle>
                    </pattern>
                    <linearGradient id="fade" x1="0" y1="1" x2="0" y2="0">
                        <stop offset="0.05" stop-color="#fff"></stop>
                        <stop offset="0.75" stop-color="#000"></stop>
                    </linearGradient>
                    <mask id="fadeMask">
                        <rect width="576" height="441" fill="url(#fade)"></rect>
                    </mask>
                </defs>
                <g clip-path="url(#panelShape)">
                    <rect width="576" height="441" fill="#F8E3B5"></rect>
                    <rect width="576" height="441" fill="url(#dots)" mask="url(#fadeMask)"></rect>
                </g>
                <path d="M226,0 H540 Q576,0 576,36 V405 Q576,441 540,441 H36 Q0,441 0,405 V136 Q0,100 36,100 H154 Q190,100 190,64 V36 Q190,0 226,0 Z"
                      fill="none" stroke="#10282E" stroke-width="3" stroke-linejoin="round"></path>
            </svg>

            <!-- Garis Kecepatan Kartun (Speed Lines) -->
            <div class="a-trail trail-1"></div>
            <div class="a-trail trail-2"></div>
            <div class="a-trail trail-3"></div>

            <!-- Pesepeda Berjersey Oranye (rider_final.png - Autentik) -->
            <div class="a-ride hero-rider-graphic" id="hero-rider">
                <div class="a-bob" style="width: 100%; height: 100%">
                    <img src="/images/hero/rider_final.png"
                         alt="Pesepeda berjersey oranye melaju"
                         fetchpriority="high"
                         style="display: block; width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(4px 4px 0 rgba(16, 40, 46, 0.15))">
                </div>
            </div>
        </div>

        <!-- 3. Deskripsi & Tombol CTA (Order 3 di Mobile, Kiri Tengah di Desktop) -->
        <div class="hero-cta-block">
            <p class="a-rise" style="animation-delay: .35s; max-width: 400px; font-size: 17px; line-height: 1.5; color: #10282E; font-weight: 500">
                Daftar Gran Fondo, Medio Fondo, dan Fun Ride di seluruh Indonesia. Tiket dan nomor BIB langsung jadi.
            </p>
            <div class="a-rise" style="animation-delay: .5s; margin-top: 20px">
                <a href="{{ route('events.index') }}" wire:navigate.hover class="btn">
                    Cari event
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                </a>
            </div>
        </div>

        <!-- 4. Perlengkapan Balap Resmi (Interactive Cycling Gear Carousel - Order 4 di Mobile, Kiri Bawah di Desktop) -->
        <div class="a-rise hero-ticket-col"
             x-data="{
                activeIdx: 0,
                gears: [
                    {
                        title: 'Helm Aero Endurance',
                        tag: 'SAFETY SPEC',
                        badge: 'STANDAR UCI',
                        category: 'GRAN FONDO GEAR',
                        spec: 'Bobot 220g • EPS High-Density',
                        desc: 'Standar keselamatan Gran Fondo dengan ventilasi optimal dan aerodinamika tinggi.',
                        img: '{{ asset('images/hero/Cycling_helmet_vector_illustration_d67f9022.jpg') }}',
                        alt: 'Helm Sepeda Balap Velosha'
                    },
                    {
                        title: 'Kacamata Gowes UV400',
                        tag: 'OPTICS GEAR',
                        badge: 'ANTI-GLARE',
                        category: 'PELINDUNG MATA',
                        spec: 'Lensa Hidrofobik • Frame TR90',
                        desc: 'Proteksi sinar UV penuh dan lensa hidrofobik anti-embun di segala kondisi rute.',
                        img: '{{ asset('images/hero/Illustration_of_cycling_sunglasses_c5a9a6dc.jpg') }}',
                        alt: 'Kacamata Sepeda Velosha'
                    },
                    {
                        title: 'Bidon Hidrasi Velosha',
                        tag: 'OFFICIAL MERCH',
                        badge: 'BPA FREE',
                        category: 'HIDRASI RESMI',
                        spec: 'Kapasitas 650ml • Fast-Flow Valve',
                        desc: 'Botol hidrasi resmi race dengan katup cepat anti-bocor untuk rehidrasi presisi.',
                        img: '{{ asset('images/hero/Cycling_water_bottle_illustration_52196d1e.jpg') }}',
                        alt: 'Bidon Minum Velosha'
                    }
                ],
                next() { this.activeIdx = (this.activeIdx + 1) % this.gears.length },
                prev() { this.activeIdx = (this.activeIdx - 1 + this.gears.length) % this.gears.length },
                get current() { return this.gears[this.activeIdx]; }
             }"
             style="animation-delay: .7s">

            <div class="bg-[#FBF4E6] rounded-2xl border-[3px] border-[#10282E] shadow-[5px_5px_0_#10282E] p-3 sm:p-4 select-none">
                <!-- Inner Gear Card -->
                <div class="relative bg-[#FFFDF8] rounded-xl border-[2.5px] border-[#10282E] p-2.5 sm:p-3 flex items-center gap-3 overflow-hidden">
                    
                    <!-- Left: Gear Illustration Thumbnail with Border & Warm Amber Background -->
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-lg border-2 border-[#10282E] bg-[#F8E3B5] overflow-hidden shrink-0 shadow-[2px_2px_0_#10282E] flex items-center justify-center p-1">
                        <img :src="current.img" 
                             :alt="current.alt" 
                             class="w-full h-full object-cover rounded transition-transform duration-300 hover:scale-105">
                        <span class="absolute bottom-1 right-1 px-1 py-0.2 bg-[#10282E] text-[#FFFDF8] text-[8px] font-mono font-bold rounded"
                              x-text="(activeIdx + 1) + '/3'">
                            1/3
                        </span>
                    </div>

                    <!-- Right: Gear Details -->
                    <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                        <!-- Top Tag & UCI Badge -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="inline-block px-1.5 py-0.5 rounded bg-[#DE7119] text-[#FFFDF8] text-[9px] sm:text-[10px] font-black uppercase tracking-wider border border-[#10282E] shadow-[1px_1px_0_#10282E]"
                                  x-text="current.tag">
                                SAFETY SPEC
                            </span>
                            <span class="inline-block px-1.5 py-0.5 rounded bg-[#1F6F4A] text-[#FFFDF8] text-[9px] font-black uppercase tracking-wider border border-[#10282E]"
                                  x-text="current.badge">
                                STANDAR UCI
                            </span>
                        </div>

                        <!-- Title -->
                        <div class="font-heading font-black text-xs sm:text-sm uppercase leading-tight text-[#10282E] truncate mt-1"
                             x-text="current.title">
                            Helm Aero Endurance
                        </div>

                        <!-- Description -->
                        <p class="text-[10px] sm:text-[11px] font-medium text-[#10282E]/85 line-clamp-2 leading-snug mt-0.5"
                           x-text="current.desc">
                            Standar keselamatan Gran Fondo dengan ventilasi optimal dan aerodinamika tinggi.
                        </p>

                        <!-- Spec Pill / Feature -->
                        <div class="mt-1 flex items-center gap-1 text-[9px] sm:text-[10px] font-bold text-[#10282E]/70 truncate">
                            <svg class="w-3 h-3 text-[#DE7119] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span x-text="current.spec" class="truncate">Bobot 220g • EPS High-Density</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar: Dots indicator & Neobrutalist arrow navigation -->
                <div class="flex items-center justify-between mt-2.5 pt-1 px-1">
                    <!-- Dots Indikator (○ ▬ ○) -->
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <template x-for="(g, idx) in gears" :key="idx">
                            <button type="button"
                                    @click="activeIdx = idx"
                                    class="transition-all duration-200 cursor-pointer p-0.5 focus:outline-none"
                                    :aria-label="'Pilih perlengkapan ' + (idx + 1)">
                                <span class="block border-2 border-[#10282E] transition-all duration-200"
                                      :class="activeIdx === idx 
                                          ? 'w-6 sm:w-7 h-2.5 sm:h-3 rounded-full bg-[#DE7119]' 
                                          : 'w-2.5 sm:w-3 h-2.5 sm:h-3 rounded-full bg-white hover:bg-[#F8E3B5]'">
                                </span>
                            </button>
                        </template>
                    </div>

                    <!-- Tombol Panah < dan > Neobrutalist -->
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="prev()"
                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg border-2 border-[#10282E] bg-white text-[#10282E] shadow-[2px_2px_0_#10282E] flex items-center justify-center font-black text-sm hover:-translate-x-0.5 active:translate-y-0.5 transition-transform cursor-pointer"
                                aria-label="Perlengkapan sebelumnya">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <button type="button"
                                @click="next()"
                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg border-2 border-[#10282E] bg-[#DE7119] text-white shadow-[2px_2px_0_#10282E] flex items-center justify-center font-black text-sm hover:translate-x-0.5 active:translate-y-0.5 transition-transform cursor-pointer"
                                aria-label="Perlengkapan berikutnya">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>


<!-- KATALOG EVENT TERBUKA & MENDATANG (Livewire Filter + Race Cards) -->
<div class="velo-reveal" id="event">
    <h2 class="sr-only">Pendaftaran terbuka &amp; mendatang</h2>
    @livewire('race-cards')
</div>

<!-- YANG KAMU DAPAT DARI SATU TIKET (Karcis Miring Perforasi + 4 Fasilitas Resmi + Benefit Flat) -->
@php
    $paket = \App\Models\TicketPackageContent::current();
@endphp
<section class="sec" style="background: var(--butter); border-block: 3px solid var(--ink); padding: 80px 0">
    <div class="wrap">
        <h2 class="h2" style="max-width: 640px">
            {{ $paket->title }}
        </h2>
        <p class="lead" style="margin: 22px 0 0; max-width: 560px; color: #10282E">
            {{ $paket->description }}
        </p>

        <!-- Ticket & Facility Responsive Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start mt-12">
            
            <!-- Kiri: Tiket Karcis 3D Interaktif (lg:col-span-5) -->
            <div class="lg:col-span-5 flex flex-col items-center tkarea" id="tkarea">
                <div class="ticket" id="ticket">
                    <!-- Main Ticket Body -->
                    <div class="main">
                        <div class="flex items-center justify-between mb-1">
                            <span class="tk-k">{{ $paket->eyebrow ?? 'VELOSHA GRAN FONDO BOROBUDUR 2026' }}</span>
                            <div class="flex items-center gap-2">
                                <span id="tk-badge" class="chip" style="background: var(--pine); color: #ffffff; border: 2px solid var(--ink); font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; display: none; height: auto; text-transform: uppercase;">
                                    ✓ Valid · Flag-off 05.42
                                </span>
                                <span class="inline-flex items-center gap-1 bg-[#F4BE3F] text-[10px] font-black text-[#10282E] px-2 py-0.5 rounded border-2 border-[#10282E]">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect x="4" y="4" width="16" height="16" rx="2"></rect><circle cx="12" cy="12" r="3"></circle></svg>
                                    RFID
                                </span>
                            </div>
                        </div>

                        <div class="bib" aria-label="Nomor BIB 0147">0147</div>

                        <div style="margin-top: 8px">
                            <div class="tk-k">PESERTA</div>
                            <div class="disp font-heading font-black text-2xl text-[var(--ink)] leading-tight">Budi Prasetyo</div>
                            <div class="tk-k font-bold" style="margin-top: 4px; color: var(--ink);">GRAN FONDO · 130 KM · WAVE B</div>
                        </div>

                        <div class="stamp" aria-hidden="true">TERVALIDASI</div>
                    </div>

                    <!-- Perforation Semicircle Notches -->
                    <div class="ticket-notch notch-top" aria-hidden="true"></div>
                    <div class="ticket-notch notch-bottom" aria-hidden="true"></div>

                    <!-- Right Ticket Stub (Tearable Section) -->
                    <div class="stub" id="stub" tabindex="0" aria-label="Potongan tiket. Tarik ke bawah untuk menyobek, atau tekan Enter.">
                        <div class="flex flex-col items-center">
                            <span class="text-[9px] font-black uppercase tracking-wider text-[var(--ink)] mb-2 flex items-center gap-1 select-none">
                                <span>SOBEK DI SINI</span>
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                            </span>
                            <div class="qr" id="qr">
                                <svg viewBox="0 0 25 25" shape-rendering="crispEdges" aria-hidden="true">
                                    <path id="qrp" fill="#10282E"></path>
                                </svg>
                                <span class="laser"></span>
                                <span class="okmark" aria-hidden="true">✓</span>
                            </div>
                            <div class="tk-k text-center font-black mt-2 text-[10px]">VLS-0147-8841</div>
                        </div>
                    </div>
                    <span class="glare"></span>
                </div>

                <!-- Controls Below Ticket -->
                <div class="tkctl flex items-center gap-3 mt-6">
                    <button type="button" id="scan" class="btn btn-sm">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="mr-1.5"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"></path></svg>
                        <span>Pindai QR</span>
                    </button>
                    <button type="button" id="reset" class="btn btn-sm btn-ghost">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="mr-1.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                        <span>Pasang lagi</span>
                    </button>
                </div>
                <p id="tkmsg" class="text-xs font-semibold text-[var(--mute)] mt-3 text-center flex items-center justify-center gap-1.5" role="status" aria-live="polite">
                    <svg class="w-3.5 h-3.5 text-[var(--orange)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span>Pindai QR dulu, lalu tarik potongan kanan ke bawah untuk menyobek.</span>
                </p>
            </div>

            <!-- Kanan: Bento Grid Fitur Kartun + Fasilitas (2 Kolom di Mobile & Desktop Gambar 4) -->
            <div class="lg:col-span-7 grid grid-cols-2 gap-3 sm:gap-4">
                <style>
                    .feat-icon-bg-0 { background-color: #F4BE3F; }
                    .feat-icon-bg-1 { background-color: #DE7119; }
                    .feat-icon-bg-2 { background-color: #D5EBD9; }
                    .feat-icon-bg-3 { background-color: #F2D3DC; }
                </style>
                @foreach(($paket->features ?? []) as $idx => $f)
                <div class="card p-3 sm:p-4 flex flex-col sm:flex-row items-start sm:items-center gap-2.5 sm:gap-3.5 rounded-xl border-[2.5px] border-[var(--ink)] bg-[var(--card)] shadow-[3px_3px_0_var(--ink)] hover:-translate-y-0.5 transition-all">
                    <div class="feat-icon-bg-{{ $idx % 4 }} w-10 h-10 sm:w-12 sm:h-12 shrink-0 border-2 border-[#10282E] rounded-lg flex items-center justify-center">
                        @if(($f['icon'] ?? '') === 'cpu')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="M9 9h6v6H9zM9 2v2M15 2v2M9 20v2M15 20v2"></path></svg>
                        @elseif(($f['icon'] ?? '') === 'award')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="9" r="6"></circle><path d="M8.5 14L7 22l5-3 5 3-1.500-8"></path></svg>
                        @elseif(($f['icon'] ?? '') === 'shield-plus')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.500 8-8 9-4.500-1-8-4-8-9V6z"></path><path d="M9 12l2 2 4-4"></path></svg>
                        @elseif(($f['icon'] ?? '') === 'utensils')
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3v8a3 3 0 0 0 6 0V3M9 3v18M18 3c-2 2-3 5-3 8h3v10"></path></svg>
                        @else
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="font-heading font-black text-xs sm:text-base text-[var(--ink)] leading-snug line-clamp-2">{{ $f['title'] ?? '' }}</div>
                        <div class="text-[11px] sm:text-xs text-[var(--mute)] leading-tight mt-0.5 line-clamp-2 sm:line-clamp-none">{{ $f['desc'] ?? '' }}</div>
                    </div>
                </div>
                @endforeach

                <!-- Kartu Flat Gelap Benefit Lain Bento Spans 2 Cols -->
                <div class="col-span-2 p-4 sm:p-5 bg-[#10282E] text-[#FBF4E6] rounded-xl border-[2.5px] border-[#10282E] shadow-[4px_4px_0_#DE7119]">
                    <div class="disp text-sm sm:text-base font-extrabold text-[#F4BE3F] uppercase tracking-wider">{{ $paket->benefits_title ?? 'Benefit Lain dari Satu Tiket' }}</div>
                    <ul class="mt-3 p-0 list-none grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-x-6 text-xs sm:text-sm leading-relaxed">
                        @foreach(($paket->benefits ?? []) as $b)
                        <li class="flex items-start gap-2">
                            <span class="text-[#F4BE3F] font-black shrink-0">•</span>
                            <span>{{ $b['text'] ?? '' }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- INFORMASI & REGULASI PESERTA (Help Center Hijau Pine + 4 Kartu Warna + Cek Cepat) -->
<section class="sec" style="background: var(--paper2); border-block: 3px solid var(--ink); position: relative; overflow: hidden; padding: 80px 0">
    <div class="dots" style="position: absolute; inset: 0; opacity: .55; pointer-events: none"></div>

    <div class="wrap" style="position: relative">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; flex-wrap: wrap">
            <h2 class="h2">Informasi &amp; regulasi<br>peserta</h2>
            <p class="lead" style="max-width: 400px; margin: 0; color: #10282E">Semua yang perlu kamu tahu sebelum start: regulasi resmi balap, pusat bantuan peserta, dan paket kuota penyelenggara.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch mt-12">
            <!-- Left Help Center Card (Warna Hijau Pine #1F6F4A dengan Balon Obrolan Melayang) -->
            <div class="card lg:col-span-5" style="background: #1F6F4A; color: #fff; padding: 36px; position: relative; overflow: hidden; display: flex; flex-direction: column; gap: 28px; min-height: 440px">
                <!-- SVG Chat Bubble Float (Diposisikan Aman di Pojok Kanan Atas Tanpa Menimpa Teks) -->
                <svg class="float hidden sm:block pointer-events-none" width="105" height="80" viewBox="0 0 200 150" aria-hidden="true" style="position: absolute; right: 16px; top: 16px; z-index: 1;">
                    <path d="M14 18 H128 Q140 18 140 30 V74 Q140 86 128 86 H64 L40 106 V86 H26 Q14 86 14 74Z" fill="#F4BE3F" stroke="#10282E" stroke-width="4" stroke-linejoin="round"></path>
                    <path d="M70 50 Q70 36 84 36 Q98 36 98 48 Q98 56 88 60 V66" fill="none" stroke="#10282E" stroke-width="5" stroke-linecap="round"></path>
                    <circle cx="88" cy="76" r="3.5" fill="#10282E"></circle>
                    <path d="M110 88 H178 Q190 88 190 100 V126 Q190 138 178 138 H166 V152 L148 138 H110 Q98 138 98 126 V100 Q98 88 110 88Z" fill="#FFFDF8" stroke="#10282E" stroke-width="4" stroke-linejoin="round"></path>
                    <path d="M114 108 H174 M114 122 H154" stroke="#10282E" stroke-width="4" stroke-linecap="round"></path>
                </svg>

                <div class="relative z-10 mt-2 sm:mt-4 max-w-[260px] sm:max-w-[280px]">
                    <span class="chip m">Bantuan 24/7</span>
                    <h3 class="h3" style="font-size: 28px; margin-top: 14px; color: #fff; line-height: 1.15">Help Center &amp; layanan peserta</h3>
                    <p style="margin: 12px 0 0; font-size: 15px; line-height: 1.55;">Cara bayar, ambil race pack, ganti nomor BIB, asuransi, dan info darurat.</p>
                </div>

                <div class="relative z-10" style="display: grid; gap: 0; font-size: 14px">
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-top: 2px dashed rgba(255,255,255,.45)">
                        <b>WhatsApp</b><span>09.00–18.00 WIB</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-top: 2px dashed rgba(255,255,255,.45)">
                        <b>Email</b><span>dibalas ≤ 1×24 jam</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-top: 2px dashed rgba(255,255,255,.45)">
                        <b>Tanya Mizu</b><span>di setiap halaman</span>
                    </div>
                </div>

                <a href="/help" wire:navigate.hover class="btn btn-block relative z-10" style="margin-top: auto">
                    Buka Pusat Bantuan
                </a>
            </div>

            <!-- Right 3 Regulation Hub Cards (Spacious Bento Grid, Tidak Berdempetan) -->
            <div class="lg:col-span-7 grid grid-cols-2 gap-4 sm:gap-5">
                <!-- 01 Tentang Velosha (Butter #F8E3B5 - Bento Spans 2 Cols) -->
                <a href="/about" wire:navigate.hover class="col-span-2 card racecard p-5 sm:p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-decoration-none rounded-xl border-[2.5px] border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] transition-transform hover:-translate-y-1" style="background: #F8E3B5;">
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="ico shrink-0 w-11 h-11 sm:w-12 sm:h-12 rounded-lg border-2 border-[#10282E] flex items-center justify-center bg-[#FFFDF8] shadow-[2px_2px_0_#10282E]">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v6M12 7.500v.01"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="disp numo text-xs sm:text-sm font-black text-[#DE7119]">01</span>
                                <h3 class="font-heading font-black text-base sm:text-xl text-[#10282E] m-0">Tentang Velosha</h3>
                            </div>
                            <p class="text-xs sm:text-sm text-[#10282E]/80 m-0 mt-1 line-clamp-2 sm:line-clamp-none">Platform kurasi &amp; registrasi resmi balap sepeda Gran Fondo Indonesia.</p>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 font-black text-xs uppercase tracking-wider text-[#10282E] shrink-0">
                        <span>Buka</span>
                        <span class="w-7 h-7 border-2 border-[#10282E] rounded-md bg-[#DE7119] flex items-center justify-center text-white shadow-[1.5px_1.5px_0_#10282E]">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                        </span>
                    </div>
                </a>

                <!-- 02 Aturan & Kode Etik (Pine Light #D5EBD9 - Bento Col 1) -->
                <a href="/rule" wire:navigate.hover class="col-span-1 card racecard p-5 sm:p-6 flex flex-col justify-between gap-3 text-decoration-none rounded-xl border-[2.5px] border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] min-h-[160px] transition-transform hover:-translate-y-1" style="background: #D5EBD9;">
                    <div class="flex justify-between items-start">
                        <div class="ico w-10 h-10 rounded-lg border-2 border-[#10282E] flex items-center justify-center bg-[#FFFDF8] shadow-[2px_2px_0_#10282E]">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.500 8-8 9-4.500-1-8-4-8-9V6z"></path><path d="M9 12l2 2 4-4"></path></svg>
                        </div>
                        <span class="disp numo text-sm sm:text-base font-black text-[#1F6F4A]">02</span>
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-sm sm:text-lg text-[#10282E] m-0 leading-tight">Aturan &amp; Etik</h3>
                        <p class="text-xs sm:text-sm text-[#10282E]/80 m-0 mt-1 line-clamp-2">Helm wajib, etika peloton &amp; penalti.</p>
                    </div>
                    <div class="flex items-center gap-1.5 font-black text-xs sm:text-sm text-[#10282E]">
                        <span>Buka</span>
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                    </div>
                </a>

                <!-- 03 Paket Penyelenggara & Kuota (Orange Light #F6B777 - Bento Col 2) -->
                <a href="/paket" wire:navigate.hover class="col-span-1 card racecard p-5 sm:p-6 flex flex-col justify-between gap-3 text-decoration-none rounded-xl border-[2.5px] border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] min-h-[160px] transition-transform hover:-translate-y-1" style="background: #F6B777;">
                    <div class="flex justify-between items-start">
                        <div class="ico w-10 h-10 rounded-lg border-2 border-[#10282E] flex items-center justify-center bg-[#FFFDF8] shadow-[2px_2px_0_#10282E]">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <span class="disp numo text-sm sm:text-base font-black text-[#DE7119]">03</span>
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-sm sm:text-lg text-[#10282E] m-0 leading-tight">Paket Kuota EO</h3>
                        <p class="text-xs sm:text-sm text-[#10282E]/80 m-0 mt-1 line-clamp-2">Publikasi race &amp; kelola tiket peserta.</p>
                    </div>
                    <div class="flex items-center gap-1.5 font-black text-xs sm:text-sm text-[#10282E]">
                        <span>Buka</span>
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                    </div>
                </a>
            </div>
        </div>

        <!-- Cek Cepat Sebelum Hari-H (High Contrast Kontras Tinggi Sesuai Gambar 5 Bebas Teks Hilang) -->
        <div class="mt-10 p-6 sm:p-8 bg-[#FFFDF8] text-[#10282E] rounded-2xl border-[2.5px] border-[#10282E] shadow-[4px_4px_0_#10282E] flex flex-col md:flex-row items-start md:items-center gap-6 sm:gap-8">
            <div class="disp text-lg sm:text-2xl font-black text-[#DE7119] leading-tight shrink-0">
                Cek cepat<br>sebelum hari-H
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 w-full flex-1">
                <div class="flex gap-2.5 items-center text-xs sm:text-sm font-bold text-[#10282E]">
                    <span class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-md border-2 border-[#10282E] bg-[#F4BE3F] flex items-center justify-center shadow-[1.5px_1.5px_0_#10282E]">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"></path></svg>
                    </span>
                    <span class="text-[#10282E]">Helm terpasang kencang</span>
                </div>
                <div class="flex gap-2.5 items-center text-xs sm:text-sm font-bold text-[#10282E]">
                    <span class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-md border-2 border-[#10282E] bg-[#F4BE3F] flex items-center justify-center shadow-[1.5px_1.5px_0_#10282E]">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"></path></svg>
                    </span>
                    <span class="text-[#10282E]">Rem depan-belakang jalan</span>
                </div>
                <div class="flex gap-2.5 items-center text-xs sm:text-sm font-bold text-[#10282E]">
                    <span class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-md border-2 border-[#10282E] bg-[#F4BE3F] flex items-center justify-center shadow-[1.5px_1.5px_0_#10282E]">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"></path></svg>
                    </span>
                    <span class="text-[#10282E]">Chip RFID di nomor BIB</span>
                </div>
                <div class="flex gap-2.5 items-center text-xs sm:text-sm font-bold text-[#10282E]">
                    <span class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-md border-2 border-[#10282E] bg-[#F4BE3F] flex items-center justify-center shadow-[1.5px_1.5px_0_#10282E]">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"></path></svg>
                    </span>
                    <span class="text-[#10282E]">E-ticket QR siap di HP</span>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection