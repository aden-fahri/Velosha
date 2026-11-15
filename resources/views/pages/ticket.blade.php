@use('App\Support\EventCardPresenter', 'P')
@extends('layouts.app', ['title' => 'E-Ticket & Pembayaran Berhasil — Velosha'])

@section('content')
<div class="vs page pb-16 min-h-[calc(100vh-140px)] bg-[#F3E7CF] relative">

    @if ($registration === null || $registration->category === null || $registration->category->event === null)

        <!-- PAGE HEADER: Belum Ada Pendaftaran (Konsisten dengan Kalender, Tentang, Regulasi, & Bantuan) -->
        <section class="relative overflow-hidden" style="background: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink, #10282E); padding: 52px 0;">
            <div class="wrap relative z-10">
                <div class="flex items-center gap-2.5 mb-3">
                    <span class="chip font-bold bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">E-Ticket &amp; Pendaftaran</span>
                </div>
                <h1 class="font-heading font-black text-4xl sm:text-5xl md:text-6xl text-[var(--ink)] leading-[1.08] tracking-tight mb-4">
                    Belum Ada E-Ticket Aktif
                </h1>
                <p class="text-base sm:text-lg text-[var(--ink)]/85 leading-relaxed font-medium max-w-2xl">
                    Anda belum terdaftar pada race manapun. Pilih event di kalender balap untuk memesan tiket dan menerbitkan nomor BIB resmi.
                </p>
                <div class="mt-6">
                    <a href="{{ route('events.index') }}" wire:navigate.hover class="btn btn-sm btn-pine">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                        <span>Jelajahi Kalender Balap</span>
                    </a>
                </div>
            </div>
        </section>

    @elseif (! $registration->isPaid())

        <!-- PAGE HEADER: Menunggu Pembayaran (Konsisten dengan Kalender, Tentang, Regulasi, & Bantuan) -->
        <section class="relative overflow-hidden" style="background: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink, #10282E); padding: 52px 0;">
            <div class="wrap relative z-10">
                <div class="flex items-center gap-2.5 mb-3 flex-wrap">
                    <span class="chip font-bold bg-[var(--orange)] text-white border border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">Menunggu Pembayaran</span>
                    <span class="chip bg-white border border-[var(--ink)] font-mono text-xs shadow-[2px_2px_0_var(--ink)]">REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h1 class="font-heading font-black text-4xl sm:text-5xl md:text-6xl text-[var(--ink)] leading-[1.08] tracking-tight mb-4">
                    Pendaftaran Belum Lunas
                </h1>
                <p class="text-base sm:text-lg text-[var(--ink)]/85 leading-relaxed font-medium max-w-2xl">
                    Selesaikan pembayaran untuk mengunci slot race, alokasi nomor BIB resmi, dan penerbitan E-Ticket Anda.
                </p>
            </div>
        </section>

        <div class="max-w-3xl mx-auto px-4 mt-8 relative z-10">
            <div class="card p-6 sm:p-8 bg-[#FFFDF8]">
                <dl class="space-y-3.5 text-sm">
                    <div class="flex justify-between gap-4 border-b border-dashed border-[var(--ink)] pb-2.5">
                        <dt class="text-[var(--mute)] font-medium">Nama Peserta</dt>
                        <dd class="font-bold text-[var(--ink)] text-right">{{ $registration->participant_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-dashed border-[var(--ink)] pb-2.5">
                        <dt class="text-[var(--mute)] font-medium">Kategori Jarak</dt>
                        <dd class="font-bold text-[var(--ink)] text-right">{{ $registration->category->name }} ({{ P::distance($registration->category->distance_km) }})</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-dashed border-[var(--ink)] pb-2.5">
                        <dt class="text-[var(--mute)] font-medium">Event</dt>
                        <dd class="font-bold text-[var(--ink)] text-right">{{ $registration->category->event->title }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-dashed border-[var(--ink)] pb-2.5">
                        <dt class="text-[var(--mute)] font-medium">Nomor Pendaftaran</dt>
                        <dd class="font-mono font-bold text-[var(--ink)] text-right">REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 pt-1">
                        <dt class="text-[var(--mute)] font-medium">Status</dt>
                        <dd><span class="chip chip-orange font-bold">PENDING</span></dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('checkout', ['event' => $registration->category->event->slug, 'category' => $registration->category->id]) }}" wire:navigate.hover
                       class="btn flex-1 text-center justify-center">
                        <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                        <span>Bayar Sekarang</span>
                    </a>
                    <a href="{{ route('events.index') }}" wire:navigate.hover
                       class="btn btn-ghost flex-1 text-center justify-center">
                        Lihat Event Lain
                    </a>
                </div>
            </div>
        </div>

    @else

        @php
            $category = $registration->category;
            $event = $category->event;
            $ticket = $registration->ticket;
            $waveParts = array_map('trim', explode(' - ', (string) $category->wave_start_time, 2));
            $waveName = $waveParts[0] !== '' ? $waveParts[0] : (string) $category->wave_start_time;
            $waveTime = $waveParts[1] ?? '05.00';
            $qrData = $ticket?->qr_code_data ?? '';
            $bibSuffix = filled($registration->bib_number) ? substr((string) $registration->bib_number, -4) : '0147';
            $scanCode = strtoupper(substr(hash('sha256', $qrData !== '' ? $qrData : (string) $registration->id), 0, 4)).'-'.$bibSuffix.'-VLS';
        @endphp

        <!-- PAGE HEADER: Pembayaran Berhasil & E-Ticket (Konsisten dengan Kalender, Tentang, Regulasi, & Bantuan) -->
        <section class="relative overflow-hidden no-print" style="background: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink, #10282E); padding: 52px 0;">
            <div class="wrap relative z-10">
                <div class="flex items-center gap-2.5 mb-3 flex-wrap">
                    <span class="chip font-bold bg-[var(--pine)] text-white border border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                        {{ $registration->status === \App\Models\Registration::STATUS_CHECKED_IN ? 'Sudah Check-In' : 'Pembayaran Berhasil' }}
                    </span>
                    <span class="chip bg-white border border-[var(--ink)] font-mono text-xs shadow-[2px_2px_0_var(--ink)]">
                        Order #VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="chip font-bold bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                        {{ $event->title }}
                    </span>
                </div>
                <h1 class="font-heading font-black text-4xl sm:text-5xl md:text-6xl text-[var(--ink)] leading-[1.08] tracking-tight mb-4">
                    E-Ticket &amp; BIB Resmi
                </h1>
                <p class="text-base sm:text-lg text-[var(--ink)]/85 leading-relaxed font-medium max-w-2xl mb-6">
                    Tiket balap dan nomor BIB RFID kamu sudah aktif. Tunjukkan QR code di bawah saat pengambilan Racepack di {{ $event->location_name }}.
                </p>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 flex-wrap">
                    <a href="#eticket" class="btn btn-sm btn-pine justify-center">
                        <i data-lucide="ticket" class="w-4 h-4 mr-1"></i>
                        <span>Lihat E-Ticket &amp; QR</span>
                    </a>
                    <a href="{{ route('ticket.bib', $registration) }}" target="_blank" class="btn btn-sm btn-orange font-black justify-center">
                        <i data-lucide="tag" class="w-4 h-4 mr-1"></i>
                        <span>Cetak Nomor BIB</span>
                    </a>
                    <a href="{{ route('ticket.download', $registration) }}" target="_blank" class="btn btn-sm btn-mustard font-black justify-center">
                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                        <span>Unduh Paspor PDF</span>
                    </a>
                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ rawurlencode('Velosha '.$event->title) }}&dates={{ $event->event_date->format('Ymd') }}T050000Z/{{ $event->event_date->format('Ymd') }}T130000Z&details={{ rawurlencode('Maraton Sepeda '.P::distance($category->distance_km).' Velosha. BIB: '.($registration->bib_number ?? '-')) }}&location={{ rawurlencode($event->location_name) }}"
                       target="_blank"
                       class="btn btn-sm btn-ghost justify-center">
                        <i data-lucide="calendar-plus" class="w-4 h-4 mr-1"></i>
                        <span>Tambah ke Google Calendar</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- SELEKTOR MULTI-TIKET MODERN: Tampil jika user memiliki lebih dari 1 tiket / kategori --}}
        @if (isset($userRegistrations) && $userRegistrations->count() > 1)
        <section class="border-b-[3px] border-[var(--ink)] bg-[#FFFDF8] py-4 px-4 sm:px-8 no-print shadow-xs">
            <div class="wrap">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-[var(--orange)] border border-[var(--ink)]"></span>
                        <span class="font-heading font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                            Pilih Tiket &amp; Kategori Race ({{ $userRegistrations->count() }} Tiket Terdaftar)
                        </span>
                    </div>
                    <span class="text-[11px] font-bold text-[var(--mute)]">
                        Geser horizontal untuk memilih tiket &rarr;
                    </span>
                </div>

                {{-- Tab Navigasi Karcis Balap Kompak --}}
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin">
                    @foreach ($userRegistrations as $item)
                        @php
                            $isCurrent = $item->id === $registration->id;
                            $itemCat = $item->category;
                            $itemEvent = $itemCat?->event;
                        @endphp
                        @if ($itemCat && $itemEvent)
                            <a href="{{ route('ticket.show', $item) }}"
                               wire:navigate.hover
                               class="shrink-0 flex items-center gap-3 px-4 py-2.5 rounded-xl border-[2.5px] border-[var(--ink)] transition-all cursor-pointer {{ $isCurrent ? 'bg-[var(--mustard)] shadow-[3px_3px_0_var(--ink)] text-[var(--ink)] -translate-y-0.5' : 'bg-white hover:bg-[var(--paper)] text-[var(--ink)] hover:shadow-[2px_2px_0_var(--ink)]' }}">
                                <div class="w-8 h-8 rounded-lg border-2 border-[var(--ink)] flex items-center justify-center font-black text-xs shrink-0 {{ $isCurrent ? 'bg-[var(--orange)] text-white' : 'bg-[var(--paper)] text-[var(--ink)]' }}">
                                    #{{ $loop->iteration }}
                                </div>
                                <div class="flex flex-col text-left">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-xs sm:text-sm truncate max-w-[200px]">{{ $itemEvent->title }}</span>
                                        @if ($isCurrent)
                                            <span class="px-1.5 py-0.5 text-[8px] font-black uppercase tracking-wider bg-[#1F6F4A] text-white rounded border border-[var(--ink)]">Aktif</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-bold text-[var(--mute)] mt-0.5">
                                        {{ $itemCat->name }} &bull; <span class="font-mono font-black text-[var(--ink)]">BIB {{ $item->bib_number ?? 'Pending' }}</span>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- KONTEN UTAMA: 2-COLUMN RINGKASAN & TIMELINE LANGKAH BERIKUTNYA -->
        <section class="sec no-print" style="padding: 56px 0 40px; position: relative">
            <div class="wrap grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Kiri: Ringkasan Pesanan Card -->
                <div class="card lg:col-span-7" style="padding: 32px; background: #FFFDF8">
                    <div class="mute" style="font-size: 13px; font-weight: 700; letter-spacing: .08em">RINGKASAN PESANAN</div>
                    <h2 class="h3" style="font-size: 30px; margin: 6px 0 10px">{{ $event->title }}</h2>

                    <div style="display: flex; justify-content: space-between; gap: 20px; padding: 13px 0; font-size: 15px; border-bottom: 2px dashed #E2D3B3">
                        <span class="mute">Event</span>
                        <b style="text-align: right">{{ $event->title }}</b>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 20px; padding: 13px 0; font-size: 15px; border-bottom: 2px dashed #E2D3B3">
                        <span class="mute">Tanggal</span>
                        <b style="text-align: right">{{ P::tanggalLengkap($event->event_date) }} · {{ $event->location_name }}</b>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 20px; padding: 13px 0; font-size: 15px; border-bottom: 2px dashed #E2D3B3">
                        <span class="mute">Kategori</span>
                        <b style="text-align: right">{{ $category->name }} · {{ P::distance($category->distance_km) }}</b>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 20px; padding: 13px 0; font-size: 15px; border-bottom: 2px dashed #E2D3B3">
                        <span class="mute">Peserta</span>
                        <b style="text-align: right">{{ $registration->participant_name }} · BIB {{ $registration->bib_number }}</b>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 20px; padding: 13px 0; font-size: 15px; border-bottom: 2px dashed #E2D3B3">
                        <span class="mute">Metode bayar</span>
                        <b style="text-align: right">QRIS / Online · Terverifikasi Lunas</b>
                    </div>

                    <div style="display: flex; justify-content: space-between; padding-top: 16px; align-items: baseline">
                        <span class="disp" style="font-size: 20px; font-weight: 700">Total dibayar</span>
                        <b class="disp" style="font-size: 28px; color: var(--ink)">
                            Rp {{ number_format($category->price ?? 200000, 0, ',', '.') }}
                        </b>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 26px; flex-wrap: wrap" class="no-print">
                        <a href="{{ route('ticket.bib', $registration) }}" target="_blank" class="btn btn-sm btn-orange font-black">
                            <i data-lucide="tag" class="w-4 h-4 mr-1.5"></i>
                            <span>Cetak Nomor BIB</span>
                        </a>
                        <a href="{{ route('ticket.download', $registration) }}" target="_blank" class="btn btn-sm btn-mustard font-black">
                            <i data-lucide="download" class="w-4 h-4 mr-1.5"></i>
                            <span>Unduh Paspor PDF (A4)</span>
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-sm btn-ghost">
                            <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i>
                            <span>Cetak e-ticket</span>
                        </button>
                    </div>
                </div>

                <!-- Kanan: Langkah Berikutnya Timeline + Box Siap Pindai -->
                <div class="lg:col-span-5 grid gap-6">
                    <div class="card p-5 sm:p-8" style="background: #FFFDF8">
                        <div class="mute" style="font-size: 13px; font-weight: 700; letter-spacing: .08em; margin-bottom: 20px">LANGKAH BERIKUTNYA</div>
                        <ol style="list-style: none; margin: 0; padding: 0; position: relative">
                            <div style="position: absolute; left: 19px; top: 20px; bottom: 30px; border-left: 3px dashed #10282E"></div>

                            <!-- 1. Tiket Aktif -->
                            <li style="display: flex; gap: 16px; padding-bottom: 20px; position: relative">
                                <span style="flex: none; width: 40px; height: 40px; border: 2.5px solid #10282E; border-radius: 12px; background: #1F6F4A; color: #fff; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; box-shadow: 2px 2px 0 #10282E;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                </span>
                                <div>
                                    <div class="disp" style="font-size: 19px; font-weight: 700">Tiket aktif</div>
                                    <div style="margin-top: 2px; color: #10282E; font-size: 14px">Pembayaran diterima dan nomor BIB <b>{{ $registration->bib_number }}</b> sudah terkunci.</div>
                                </div>
                            </li>

                            <!-- 2. Ambil Race Pack -->
                            <li style="display: flex; gap: 16px; padding-bottom: 20px; position: relative">
                                <span style="flex: none; width: 40px; height: 40px; border: 2.5px solid #10282E; border-radius: 12px; background: #FFFDF8; color: #10282E; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; box-shadow: 2px 2px 0 #10282E;">
                                    <b class="disp" style="font-size: 18px">2</b>
                                </span>
                                <div>
                                    <div class="disp" style="font-size: 19px; font-weight: 700">Ambil race pack</div>
                                    <div style="margin-top: 2px; color: #10282E; font-size: 14px">Bawa e-ticket dan KTP asli saat Race Pack Collection (RPC).</div>
                                </div>
                            </li>

                            <!-- 3. Cek Wave Start -->
                            <li style="display: flex; gap: 16px; padding-bottom: 20px; position: relative">
                                <span style="flex: none; width: 40px; height: 40px; border: 2.5px solid #10282E; border-radius: 12px; background: #FFFDF8; color: #10282E; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; box-shadow: 2px 2px 0 #10282E;">
                                    <b class="disp" style="font-size: 18px">3</b>
                                </span>
                                <div>
                                    <div class="disp" style="font-size: 19px; font-weight: 700">Cek wave start</div>
                                    <div style="margin-top: 2px; color: #10282E; font-size: 14px">Gelombang start: <b>{{ $waveName }}</b>. Masuk holding area tepat waktu.</div>
                                </div>
                            </li>

                            <!-- 4. Hari-H -->
                            <li style="display: flex; gap: 16px; position: relative">
                                <span style="flex: none; width: 40px; height: 40px; border: 2.5px solid #10282E; border-radius: 12px; background: #FFFDF8; color: #10282E; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; box-shadow: 2px 2px 0 #10282E;">
                                    <b class="disp" style="font-size: 18px">4</b>
                                </span>
                                <div>
                                    <div class="disp" style="font-size: 19px; font-weight: 700">Hari-H</div>
                                    <div style="margin-top: 2px; color: #10282E; font-size: 14px">Flag-off pukul <b>{{ $waveTime }} WIB</b>. Datang 60 menit sebelumnya.</div>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <!-- Card E-ticket Siap Dipindai -->
                    <div class="card p-4 sm:p-6 flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 sm:gap-5" style="background: #F8E3B5">
                        <div style="width: 84px; height: 84px; background: #fff; border: 3px solid #10282E; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex: none">
                            <img src="{{ \App\Support\QrCodeService::uri($qrData) }}" alt="QR Mini" class="w-16 h-16 object-contain">
                        </div>
                        <div>
                            <div class="disp" style="font-size: 20px; font-weight: 700">E-ticket siap dipindai</div>
                            <div style="margin-top: 2px; font-size: 14px">Tunjukkan QR ini di meja race pack untuk pengambilan BIB &amp; chip.</div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- KARTU E-TIKET RESMI DENGAN STUB KUPON ROBEK PERSIS MOCKUP (Ticket.dc.html) -->
        <section id="eticket" class="sec" style="padding: 40px 0 88px; position: relative">
            <div class="wrap" style="position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 36px; flex-wrap: wrap; gap: 16px">
                    <h2 class="h2" style="font-size: 44px">E-ticket resmi kamu</h2>
                    <div style="display: flex; gap: 12px" class="no-print">
                        <a href="{{ route('ticket.bib', $registration) }}" target="_blank" class="btn btn-sm btn-orange font-black">
                            <i data-lucide="tag" class="w-4 h-4 mr-1.5"></i>
                            <span>Cetak Nomor BIB</span>
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-sm">
                            <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i>
                            <span>Unduh / Cetak PDF</span>
                        </button>
                    </div>
                </div>

                <!-- TIKET KARTUN 2-KOLOM (Left: Detail Putih + Takik, Right: Oranye Stub BIB + QR) -->
                <div class="tk card grid grid-cols-1 lg:grid-cols-12 max-w-[1100px] mx-auto shadow-[8px_8px_0_#10282E] p-0 overflow-hidden rounded-2xl border-[2.5px] border-[var(--ink)]">
                    <!-- Left Body -->
                    <div class="lg:col-span-8 bg-[#FFFDF8] p-6 sm:p-10 relative border-b-[2.5px] lg:border-b-0 lg:border-r-[2.5px] border-[var(--ink)]">
                        <div class="flex gap-2.5 flex-wrap">
                            <span class="chip o">{{ $category->name }}</span>
                            <span class="chip g">{{ $registration->status === \App\Models\Registration::STATUS_CHECKED_IN ? 'Check-In' : 'Lunas' }}</span>
                        </div>

                        <h2 class="font-heading font-black text-2xl sm:text-4xl text-[var(--ink)] mt-4 max-w-[560px] leading-tight">
                            {{ $event->title }}
                        </h2>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6 mt-6 sm:mt-8">
                            <div>
                                <div class="mute text-xs font-bold tracking-wider">PESERTA</div>
                                <div class="font-bold text-sm sm:text-lg text-[var(--ink)] mt-1 truncate">{{ $registration->participant_name }}</div>
                            </div>
                            <div>
                                <div class="mute text-xs font-bold tracking-wider">TANGGAL</div>
                                <div class="font-bold text-sm sm:text-lg text-[var(--ink)] mt-1">{{ P::tanggalLengkap($event->event_date) }}</div>
                            </div>
                            <div>
                                <div class="mute text-xs font-bold tracking-wider">GUN START</div>
                                <div class="font-bold text-sm sm:text-lg text-[var(--ink)] mt-1">{{ $waveTime }} WIB</div>
                            </div>
                            <div>
                                <div class="mute text-xs font-bold tracking-wider">LOKASI</div>
                                <div class="font-bold text-sm sm:text-lg text-[var(--ink)] mt-1 truncate">{{ $event->location_name }}</div>
                            </div>
                            <div>
                                <div class="mute text-xs font-bold tracking-wider">KODE TIKET</div>
                                <div class="font-mono font-bold text-sm sm:text-lg text-[var(--ink)] mt-1">{{ $scanCode }}</div>
                            </div>
                            <div>
                                <div class="mute text-xs font-bold tracking-wider">GOL. DARAH</div>
                                <div class="font-bold text-sm sm:text-lg text-[var(--ink)] mt-1">Gol. {{ $registration->blood_type ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="mt-6 sm:mt-8 p-3.5 sm:p-4 border-2 border-dashed border-[var(--ink)] rounded-xl bg-[#F8E3B5] text-xs sm:text-sm font-semibold text-[var(--ink)]">
                            Tukarkan tiket ini di meja <b>Race Pack Collection</b> bersama kartu identitas resmi (KTP/SIM/Paspor).
                        </div>
                    </div>

                    <!-- Right Stub (Orange #DE7119 + Dashed Border) -->
                    <div class="lg:col-span-4 bg-[var(--orange)] p-6 sm:p-10 text-white relative flex flex-col justify-between items-center text-center gap-4">
                        <div class="text-xs font-black tracking-widest text-[#10282E] uppercase">NOMOR BIB RESMI</div>
                        <div class="font-heading font-black text-6xl sm:text-7xl lg:text-8xl text-[#10282E] tracking-tight leading-none my-1">
                            {{ $registration->bib_number }}
                        </div>

                        <div class="p-3 bg-white border-2 border-[var(--ink)] rounded-xl shadow-[3px_3px_0_var(--ink)]">
                            <img src="{{ \App\Support\QrCodeService::uri($qrData) }}"
                                 alt="QR Code Tiket Peserta"
                                 class="w-32 h-32 sm:w-36 sm:h-36 object-contain">
                        </div>

                        <div class="text-xs font-black text-[#10282E] uppercase tracking-wider">
                            Pindai di meja race pack
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Links -->
                <div class="mt-8 flex justify-center gap-4 no-print">
                    @if (Auth::user()?->isAdmin() || Auth::user()?->isPenyelenggara())
                    <a href="/admin/registrations" wire:navigate class="btn btn-sm btn-ghost">
                        <span>Kembali ke Panel Pengelola</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                    @else
                    @php
                        $u = Auth::user();
                        $homeUrl = $u ? $u->home() : '/dashboard';
                        $homeLabel = $u?->isOperator() ? 'Antrian Helpdesk' : 'Buka Dashboard Peserta';
                    @endphp
                    <a href="{{ $homeUrl }}" wire:navigate.hover class="btn btn-sm btn-ghost">
                        <span>{{ $homeLabel }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                    @endif
                </div>

            </div>
        </section>

    @endif
</div>

<style>
@media print {
    header, footer, #veloChatWidget, .no-print, .skip-intro, .pagehead, #intro-overlay {
        display: none !important;
    }
    body, .vs.page, main {
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #eticket {
        padding: 0 !important;
        margin: 0 !important;
    }
    .tk.card {
        box-shadow: none !important;
        border: 2.5px solid #10282E !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
}
</style>
@endsection
