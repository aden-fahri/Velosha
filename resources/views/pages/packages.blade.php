@extends('layouts.app', ['title' => 'Paket Kuota & Harga — Velosha — Platform Balap Sepeda'])

@section('content')

{{-- PAGE HEADER RESMI: Konsisten dengan Kalender, Tentang, Regulasi, & Bantuan --}}
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
        <div class="max-w-3xl">
            <h1 class="font-heading font-black text-4xl sm:text-5xl md:text-6xl text-[var(--ink)] leading-[1.08] tracking-tight mb-4">
                Paket Kuota Event Balap Sepeda
            </h1>
            <p class="text-base sm:text-lg text-[var(--ink)]/85 leading-relaxed font-medium max-w-2xl">
                Beli kuota sekali, gunakan kapan saja tanpa kedaluwarsa. Termasuk sistem checkout tiket QR Midtrans, registrasi BIB otomatis, laporan buku kas, dan pencairan dana langsung ke rekening.
            </p>

            @auth
                @php
                    $userOrg = auth()->user()->organizations()->first() ?? auth()->user()->ownedOrganizations()->first();
                @endphp
                @if ($userOrg)
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl border-2 border-[var(--ink)] bg-white shadow-[3px_3px_0_var(--ink)] text-xs font-bold mt-5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--pine)] inline-block"></span>
                        <span>Akun Komunitas Aktif: <strong>{{ $userOrg->name }}</strong></span>
                        <span class="text-[var(--mute)]">&bull;</span>
                        <span class="text-[var(--pine)]">Saldo: {{ $userOrg->quotaBalance() }} Kuota Race</span>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</section>

{{-- KONTEN UTAMA: 3 KARTU HARGA & FAQ DENGAN JARAK LEGA --}}
<section class="py-14 sm:py-20 px-4 sm:px-6 lg:px-8 bg-[var(--paper)] min-h-[calc(100vh-280px)]">
    <div class="max-w-6xl mx-auto space-y-20">

        {{-- 3 Kartu Paket Kuota --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
            @foreach ($packages as $pkg)
                @php
                    $isPopular = $pkg->events_count === 3;
                @endphp
                <div class="flex flex-col justify-between rounded-2xl border-[3px] border-[var(--ink)] {{ $isPopular ? 'bg-[#10282E] text-white shadow-[8px_8px_0_#F4BE3F] ring-2 ring-[var(--ink)] md:-translate-y-3' : 'bg-white text-[var(--ink)] shadow-[5px_5px_0_var(--ink)]' }} p-7 sm:p-8 transition-transform duration-200">
                    
                    <div class="space-y-6">
                        {{-- Top Badge / Label Row --}}
                        <div class="flex items-center justify-between gap-2 min-h-[32px]">
                            @if ($isPopular)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[var(--orange)] text-white border border-white/20 shadow-xs">
                                    ★ Pilihan Terpopuler
                                </span>
                            @else
                                <span class="text-[11px] font-extrabold uppercase tracking-wider {{ $isPopular ? 'text-white/60' : 'text-[var(--mute)]' }}">
                                    Paket Penyelenggara
                                </span>
                            @endif

                            <span class="px-2.5 py-1 text-xs font-black border-2 border-[var(--ink)] rounded-lg {{ $isPopular ? 'bg-[var(--mustard)] text-[var(--ink)]' : 'bg-[var(--butter)] text-[var(--ink)]' }} shadow-[2px_2px_0_var(--ink)] shrink-0">
                                {{ $pkg->events_count }} Race
                            </span>
                        </div>

                        {{-- Title & Description --}}
                        <div>
                            <h2 class="text-2xl sm:text-3xl font-black tracking-tight {{ $isPopular ? 'text-white' : 'text-[var(--ink)]' }}" style="font-family: 'Bricolage Grotesque', sans-serif">
                                {{ $pkg->name }}
                            </h2>
                            <p class="text-xs leading-relaxed mt-2 min-h-[40px] font-medium {{ $isPopular ? 'text-white/75' : 'text-[var(--mute)]' }}">
                                {{ $pkg->description }}
                            </p>
                        </div>

                        {{-- Pricing Section --}}
                        <div class="border-y-2 {{ $isPopular ? 'border-white/15' : 'border-[var(--line)]' }} py-4">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xs font-black uppercase {{ $isPopular ? 'text-white/60' : 'text-[var(--mute)]' }}">Rp</span>
                                <span class="text-3xl sm:text-4xl font-black tracking-tight {{ $isPopular ? 'text-[var(--mustard)]' : 'text-[var(--ink)]' }}" style="font-family: 'Bricolage Grotesque', sans-serif">
                                    {{ number_format($pkg->price, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="text-[11px] font-bold mt-1.5 flex items-center gap-1.5 {{ $isPopular ? 'text-[#85E3B3]' : 'text-[var(--pine)]' }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Masa aktif selamanya (tanpa kedaluwarsa)</span>
                            </div>
                        </div>

                        {{-- Fitur Paket --}}
                        <ul class="space-y-3.5 text-xs font-semibold {{ $isPopular ? 'text-white/90' : 'text-[var(--ink)]' }}">
                            <li class="flex items-center gap-3">
                                <span class="w-5 h-5 rounded-full {{ $isPopular ? 'bg-[var(--orange)] text-white' : 'bg-[var(--pine)] text-white' }} flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Publikasi <strong>{{ $pkg->events_count }} Event Balap</strong> di kalender resmi</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-5 h-5 rounded-full {{ $isPopular ? 'bg-[var(--orange)] text-white' : 'bg-[var(--pine)] text-white' }} flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Penomoran BIB &amp; QR E-Ticket instan</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-5 h-5 rounded-full {{ $isPopular ? 'bg-[var(--orange)] text-white' : 'bg-[var(--pine)] text-white' }} flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Payment gateway Midtrans (QRIS, VA, Kartu)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-5 h-5 rounded-full {{ $isPopular ? 'bg-[var(--orange)] text-white' : 'bg-[var(--pine)] text-white' }} flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Buku kas &amp; pencairan saldo (*payout*) bank</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-5 h-5 rounded-full {{ $isPopular ? 'bg-[var(--orange)] text-white' : 'bg-[var(--pine)] text-white' }} flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Dukungan teknis &amp; jalur prioritas operator</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Tombol Tindakan --}}
                    <div class="pt-8 mt-auto">
                        @auth
                            @php
                                $userOrg = auth()->user()->organizations()->first() ?? auth()->user()->ownedOrganizations()->first();
                            @endphp
                            @if ($userOrg)
                                <form action="{{ route('organizations.quota.purchase', $userOrg) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider {{ $isPopular ? 'bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white shadow-[3px_3px_0_#fff]' : 'bg-[var(--mustard)] hover:bg-[var(--butter)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)]' }} hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
                                        <span>Beli Kuota ({{ $userOrg->name }}) &rarr;</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('organizations.create', ['package' => $pkg->id]) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider {{ $isPopular ? 'bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white shadow-[3px_3px_0_#fff]' : 'bg-[var(--mustard)] hover:bg-[var(--butter)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)]' }} hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-center">
                                    <span>Daftarkan Komunitas &amp; Beli &rarr;</span>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('organizations.create', ['package' => $pkg->id]) }}"
                               class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider {{ $isPopular ? 'bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white shadow-[3px_3px_0_#fff]' : 'bg-[var(--mustard)] hover:bg-[var(--butter)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)]' }} hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-center">
                                <span>Mulai Jadi Penyelenggara &rarr;</span>
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        {{-- FAQ Penyelenggara (Berjarak Lega dengan Kontras Neobrutalism) --}}
        <div class="mt-20 bg-white border-[3px] border-[var(--ink)] rounded-2xl p-8 sm:p-12 shadow-[6px_6px_0_var(--ink)] max-w-4xl mx-auto space-y-8">
            <div class="border-b-2 border-[var(--ink)] pb-4">
                <span class="text-[11px] font-black uppercase tracking-wider text-[var(--orange)]">Pusat Informasi</span>
                <h3 class="text-2xl sm:text-3xl font-black text-[var(--ink)] mt-1" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Pertanyaan Umum Penyelenggara Velosha
                </h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-xs leading-relaxed text-[var(--ink)]">
                <div class="space-y-2 p-4 rounded-xl bg-[var(--paper)] border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                    <h4 class="font-black text-sm text-[var(--ink)]">Kapan kuota event saya terpotong?</h4>
                    <p class="text-[var(--mute)] font-medium">
                        Kuota terpotong 1 saat event dipublikasikan (*published*). Membuat rancangan draf atau preview tidak memotong kuota.
                    </p>
                </div>
                <div class="space-y-2 p-4 rounded-xl bg-[var(--paper)] border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                    <h4 class="font-black text-sm text-[var(--ink)]">Bagaimana hasil penjualan tiket dicairkan?</h4>
                    <p class="text-[var(--mute)] font-medium">
                        Setiap tiket yang dibayar peserta langsung masuk ke buku kas (*financial ledger*). Anda dapat mengajukan pencairan dana (*payout*) kapan saja ke rekening bank.
                    </p>
                </div>
                <div class="space-y-2 p-4 rounded-xl bg-[var(--paper)] border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                    <h4 class="font-black text-sm text-[var(--ink)]">Apakah kuota memiliki batas waktu?</h4>
                    <p class="text-[var(--mute)] font-medium">
                        Tidak ada masa kedaluwarsa. Kuota yang Anda miliki berlaku selamanya hingga seluruh event selesai digelar.
                    </p>
                </div>
                <div class="space-y-2 p-4 rounded-xl bg-[var(--paper)] border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                    <h4 class="font-black text-sm text-[var(--ink)]">Bagaimana alur dari peserta menjadi penyelenggara?</h4>
                    <p class="text-[var(--mute)] font-medium">
                        Daftarkan nama komunitas/EO Anda lalu beli paket kuota. Akun Anda otomatis memiliki akses Dasbor Penyelenggara.
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection