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

    <!-- Tombol Putar Ulang Animasi -->
    <button type="button"
            id="replay-hero"
            class="btn btn-ghost btn-sm"
            @click="replay()"
            style="position: absolute; top: 16px; right: 24px; z-index: 20"
            aria-label="Putar ulang animasi hero">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M4 12a8 8 0 1 0 3-6.2"></path><path d="M4 4v5h5"></path></svg>
        <span>Putar ulang</span>
    </button>

    <div class="stage" :class="off ? 'off' : 'on'" style="position: relative; width: 1200px; height: 680px; margin: 0 auto; padding: 0 24px">

        <!-- Panel Berlekuk SVG (Blok Oranye di Belakang) -->
        <svg class="a-amber" width="580" height="445" viewBox="-2 -2 580 445" aria-hidden="true"
             style="position: absolute; left: 566px; top: 56px; z-index: 1; overflow: visible">
            <path d="M226,0 H540 Q576,0 576,36 V405 Q576,441 540,441 H36 Q0,441 0,405 V136 Q0,100 36,100 H154 Q190,100 190,64 V36 Q190,0 226,0 Z"
                  fill="#DE7119" stroke="#10282E" stroke-width="3" stroke-linejoin="round"></path>
        </svg>

        <!-- Panel Berlekuk SVG (Krem Bertitik Halus di Depan) -->
        <svg class="a-panel" id="hero-panel" width="580" height="445" viewBox="-2 -2 580 445" aria-hidden="true"
             style="position: absolute; left: 552px; top: 42px; z-index: 2; overflow: visible">
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
        <div class="a-trail" style="position: absolute; left: 520px; top: 250px; width: 140px; height: 6px; border-radius: 3px; background: #F4BE3F; border: 2px solid #10282E; z-index: 3"></div>
        <div class="a-trail" style="position: absolute; left: 550px; top: 278px; width: 100px; height: 6px; border-radius: 3px; background: #1F6F4A; border: 2px solid #10282E; z-index: 3"></div>
        <div class="a-trail" style="position: absolute; left: 500px; top: 306px; width: 160px; height: 6px; border-radius: 3px; background: #DE7119; border: 2px solid #10282E; z-index: 3"></div>

        <!-- Pesepeda Berjersey Oranye (rider_final.png - Autentik) -->
        <div class="a-ride" id="hero-rider" style="position: absolute; left: 610px; top: -40px; width: 640px; height: 640px; z-index: 10; pointer-events: none">
            <div class="a-bob" style="width: 100%; height: 100%">
                <img src="/images/hero/rider_final.png"
                     alt="Pesepeda berjersey oranye melaju"
                     fetchpriority="high"
                     style="display: block; width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(4px 4px 0 rgba(16, 40, 46, 0.15))">
            </div>
        </div>

        <!-- Kolom Kiri -->
        <div style="position: absolute; left: 0; top: 40px; width: 500px; z-index: 5">
            <h1 class="a-rise" style="animation-delay: .2s; font-size: 72px; line-height: .98; font-weight: 800; letter-spacing: -0.04em; color: var(--ink); margin: 0">
                Siap gowes<br>
                sampai <span class="squig">finis?<svg viewBox="0 0 100 14" preserveAspectRatio="none" aria-hidden="true"><path d="M2 8 Q14 1 26 8 T50 8 T74 8 T98 8" fill="none" stroke="#DE7119" stroke-width="5" stroke-linecap="round" vector-effect="non-scaling-stroke"></path></svg></span>
            </h1>
            <p class="a-rise" style="animation-delay: .35s; margin: 20px 0 0; max-width: 400px; font-size: 17px; line-height: 1.5; color: #10282E; font-weight: 500">
                Daftar Gran Fondo, Medio Fondo, dan Fun Ride di seluruh Indonesia. Tiket dan nomor BIB langsung jadi.
            </p>
            <div class="a-rise" style="animation-delay: .5s; margin-top: 22px">
                <a href="#event" class="btn">
                    Cari event
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                </a>
            </div>
        </div>

        <!-- Slider Media Kiri Bawah (Maks 3 Gambar Ilustrasi dengan Dot & Panah) -->
        <div class="a-rise"
             style="animation-delay: .7s; position: absolute; left: 0; top: 425px; width: 480px; height: 215px; z-index: 5"
             x-data="{
                slide: 0,
                timer: null,
                reduced: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
                start() {
                    if (this.reduced || this.timer) return;
                    this.timer = setInterval(() => { this.slide = (this.slide + 1) % 3; }, 5000);
                },
                stop() { clearInterval(this.timer); this.timer = null; },
                next() { this.slide = (this.slide + 1) % 3; },
                prev() { this.slide = (this.slide - 1 + 3) % 3; }
             }"
             x-init="start()"
             @mouseenter="stop()"
             @mouseleave="start()"
             @focusin="stop()"
             @focusout="start()"
             @visibilitychange.window="document.hidden ? stop() : start()">

            <div class="card" style="position: relative; width: 100%; height: 100%; overflow: hidden; border-radius: 16px; border: 2.5px solid #10282E; box-shadow: 4px 4px 0 #10282E">
                <!-- Slide 0: Foto Nyata Gran Fondo Borobudur -->
                <div x-show="slide === 0" style="position: absolute; inset: 0">
                    <img src="/images/events/kartu-1.jpg"
                         alt="Foto Gran Fondo Borobudur"
                         style="position: absolute; left: 0; top: 0; width: 100%; height: calc(100% - 46px); object-fit: cover; display: block;">
                </div>

                <!-- Slide 1: Foto Nyata Peloton Cycling -->
                <div x-show="slide === 1" style="position: absolute; inset: 0">
                    <img src="/images/hero-peloton.jpg"
                         alt="Foto Peloton Balap Sepeda"
                         style="position: absolute; left: 0; top: 0; width: 100%; height: calc(100% - 46px); object-fit: cover; display: block;">
                </div>

                <!-- Slide 2: Foto Nyata Tanjakan Kaldera Bromo -->
                <div x-show="slide === 2" style="position: absolute; inset: 0">
                    <img src="/images/events/kartu-6.jpg"
                         alt="Foto Bromo KOM Challenge"
                         style="position: absolute; left: 0; top: 0; width: 100%; height: calc(100% - 46px); object-fit: cover; display: block;">
                </div>

                <!-- Bar Kontrol Slider Bawah (Dot + Navigasi) -->
                <div style="position: absolute; left: 0; right: 0; bottom: 0; height: 46px; display: flex; align-items: center; justify-content: space-between; padding: 0 14px; background: #FFFDF8; border-top: 2.5px solid #10282E">
                    <div style="display: flex; align-items: center">
                        <button type="button" class="dotbtn" :class="{ 'on': slide === 0 }" @click="slide = 0" aria-label="Gambar 1"><i></i></button>
                        <button type="button" class="dotbtn" :class="{ 'on': slide === 1 }" @click="slide = 1" aria-label="Gambar 2"><i></i></button>
                        <button type="button" class="dotbtn" :class="{ 'on': slide === 2 }" @click="slide = 2" aria-label="Gambar 3"><i></i></button>
                    </div>
                    <div style="display: flex; gap: 8px">
                        <button type="button" class="btn btn-ghost btn-sm" @click="prev()" aria-label="Gambar sebelumnya" style="width: 32px; height: 28px; padding: 0; min-height: 28px; border: 2px solid #10282E; border-radius: 8px; background: #FFFDF8; box-shadow: 2px 2px 0 #10282E;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"></path></svg>
                        </button>
                        <button type="button" class="btn btn-sm" @click="next()" aria-label="Gambar berikutnya" style="width: 32px; height: 28px; padding: 0; min-height: 28px; border: 2px solid #10282E; border-radius: 8px; background: #DE7119; color: #FFF; box-shadow: 2px 2px 0 #10282E;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#FFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"></path></svg>
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
                <p id="tkmsg" class="text-xs font-semibold text-[var(--mute)] mt-3 text-center" role="status" aria-live="polite">
                    💡 Pindai QR dulu, lalu tarik potongan kanan ke bawah untuk menyobek.
                </p>
            </div>

            <!-- Kanan: Grid Fitur Kartun + Fasilitas (lg:col-span-7) -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <style>
                    .feat-icon-bg-0 { background-color: #F4BE3F; }
                    .feat-icon-bg-1 { background-color: #DE7119; }
                    .feat-icon-bg-2 { background-color: #D5EBD9; }
                    .feat-icon-bg-3 { background-color: #F2D3DC; }
                </style>
                @foreach(($paket->features ?? []) as $idx => $f)
                <div class="card" style="padding: 22px; display: flex; gap: 16px; align-items: center">
                    <div class="feat-icon-bg-{{ $idx % 4 }}" style="width: 56px; height: 56px; flex: none; border: 2.5px solid #10282E; border-radius: 10px; display: flex; align-items: center; justify-content: center">
                        @if(($f['icon'] ?? '') === 'cpu')
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="M9 9h6v6H9zM9 2v2M15 2v2M9 20v2M15 20v2"></path></svg>
                        @elseif(($f['icon'] ?? '') === 'award')
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="9" r="6"></circle><path d="M8.5 14L7 22l5-3 5 3-1.500-8"></path></svg>
                        @elseif(($f['icon'] ?? '') === 'shield-plus')
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.500 8-8 9-4.500-1-8-4-8-9V6z"></path><path d="M9 12l2 2 4-4"></path></svg>
                        @elseif(($f['icon'] ?? '') === 'utensils')
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3v8a3 3 0 0 0 6 0V3M9 3v18M18 3c-2 2-3 5-3 8h3v10"></path></svg>
                        @else
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        @endif
                    </div>
                    <div>
                        <div class="h3" style="font-size: 20px">{{ $f['title'] ?? '' }}</div>
                        <div class="mute" style="font-size: 14px">{{ $f['desc'] ?? '' }}</div>
                    </div>
                </div>
                @endforeach

                <!-- Kartu Flat Gelap Benefit Lain Persis Mockup -->
                <div class="card-flat" style="grid-column: 1 / -1; padding: 22px 24px; background: #10282E; color: #FBF4E6; border-radius: 14px">
                    <div class="disp" style="font-size: 18px; font-weight: 800; color: #F4BE3F; text-transform: uppercase; letter-spacing: .04em">{{ $paket->benefits_title ?? 'Benefit Lain dari Satu Tiket' }}</div>
                    <ul style="margin: 14px 0 0; padding: 0; list-style: none; display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px 28px; font-size: 14px; line-height: 1.45">
                        @foreach(($paket->benefits ?? []) as $b)
                        <li style="display: flex; align-items: center; gap: 8px">
                            <span style="color: #F4BE3F">•</span> {{ $b['text'] ?? '' }}
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

        <div style="display: grid; grid-template-columns: 5fr 7fr; gap: 32px; margin-top: 48px; align-items: stretch">
            <!-- Left Help Center Card (Warna Hijau Pine #1F6F4A dengan Balon Obrolan Melayang) -->
            <div class="card" style="background: #1F6F4A; color: #fff; padding: 36px; position: relative; overflow: hidden; display: flex; flex-direction: column; gap: 28px; min-height: 470px">
                <!-- SVG Chat Bubble Float -->
                <svg class="float" width="200" height="150" viewBox="0 0 200 150" aria-hidden="true" style="position: absolute; right: 18px; top: 20px">
                    <path d="M14 18 H128 Q140 18 140 30 V74 Q140 86 128 86 H64 L40 106 V86 H26 Q14 86 14 74Z" fill="#F4BE3F" stroke="#10282E" stroke-width="4" stroke-linejoin="round"></path>
                    <path d="M70 50 Q70 36 84 36 Q98 36 98 48 Q98 56 88 60 V66" fill="none" stroke="#10282E" stroke-width="5" stroke-linecap="round"></path>
                    <circle cx="88" cy="76" r="3.5" fill="#10282E"></circle>
                    <path d="M110 88 H178 Q190 88 190 100 V126 Q190 138 178 138 H166 V152 L148 138 H110 Q98 138 98 126 V100 Q98 88 110 88Z" fill="#FFFDF8" stroke="#10282E" stroke-width="4" stroke-linejoin="round"></path>
                    <path d="M114 108 H174 M114 122 H154" stroke="#10282E" stroke-width="4" stroke-linecap="round"></path>
                </svg>

                <div style="position: relative; margin-top: 108px">
                    <span class="chip m">Bantuan 24/7</span>
                    <h3 class="h3" style="font-size: 34px; margin-top: 16px; color: #fff; line-height: 1.05">Help Center &amp; layanan peserta</h3>
                    <p style="margin: 14px 0 0; font-size: 16px; line-height: 1.55; max-width: 380px">Cara bayar, ambil race pack, ganti nomor BIB, asuransi, dan info darurat.</p>
                </div>

                <div style="position: relative; display: grid; gap: 0; font-size: 15px">
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

                <a href="/help" wire:navigate.hover class="btn btn-block" style="position: relative; margin-top: auto">
                    Buka Pusat Bantuan
                </a>
            </div>

            <!-- Right 3 Regulation Hub Cards (.numo outline 01-03, Ranking dihapus) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px">
                <!-- 01 Tentang Velosha (Butter #F8E3B5 - Spans 2 Cols) -->
                <a href="/about" wire:navigate.hover class="card racecard" style="grid-column: 1 / -1; background: #F8E3B5; padding: 26px; display: flex; flex-direction: column; gap: 16px; text-decoration: none; min-height: 190px">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start">
                        <div class="ico" style="background: #FFFDF8">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v6M12 7.500v.01"></path></svg>
                        </div>
                        <span class="disp numo">01</span>
                    </div>
                    <div>
                        <h3 class="h3" style="font-size: 24px">Tentang Velosha</h3>
                        <p style="margin: 6px 0 0; font-size: 15px; color: #10282E">Platform kurasi dan registrasi resmi maraton sepeda serta Gran Fondo Indonesia.</p>
                    </div>
                    <div style="margin-top: auto; display: flex; align-items: center; gap: 10px; font-weight: 700">
                        Buka
                        <span style="width: 28px; height: 28px; border: 2.5px solid #10282E; border-radius: 6px; background: #DE7119; display: inline-flex; align-items: center; justify-content: center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                        </span>
                    </div>
                </a>

                <!-- 02 Aturan & Kode Etik (Pine Light #D5EBD9) -->
                <a href="/rule" wire:navigate.hover class="card racecard" style="background: #D5EBD9; padding: 26px; display: flex; flex-direction: column; gap: 16px; text-decoration: none; min-height: 200px">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start">
                        <div class="ico" style="background: #FFFDF8">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.500 8-8 9-4.500-1-8-4-8-9V6z"></path><path d="M9 12l2 2 4-4"></path></svg>
                        </div>
                        <span class="disp numo">02</span>
                    </div>
                    <div>
                        <h3 class="h3" style="font-size: 22px">Aturan &amp; kode etik</h3>
                        <p style="margin: 6px 0 0; font-size: 14px; color: #10282E">Helm wajib, etika peloton, penalti, dan diskualifikasi.</p>
                    </div>
                    <div style="margin-top: auto; display: flex; align-items: center; gap: 10px; font-weight: 700">
                        Buka
                        <span style="width: 28px; height: 28px; border: 2.5px solid #10282E; border-radius: 6px; background: #DE7119; display: inline-flex; align-items: center; justify-content: center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                        </span>
                    </div>
                </a>

                <!-- 03 Paket Penyelenggara & Kuota (Orange Light #F6B777) -->
                <a href="/paket" wire:navigate.hover class="card racecard" style="background: #F6B777; padding: 26px; display: flex; flex-direction: column; gap: 16px; text-decoration: none; min-height: 200px">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start">
                        <div class="ico" style="background: #FFFDF8">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <span class="disp numo">03</span>
                    </div>
                    <div>
                        <h3 class="h3" style="font-size: 22px">Paket kuota EO</h3>
                        <p style="margin: 6px 0 0; font-size: 14px; color: #10282E">Publikasikan race komunitas &amp; kelola pendaftaran tiket.</p>
                    </div>
                    <div style="margin-top: auto; display: flex; align-items: center; gap: 10px; font-weight: 700">
                        Buka
                        <span style="width: 28px; height: 28px; border: 2.5px solid #10282E; border-radius: 6px; background: #FFFDF8; display: inline-flex; align-items: center; justify-content: center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                        </span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Cek Cepat Sebelum Hari-H (Dark Strip with Mustard Checkboxes) -->
        <div class="card-flat" style="margin-top: 32px; background: #10282E; color: #FBF4E6; padding: 24px 32px; display: flex; align-items: center; gap: 32px; box-shadow: 5px 5px 0 #DE7119; flex-wrap: wrap">
            <div class="disp" style="font-size: 22px; font-weight: 800; color: #F4BE3F; line-height: 1.1">Cek cepat<br>sebelum hari-H</div>
            <div style="flex: 1; display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; min-width: 640px">
                <div style="display: flex; gap: 12px; align-items: center; font-size: 15px; font-weight: 600">
                    <span style="width: 32px; height: 32px; flex: none; border: 2.5px solid #FBF4E6; border-radius: 6px; background: #F4BE3F; display: flex; align-items: center; justify-content: center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"></path></svg>
                    </span>
                    Helm terpasang kencang
                </div>
                <div style="display: flex; gap: 12px; align-items: center; font-size: 15px; font-weight: 600">
                    <span style="width: 32px; height: 32px; flex: none; border: 2.5px solid #FBF4E6; border-radius: 6px; background: #F4BE3F; display: flex; align-items: center; justify-content: center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"></path></svg>
                    </span>
                    Rem depan-belakang jalan
                </div>
                <div style="display: flex; gap: 12px; align-items: center; font-size: 15px; font-weight: 600">
                    <span style="width: 32px; height: 32px; flex: none; border: 2.5px solid #FBF4E6; border-radius: 6px; background: #F4BE3F; display: flex; align-items: center; justify-content: center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"></path></svg>
                    </span>
                    Chip RFID di nomor BIB
                </div>
                <div style="display: flex; gap: 12px; align-items: center; font-size: 15px; font-weight: 600">
                    <span style="width: 32px; height: 32px; flex: none; border: 2.5px solid #FBF4E6; border-radius: 6px; background: #F4BE3F; display: flex; align-items: center; justify-content: center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"></path></svg>
                    </span>
                    E-ticket QR siap di HP
                </div>
            </div>
        </div>
    </div>
</section>

@endsection