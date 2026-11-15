@use('App\Support\EventCardPresenter', 'P')
@php
    $category = $registration->category;
    $event = $category->event;
    $ticket = $registration->ticket;
    $waveParts = array_map('trim', explode(' - ', (string) $category->wave_start_time, 2));
    $waveName = $waveParts[0] !== '' ? $waveParts[0] : (string) $category->wave_start_time;
    $waveTime = $waveParts[1] ?? '05.30';
    $qrData = $ticket?->qr_code_data ?? sprintf('VELOSHA-VERIFY-%s-%s', strtoupper(hash('sha256', (string) $registration->id)), $registration->bib_number);
    $bibSuffix = filled($registration->bib_number) ? substr((string) $registration->bib_number, -4) : '0001';
    $scanCode = strtoupper(substr(hash('sha256', $qrData), 0, 4)) . '-' . $bibSuffix . '-VLS';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomor Dada (BIB) — {{ $registration->bib_number }} — {{ $registration->participant_name }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #EDE6D8;
            color: #10282E;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Top Web Bar (Tersembunyi saat Cetak) */
        .control-bar {
            width: 100%;
            max-width: 820px;
            background: #10282E;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 4px 4px 0 rgba(16, 40, 46, 0.3);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            font-size: 13px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.15s ease;
        }

        .btn-print {
            background: #DE7119;
            color: #ffffff;
            border: 2px solid #ffffff;
        }
        .btn-print:hover {
            background: #F4BE3F;
            color: #10282E;
        }

        .btn-back {
            background: transparent;
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Plat Nomor Dada Resmi (Authentic Cycling Race Plate) */
        .bib-plate {
            width: 100%;
            max-width: 800px;
            background: #FFFFFF;
            border: 4px solid #10282E;
            border-radius: 16px;
            box-shadow: 6px 6px 0 #10282E;
            padding: 24px 30px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 500px;
            overflow: hidden;
        }

        /* 4 Tanda Lubang Peniti Presisi (Cycling Race Pin Holes) */
        .pin-hole {
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px dashed #10282E;
            background: #EDE6D8;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0.55;
        }
        .pin-hole::after {
            content: '';
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #10282E;
        }
        .pin-tl { top: 14px; left: 16px; }
        .pin-tr { top: 14px; right: 16px; }
        .pin-bl { bottom: 14px; left: 16px; }
        .pin-br { bottom: 14px; right: 16px; }

        /* Header Plat Balap */
        .plate-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #10282E;
            padding-bottom: 12px;
        }

        .event-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .velosha-mark {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -0.04em;
            color: #10282E;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding-right: 12px;
            border-right: 2.5px solid #10282E;
        }

        .event-title {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 22px;
            font-weight: 900;
            color: #10282E;
            line-height: 1.15;
            text-transform: uppercase;
        }

        .event-sub {
            font-size: 11px;
            font-weight: 700;
            color: #56666A;
            margin-top: 2px;
        }

        .order-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 800;
            color: #10282E;
            background: #F4BE3F;
            border: 2px solid #10282E;
            padding: 3px 8px;
            border-radius: 6px;
            text-align: right;
            white-space: nowrap;
        }

        /* Banner Kategori Balap & Wave */
        .category-bar {
            background: #10282E;
            color: #FFFFFF;
            padding: 8px 16px;
            border-radius: 8px;
            margin: 12px 0 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cat-name {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #F4BE3F;
        }

        .wave-tag {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            background: #DE7119;
            color: #FFFFFF;
            padding: 3px 9px;
            border-radius: 5px;
        }

        /* Area Utama Nomor BIB */
        .bib-hero {
            text-align: center;
            padding: 6px 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .bib-digits {
            font-family: 'JetBrains Mono', monospace;
            font-size: 110px;
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -0.04em;
            color: #10282E;
            user-select: all;
        }

        .rider-name {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 24px;
            font-weight: 900;
            color: #10282E;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 8px;
        }

        .rider-team {
            font-size: 12px;
            font-weight: 800;
            color: #DE7119;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 2px;
        }

        /* Footer Informasi Keselamatan & RFID Gate */
        .plate-footer {
            border-top: 2.5px solid #10282E;
            padding-top: 12px;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 16px;
        }

        .info-table {
            display: grid;
            grid-template-columns: repeat(3, auto);
            gap: 16px;
            text-align: left;
        }

        .info-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #56666A;
            letter-spacing: 0.04em;
            display: block;
        }

        .info-value {
            font-size: 12px;
            font-weight: 800;
            color: #10282E;
            margin-top: 1px;
            display: block;
        }

        .qr-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qr-text {
            text-align: right;
            font-size: 10px;
            font-weight: 800;
            color: #10282E;
            line-height: 1.25;
        }

        .qr-frame {
            width: 60px;
            height: 60px;
            border: 2px solid #10282E;
            border-radius: 6px;
            background: #FFFFFF;
            padding: 3px;
        }

        .qr-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Konfigurasi Responsif Layar Mobile (< 640px) */
        @media (max-width: 640px) {
            body { padding: 12px; }
            .control-bar { flex-direction: column; gap: 10px; align-items: stretch; text-align: center; }
            .bib-plate { width: 100%; padding: 16px; border-radius: 14px; min-height: auto; }
            .bib-number { font-size: clamp(52px, 16vw, 84px); }
            .rider-name { font-size: 18px; }
            .info-table { grid-template-columns: 1fr 1fr; gap: 8px; }
            .plate-footer { grid-template-columns: 1fr; gap: 12px; }
            .qr-block { justify-content: space-between; }
        }

        /* Konfigurasi Cetak Bersih & Otomatis Pas A5 */
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .control-bar {
                display: none !important;
            }
            .bib-plate {
                border: 3.5px solid #000000 !important;
                box-shadow: none !important;
                margin: 0 auto;
                page-break-inside: avoid;
                width: 100% !important;
                max-width: 100% !important;
                height: 98vh !important;
            }
            @page {
                size: A5 landscape;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Navigasi & Cetak Web -->
    <div class="control-bar">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-weight: 900; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">
                Nomor Dada Resmi (BIB Plate)
            </span>
            <span style="background: #F4BE3F; color: #10282E; padding: 2px 7px; border-radius: 4px; font-weight: 800; font-size: 10px;">
                Resmi Velosha
            </span>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('ticket.show', $registration) }}" wire:navigate.hover class="btn btn-back">
                &larr; Kembali ke E-Ticket
            </a>
            <button type="button" onclick="window.print()" class="btn btn-print">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Plat Nomor BIB Resmi -->
    <div class="bib-plate">
        <!-- 4 Tanda Lubang Peniti Presisi -->
        <div class="pin-hole pin-tl" aria-hidden="true"></div>
        <div class="pin-hole pin-tr" aria-hidden="true"></div>
        <div class="pin-hole pin-bl" aria-hidden="true"></div>
        <div class="pin-hole pin-br" aria-hidden="true"></div>

        <!-- Header -->
        <div class="plate-header">
            <div class="event-brand">
                <span class="velosha-mark">VELOSHA</span>
                <div>
                    <h1 class="event-title">{{ $event->title }}</h1>
                    <div class="event-sub">
                        {{ P::tanggalLengkap($event->event_date) }} &bull; {{ $event->location_name }}
                    </div>
                </div>
            </div>
            <div class="order-badge">
                #VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <!-- Strip Kategori Balap -->
        <div class="category-bar">
            <span class="cat-name">{{ $category->name }} ({{ P::distance($category->distance_km) }})</span>
            <span class="wave-tag">Wave: {{ $waveName }} &bull; {{ $waveTime }} WIB</span>
        </div>

        <!-- Nomor BIB Raksasa & Profil Rider -->
        <div class="bib-hero">
            <div class="bib-digits">{{ $registration->bib_number }}</div>
            <div class="rider-name">{{ $registration->participant_name }}</div>
            <div class="rider-team">{{ filled($registration->club) ? $registration->club : 'PESERTA INDIVIDUAL' }}</div>
        </div>

        <!-- Footer Data Keselamatan & Check-in QR -->
        <div class="plate-footer">
            <div class="info-table">
                <div>
                    <span class="info-label">Gol. Darah</span>
                    <span class="info-value">Gol. {{ $registration->blood_type ?? '-' }}</span>
                </div>
                <div>
                    <span class="info-label">Kontak Darurat</span>
                    <span class="info-value">{{ $registration->emergency_contact_name }} ({{ $registration->emergency_contact_phone }})</span>
                </div>
                <div>
                    <span class="info-label">Verifikasi RFID</span>
                    <span class="info-value" style="font-family: 'JetBrains Mono', monospace;">{{ $scanCode }}</span>
                </div>
            </div>

            <div class="qr-block">
                <div class="qr-text">
                    <span>TIMING CHIP</span><br>
                    <span style="color: #56666A; font-weight: 600;">Gate &amp; RPC</span>
                </div>
                <div class="qr-frame">
                    <img src="{{ \App\Support\QrCodeService::uri($qrData) }}" alt="QR BIB">
                </div>
            </div>
        </div>
    </div>

</body>
</html>
