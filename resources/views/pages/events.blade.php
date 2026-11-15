@extends('layouts.app', ['title' => 'Kalender Race — Velosha — Platform Maraton & Balap Sepeda Indonesia'])

@section('content')

<!-- HEADER / BANNER KALENDER RACE (Simple, Clean, Menggunakan var(--butter) Sesuai /about) -->
<section class="relative overflow-hidden pt-10 pb-20 sm:pt-16 sm:pb-28"
         style="background-color: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink);">
    {{-- Elemen Dekorasi Geometris Neobrutalism di Sisi Kanan (Preserve Aspect Ratio agar tidak gepeng & tidak menimpa teks) --}}
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

    <div class="wrap relative z-10 px-4 sm:px-6">
        <div class="max-w-3xl">
            <h1 class="font-heading font-black text-3xl sm:text-5xl md:text-6xl text-[var(--ink)] leading-[1.08] tracking-tight mb-3 sm:mb-5">
                Platform Registrasi Balap<br>Sepeda Indonesia
            </h1>
            <p class="text-sm sm:text-lg text-[var(--ink)]/85 leading-relaxed font-medium max-w-2xl">
                Kami membangun infrastruktur terpadu untuk endurance ride di Indonesia — mulai dari kurasi rute, timing chip RFID, hingga Race Village hari-H.
            </p>
        </div>
    </div>
</section>

<!-- Search, Filter Melayang & Katalog Grid: in-place via Livewire -->
@livewire('event-catalog')

@endsection
