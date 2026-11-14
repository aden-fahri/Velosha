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
    <title>E-Ticket — {{ $registration->bib_number }} — {{ $event->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;800;900&family=JetBrains+Mono:wght@700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
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
        }

        /* Top control bar for web preview */
        .control-bar {
            width: 100%;
            max-width: 900px;
            background: #10282E;
            color: #ffffff;
            padding: 14px 20px;
            border-radius: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 4px 4px 0 rgba(16, 40, 46, 0.4);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 13px;
            padding: 10px 18px;
            border-radius: 10px;
            border: 2px solid #10282E;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.15s ease;
        }
        .btn:hover {
            transform: translate(-1px, -1px);
        }
        .btn-mustard {
            background: #F4BE3F;
            color: #10282E;
            box-shadow: 2px 2px 0 #10282E;
        }
        .btn-ghost {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Printable Sheet Canvas (A4 landscape/portrait target) */
        .print-sheet {
            width: 100%;
            max-width: 900px;
            background: #FFFDF8;
            border: 3.5px solid #10282E;
            border-radius: 20px;
            box-shadow: 8px 8px 0 #10282E;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Ticket Top Header */
        .sheet-header {
            background: #F8E3B5;
            border-bottom: 3.5px solid #10282E;
            padding: 22px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sheet-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sheet-logo img {
            height: 38px;
            width: auto;
        }
        .sheet-logo-title {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 900;
            font-size: 24px;
            letter-spacing: -0.03em;
        }
        .passport-tag {
            background: #DE7119;
            color: #ffffff;
            font-weight: 900;
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 6px 12px;
            border: 2px solid #10282E;
            border-radius: 8px;
            box-shadow: 2px 2px 0 #10282E;
        }

        /* Ticket Body: 2 Columns */
        .sheet-body {
            display: grid;
            grid-template-columns: 1fr 280px;
        }

        .sheet-left {
            padding: 36px 38px;
            border-right: 3px dashed #10282E;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .event-badge-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .badge {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1.5px solid #10282E;
        }
        .badge-orange { background: #DE7119; color: #ffffff; }
        .badge-green { background: #1F6F4A; color: #ffffff; }

        .event-title {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 32px;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 24px;
            color: #10282E;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px 20px;
            margin-bottom: 24px;
        }
        .spec-item {
            display: flex;
            flex-direction: column;
        }
        .spec-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6B7C80;
            margin-bottom: 2px;
        }
        .spec-val {
            font-size: 15px;
            font-weight: 800;
            color: #10282E;
        }

        .marshall-notice {
            background: #F8E3B5;
            border: 2px dashed #10282E;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
        }

        /* Right Column (Stub & BIB) */
        .sheet-right {
            background: #DE7119;
            color: #10282E;
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 16px;
        }
        .bib-label {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #10282E;
        }
        .bib-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 64px;
            font-weight: 900;
            line-height: 0.9;
            color: #10282E;
            letter-spacing: -0.03em;
        }
        .qr-box {
            background: #ffffff;
            border: 2.5px solid #10282E;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 3px 3px 0 #10282E;
        }
        .qr-box img {
            width: 140px;
            height: 140px;
            display: block;
            object-contain: contain;
        }
        .scan-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 800;
            color: #10282E;
        }

        /* Bottom Footer Strip */
        .sheet-footer {
            background: #10282E;
            color: #ffffff;
            padding: 12px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 700;
        }

        /* Print CSS Rules */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .print-sheet {
                box-shadow: none !important;
                max-width: 100% !important;
                border: 2.5px solid #10282E !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    {{-- Control Bar (Web Preview Only) --}}
    <div class="control-bar no-print">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('ticket.show', $registration) }}" class="btn btn-ghost">
                &larr; Kembali ke E-Ticket
            </a>
            <span style="font-size: 12px; font-weight: 700; color: #F4BE3F;">
                Pratinjau Paspor Balap A4 Siap Cetak
            </span>
        </div>
        <div>
            <button type="button" onclick="window.print()" class="btn btn-mustard">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    {{-- Official Printable Sheet --}}
    <div class="print-sheet">
        {{-- Header --}}
        <div class="sheet-header">
            <div class="sheet-logo">
                <img src="/images/logo-velosha.png" alt="Velosha">
                <span class="sheet-logo-title">Velosha</span>
            </div>
            <div class="passport-tag">
                PASPOR RESMI BALAP SEPEDA
            </div>
        </div>

        {{-- Body --}}
        <div class="sheet-body">
            {{-- Left: Race & Cyclist Info --}}
            <div class="sheet-left">
                <div>
                    <div class="event-badge-row">
                        <span class="badge badge-orange">{{ $category->name }}</span>
                        <span class="badge badge-green">{{ $registration->isPaid() ? 'Terverifikasi Lunas' : 'Pending' }}</span>
                        <span class="badge" style="background: #ffffff;">{{ P::distance($category->distance_km) }}</span>
                    </div>

                    <h1 class="event-title">{{ $event->title }}</h1>

                    <div class="specs-grid">
                        <div class="spec-item">
                            <span class="spec-label">Nama Peserta</span>
                            <span class="spec-val">{{ $registration->participant_name }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Tanggal Pelaksanaan</span>
                            <span class="spec-val">{{ P::tanggalLengkap($event->event_date) }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Waktu Start / Wave</span>
                            <span class="spec-val">{{ $waveName }} &bull; {{ $waveTime }} WIB</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Lokasi Balap (Venue)</span>
                            <span class="spec-val">{{ $event->location_name }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Golongan Darah</span>
                            <span class="spec-val">Gol. {{ $registration->blood_type ?? '-' }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Kontak Darurat (ICE)</span>
                            <span class="spec-val">{{ $registration->emergency_contact_name }} ({{ $registration->emergency_contact_phone ?? '-' }})</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Komunitas / Klub</span>
                            <span class="spec-val">{{ $registration->club ?: 'Independen / Individual' }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Batas Cut-Off (COT)</span>
                            <span class="spec-val">{{ P::jam((float) $category->cut_off_hours) }} (Elevasi +{{ P::angka((float) $category->elevation_gain_m) }}m)</span>
                        </div>
                    </div>
                </div>

                <div class="marshall-notice">
                    Tunjukkan paspor ini bersama <b>KTP/SIM/Paspor asli</b> di meja <b>Race Pack Collection (RPC)</b> untuk pengambilan nomor BIB fisik, stiker timing RFID, dan jersey finisher resmi.
                </div>
            </div>

            {{-- Right: Stub & BIB --}}
            <div class="sheet-right">
                <div class="bib-label">NOMOR BIB RESMI</div>
                <div class="bib-number">{{ $registration->bib_number }}</div>

                <div class="qr-box">
                    <img src="{{ \App\Support\QrCodeService::uri($qrData) }}" alt="QR Code Tiket">
                </div>

                <div class="scan-code">{{ $scanCode }}</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="sheet-footer">
            <span>Order #VLS-REG-{{ str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) }}</span>
            <span>VELOSHA ENDURANCE CYCLING PLATFORM &bull; HAK CIPTA DILINDUNGI</span>
            <span>STATUS: VALID</span>
        </div>
    </div>

    {{-- Auto trigger print dialog --}}
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>

