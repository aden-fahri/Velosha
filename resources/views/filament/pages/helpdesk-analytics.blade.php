<x-filament-panels::page>
    {{-- Header Intro --}}
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

    {{-- 1. KPI Metric Cards (Filament Style) --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">
            Ringkasan Volume Tiket
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Tiket --}}
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Tiket Masuk</span>
                    <span class="p-2 bg-primary-50 dark:bg-primary-950 text-primary-600 dark:text-primary-400 rounded-lg">
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
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tiket Baru (Open)</span>
                    <span class="p-2 bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 rounded-lg">
                        <x-filament::icon icon="heroicon-m-envelope" class="w-5 h-5" />
                    </span>
                </div>
                <div class="mt-2 text-3xl font-extrabold tracking-tight text-amber-600 dark:text-amber-400">
                    {{ $kpi['open'] ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Menunggu respons awal</p>
            </x-filament::section>

            {{-- Dalam Penanganan --}}
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Sedang Diproses</span>
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
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tiket Selesai</span>
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

    {{-- 2. Performa Waktu Respons Operator (Filament Table) --}}
    <x-filament::section>
        <x-slot name="heading">
            Rata-Rata Waktu Respons Per Operator
        </x-slot>
        <x-slot name="description">
            Durasi waktu sejak tiket dibuat hingga balasan pertama operator ke pelapor (auto-reply bot dikecualikan).
        </x-slot>

        @if ($responseTimes->isEmpty())
            <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada data respons operator yang tercatat.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm divide-y divide-gray-200 dark:divide-gray-800">
                    <thead>
                        <tr class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50/50 dark:bg-gray-900/50">
                            <th class="py-3 px-4">Operator</th>
                            <th class="py-3 px-4 text-center">Total Tiket</th>
                            <th class="py-3 px-4 text-center">Direspos</th>
                            <th class="py-3 px-4 text-center">Waktu Rata-rata</th>
                            <th class="py-3 px-4">Indikator Respons</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($responseTimes as $op)
                            @php
                                $percent = $maxMinutes > 0 ? min(100, (($op->avg_response_minutes ?? 0) / $maxMinutes) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                                <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $op->operator_name }}
                                </td>
                                <td class="py-3 px-4 text-center font-medium text-gray-600 dark:text-gray-300">
                                    {{ $op->total_tickets ?? 0 }}
                                </td>
                                <td class="py-3 px-4 text-center font-medium text-gray-600 dark:text-gray-300">
                                    {{ $op->response_count ?? 0 }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                        {{ $op->formatted_response_time ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-amber-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ max(6, $percent) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>

    {{-- 3. Skor Kepuasan Pelanggan (CSAT) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Rangkuman Kepuasan Keseluruhan --}}
        <x-filament::section>
            <x-slot name="heading">
                Kepuasan Pelanggan (Keseluruhan)
            </x-slot>
            <x-slot name="description">
                Rangkuman rating bintang dari tiket yang telah selesai ditangani.
            </x-slot>

            <div class="flex items-center gap-6 py-4">
                <div class="text-center px-4 py-3 bg-amber-50/60 dark:bg-amber-950/40 rounded-2xl border border-amber-200 dark:border-amber-900">
                    <div class="text-4xl font-black text-amber-600 dark:text-amber-400">
                        {{ $satisfaction['overall']['formatted_average'] ?? '-' }}
                    </div>
                    <div class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 mt-1">
                        dari skala 5.0
                    </div>
                </div>

                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center gap-1 text-amber-500">
                        @for ($i = 1; $i <= 5; $i++)
                            <x-filament::icon icon="heroicon-s-star" class="w-5 h-5 {{ $i <= round((float)($satisfaction['overall']['average_rating'] ?? 0)) ? 'text-amber-400' : 'text-gray-300 dark:text-gray-700' }}" />
                        @endfor
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                        Berdasarkan total <strong>{{ $satisfaction['overall']['total_ratings'] ?? 0 }} ulasan</strong> yang diberikan peserta.
                    </p>
                </div>
            </div>
        </x-filament::section>

        {{-- Kepuasan per Operator --}}
        <x-filament::section>
            <x-slot name="heading">
                Kepuasan Per Operator
            </x-slot>
            <x-slot name="description">
                Rating bintang yang diatribusikan ke masing-masing petugas.
            </x-slot>

            @php
                $byOperator = $satisfaction['by_operator'] ?? collect();
            @endphp

            @if ($byOperator->isEmpty())
                <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Belum ada ulasan rating per operator.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="py-2.5 px-3">Operator</th>
                                <th class="py-2.5 px-3 text-center">Rata-rata</th>
                                <th class="py-2.5 px-3 text-center">Total Ulasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($byOperator as $opSat)
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-gray-900 dark:text-white">
                                        {{ $opSat->operator_name }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="inline-flex items-center gap-1 font-bold text-amber-600 dark:text-amber-400 text-xs">
                                            ★ {{ $opSat->formatted_average ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center text-xs text-gray-500">
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
