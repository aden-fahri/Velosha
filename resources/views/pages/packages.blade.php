@extends('layouts.app', ['title' => 'Paket Kuota & Harga — Velosha SaaS'])

@section('content')
<section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 bg-[var(--paper)] min-h-[calc(100vh-140px)]">
    <div class="max-w-6xl mx-auto space-y-12">

        {{-- Hero Header --}}
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <span class="inline-block px-3.5 py-1 bg-[var(--mustard)] border-2 border-[var(--ink)] font-extrabold text-xs uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)]">
                Penyelenggara &bull; SaaS Platform
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[var(--ink)] tracking-tight leading-tight" style="font-family: 'Bricolage Grotesque', sans-serif">
                Pilih Paket Kuota Event untuk Komunitas & EO Anda
            </h1>
            <p class="text-[var(--mute)] text-sm sm:text-base leading-relaxed">
                Platform pendaftaran balap sepeda lengkap dengan tiket QR, penomoran BIB otomatis, sistem pembayaran resmi Midtrans, hingga laporan pembukuan finansial dan pencairan dana.
            </p>
        </div>

        {{-- 3 Kartu Paket --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch pt-4">
            @foreach ($packages as $pkg)
                @php
                    $isPopular = $pkg->events_count === 3;
                @endphp
                <div class="relative flex flex-col justify-between p-8 rounded-2xl border-[3px] border-[var(--ink)] {{ $isPopular ? 'bg-[var(--card)] shadow-[8px_8px_0_var(--ink)] ring-2 ring-[var(--orange)] -translate-y-2' : 'bg-[var(--card)] shadow-[5px_5px_0_var(--ink)]' }} transition-transform duration-200">
                    
                    @if ($isPopular)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-[var(--orange)] text-[var(--paper)] border-2 border-[var(--ink)] text-[11px] font-black uppercase px-4 py-1 rounded-full shadow-[2px_2px_0_var(--ink)] tracking-wider">
                            Paling Populer &bull; Hemat
                        </div>
                    @endif

                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                                    {{ $pkg->name }}
                                </h2>
                                <span class="px-2.5 py-0.5 text-xs font-extrabold border-2 border-[var(--ink)] rounded-md bg-[var(--paper)] shadow-[1.5px_1.5px_0_var(--ink)]">
                                    {{ $pkg->events_count }} Race
                                </span>
                            </div>
                            <p class="text-xs text-[var(--mute)] leading-relaxed min-h-[38px]">
                                {{ $pkg->description }}
                            </p>
                        </div>

                        <div class="border-y-2 border-[var(--line)] py-4">
                            <div class="flex items-baseline gap-1">
                                <span class="text-sm font-bold text-[var(--mute)]">Rp</span>
                                <span class="text-3xl sm:text-4xl font-black text-[var(--ink)] font-mono">
                                    {{ number_format($pkg->price, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="text-[11px] font-bold text-[var(--pine)] mt-1 flex items-center gap-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Tanpa batas masa aktif (berlaku selamanya)</span>
                            </div>
                        </div>

                        {{-- Fitur Paket --}}
                        <ul class="space-y-3 text-xs font-semibold text-[var(--ink)]">
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span><strong>{{ $pkg->events_count }} Event</strong> aktif di kalender balap</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Penerbitan nomor BIB & e-ticket QR otomatis</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Integrasi pembayaran QRIS, VA, & Kartu Midtrans</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Dasbor keuangan & pencairan dana bank (*payout*)</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                                <span>Pemindai QR petugas lapangan di hari-H lomba</span>
                            </li>
                        </ul>
                    </div>

                    {{-- CTA Tombol --}}
                    <div class="pt-8">
                        @auth
                            @php
                                $userOrg = auth()->user()->organizations()->first() ?? auth()->user()->ownedOrganizations()->first();
                            @endphp
                            @if ($userOrg)
                                <a href="{{ route('organizations.quota', $userOrg) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-[2.5px] border-[var(--ink)] font-black text-sm text-[var(--paper)] bg-[var(--ink)] hover:bg-[var(--pine)] shadow-[3px_3px_0_var(--ink)] transition-all">
                                    <span>Beli untuk {{ $userOrg->name }}</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>
                            @else
                                <a href="{{ route('organizations.create') }}"
                                   class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-[2.5px] border-[var(--ink)] font-black text-sm text-[var(--paper)] bg-[var(--ink)] hover:bg-[var(--pine)] shadow-[3px_3px_0_var(--ink)] transition-all">
                                    <span>Daftarkan Komunitas & Beli</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('organizations.create') }}"
                               class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-[2.5px] border-[var(--ink)] font-black text-sm text-[var(--paper)] {{ $isPopular ? 'bg-[var(--orange)] text-[var(--ink)] hover:bg-[var(--mustard)]' : 'bg-[var(--ink)] text-[var(--paper)] hover:bg-[var(--pine)]' }} shadow-[3px_3px_0_var(--ink)] transition-all">
                                <span>Mulai Jadi Penyelenggara</span>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        {{-- FAQ Ringkas --}}
        <div class="mt-16 bg-[var(--card)] border-[3px] border-[var(--ink)] rounded-2xl p-8 sm:p-10 shadow-[6px_6px_0_var(--ink)] max-w-4xl mx-auto space-y-6">
            <h3 class="text-xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                Pertanyaan Umum Seputar Penyelenggara Velosha
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs leading-relaxed text-[var(--ink)]">
                <div class="space-y-1.5">
                    <h4 class="font-extrabold text-sm text-[var(--ink)]">Kapan kuota event saya terpotong?</h4>
                    <p class="text-[var(--mute)]">
                        Kuota hanya berkurang 1 saat event disetujui moderasi dan dipublikasikan (*published*). Membuat draf atau menyimpan rancangan rute tidak memotong kuota.
                    </p>
                </div>
                <div class="space-y-1.5">
                    <h4 class="font-extrabold text-sm text-[var(--ink)]">Bagaimana uang hasil penjualan tiket dicairkan?</h4>
                    <p class="text-[var(--mute)]">
                        Uang tiket otomatis dicatat ke buku kas (*financial ledger*) organisasi Anda. Anda dapat mengajukan pencairan dana (*payout*) kapan saja ke rekening bank terdaftar.
                    </p>
                </div>
                <div class="space-y-1.5">
                    <h4 class="font-extrabold text-sm text-[var(--ink)]">Apakah kuota memiliki masa kedaluwarsa?</h4>
                    <p class="text-[var(--mute)]">
                        Tidak ada. Kuota yang sudah dibeli berlaku selamanya (*no expiry date*) hingga seluruh event selesai dipublikasikan.
                    </p>
                </div>
                <div class="space-y-1.5">
                    <h4 class="font-extrabold text-sm text-[var(--ink)]">Bisakah satu organisasi dikelola bersama tim?</h4>
                    <p class="text-[var(--mute)]">
                        Bisa. Organisasi mendukung multi-anggota dengan hak akses Owner, Manager, dan Staf Lapangan untuk memindai tiket peserta di hari-H.
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
