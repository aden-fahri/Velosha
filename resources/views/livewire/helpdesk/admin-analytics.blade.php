<div class="min-h-screen bg-[var(--paper)] text-[var(--ink)] flex flex-col md:flex-row font-sans selection:bg-[var(--mustard)]">

    {{-- ==================== SIDEBAR ADMIN ==================== --}}
    <aside class="w-full md:w-64 bg-[#FBF4E6] border-b-[3px] md:border-b-0 md:border-r-[3px] border-[var(--ink)] p-5 md:p-6 flex flex-col shrink-0 justify-between">
        <div class="space-y-6">
            {{-- Brand Header --}}
            <div class="flex items-center justify-between pb-5 border-b border-[#10282E]/10">
                <a href="/" wire:navigate class="font-heading font-black text-2xl tracking-tight text-[var(--ink)] flex items-center gap-2">
                    <span>Velosha</span>
                </a>
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider bg-[var(--orange)] text-[var(--ink)] border-2 border-[var(--ink)] rounded-md shadow-[1px_1px_0_var(--ink)]">
                    ADMIN
                </span>
            </div>

            {{-- Main Helpdesk Navigation --}}
            <div>
                <div class="text-[11px] font-extrabold tracking-wider text-[var(--mute)] uppercase px-2 mb-2">Helpdesk</div>
                <nav class="space-y-1 text-sm font-semibold">
                    <a href="/helpdesk" wire:navigate
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-[var(--ink)] hover:bg-[#F3E7CF] transition text-left" style="text-decoration: none">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <span>Antrian tiket</span>
                        </div>
                    </a>

                    <a href="/helpdesk/analitik" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg bg-[var(--mustard)] border-2 border-[var(--ink)] font-black text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] text-left" style="text-decoration: none">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        <span>Analitik Helpdesk</span>
                    </a>
                </nav>
            </div>

            {{-- System Navigation --}}
            <div>
                <div class="text-[11px] font-extrabold tracking-wider text-[var(--mute)] uppercase px-2 mb-2">Navigasi Sistem</div>
                <nav class="space-y-1 text-sm font-semibold">
                    <a href="/admin"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-[var(--ink)] hover:bg-[#F3E7CF] transition text-left" style="text-decoration: none">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Admin Panel</span>
                    </a>

                    <a href="/" wire:navigate
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-[var(--ink)] hover:bg-[#F3E7CF] transition text-left" style="text-decoration: none">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                </nav>
            </div>
        </div>

        {{-- Admin Profile Card & Logout --}}
        <div class="mt-8 pt-6 border-t border-[#10282E]/10 space-y-3">
            <div class="bg-white border-2 border-[var(--ink)] p-3 rounded-xl flex items-center gap-3 shadow-[2px_2px_0_var(--ink)]">
                <div class="w-9 h-9 rounded-lg bg-[var(--mustard)] text-[var(--ink)] flex items-center justify-center font-extrabold text-sm border-2 border-[var(--ink)] shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-xs truncate text-[var(--ink)]">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-[var(--mute)] uppercase tracking-wider font-semibold">Administrator</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-center py-2 px-3 bg-red-50 hover:bg-red-100 text-[#C23B22] border-2 border-[#C23B22] rounded-lg text-xs font-extrabold transition cursor-pointer">
                    Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    {{-- ==================== MAIN CONTENT AREA ==================== --}}
    <main class="flex-1 p-6 md:p-8 space-y-8 min-w-0">

        {{-- Top Header --}}
        <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-[3px] border-[var(--ink)] pb-5">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-3 h-3 bg-[var(--mustard)] rotate-45 border border-[var(--ink)]"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-[var(--mute)]">Helpdesk Administrator Panel</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-heading tracking-tight mt-1">Analitik &amp; Performa Helpdesk</h1>
                <p class="text-xs sm:text-sm text-[var(--mute)] mt-0.5">
                    Metrik volume tiket, waktu respons per operator, dan skor kepuasan layanan secara agregat SQL.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border-[2px] border-[var(--ink)] rounded-lg text-xs font-bold shadow-[2px_2px_0_var(--ink)]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ auth()->user()->name }} (ADMIN)
                </span>

                <a href="/helpdesk" wire:navigate class="px-3.5 py-1.5 bg-white hover:bg-[var(--paper2)] text-[var(--ink)] border-[2px] border-[var(--ink)] rounded-lg text-xs font-extrabold shadow-[2px_2px_0_var(--ink)] transition">
                    ← Antrian Tiket
                </a>
            </div>
        </header>

        {{-- Section 1: 4 KPI TILES --}}
        <section aria-labelledby="kpi-heading" class="space-y-3">
            <h2 id="kpi-heading" class="text-xs font-black uppercase tracking-wider text-[var(--mute)]">Volume Tiket Helpdesk</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)]">
                    <div class="flex items-center justify-between text-xs font-extrabold text-[var(--mute)] uppercase tracking-wider">
                        <span>Total Tiket</span>
                        <span class="w-2.5 h-2.5 bg-purple-500 rounded-full border border-[var(--ink)]"></span>
                    </div>
                    <div class="mt-2 text-3xl sm:text-4xl font-black font-heading">{{ $kpi['total'] }}</div>
                    <div class="mt-1 text-xs text-[var(--mute)]">Semua tiket yang masuk</div>
                </div>

                {{-- Open --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)]">
                    <div class="flex items-center justify-between text-xs font-extrabold text-[var(--mute)] uppercase tracking-wider">
                        <span>Tiket Baru (Open)</span>
                        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full border border-[var(--ink)]"></span>
                    </div>
                    <div class="mt-2 text-3xl sm:text-4xl font-black font-heading">{{ $kpi['open'] }}</div>
                    <div class="mt-1 text-xs text-[var(--mute)]">Menunggu respons awal</div>
                </div>

                {{-- In-Progress --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)]">
                    <div class="flex items-center justify-between text-xs font-extrabold text-[var(--mute)] uppercase tracking-wider">
                        <span>Dalam Proses</span>
                        <span class="w-2.5 h-2.5 bg-amber-500 rounded-full border border-[var(--ink)]"></span>
                    </div>
                    <div class="mt-2 text-3xl sm:text-4xl font-black font-heading">{{ $kpi['in_progress'] }}</div>
                    <div class="mt-1 text-xs text-[var(--mute)]">Sedang ditangani operator</div>
                </div>

                {{-- Closed --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)]">
                    <div class="flex items-center justify-between text-xs font-extrabold text-[var(--mute)] uppercase tracking-wider">
                        <span>Selesai (Closed)</span>
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full border border-[var(--ink)]"></span>
                    </div>
                    <div class="mt-2 text-3xl sm:text-4xl font-black font-heading">{{ $kpi['closed'] }}</div>
                    <div class="mt-1 text-xs text-[var(--mute)]">Telah terselesaikan</div>
                </div>
            </div>
        </section>

        {{-- Section 2: AVERAGE RESPONSE TIME PER OPERATOR (Table + CSS Bar Chart) --}}
        <section aria-labelledby="response-heading" class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 sm:p-6 shadow-[4px_4px_0_var(--ink)] space-y-4">
            <div class="border-b-2 border-[var(--ink)] pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 id="response-heading" class="text-base sm:text-lg font-black font-heading">
                        Rata-Rata Waktu Respons per Operator
                    </h2>
                    <p class="text-xs text-[var(--mute)]">
                        Waktu antara pembuatan tiket hingga balasan manusia pertama (hanya respons staf, auto-reply bot dikecualikan).
                    </p>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-1 bg-[var(--paper)] border-[1.5px] border-[var(--ink)] rounded-md self-start sm:self-auto">
                    Satuan: menit (&lt;120m) / jam (&ge;120m)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b-2 border-[var(--ink)] bg-[var(--paper)] text-[var(--ink)]">
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider">Operator</th>
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider text-center">Total Tiket</th>
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider text-center">Direspons</th>
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider text-center">Waktu Rata-rata</th>
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider min-w-[180px]">Grafik Respons (CSS Bar)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-b border-[var(--ink)]">
                        @forelse ($responseTimes as $op)
                            <tr class="hover:bg-[var(--paper)] transition">
                                <td class="py-3 px-3 font-bold text-[var(--ink)]">
                                    {{ $op->operator_name }}
                                </td>
                                <td class="py-3 px-3 text-center font-semibold">
                                    {{ $op->total_tickets }}
                                </td>
                                <td class="py-3 px-3 text-center font-semibold">
                                    {{ $op->response_count }}
                                </td>
                                <td class="py-3 px-3 text-center font-black text-sm">
                                    <span class="inline-block px-2 py-0.5 bg-[var(--paper)] border border-[var(--ink)] rounded font-mono">
                                        {{ $op->formatted_response_time }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    @if ($op->avg_response_minutes !== null)
                                        <div class="w-full bg-[var(--paper2)] border-[1.5px] border-[var(--ink)] rounded-md h-5 p-0.5 overflow-hidden">
                                            <div class="h-full bg-[var(--mustard)] border border-[var(--ink)] rounded-xs transition-all duration-300"
                                                 style="width: {{ min(100, max(8, round(($op->avg_response_minutes / $maxMinutes) * 100))) }}%">
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-full border-[1.5px] border-dashed border-[var(--mute)] rounded-md h-5 flex items-center justify-center text-[10px] text-[var(--mute)] font-bold">
                                            Belum ada data
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-xs text-[var(--mute)] font-semibold">
                                    Tidak ada data operator helpdesk terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Section 3: SATISFACTION RATINGS (Overall & Per Operator 1-5 Distribution) --}}
        <section aria-labelledby="satisfaction-heading" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- Overall Satisfaction Box --}}
            <div class="lg:col-span-5 bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 sm:p-6 shadow-[4px_4px_0_var(--ink)] space-y-4">
                <div class="border-b-2 border-[var(--ink)] pb-3">
                    <h2 id="satisfaction-heading" class="text-base sm:text-lg font-black font-heading">
                        Kepuasan Pelanggan (Keseluruhan)
                    </h2>
                    <p class="text-xs text-[var(--mute)]">
                        Rangkuman penilaian layanan dari seluruh tiket terselesaikan.
                    </p>
                </div>

                <div class="flex items-center gap-4 p-4 bg-[var(--paper)] border-[2px] border-[var(--ink)] rounded-xl">
                    <div class="text-center shrink-0">
                        <div class="text-3xl sm:text-4xl font-black font-heading text-[var(--ink)]">
                            {{ $satisfaction['overall']['formatted_average'] }}
                        </div>
                        <div class="text-[11px] font-bold text-[var(--mute)] mt-0.5">dari 5.0</div>
                    </div>
                    <div class="min-w-0 flex-1 border-l-2 border-[var(--ink)] pl-4">
                        <div class="flex items-center gap-1 text-amber-500 text-lg">
                            ★★★★★
                        </div>
                        <div class="text-xs font-bold text-[var(--ink)] mt-1">
                            {{ $satisfaction['overall']['total'] }} ulasan masuk
                        </div>
                    </div>
                </div>

                {{-- 1-5 Star Distribution (CSS Bars) --}}
                <div class="space-y-2 pt-2">
                    <div class="text-xs font-black uppercase tracking-wider text-[var(--mute)] mb-2">Distribusi Bintang</div>
                    @for ($star = 5; $star >= 1; $star--)
                        @php
                            $count = $satisfaction['overall']['distribution'][$star] ?? 0;
                            $pct = $satisfaction['overall']['total'] > 0 ? round(($count / $satisfaction['overall']['total']) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3 text-xs">
                            <span class="w-12 font-bold shrink-0 text-right">{{ $star }} Bintang</span>
                            <div class="flex-1 bg-[var(--paper)] border-[1.5px] border-[var(--ink)] rounded-md h-4 p-0.5 overflow-hidden">
                                <div class="h-full bg-amber-400 border border-[var(--ink)] rounded-xs" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="w-12 font-mono text-right text-[var(--mute)] font-bold shrink-0">{{ $count }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Per Operator Satisfaction Breakdown Table --}}
            <div class="lg:col-span-7 bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 sm:p-6 shadow-[4px_4px_0_var(--ink)] space-y-4">
                <div class="border-b-2 border-[var(--ink)] pb-3">
                    <h2 class="text-base sm:text-lg font-black font-heading">
                        Kepuasan per Operator (Atribusi Tiket)
                    </h2>
                    <p class="text-xs text-[var(--mute)]">
                        Rating dikaitkan ke operator melalui kolom `helpdesk_tickets.operator_id`.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b-2 border-[var(--ink)] bg-[var(--paper)] text-[var(--ink)]">
                                <th class="py-2.5 px-3 font-black uppercase tracking-wider">Operator</th>
                                <th class="py-2.5 px-2 font-black uppercase tracking-wider text-center">Rata-rata</th>
                                <th class="py-2.5 px-2 font-black uppercase tracking-wider text-center">Total</th>
                                <th class="py-2.5 px-2 font-black uppercase tracking-wider text-center">5★</th>
                                <th class="py-2.5 px-2 font-black uppercase tracking-wider text-center">4★</th>
                                <th class="py-2.5 px-2 font-black uppercase tracking-wider text-center">3★</th>
                                <th class="py-2.5 px-2 font-black uppercase tracking-wider text-center">2★</th>
                                <th class="py-2.5 px-2 font-black uppercase tracking-wider text-center">1★</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y border-b border-[var(--ink)]">
                            @forelse ($satisfaction['by_operator'] as $op)
                                <tr class="hover:bg-[var(--paper)] transition">
                                    <td class="py-2.5 px-3 font-bold text-[var(--ink)]">{{ $op->operator_name }}</td>
                                    <td class="py-2.5 px-2 text-center font-black">
                                        <span class="inline-block px-1.5 py-0.5 bg-[var(--paper)] border border-[var(--ink)] rounded font-mono text-amber-900">
                                            {{ $op->formatted_average }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-2 text-center font-semibold">{{ $op->total_ratings }}</td>
                                    <td class="py-2.5 px-2 text-center font-mono font-bold">{{ $op->distribution[5] }}</td>
                                    <td class="py-2.5 px-2 text-center font-mono font-bold">{{ $op->distribution[4] }}</td>
                                    <td class="py-2.5 px-2 text-center font-mono font-bold">{{ $op->distribution[3] }}</td>
                                    <td class="py-2.5 px-2 text-center font-mono font-bold">{{ $op->distribution[2] }}</td>
                                    <td class="py-2.5 px-2 text-center font-mono font-bold">{{ $op->distribution[1] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 text-center text-xs text-[var(--mute)]">Tidak ada data rating.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Section 4: TOP OPERATORS TABLE --}}
        <section aria-labelledby="top-ops-heading" class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 sm:p-6 shadow-[4px_4px_0_var(--ink)] space-y-4">
            <div class="border-b-2 border-[var(--ink)] pb-3">
                <h2 id="top-ops-heading" class="text-base sm:text-lg font-black font-heading">
                    Peringkat Operator Terbaik (Top Operators)
                </h2>
                <p class="text-xs text-[var(--mute)]">
                    Diurutkan berdasarkan tiket Closed terbanyak, rata-rata waktu respons tercepat, dan rating kepuasan tertinggi.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b-2 border-[var(--ink)] bg-[var(--paper)] text-[var(--ink)]">
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider text-center w-12">#</th>
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider">Nama Operator</th>
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider text-center">Tiket Selesai (Closed)</th>
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider text-center">Rata-Rata Respons</th>
                            <th class="py-2.5 px-3 font-black uppercase tracking-wider text-center">Skor Kepuasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-b border-[var(--ink)]">
                        @forelse ($topOperators as $index => $op)
                            <tr class="hover:bg-[var(--paper)] transition">
                                <td class="py-3 px-3 text-center font-black">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $index === 0 ? 'bg-[var(--mustard)] border-2 border-[var(--ink)] shadow-[1px_1px_0_var(--ink)]' : 'bg-[var(--paper2)]' }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-extrabold text-sm text-[var(--ink)]">
                                    {{ $op->operator_name }}
                                </td>
                                <td class="py-3 px-3 text-center font-bold text-sm">
                                    {{ $op->closed_count }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-xs">
                                    <span class="inline-block px-2 py-0.5 bg-[var(--paper)] border border-[var(--ink)] rounded">
                                        {{ $op->formatted_response_time }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-xs">
                                    @if ($op->avg_rating !== null)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-900 rounded font-bold">
                                            ★ {{ $op->formatted_rating }}
                                        </span>
                                    @else
                                        <span class="text-[var(--mute)]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-xs text-[var(--mute)] font-semibold">
                                    Belum ada data aktivitas operator.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </main>
</div>
