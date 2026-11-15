<x-filament-panels::page>
    <style>
        /* Scoped Neobrutalism Protection to ensure layout NEVER collapses even without external Tailwind */
        .velo-neo-grid-4 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 16px;
        }
        @media (min-width: 640px) {
            .velo-neo-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .velo-neo-grid-4 {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        .velo-neo-grid-split {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }
        @media (min-width: 1024px) {
            .velo-neo-grid-split {
                grid-template-columns: 5fr 7fr;
            }
        }
        .velo-neo-card {
            background-color: #FFFDF8;
            border: 2.5px solid #10282E;
            box-shadow: 4px 4px 0 #10282E;
            border-radius: 12px;
            padding: 20px;
            box-sizing: border-box;
        }
        .velo-neo-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .velo-neo-table th {
            padding: 12px 18px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #56666A;
            border-bottom: 2px solid #10282E;
            background: #F8E3B5;
        }
        .velo-neo-table td {
            padding: 14px 18px;
            border-bottom: 1.5px solid #10282E;
            font-size: 13px;
            vertical-align: middle;
        }
        .velo-star-row {
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 4px !important;
        }
        .velo-star-row svg {
            display: inline-block !important;
            width: 22px !important;
            height: 22px !important;
            flex-shrink: 0 !important;
        }
    </style>

    {{-- Top Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-[2.5px] border-[#10282E]">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 bg-[#F4BE3F] rotate-45 border border-[#10282E]"></span>
                <span class="text-xs font-black uppercase tracking-wider text-[#56666A]">Modul Helpdesk Pusat</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-[#10282E] mt-1" style="font-family: 'Bricolage Grotesque', sans-serif;">
                Analitik &amp; Performa Helpdesk
            </h1>
            <p class="text-xs sm:text-sm text-[#56666A] font-medium mt-0.5">
                Metrik volume tiket bantuan, waktu respons SLA per operator, dan evaluasi kepuasan layanan (CSAT) terintegrasi.
            </p>
        </div>
        <div>
            <a href="{{ \App\Filament\Resources\HelpdeskTickets\HelpdeskTicketResource::getUrl('index') }}"
               class="inline-flex items-center gap-2 bg-[#FFFDF8] hover:bg-[#F8E3B5] text-[#10282E] border-[2.5px] border-[#10282E] shadow-[3px_3px_0_#10282E] hover:shadow-[1px_1px_0_#10282E] hover:translate-x-0.5 hover:translate-y-0.5 rounded-lg px-4 py-2 text-xs font-black transition-all">
                <span>&larr; Lihat Antrian Tiket</span>
            </a>
        </div>
    </div>

    {{-- SECTION 1: RINGKASAN VOLUME TIKET (4-Column Neobrutalist Grid) --}}
    <div class="space-y-3">
        <h2 class="text-xs font-black uppercase tracking-wider text-[#10282E]">
            Ringkasan Volume Tiket
        </h2>
        <div class="velo-neo-grid-4">
            {{-- Total Tiket Masuk --}}
            <div class="velo-neo-card">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-[#56666A]">Total Tiket</span>
                    <div class="w-10 h-10 rounded-lg bg-[#F8E3B5] border-2 border-[#10282E] shadow-[2px_2px_0_#10282E] flex items-center justify-center text-[#10282E]">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                </div>
                <div class="mt-2 text-4xl font-black text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">
                    {{ $kpi['total'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-[#56666A] font-semibold">Semua tiket yang masuk ke sistem</p>
            </div>

            {{-- Tiket Baru (Open) --}}
            <div class="velo-neo-card">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-[#DE7119]">Tiket Baru (Open)</span>
                    <div class="w-10 h-10 rounded-lg bg-[#F6B777] border-2 border-[#10282E] shadow-[2px_2px_0_#10282E] flex items-center justify-center text-[#10282E]">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                </div>
                <div class="mt-2 text-4xl font-black text-[#DE7119]" style="font-family: 'Bricolage Grotesque', sans-serif;">
                    {{ $kpi['open'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-[#56666A] font-semibold">Menunggu respons awal operator</p>
            </div>

            {{-- Sedang Diproses --}}
            <div class="velo-neo-card">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-[#10282E]">Dalam Proses</span>
                    <div class="w-10 h-10 rounded-lg bg-[#F4BE3F] border-2 border-[#10282E] shadow-[2px_2px_0_#10282E] flex items-center justify-center text-[#10282E]">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                    </div>
                </div>
                <div class="mt-2 text-4xl font-black text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">
                    {{ $kpi['in_progress'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-[#56666A] font-semibold">Sedang ditangani oleh staf/petugas</p>
            </div>

            {{-- Selesai (Closed) --}}
            <div class="velo-neo-card">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-[#1F6F4A]">Selesai (Closed)</span>
                    <div class="w-10 h-10 rounded-lg bg-[#D5EBD9] border-2 border-[#10282E] shadow-[2px_2px_0_#10282E] flex items-center justify-center text-[#1F6F4A]">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                </div>
                <div class="mt-2 text-4xl font-black text-[#1F6F4A]" style="font-family: 'Bricolage Grotesque', sans-serif;">
                    {{ $kpi['closed'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-[#56666A] font-semibold">Telah terselesaikan dengan baik</p>
            </div>
        </div>
    </div>

    {{-- SECTION 2: RATA-RATA WAKTU RESPONS PER OPERATOR (Full-Width Card & Padded Table) --}}
    <div class="velo-neo-card p-0! overflow-hidden">
        <div class="bg-[#F8E3B5] border-b-[2.5px] border-[#10282E] px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-heading font-black text-base text-[#10282E]">
                    Rata-Rata Waktu Respons Per Operator
                </h3>
                <p class="text-xs text-[#56666A] font-medium">
                    Waktu antara pembuatan tiket hingga balasan manusia pertama (hanya respons staf, auto-reply bot dikecualikan).
                </p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded bg-[#FFFDF8] border border-[#10282E] text-[10px] font-black uppercase text-[#10282E]">
                Satuan: Menit (&lt;120m) / Jam (&ge;120m)
            </span>
        </div>

        @if ($responseTimes->isEmpty())
            <div class="p-8 text-center text-sm font-bold text-[#56666A]">
                Belum ada data respons operator yang tercatat.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="velo-neo-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Operator</th>
                            <th style="width: 15%; text-align: center;">Total Tiket</th>
                            <th style="width: 15%; text-align: center;">Direspons</th>
                            <th style="width: 20%; text-align: center;">Waktu Rata-rata</th>
                            <th style="width: 25%;">Indikator Respons (CSS Bar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($responseTimes as $op)
                            @php
                                $percent = $maxMinutes > 0 ? min(100, (($op->avg_response_minutes ?? 0) / $maxMinutes) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-[#FBF4E6]/50 transition-colors">
                                <td class="font-bold text-[#10282E]">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-md bg-[#F4BE3F] border border-[#10282E] flex items-center justify-center font-black text-xs text-[#10282E] shrink-0">
                                            {{ strtoupper(substr($op->operator_name, 0, 1)) }}
                                        </div>
                                        <span class="truncate">{{ $op->operator_name }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center;" class="font-bold text-[#10282E]">
                                    {{ $op->total_tickets ?? 0 }}
                                </td>
                                <td style="text-align: center;" class="font-bold text-[#10282E]">
                                    {{ $op->response_count ?? 0 }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="inline-block px-3 py-1 rounded-md bg-[#FFFDF8] border-2 border-[#10282E] shadow-[1.5px_1.5px_0_#10282E] text-xs font-black text-[#10282E]">
                                        {{ $op->formatted_response_time ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="w-full bg-[#E2D3B3] rounded-full h-3 border border-[#10282E] overflow-hidden p-0.5">
                                        <div class="bg-[#F4BE3F] h-full rounded-full border-r border-[#10282E] transition-all duration-300" style="width: {{ max(8, $percent) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- SECTION 3: 2-COLUMN SPLIT (Kepuasan Pelanggan & Kepuasan per Operator) --}}
    <div class="velo-neo-grid-split items-start">
        {{-- Card Kiri: CSAT Score Keseluruhan --}}
        <div class="velo-neo-card space-y-4">
            <div class="border-b-[2px] border-[#10282E] pb-3">
                <h3 class="font-heading font-black text-base text-[#10282E]">
                    Kepuasan Pelanggan (Keseluruhan)
                </h3>
                <p class="text-xs text-[#56666A] font-medium">
                    Rangkuman penilaian layanan dari seluruh tiket terselesaikan.
                </p>
            </div>

            <div class="flex items-center gap-5 p-4 rounded-xl bg-[#FBF4E6] border-2 border-[#10282E] shadow-[3px_3px_0_#10282E]">
                <div class="text-center px-4 py-2 border-r-2 border-[#10282E] shrink-0">
                    <div class="text-4xl sm:text-5xl font-black text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">
                        {{ $satisfaction['overall']['formatted_average'] ?? '-' }}
                    </div>
                    <div class="text-[11px] font-bold text-[#56666A] mt-0.5">
                        dari 5.0
                    </div>
                </div>

                <div class="space-y-1.5 min-w-0 flex-1">
                    <div class="velo-star-row text-amber-500">
                        @php
                            $avg = round((float) ($satisfaction['overall']['average'] ?? 0));
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $avg && $avg > 0)
                                <svg class="fill-amber-400 stroke-[#10282E] stroke-[1.5]" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            @else
                                <svg class="fill-gray-200 stroke-[#10282E] stroke-[1.5]" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            @endif
                        @endfor
                    </div>
                    <div class="text-xs font-bold text-[#10282E]">
                        {{ $satisfaction['overall']['total'] ?? 0 }} ulasan masuk
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Kanan: Kepuasan per Operator Table --}}
        <div class="velo-neo-card p-0! overflow-hidden">
            <div class="bg-[#F8E3B5] border-b-[2.5px] border-[#10282E] px-6 py-3.5">
                <h3 class="font-heading font-black text-sm text-[#10282E]">
                    Kepuasan per Operator (Atribusi Tiket)
                </h3>
                <p class="text-[11px] text-[#56666A] font-medium">
                    Rating dikaitkan ke operator melalui kolom `helpdesk_tickets.operator_id`.
                </p>
            </div>

            @php
                $byOperator = $satisfaction['by_operator'] ?? collect();
            @endphp

            @if ($byOperator->isEmpty())
                <div class="p-6 text-center text-xs font-bold text-[#56666A]">
                    Belum ada ulasan rating per operator.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="velo-neo-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Operator</th>
                                <th style="width: 25%; text-align: center;">Rata-rata</th>
                                <th style="width: 30%; text-align: center;">Total Ulasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($byOperator as $opSat)
                                <tr class="hover:bg-[#FBF4E6]/50 transition-colors">
                                    <td class="font-bold text-[#10282E]">
                                        {{ $opSat->operator_name }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="inline-flex items-center gap-1 font-black text-xs px-2 py-0.5 rounded bg-[#FFFDF8] border border-[#10282E] text-[#10282E]">
                                            <span class="text-amber-500">★</span> {{ $opSat->formatted_average ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;" class="font-bold text-xs text-[#56666A]">
                                        {{ $opSat->total_ratings ?? 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
