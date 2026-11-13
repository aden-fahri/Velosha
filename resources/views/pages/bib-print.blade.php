@use('App\Support\EventCardPresenter', 'P')
@php
    $category = $registration->category;
    $event = $category->event;
    $ticket = $registration->ticket;
    $waveParts = array_map('trim', explode(' - ', (string) $category->wave_start_time, 2));
    $waveName = $waveParts[0] !== '' ? $waveParts[0] : (string) $category->wave_start_time;
    $waveTime = $waveParts[1] ?? '05.00';
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;800;900&family=JetBrains+Mono:wght@700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #EFE6D5;
            color: #10282E;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .control-bar {
            width: 100%;
            max-width: 820px;
            background: #10282E;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 4px 4px 0 rgba(16, 40, 46, 0.4);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            font-size: 13px;
            padding: 8px 16px;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.15s ease;
        }

        .btn-print {
            background: #DE7119;
            color: #ffffff;
            border-color: #ffffff;
        }
        .btn-print:hover {
            background: #F6B777;
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

        /* Kartu Nomor Dada (BIB) Standar Lomba Balap / Maraton */
        .bib-card {
            width: 100%;
            max-width: 820px;
            aspect-ratio: 1.414 / 1;
            min-height: 520px;
            background: #FFFFFF;
            border: 4px solid #10282E;
            border-radius: 20px;
            box-shadow: 8px 8px 0 #10282E;
            padding: 28px 32px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        /* 4 Pin Punch Marker Circles in Corners */
        .pin-marker {
            position: absolute;
            width: 22px;
            height: 22px;
            border: 2.5px dashed #10282E;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            color: #10282E;
            background: #FAF9F5;
        }
        .pin-tl { top: 12px; left: 12px; }
        .pin-tr { top: 12px; right: 12px; }
        .pin-bl { bottom: 12px; left: 12px; }
        .pin-br { bottom: 12px; right: 12px; }

        /* Header BIB */
        .bib-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3.5px solid #10282E;
            padding-bottom: 14px;
        }

        .event-badge {
            background: #F4BE3F;
            border: 2px solid #10282E;
            padding: 4px 12px;
            border-radius: 8px;
            font-weight: 900;
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 2px 2px 0 #10282E;
        }

        .event-name {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 26px;
            font-weight: 900;
            color: #10282E;
            line-height: 1.1;
            margin-top: 4px;
        }

        .event-meta {
            font-size: 12px;
            font-weight: 700;
            color: #56666A;
            margin-top: 2px;
        }

        /* Category Strip */
        .category-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #10282E;
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 10px;
            margin: 12px 0 6px;
        }

        .cat-title {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 900;
            font-size: 16px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #F4BE3F;
        }

        .wave-badge {
            font-size: 11px;
            font-weight: 800;
            background: #DE7119;
            color: #FFFFFF;
            padding: 3px 10px;
            border-radius: 6px;
        }

        /* Central Big BIB Number */
        .bib-number-wrap {
            text-align: center;
            padding: 10px 0;
            position: relative;
        }

        .bib-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 120px;
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -0.04em;
            color: #10282E;
            text-shadow: 3px 3px 0 #F4BE3F;
        }

        .rider-name {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 26px;
            font-weight: 900;
            color: #10282E;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 6px;
        }

        .rider-club {
            font-size: 13px;
            font-weight: 800;
            color: #DE7119;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* Footer BIB */
        .bib-footer {
            border-top: 3px solid #10282E;
            padding-top: 12px;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 16px;
        }

        .safety-grid {
            display: grid;
            grid-template-columns: repeat(3, auto);
            gap: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .safety-item {
            background: #FAF9F5;
            border: 2px solid #10282E;
            padding: 6px 10px;
            border-radius: 8px;
            box-shadow: 1.5px 1.5px 0 #10282E;
        }

        .safety-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 800;
            color: #56666A;
            display: block;
        }

        .safety-val {
            font-weight: 900;
            color: #10282E;
            font-size: 12px;
        }

        .qr-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qr-box {
            width: 64px;
            height: 64px;
            border: 2px solid #10282E;
            border-radius: 8px;
            background: #fff;
            padding: 4px;
            box-shadow: 2px 2px 0 #10282E;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .timing-chip-notice {
            font-size: 10px;
            font-weight: 800;
            color: #10282E;
            line-height: 1.25;
            text-align: right;
        }

        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
            }
            .control-bar {
                display: none !important;
            }
            .bib-card {
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
                margin: 6mm;
            }
        }
    </style>
</head>
<body>

    <!-- Top Web Control Bar -->
    <div class="control-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-weight: 900; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em;">
                Kartu Nomor Dada Peserta (BIB Plate)
            </span>
            <span style="background: #F4BE3F; color: #10282E; padding: 2px 8px; border-radius: 6px; font-weight: 800; font-size: 11px;">
                Resmi Velosha
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('ticket.show', $registration) }}" class="btn btn-back">
                &larr; Kembali ke E-Ticket
            </a>
            <button type="button" onclick="window.print()" class="btn btn-print">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Cetak Nomor BIB</span>
            </button>
        </div>
    </div>

    <!-- Official Race BIB Card -->
    <div class="bib-card">
        <!-- 4 Pin Punch Markers -->
        <div class="pin-marker pin-tl">&bull;</div>
        <div class="pin-marker pin-tr">&bull;</div>
        <div class="pin-marker pin-bl">&bull;</div>
        <div class="pin-marker pin-br">&bull;</div>

        <!-- Header -->
        <div class="bib-header">
            <div>
                <span class="event-badge">Official Race BIB</span>
                <h1 class="event-name">{{ $event->title }}</h1>
                <div class="event-meta">
                    {{ P::tanggalLengkap($event->event_date) }} &bull; {{ $event->location_name }}
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #56666A;">Order ID</div>
                <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 800; color: #10282E;">
                    #VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        </div>

        <!-- Category Banner -->
        <div class="category-strip">
            <span class="cat-title">{{ $category->name }} ({{ P::distance($category->distance_km) }})</span>
            <span class="wave-badge">Start: {{ $waveName }} ({{ $waveTime }} WIB)</span>
        </div>

        <!-- Giant BIB Number & Rider Info -->
        <div class="bib-number-wrap">
            <div class="bib-number">{{ $registration->bib_number }}</div>
            <div class="rider-name">{{ $registration->participant_name }}</div>
            <div class="rider-club">{{ filled($registration->club) ? $registration->club : 'PESERTA INDIVIDUAL' }}</div>
        </div>

        <!-- Footer / Safety & RFID Timing Section -->
        <div class="bib-footer">
            <div class="safety-grid">
                <div class="safety-item">
                    <span class="safety-label">Golongan Darah</span>
                    <span class="safety-val">Gol. {{ $registration->blood_type ?? '-' }}</span>
                </div>
                <div class="safety-item">
                    <span class="safety-label">Kontak Darurat</span>
                    <span class="safety-val">{{ $registration->emergency_contact_name }} ({{ $registration->emergency_contact_phone }})</span>
                </div>
                <div class="safety-item">
                    <span class="safety-label">Kode Verifikasi</span>
                    <span class="safety-val" style="font-family: 'JetBrains Mono', monospace;">{{ $scanCode }}</span>
                </div>
            </div>

            <div class="qr-section">
                <div class="timing-chip-notice">
                    <span>RFID TIMING CHIP</span><br>
                    <span style="color: #56666A; font-weight: 600;">Scan di Gate &amp; RPC</span>
                </div>
                <div class="qr-box">
                    <img src="{{ \App\Support\QrCodeService::uri($qrData) }}" alt="QR BIB">
                </div>
            </div>
        </div>
    </div>

</body>
</html>
