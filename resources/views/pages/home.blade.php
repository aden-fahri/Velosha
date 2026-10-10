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

<!-- Route & Elevation -->
<section class="velo-reveal py-16 bg-white border-b border-veloborder" style="transition-delay: 160ms">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">

            <div class="md:col-span-5">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight leading-snug mb-4">
                    Rute Borobudur 130KM
                </h2>

                <div class="space-y-3">
                    @foreach([
                        ['1', 'Start Gate', 'Candi Borobudur · 05:45 WIB'],
                        ['2', 'Feed Zone', 'KM 35 &amp; KM 72'],
                        ['3', 'KOM Menoreh', 'KM 88–98 · Puncak 920m'],
                    ] as $cp)
                    <div class="flex items-start gap-3 bg-white p-3.5 border border-veloborder velo-box-1">
                        <div class="w-6 h-6 bg-race-600 text-white text-xs font-bold flex items-center justify-center velo-box-3 shrink-0">{{ $cp[0] }}</div>
                        <div>
                            <div class="text-xs font-bold text-teal-900">{!! $cp[1] !!}</div>
                            <div class="text-[11px] text-[#556066] mt-0.5">{!! $cp[2] !!}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="md:col-span-7">
                <div class="bg-white border border-veloborderstrong velo-box-1 overflow-hidden">
                    <div class="bg-velosurface px-4 py-2.5 border-b border-veloborder flex items-center gap-2 text-[11px] font-semibold text-teal-700">
                        <span class="w-1.5 h-1.5 bg-teal-500 velo-box-1 shrink-0"></span>
                        <i data-lucide="map" class="w-3.5 h-3.5 text-teal-700"></i>
                        <span>GPS Track Active</span>
                        <span class="ml-auto font-mono text-[#556066]">230m – 920m</span>
                    </div>
                    <div class="h-80 w-full relative">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63245.970876404394!2d110.1691244!3d-7.6078738!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a8cf009a7d305%3A0xb396a452337e6a60!2sBorobudur%20Temple!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full grayscale-[25%] contrast-110"></iframe>
                    </div>
                    <div class="px-4 py-3 bg-white border-t border-veloborder">
                        <svg viewBox="0 0 500 100" class="w-full h-14" fill="none" preserveAspectRatio="none">
                            <path d="M0,85 Q60,80 120,65 T240,40 T320,15 T400,60 T500,80 L500,100 L0,100 Z" fill="#DBEFEF"/>
                            <path d="M0,85 Q60,80 120,65 T240,40 T320,15 T400,60 T500,80" stroke="#116979" stroke-width="2"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Isi Paket Tiket -->
@php $paket = \App\Models\TicketPackageContent::current(); @endphp
<section class="velo-reveal py-16 bg-velosurfacealt border-b border-veloborder" style="transition-delay: 240ms">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight">{{ $paket->title }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $paketImages = [
                    'cpu' => 'paket/bib-rfid.png',
                    'award' => 'paket/medali.png',
                    'shield-plus' => 'paket/asuransi.png',
                    'utensils' => 'paket/feedzone.png',
                ];
            @endphp
            @foreach (($paket->features ?? []) as $f)
            <div class="p-6 border border-veloborder bg-white velo-box-1 flex flex-col items-center text-center group hover:border-teal-700 transition">
                @if (isset($paketImages[$f['icon'] ?? '']))
                    <img src="{{ asset('images/' . $paketImages[$f['icon']]) }}"
                         alt="{{ $f['title'] ?? '' }}" width="80" height="80" loading="lazy"
                         class="w-20 h-20 object-contain mb-4">
                @else
                    <div class="w-10 h-10 bg-teal-50 text-teal-700 flex items-center justify-center velo-box-3 mb-4">
                        <i data-lucide="{{ $f['icon'] ?? 'check' }}" class="w-5 h-5"></i>
                    </div>
                @endif
                <h4 class="text-sm font-bold text-teal-900 mb-1">{!! $f['title'] ?? '' !!}</h4>
                <p class="text-xs text-[#556066] leading-relaxed">{!! $f['desc'] ?? '' !!}</p>
            </div>
            @endforeach
        </div>

        <div class="mt-6 bg-white border border-veloborder velo-box-1 p-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-teal-800 mb-5">{{ $paket->benefits_title }}</h3>
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6">
                @foreach (($paket->benefits ?? []) as $b)
                <li class="flex items-start gap-2.5 text-xs text-[#4b555b] py-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5 text-velogreen shrink-0 mt-0.5"></i>
                    <span>{!! $b['text'] ?? '' !!}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

<!-- Panduan & Pusat Informasi Balap (About, Rule, Riwayat, Ranking, Help) -->
<section class="velo-reveal py-16 bg-white border-b border-veloborder" style="transition-delay: 280ms">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight">Informasi &amp; Regulasi Peserta</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- About -->
            <div class="p-6 border border-veloborder bg-white velo-box-1 flex flex-col items-center text-center group hover:border-teal-700 transition">
                <div class="flex-1 flex flex-col items-center">
                    <img src="{{ asset('images/guides/about.png') }}" alt="Ilustrasi sepeda balap" width="80" height="80" loading="lazy" class="w-20 h-20 object-contain mb-4">
                    <h3 class="text-base font-extrabold text-teal-900 mb-2">Tentang Velosha</h3>
                    <p class="text-xs text-[#556066] leading-relaxed mb-6">
                        Platform kurasi resmi maraton sepeda dan Gran Fondo Indonesia berstandar timing chip RFID dan komisioner balap.
                    </p>
                </div>
                <a href="/about" wire:navigate class="inline-flex items-center text-xs font-bold text-teal-800 group-hover:text-race-600 transition">
                    <span>Pelajari Selengkapnya</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>

            <!-- Rule -->
            <div class="p-6 border border-veloborder bg-white velo-box-1 flex flex-col items-center text-center group hover:border-teal-700 transition">
                <div class="flex-1 flex flex-col items-center">
                    <img src="{{ asset('images/guides/rules.png') }}" alt="Ilustrasi helm sepeda" width="80" height="80" loading="lazy" class="w-20 h-20 object-contain mb-4">
                    <h3 class="text-base font-extrabold text-teal-900 mb-2">Aturan &amp; Kode Etik</h3>
                    <p class="text-xs text-[#556066] leading-relaxed mb-6">
                        Regulasi keselamatan wajib, perlengkapan sepeda standar, etika peloton, dan sanksi diskualifikasi resmi.
                    </p>
                </div>
                <a href="/rule" wire:navigate class="inline-flex items-center text-xs font-bold text-teal-800 group-hover:text-race-600 transition">
                    <span>Lihat Aturan Lengkap</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>

            <!-- Riwayat -->
            <div class="p-6 border border-veloborder bg-white velo-box-1 flex flex-col items-center text-center group hover:border-teal-700 transition">
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-20 h-20 border border-veloborder velo-box-1 bg-velosurface flex items-center justify-center mb-4">
                        <i data-lucide="history" class="w-9 h-9 text-teal-700"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-teal-900 mb-2">Riwayat Balap</h3>
                    <p class="text-xs text-[#556066] leading-relaxed mb-6">
                        Arsip race yang sudah digelar dan jejak pendaftaranmu: status bayar, nomor BIB, dan e-ticket dalam satu tempat.
                    </p>
                </div>
                <a href="/riwayat" wire:navigate class="inline-flex items-center text-xs font-bold text-teal-800 group-hover:text-race-600 transition">
                    <span>Lihat Riwayat Balap</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>

            <!-- Ranking -->
            <div class="p-6 border border-veloborder bg-white velo-box-1 flex flex-col items-center text-center group hover:border-teal-700 transition">
                <div class="flex-1 flex flex-col items-center">
                    <img src="{{ asset('images/guides/ranking.png') }}" alt="Ilustrasi podium juara dan medali" width="80" height="80" loading="lazy" class="w-20 h-20 object-contain mb-4">
                    <h3 class="text-base font-extrabold text-teal-900 mb-2">Ranking &amp; Leaderboard</h3>
                    <p class="text-xs text-[#556066] leading-relaxed mb-6">
                        Hasil resmi catatan waktu RFID per checkpoint, podium juara per kategori, dan catatan kecepatan rata-rata.
                    </p>
                </div>
                <a href="/ranking" wire:navigate class="inline-flex items-center text-xs font-bold text-teal-800 group-hover:text-race-600 transition">
                    <span>Lihat Leaderboard</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>

            <!-- Help Center -->
            <div class="p-6 border border-veloborder bg-white velo-box-1 flex flex-col items-center text-center group hover:border-teal-700 transition lg:col-span-2">
                <div class="flex-1 flex flex-col items-center">
                    <img src="{{ asset('images/guides/help.png') }}" alt="Ilustrasi gelembung chat bantuan" width="80" height="80" loading="lazy" class="w-20 h-20 object-contain mb-4">
                    <h3 class="text-base font-extrabold text-teal-900 mb-2">Help Center &amp; Bantuan</h3>
                    <p class="text-xs text-[#556066] leading-relaxed mb-6">
                        Panduan pembayaran, pengambilan Race Pack (RPC), transfer BIB, asuransi, serta kontak WhatsApp panitia.
                    </p>
                </div>
                <a href="/help" wire:navigate class="inline-flex items-center text-xs font-bold text-teal-800 group-hover:text-race-600 transition">
                    <span>Buka Pusat Bantuan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection