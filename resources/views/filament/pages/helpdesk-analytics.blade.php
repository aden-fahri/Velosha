<x-filament-panels::page>
    <style>
        .fi-helpdesk-kpi-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .fi-helpdesk-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .fi-helpdesk-kpi-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        .fi-helpdesk-split-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .fi-helpdesk-split-grid {
                grid-template-columns: 5fr 7fr;
            }
        }
        .fi-helpdesk-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .fi-helpdesk-table th {
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .dark .fi-helpdesk-table th {
            color: #9ca3af;
            border-bottom: 1px solid #374151;
            background-color: #111827;
        }
        .fi-helpdesk-table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .dark .fi-helpdesk-table td {
            border-bottom: 1px solid #1f2937;
        }
        .fi-star-horizontal {
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 4px !important;
        }
        .fi-star-horizontal svg {
            display: inline-block !important;
            width: 20px !important;
            height: 20px !important;
            flex-shrink: 0 !important;
        }
    </style>

    {{-- Top Action / Header Info --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200 dark:border-gray-800">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Metrik volume tiket bantuan, waktu respons SLA per operator, dan evaluasi kepuasan layanan (CSAT) terintegrasi.
            </p>
        </div>
        <div>
            <x-filament::button
                href="{{ \App\Filament\Resources\HelpdeskTickets\HelpdeskTicketResource::getUrl('index') }}"
                tag="a"
                color="gray"
                icon="heroicon-m-arrow-left"
                size="sm"
            >
                Lihat Antrian Tiket
            </x-filament::button>
        </div>
    </div>

    {{-- 1. KPI Metric Cards (Pure Filament Section Design) --}}
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
            Ringkasan Volume Tiket
        </h3>
        <div class="fi-helpdesk-kpi-grid">
            {{-- Total Tiket --}}
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Tiket Masuk</span>
                    <span class="p-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg">
                        <x-filament::icon icon="heroicon-m-inbox-stack" class="w-5 h-5" />
                    </span>
                </div>
                <div class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    {{ $kpi['total'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Semua riwayat tiket bantuan</p>
            </x-filament::section>

            {{-- Tiket Baru (Open) --}}
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Tiket Baru (Open)</span>
                    <span class="p-2 bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 rounded-lg">
                        <x-filament::icon icon="heroicon-m-envelope" class="w-5 h-5" />
                    </span>
                </div>
                <div class="mt-2 text-3xl font-extrabold tracking-tight text-amber-600 dark:text-amber-400">
                    {{ $kpi['open'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Menunggu respons awal</p>
            </x-filament::section>

            {{-- Sedang Diproses --}}
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-sky-600 dark:text-sky-400">Sedang Diproses</span>
                    <span class="p-2 bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 rounded-lg">
                        <x-filament::icon icon="heroicon-m-arrow-path" class="w-5 h-5" />
                    </span>
                </div>
                <div class="mt-2 text-3xl font-extrabold tracking-tight text-sky-600 dark:text-sky-400">
                    {{ $kpi['in_progress'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ditangani oleh operator</p>
            </x-filament::section>

            {{-- Selesai (Closed) --}}
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Tiket Selesai</span>
                    <span class="p-2 bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <x-filament::icon icon="heroicon-m-check-circle" class="w-5 h-5" />
                    </span>
                </div>
                <div class="mt-2 text-3xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400">
                    {{ $kpi['closed'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Telah terselesaikan</p>
            </x-filament::section>
        </div>
    </div>

    {{-- 2. Rata-Rata Waktu Respons Per Operator (Filament Pure Table Section) --}}
    <x-filament::section>
        <x-slot name="heading">
            Rata-Rata Waktu Respons Per Operator
        </x-slot>
        <x-slot name="description">
            Waktu antara pembuatan tiket hingga balasan manusia pertama (hanya respons staf, auto-reply bot dikecualikan).
        </x-slot>

        @if ($responseTimes->isEmpty())
            <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada data respons operator yang tercatat.
            </div>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                <table class="fi-helpdesk-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Operator</th>
                            <th style="width: 15%; text-align: center;">Total Tiket</th>
                            <th style="width: 15%; text-align: center;">Direspons</th>
                            <th style="width: 20%; text-align: center;">Waktu Rata-rata</th>
                            <th style="width: 25%;">Indikator Respons</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        @foreach ($responseTimes as $op)
                            @php
                                $percent = $maxMinutes > 0 ? min(100, (($op->avg_response_minutes ?? 0) / $maxMinutes) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <td class="font-semibold text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($op->operator_name, 0, 1)) }}
                                        </div>
                                        <span class="truncate">{{ $op->operator_name }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center;" class="font-medium text-gray-600 dark:text-gray-300">
                                    {{ $op->total_tickets ?? 0 }}
                                </td>
                                <td style="text-align: center;" class="font-medium text-gray-600 dark:text-gray-300">
                                    {{ $op->response_count ?? 0 }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                        {{ $op->formatted_response_time ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-primary-600 dark:bg-primary-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ max(8, $percent) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>

    {{-- 3. Skor Kepuasan Pelanggan (CSAT) (Filament 2-Column Split) --}}
    <div class="fi-helpdesk-split-grid items-start">
        {{-- Card Kiri: CSAT Score Keseluruhan --}}
        <x-filament::section>
            <x-slot name="heading">
                Kepuasan Pelanggan (Keseluruhan)
            </x-slot>
            <x-slot name="description">
                Rangkuman penilaian layanan dari seluruh tiket terselesaikan.
            </x-slot>

            <div class="flex items-center gap-6 py-4">
                <div class="text-center px-5 py-3 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shrink-0">
                    <div class="text-4xl font-extrabold text-primary-600 dark:text-primary-400">
                        {{ $satisfaction['overall']['formatted_average'] ?? '-' }}
                    </div>
                    <div class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 mt-1">
                        dari skala 5.0
                    </div>
                </div>

                <div class="space-y-1.5 min-w-0 flex-1">
                    <div class="fi-star-horizontal text-amber-500">
                        @php
                            $avg = round((float) ($satisfaction['overall']['average'] ?? 0));
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $avg && $avg > 0)
                                <svg class="fill-amber-400 text-amber-400" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            @else
                                <svg class="fill-gray-200 dark:fill-gray-700 text-gray-200 dark:text-gray-700" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            @endif
                        @endfor
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                        Berdasarkan total <strong>{{ $satisfaction['overall']['total'] ?? 0 }} ulasan</strong> yang diberikan peserta.
                    </p>
                </div>
            </div>
        </x-filament::section>

        {{-- Card Kanan: Kepuasan per Operator Table --}}
        <x-filament::section>
            <x-slot name="heading">
                Kepuasan per Operator (Atribusi Tiket)
            </x-slot>
            <x-slot name="description">
                Rating dikaitkan ke operator melalui kolom `helpdesk_tickets.operator_id`.
            </x-slot>

            @php
                $byOperator = $satisfaction['by_operator'] ?? collect();
            @endphp

            @if ($byOperator->isEmpty())
                <div class="py-6 text-center text-xs text-gray-500 dark:text-gray-400">
                    Belum ada ulasan rating per operator.
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                    <table class="fi-helpdesk-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Operator</th>
                                <th style="width: 25%; text-align: center;">Rata-rata</th>
                                <th style="width: 30%; text-align: center;">Total Ulasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @foreach ($byOperator as $opSat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="font-semibold text-gray-900 dark:text-white">
                                        {{ $opSat->operator_name }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="inline-flex items-center gap-1 font-bold text-amber-600 dark:text-amber-400 text-xs">
                                            ★ {{ $opSat->formatted_average ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;" class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $opSat->total_ratings ?? 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
