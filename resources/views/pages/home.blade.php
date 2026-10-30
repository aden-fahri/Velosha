@extends('layouts.app', ['title' => 'Velosha — Platform Maraton & Gran Fondo Indonesia'])

@section('content')

<!-- INTRO LOADING OVERLAY (3.4s, sessionStorage guard, skip button, reduced motion support) -->
<x-intro-loading />

<!-- HERO SECTION (Screen-Print Neobrutalist Stage dengan Animasi 1:1 Mockup) -->
<section style="position: relative; min-height: 760px; background: var(--paper); overflow: hidden; border-bottom: 3px solid var(--ink)"
         x-data="{
            off: false,
            replay() {
                this.off = true;
                setTimeout(() => this.off = false, 90);
            }
         }">

    <!-- Tombol Putar Ulang Animasi -->
    <button type="button"
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
        <svg class="a-panel" width="580" height="445" viewBox="-2 -2 580 445" aria-hidden="true"
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
        <div class="a-trail" style="position: absolute; left: 470px; top: 300px; width: 150px; height: 6px; border-radius: 3px; background: #F4BE3F; border: 2px solid #10282E; z-index: 3"></div>
        <div class="a-trail" style="position: absolute; left: 520px; top: 326px; width: 100px; height: 6px; border-radius: 3px; background: #1F6F4A; border: 2px solid #10282E; z-index: 3"></div>
        <div class="a-trail" style="position: absolute; left: 450px; top: 352px; width: 170px; height: 6px; border-radius: 3px; background: #DE7119; border: 2px solid #10282E; z-index: 3"></div>

        <!-- Pesepeda Berjersey Oranye Melaju Miring Menikung (miring.png) -->
        <div class="a-ride" style="position: absolute; left: 610px; top: -40px; width: 640px; height: 640px; z-index: 10; pointer-events: none">
            <div class="a-bob" style="width: 100%; height: 100%">
                <img src="/images/mockup/miring.png"
                     data-rider="/images/hero/rider_final.png"
                     alt="Pesepeda berjersey oranye melaju miring menikung"
                     fetchpriority="high"
                     style="display: block; width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(4px 4px 0 rgba(16, 40, 46, 0.15))">
            </div>
        </div>

        <!-- Kolom Kiri -->
        <div style="position: absolute; left: 0; top: 64px; width: 500px; z-index: 5">
            <h1 class="a-rise" style="animation-delay: .2s; font-size: 72px; line-height: .98; font-weight: 800; letter-spacing: -0.04em; color: var(--ink); margin: 0">
                Siap gowes<br>
                sampai <span class="squig">finis?<svg viewBox="0 0 100 14" preserveAspectRatio="none" aria-hidden="true"><path d="M2 8 Q14 1 26 8 T50 8 T74 8 T98 8" fill="none" stroke="#DE7119" stroke-width="5" stroke-linecap="round" vector-effect="non-scaling-stroke"></path></svg></span>
            </h1>
            <p class="a-rise" style="animation-delay: .35s; margin: 28px 0 0; max-width: 400px; font-size: 18px; line-height: 1.5; color: #10282E; font-weight: 500">
                Daftar Gran Fondo, Medio Fondo, dan Fun Ride di seluruh Indonesia. Tiket dan nomor BIB langsung jadi.
            </p>
            <div class="a-rise" style="animation-delay: .5s; margin-top: 28px">
                <a href="/events" wire:navigate class="btn">
                    Cari event
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                </a>
            </div>
        </div>

        <!-- Slider Media Kiri Bawah (Maks 3 Gambar Ilustrasi dengan Dot & Panah) -->
        <div class="a-rise"
             style="animation-delay: .7s; position: absolute; left: 0; top: 400px; width: 480px; height: 220px; z-index: 5"
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

            <div class="card" style="position: relative; width: 100%; height: 100%; overflow: hidden; border-radius: 14px">
                <!-- Slide 0: Matahari Terbit & Jalan Pegunungan -->
                <div x-show="slide === 0" style="position: absolute; inset: 0">
                    <svg viewBox="0 0 360 180" preserveAspectRatio="xMidYMid slice" width="100%" height="100%" role="img" aria-label="Foto event 1: jalan raya menurun saat matahari terbit" style="position: absolute; left: 0; top: 0; width: 100%; height: calc(100% - 63px)">
                        <rect width="360" height="180" fill="#FFD9A0"></rect>
                        <circle cx="270" cy="56" r="28" fill="#F4BE3F" stroke="#10282E" stroke-width="3"></circle>
                        <path d="M0 118 Q60 66 120 106 T240 96 T360 84 V180 H0Z" fill="#9CCB9A" stroke="#10282E" stroke-width="3"></path>
                        <path d="M0 150 Q90 108 180 140 T360 128 V180 H0Z" fill="#1F6F4A" stroke="#10282E" stroke-width="3"></path>
                        <path d="M150 190 Q180 150 232 128 T330 112" fill="none" stroke="#10282E" stroke-width="26" stroke-linecap="round"></path>
                        <path d="M150 190 Q180 150 232 128 T330 112" fill="none" stroke="#FBF4E6" stroke-width="3" stroke-dasharray="10 12" stroke-linecap="round"></path>
                    </svg>
                </div>

                <!-- Slide 1: Peleton Candi -->
                <div x-show="slide === 1" style="position: absolute; inset: 0">
                    <svg viewBox="0 0 360 180" preserveAspectRatio="xMidYMid slice" width="100%" height="100%" role="img" aria-label="Foto event 2: peleton melintas candi" style="position: absolute; left: 0; top: 0; width: 100%; height: calc(100% - 63px)">
                        <rect width="360" height="180" fill="#F6B777"></rect>
                        <circle cx="90" cy="60" r="30" fill="#FBF4E6" stroke="#10282E" stroke-width="3"></circle>
                        <path d="M200 120 L230 70 L260 120Z M240 120 L275 56 L310 120Z" fill="#B2590F" stroke="#10282E" stroke-width="3" stroke-linejoin="round"></path>
                        <path d="M0 128 Q90 96 180 120 T360 112 V180 H0Z" fill="#7DB88A" stroke="#10282E" stroke-width="3"></path>
                        <path d="M0 160 H360 V180 H0Z" fill="#10282E"></path>
                        <circle cx="120" cy="150" r="11" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <circle cx="156" cy="150" r="11" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <circle cx="206" cy="150" r="11" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <circle cx="242" cy="150" r="11" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                    </svg>
                </div>

                <!-- Slide 2: Tanjakan Kaldera Bromo -->
                <div x-show="slide === 2" style="position: absolute; inset: 0">
                    <svg viewBox="0 0 360 180" preserveAspectRatio="xMidYMid slice" width="100%" height="100%" role="img" aria-label="Foto event 3: tanjakan menuju kaldera" style="position: absolute; left: 0; top: 0; width: 100%; height: calc(100% - 63px)">
                        <rect width="360" height="180" fill="#F8E3B5"></rect>
                        <path d="M-10 150 L90 50 L150 100 L230 30 L370 150Z" fill="#9B3D5A" stroke="#10282E" stroke-width="3" stroke-linejoin="round"></path>
                        <path d="M210 52 L230 30 L250 52 L240 48 L230 56 L220 48Z" fill="#FBF4E6" stroke="#10282E" stroke-width="2.5" stroke-linejoin="round"></path>
                        <path d="M0 150 Q100 120 200 140 T360 130 V180 H0Z" fill="#7DB88A" stroke="#10282E" stroke-width="3"></path>
                        <path d="M30 190 Q110 150 180 150 T330 128" fill="none" stroke="#10282E" stroke-width="24" stroke-linecap="round"></path>
                        <path d="M30 190 Q110 150 180 150 T330 128" fill="none" stroke="#FBF4E6" stroke-width="3" stroke-dasharray="10 12" stroke-linecap="round"></path>
                    </svg>
                </div>

                <!-- Bar Kontrol Slider Bawah (Dot + Navigasi) -->
                <div style="position: absolute; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: #FFFDF8; border-top: 2.5px solid #10282E">
                    <div style="display: flex; align-items: center">
                        <button type="button" class="dotbtn" :class="{ 'on': slide === 0 }" @click="slide = 0" aria-label="Gambar 1"><i></i></button>
                        <button type="button" class="dotbtn" :class="{ 'on': slide === 1 }" @click="slide = 1" aria-label="Gambar 2"><i></i></button>
                        <button type="button" class="dotbtn" :class="{ 'on': slide === 2 }" @click="slide = 2" aria-label="Gambar 3"><i></i></button>
                    </div>
                    <div style="display: flex; gap: 8px">
                        <button type="button" class="btn btn-ghost btn-sm" @click="prev()" aria-label="Gambar sebelumnya" style="width: 44px; padding: 0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"></path></svg>
                        </button>
                        <button type="button" class="btn btn-sm" @click="next()" aria-label="Gambar berikutnya" style="width: 44px; padding: 0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- LENGKUNG TRANSISI: KELOPAK LENGKUNG SVG DENGAN TEKS MENGIKUTI KURVA (Arc Cap) -->
<div class="arc-cap" aria-hidden="true">
    <div class="arc-clip">
        <div class="arc-dome"></div>
        <svg class="arc-text" viewBox="0 0 1440 110" preserveAspectRatio="none" width="100%" height="110">
            <defs>
                <path id="arcpath" d="M0,126 Q720,-66 1440,126"></path>
            </defs>
            <text>
                <textPath href="#arcpath" startOffset="50%" text-anchor="middle">
                    Gran Fondo<tspan fill="#DE7119" dx="14" dy="0">◆</tspan><tspan dx="14">Medio Fondo</tspan><tspan fill="#DE7119" dx="14">◆</tspan><tspan dx="14">Fun Ride</tspan><tspan fill="#DE7119" dx="14">◆</tspan><tspan dx="14">KOM Climb</tspan><tspan fill="#DE7119" dx="14">◆</tspan><tspan dx="14">Timing chip RFID</tspan>
                </textPath>
            </text>
        </svg>
    </div>
</div>

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

        <div style="display: grid; grid-template-columns: 5fr 7fr; gap: 48px; margin-top: 48px; align-items: start">
            <!-- Kiri: Tiket Karcis Kartun Miring (-2.5deg) dengan Cutout Semicircle Persis Mockup -->
            <div style="position: relative; transform: rotate(-2.5deg)">
                <div class="card" style="position: relative; padding: 28px 28px 24px; border-radius: 14px">
                    <div style="display: flex; justify-content: space-between; align-items: center">
                        <span class="chip m">{{ $paket->eyebrow ?? 'Contoh tiket' }}</span>
                        <span class="mono" style="font-weight: 700; font-size: 14px">VLS-GF-0001</span>
                    </div>

                    <div class="disp" style="font-size: 30px; font-weight: 800; margin-top: 20px; line-height: 1.05">
                        Velosha Gran Fondo<br>Borobudur 2026
                    </div>

                    <div style="margin-top: 22px; border-top: 3px dashed #10282E; position: relative">
                        <i style="position: absolute; left: -41px; top: -15px; width: 28px; height: 28px; border-radius: 50%; background: #F8E3B5; border: 2.5px solid #10282E; display: block"></i>
                        <i style="position: absolute; right: -41px; top: -15px; width: 28px; height: 28px; border-radius: 50%; background: #F8E3B5; border: 2.5px solid #10282E; display: block"></i>
                    </div>

                    <div style="display: flex; gap: 20px; margin-top: 22px; align-items: center">
                        <svg width="104" height="104" viewBox="0 0 10 10" aria-label="Contoh kode QR" role="img" style="flex: none">
                            <rect width="10" height="10" fill="#fff"></rect>
                            <path fill="#10282E" d="M0 0h3v3H0zM7 0h3v3H7zM0 7h3v3H0zM4 0h1v1H4zM4 2h2v1H4zM5 4h1v1H5zM3 4h1v2H3zM7 4h2v1H7zM6 6h2v1H6zM8 7h2v1H8zM4 8h2v2H4zM7 9h1v1H7zM9 5h1v1H9z"></path>
                            <path fill="#fff" d="M1 1h1v1H1zM8 1h1v1H8zM1 8h1v1H1z"></path>
                        </svg>
                        <div>
                            <div class="mute" style="font-size: 12px; font-weight: 700; letter-spacing: .06em">NOMOR BIB</div>
                            <div class="disp" style="font-size: 44px; font-weight: 800; line-height: 1">0147</div>
                            <div class="mute" style="font-size: 13px; margin-top: 6px">Tunjukkan QR saat ambil race pack</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Grid Fitur Kartun + Kartu Flat Hitam Benefit -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px">
                @php
                    $colors = ['#F4BE3F', '#DE7119', '#D5EBD9', '#F2D3DC'];
                @endphp
                @foreach(($paket->features ?? []) as $idx => $f)
                <div class="card" style="padding: 22px; display: flex; gap: 16px; align-items: center">
                    <div style="width: 56px; height: 56px; flex: none; border: 2.5px solid #10282E; border-radius: 10px; background: {{ $colors[$idx % count($colors)] }}; display: flex; align-items: center; justify-content: center">
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
            <p class="lead" style="max-width: 400px; margin: 0; color: #10282E">Semua yang perlu kamu tahu sebelum start: aturan, bantuan, riwayat, dan hasil resmi.</p>
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

                <a href="/help" wire:navigate class="btn btn-block" style="position: relative; margin-top: auto">
                    Buka Pusat Bantuan
                </a>
            </div>

            <!-- Right 4 Regulation Hub Cards (.numo outline 01-04) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px">
                <!-- 01 Tentang Velosha (Butter #F8E3B5) -->
                <a href="/about" wire:navigate class="card racecard" style="background: #F8E3B5; padding: 26px; display: flex; flex-direction: column; gap: 18px; text-decoration: none; min-height: 210px">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start">
                        <div class="ico" style="background: #FFFDF8">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v6M12 7.500v.01"></path></svg>
                        </div>
                        <span class="disp numo">01</span>
                    </div>
                    <div>
                        <h3 class="h3" style="font-size: 24px">Tentang Velosha</h3>
                        <p style="margin: 6px 0 0; font-size: 15px; color: #10282E">Platform resmi maraton sepeda dan Gran Fondo Indonesia.</p>
                    </div>
                    <div style="margin-top: auto; display: flex; align-items: center; gap: 10px; font-weight: 700">
                        Buka
                        <span style="width: 28px; height: 28px; border: 2.5px solid #10282E; border-radius: 6px; background: #DE7119; display: inline-flex; align-items: center; justify-content: center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                        </span>
                    </div>
                </a>

                <!-- 02 Aturan & Kode Etik (Pine Light #D5EBD9) -->
                <a href="/rule" wire:navigate class="card racecard" style="background: #D5EBD9; padding: 26px; display: flex; flex-direction: column; gap: 18px; text-decoration: none; min-height: 210px">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start">
                        <div class="ico" style="background: #FFFDF8">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.500 8-8 9-4.500-1-8-4-8-9V6z"></path><path d="M9 12l2 2 4-4"></path></svg>
                        </div>
                        <span class="disp numo">02</span>
                    </div>
                    <div>
                        <h3 class="h3" style="font-size: 24px">Aturan &amp; kode etik</h3>
                        <p style="margin: 6px 0 0; font-size: 15px; color: #10282E">Helm wajib, etika peloton, penalti, dan diskualifikasi.</p>
                    </div>
                    <div style="margin-top: auto; display: flex; align-items: center; gap: 10px; font-weight: 700">
                        Buka
                        <span style="width: 28px; height: 28px; border: 2.5px solid #10282E; border-radius: 6px; background: #DE7119; display: inline-flex; align-items: center; justify-content: center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                        </span>
                    </div>
                </a>

                <!-- 03 Arsip Race History (Orange Light #F6B777) -->
                <a href="/riwayat" wire:navigate class="card racecard" style="background: #F6B777; padding: 26px; display: flex; flex-direction: column; gap: 18px; text-decoration: none; min-height: 210px">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start">
                        <div class="ico" style="background: #FFFDF8">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                        </div>
                        <span class="disp numo">03</span>
                    </div>
                    <div>
                        <h3 class="h3" style="font-size: 24px">Arsip race history</h3>
                        <p style="margin: 6px 0 0; font-size: 15px; color: #10282E">Daftar balapan yang pernah diselenggarakan.</p>
                    </div>
                    <div style="margin-top: auto; display: flex; align-items: center; gap: 10px; font-weight: 700">
                        Buka
                        <span style="width: 28px; height: 28px; border: 2.5px solid #10282E; border-radius: 6px; background: #FFFDF8; display: inline-flex; align-items: center; justify-content: center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                        </span>
                    </div>
                </a>

                <!-- 04 Ranking & Podium (Berry Light #F2D3DC) -->
                <a href="/ranking" wire:navigate class="card racecard" style="background: #F2D3DC; padding: 26px; display: flex; flex-direction: column; gap: 18px; text-decoration: none; min-height: 210px">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start">
                        <div class="ico" style="background: #FFFDF8">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4h10v5a5 5 0 0 1-10 0zM7 6H4v2a3 3 0 0 0 3 3M17 6h3v2a3 3 0 0 1-3 3M12 14v4M8 21h8"></path></svg>
                        </div>
                        <span class="disp numo">04</span>
                    </div>
                    <div>
                        <h3 class="h3" style="font-size: 24px">Ranking &amp; podium</h3>
                        <p style="margin: 6px 0 0; font-size: 15px; color: #10282E">Catatan waktu resmi per checkpoint RFID.</p>
                    </div>
                    <div style="margin-top: auto; display: flex; align-items: center; gap: 10px; font-weight: 700">
                        Buka
                        <span style="width: 28px; height: 28px; border: 2.5px solid #10282E; border-radius: 6px; background: #DE7119; display: inline-flex; align-items: center; justify-content: center">
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

<!-- BANNER PESEPEDA PEMISAH MENUJU FOOTER -->
<section class="wrap mt-20 mb-20">
    <div class="card bg-[#FFFDF8] text-[var(--ink)] p-10 sm:p-14 relative overflow-hidden border-[3px] border-[var(--ink)]">
        <div class="hidden lg:block absolute -right-4 -bottom-6 w-[520px] h-[400px] pointer-events-none z-10">
            <img src="/images/hero/rider_final.png" alt="" class="w-full h-full object-contain filter drop-shadow-[6px_6px_0_var(--ink)]">
        </div>

        <div class="relative z-20 max-w-xl">
            <span class="chip chip-mustard font-extrabold">SIAPKAN SEPEDA ANDA</span>
            <h2 class="h2" style="font-size: 52px !important; line-height: 1.05 !important; margin-top: 18px !important">
                Mulai petualangan rute berikutnya hari ini.
            </h2>
            <p class="lead" style="margin-top: 14px !important">
                Temukan jadwal race terdekat, ajak komunitas Anda, dan taklukkan kilometer demi kilometer.
            </p>
            <div style="margin-top: 32px">
                <a href="/events" wire:navigate class="btn btn-ink text-base">
                    <span>Eksplorasi Kalender Race</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection