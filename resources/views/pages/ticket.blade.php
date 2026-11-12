@use('App\Support\EventCardPresenter', 'P')
@extends('layouts.app', ['title' => 'E-Ticket & Pembayaran Berhasil — Velosha'])

@section('content')
<div class="vs page pb-16 min-h-[calc(100vh-140px)] bg-[#F3E7CF] relative">

    @if ($registration === null || $registration->category === null || $registration->category->event === null)

        <!-- PAGE HEADER: Belum Ada Pendaftaran (Konsisten dengan Kalender, Tentang, Regulasi, & Bantuan) -->
        <section class="relative overflow-hidden" style="background: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink, #10282E); padding: 52px 0;">
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
                    <a href="{{ route('events.index') }}" wire:navigate class="btn btn-sm btn-pine">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                        <span>Jelajahi Kalender Balap</span>
                    </a>
                </div>
            </div>
        </section>

    @elseif (! $registration->isPaid())

        <!-- PAGE HEADER: Menunggu Pembayaran (Konsisten dengan Kalender, Tentang, Regulasi, & Bantuan) -->
        <section class="relative overflow-hidden" style="background: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink, #10282E); padding: 52px 0;">
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
                    <a href="{{ route('checkout', ['event' => $registration->category->event->slug, 'category' => $registration->category->id]) }}" wire:navigate
                       class="btn flex-1 text-center justify-center">
                        <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                        <span>Bayar Sekarang</span>
                    </a>
                    <a href="{{ route('events.index') }}" wire:navigate
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
                <div class="flex items-center gap-3 flex-wrap">
                    <a href="#eticket" class="btn btn-sm btn-pine">
                        <i data-lucide="ticket" class="w-4 h-4 mr-1"></i>
                        <span>Lihat E-Ticket &amp; QR</span>
                    </a>
                    <a href="{{ route('ticket.download', $registration) }}" target="_blank" class="btn btn-sm btn-mustard font-black">
                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                        <span>Unduh Paspor PDF</span>
                    </a>
                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ rawurlencode('Velosha '.$event->title) }}&dates={{ $event->event_date->format('Ymd') }}T050000Z/{{ $event->event_date->format('Ymd') }}T130000Z&details={{ rawurlencode('Maraton Sepeda '.P::distance($category->distance_km).' Velosha. BIB: '.($registration->bib_number ?? '-')) }}&location={{ rawurlencode($event->location_name) }}"
                       target="_blank"
                       class="btn btn-sm btn-ghost">
                        <i data-lucide="calendar-plus" class="w-4 h-4 mr-1"></i>
                        <span>Tambah ke Google Calendar</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- KONTEN UTAMA: 2-COLUMN RINGKASAN & TIMELINE LANGKAH BERIKUTNYA -->
        <section class="sec" style="padding: 56px 0 40px; position: relative">
            <div class="wrap" style="position: relative; display: grid; grid-template-columns: 1.1fr .9fr; gap: 40px; align-items: start">

                <!-- Kiri: Ringkasan Pesanan Card -->
                <div class="card" style="padding: 32px; background: #FFFDF8">
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
                        <a href="{{ route('ticket.download', $registration) }}" target="_blank" class="btn btn-sm btn-mustard font-black">
                            <i data-lucide="download" class="w-4 h-4 mr-1.5"></i>
                            <span>Unduh Paspor PDF (A4)</span>
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-sm btn-ghost">
                            <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i>
                            <span>Cetak e-ticket</span>
                        </button>
                        <a href="mailto:{{ $registration->email }}?subject=E-Ticket%20{{ rawurlencode($event->title) }}&body=Nomor%20BIB:%20{{ $registration->bib_number }}" class="btn btn-sm btn-ghost">
                            <i data-lucide="mail" class="w-4 h-4 mr-1.5"></i>
                            <span>Kirim ke email</span>
                        </a>
                    </div>
                </div>

                <!-- Kanan: Langkah Berikutnya Timeline + Box Siap Pindai -->
                <div style="display: grid; gap: 24px">
                    <div class="card" style="padding: 32px; background: #FFFDF8">
                        <div class="mute" style="font-size: 13px; font-weight: 700; letter-spacing: .08em; margin-bottom: 20px">LANGKAH BERIKUTNYA</div>
                        <ol style="list-style: none; margin: 0; padding: 0; position: relative">
                            <div style="position: absolute; left: 19px; top: 20px; bottom: 30px; border-left: 3px dashed #10282E"></div>

                            <!-- 1. Tiket Aktif -->
                            <li style="display: flex; gap: 16px; padding-bottom: 20px; position: relative">
                                <span style="flex: none; width: 40px; height: 40px; border: 2.5px solid #10282E; border-radius: 50%; background: #1F6F4A; color: #fff; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                </span>
                                <div>
                                    <div class="disp" style="font-size: 19px; font-weight: 700">Tiket aktif</div>
                                    <div style="margin-top: 2px; color: #10282E; font-size: 14px">Pembayaran diterima dan nomor BIB <b>{{ $registration->bib_number }}</b> sudah terkunci.</div>
                                </div>
                            </li>

                            <!-- 2. Ambil Race Pack -->
                            <li style="display: flex; gap: 16px; padding-bottom: 20px; position: relative">
                                <span style="flex: none; width: 40px; height: 40px; border: 2.5px solid #10282E; border-radius: 50%; background: #FFFDF8; color: #10282E; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1">
                                    <b class="disp" style="font-size: 18px">2</b>
                                </span>
                                <div>
                                    <div class="disp" style="font-size: 19px; font-weight: 700">Ambil race pack</div>
                                    <div style="margin-top: 2px; color: #10282E; font-size: 14px">Bawa e-ticket dan KTP asli saat Race Pack Collection (RPC).</div>
                                </div>
                            </li>

                            <!-- 3. Cek Wave Start -->
                            <li style="display: flex; gap: 16px; padding-bottom: 20px; position: relative">
                                <span style="flex: none; width: 40px; height: 40px; border: 2.5px solid #10282E; border-radius: 50%; background: #FFFDF8; color: #10282E; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1">
                                    <b class="disp" style="font-size: 18px">3</b>
                                </span>
                                <div>
                                    <div class="disp" style="font-size: 19px; font-weight: 700">Cek wave start</div>
                                    <div style="margin-top: 2px; color: #10282E; font-size: 14px">Gelombang start: <b>{{ $waveName }}</b>. Masuk holding area tepat waktu.</div>
                                </div>
                            </li>

                            <!-- 4. Hari-H -->
                            <li style="display: flex; gap: 16px; position: relative">
                                <span style="flex: none; width: 40px; height: 40px; border: 2.5px solid #10282E; border-radius: 50%; background: #FFFDF8; color: #10282E; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1">
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
                    <div class="card" style="padding: 22px 26px; display: flex; align-items: center; gap: 20px; background: #F8E3B5">
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
                        <button type="button" onclick="window.print()" class="btn btn-sm">
                            <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i>
                            <span>Unduh / Cetak PDF</span>
                        </button>
                    </div>
                </div>

                <!-- TIKET KARTUN 2-KOLOM (Left: Detail Putih + Takik, Right: Oranye Stub BIB + QR) -->
                <div class="tk card" style="display: grid; grid-template-columns: 1fr 340px; max-width: 1100px; margin: 0 auto; box-shadow: 8px 8px 0 #10282E; padding: 0; overflow: hidden; border-radius: 18px">
                    <!-- Left Body -->
                    <div style="background: #FFFDF8; border-right: 0; padding: 40px 44px; position: relative">
                        <div style="display: flex; gap: 10px">
                            <span class="chip o">{{ $category->name }}</span>
                            <span class="chip g">{{ $registration->status === \App\Models\Registration::STATUS_CHECKED_IN ? 'Check-In' : 'Lunas' }}</span>
                        </div>

                        <h2 class="h2" style="font-size: 42px; margin-top: 20px; max-width: 560px; line-height: 1.05">
                            {{ $event->title }}
                        </h2>

                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 34px">
                            <div>
                                <div class="mute" style="font-size: 12px; font-weight: 700; letter-spacing: .08em">PESERTA</div>
                                <div style="font-weight: 700; font-size: 18px; margin-top: 4px">{{ $registration->participant_name }}</div>
                            </div>
                            <div>
                                <div class="mute" style="font-size: 12px; font-weight: 700; letter-spacing: .08em">TANGGAL</div>
                                <div style="font-weight: 700; font-size: 18px; margin-top: 4px">{{ P::tanggalLengkap($event->event_date) }}</div>
                            </div>
                            <div>
                                <div class="mute" style="font-size: 12px; font-weight: 700; letter-spacing: .08em">GUN START</div>
                                <div style="font-weight: 700; font-size: 18px; margin-top: 4px">{{ $waveTime }} WIB</div>
                            </div>
                            <div>
                                <div class="mute" style="font-size: 12px; font-weight: 700; letter-spacing: .08em">LOKASI</div>
                                <div style="font-weight: 700; font-size: 18px; margin-top: 4px">{{ $event->location_name }}</div>
                            </div>
                            <div>
                                <div class="mute" style="font-size: 12px; font-weight: 700; letter-spacing: .08em">KODE TIKET</div>
                                <div class="mono" style="font-weight: 700; font-size: 18px; margin-top: 4px">{{ $scanCode }}</div>
                            </div>
                            <div>
                                <div class="mute" style="font-size: 12px; font-weight: 700; letter-spacing: .08em">GOL. DARAH</div>
                                <div style="font-weight: 700; font-size: 18px; margin-top: 4px">Gol. {{ $registration->blood_type ?? '-' }}</div>
                            </div>
                        </div>

                        <div style="margin-top: 36px; padding: 16px 18px; border: 2.5px dashed #10282E; border-radius: 10px; background: #F8E3B5; font-size: 14px">
                            Tukarkan tiket ini di meja <b>Race Pack Collection</b> bersama kartu identitas resmi (KTP/SIM/Paspor).
                        </div>

                        <!-- Perforation punch notch -->
                        <i style="position: absolute; right: -19px; top: -19px; width: 38px; height: 38px; border-radius: 50%; background: #F3E7CF; border: 3px solid #10282E; clip-path: inset(50% 0 0 0)"></i>
                        <i style="position: absolute; right: -19px; bottom: -19px; width: 38px; height: 38px; border-radius: 50%; background: #F3E7CF; border: 3px solid #10282E; clip-path: inset(0 0 50% 0)"></i>
                    </div>

                    <!-- Right Stub (Orange #DE7119 + Dashed Border) -->
                    <div style="background: #DE7119; border-left: 3px dashed #10282E; padding: 40px 32px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 18px; text-align: center">
                        <div class="mute" style="font-size: 12px; font-weight: 700; letter-spacing: .1em; color: #10282E">NOMOR BIB RESMI</div>
                        <div class="disp font-mono" style="font-size: 84px; font-weight: 800; line-height: .9; color: #10282E">
                            {{ $registration->bib_number }}
                        </div>

                        <div style="padding: 12px; background: #fff; border: 3px solid #10282E; border-radius: 10px">
                            <img src="{{ \App\Support\QrCodeService::uri($qrData) }}"
                                 alt="QR Code Tiket Peserta"
                                 class="w-40 h-40 object-contain">
                        </div>

                        <div style="font-size: 13px; font-weight: 700; color: #10282E">
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
                    <a href="{{ $homeUrl }}" wire:navigate class="btn btn-sm btn-ghost">
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
@media (max-width: 900px) {
    .tk { grid-template-columns: 1fr !important; }
    .tk > div:last-child { border-left: 0 !important; border-top: 3px dashed #10282E !important; }
}
@media print {
    header, footer, #veloChatWidget, .no-print, .skip-intro {
        display: none !important;
    }
    body, .vs.page {
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .pagehead {
        min-height: auto !important;
        padding: 24px 0 !important;
    }
    .band-art {
        display: none !important;
    }
}
</style>
@endsection
