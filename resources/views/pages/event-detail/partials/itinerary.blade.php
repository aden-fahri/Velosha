@use('App\Support\EventCardPresenter', 'P')
{{-- Timeline race weekend: Race Pack Collection (H-2/H-1) & Race Day --}}
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <div>
            <span class="text-[11px] font-black uppercase tracking-wider text-[var(--mute)]">RUNDOWN ACARA</span>
            <h2 class="font-heading font-black text-2xl sm:text-3xl text-[var(--ink)] tracking-tight">Jadwal Race Weekend</h2>
        </div>
        <span class="chip bg-white text-[var(--ink)] border border-[var(--ink)] font-bold shadow-[2px_2px_0_var(--ink)] text-xs">
            Zona Waktu WIB (UTC+7)
        </span>
    </div>

    <div class="space-y-4">
        {{-- Card 1: RPC & Bike Expo --}}
        <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-2xl p-6 sm:p-7 shadow-[4px_4px_0_var(--ink)] space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <span class="inline-block px-3 py-1 rounded-lg border-2 border-[var(--ink)] font-black text-xs shadow-[2px_2px_0_var(--ink)] bg-[#F8E3B5] text-[#10282E]" style="background-color: #F8E3B5 !important; color: #10282E !important;">
                    {{ P::hari($rpcStart) }} – {{ P::hari($rpcEnd) }}, {{ $rpcLabel }}
                </span>
                <span class="inline-flex items-center gap-1.5 font-mono text-xs font-bold px-2.5 py-1 rounded-md bg-[var(--paper)] border border-[var(--ink)] text-[var(--ink)]">
                    10:00 – 20:00 WIB
                </span>
            </div>

            <h3 class="font-heading font-black text-xl text-[var(--ink)]">Race Pack Collection (RPC) &amp; Bike Expo</h3>
            <p class="text-xs sm:text-sm text-[var(--mute)] font-medium leading-relaxed">
                Pengambilan race pack resmi meliputi nomor BIB RFID, timing chip, official jersey, dan race briefing teknis rute. Wajib membawa E-Ticket QR Code serta kartu identitas diri resmi (KTP/Paspor/SIM).
            </p>
        </div>

        {{-- Card 2: Race Day --}}
        <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-2xl p-6 sm:p-7 shadow-[4px_4px_0_var(--ink)] space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <span class="inline-block px-3 py-1 rounded-lg border-2 border-[var(--ink)] font-black text-xs shadow-[2px_2px_0_var(--ink)] bg-[#1F6F4A] text-white" style="background-color: #1F6F4A !important; color: #ffffff !important;">
                    {{ P::hari($eventDate) }}, {{ $raceLabel }}
                </span>
                <span class="inline-flex items-center gap-1.5 font-bold text-xs px-2.5 py-1 rounded-md bg-[var(--mustard)] border border-[var(--ink)] text-[var(--ink)] shadow-[1px_1px_0_var(--ink)]">
                    Hari Perlombaan Resmi
                </span>
            </div>

            <h3 class="font-heading font-black text-xl text-[var(--ink)]">Race Day &mdash; {{ $event->title ?: 'Hari Balapan Resmi' }}</h3>

            <div class="space-y-2.5 pt-1">
                {{-- Open Gate --}}
                <div class="flex items-start sm:items-center gap-3 p-3 rounded-xl bg-[var(--paper)] border border-[var(--ink)]/20">
                    <span class="font-mono font-bold text-xs text-[var(--ink)] px-2.5 py-1 rounded-md bg-white border border-[var(--ink)] shrink-0 shadow-[1px_1px_0_var(--ink)]">
                        04:45 WIB
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-[var(--ink)]">
                        Open Gate &amp; Loading Corral Peserta
                    </span>
                </div>

                {{-- Category Waves --}}
                @foreach ($categories as $category)
                    @php
                        $rawWave = trim((string) $category->wave_start_time);
                        if (str_contains($rawWave, ' - ')) {
                            [$waveName, $waveTime] = array_map('trim', explode(' - ', $rawWave, 2));
                        } elseif ($rawWave !== '') {
                            $waveTime = $rawWave . (str_contains($rawWave, 'WIB') ? '' : ' WIB');
                            $waveName = 'Start ' . $category->name;
                        } else {
                            $waveTime = ($startTime ?? '06:00') . ' WIB';
                            $waveName = 'Start ' . $category->name;
                        }
                    @endphp
                    <div class="flex items-start sm:items-center gap-3 p-3 rounded-xl bg-[var(--paper)] border border-[var(--ink)]/20">
                        <span class="font-mono font-bold text-xs text-[var(--ink)] px-2.5 py-1 rounded-md bg-[var(--butter)] border border-[var(--ink)] shrink-0 shadow-[1px_1px_0_var(--ink)]">
                            {{ $waveTime }}
                        </span>
                        <div class="text-xs sm:text-sm font-bold text-[var(--ink)]">
                            {{ $waveName }} <span class="text-[var(--mute)] font-medium">({{ $category->name }} &bull; {{ P::distance($category->distance_km) }})</span>
                        </div>
                    </div>
                @endforeach

                {{-- COT & Ceremony --}}
                @if ($maxCutOff !== null)
                    <div class="flex items-start sm:items-center gap-3 p-3 rounded-xl bg-[var(--paper)] border border-[var(--ink)]/20">
                        <span class="font-mono font-bold text-xs text-red-900 px-2.5 py-1 rounded-md bg-[var(--red-l)] border border-red-700 shrink-0 shadow-[1px_1px_0_var(--ink)]">
                            {{ $cutoffEnd }} WIB
                        </span>
                        <span class="text-xs sm:text-sm font-bold text-[var(--ink)]">
                            Course Cut-Off Time (COT) &amp; Upacara Penyerahan Medali Podium
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
