@use('App\Support\EventCardPresenter', 'P')
{{-- 4 Kartu Sorotan Spesifikasi Cepat (Neobrutalisme Velosha) --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)] text-center transition-transform hover:-translate-y-0.5 flex flex-col justify-center min-h-[96px] sm:min-h-[112px]">
        <div class="text-[11px] font-black uppercase tracking-wider text-[var(--mute)]">Jarak Tempuh</div>
        <div class="font-heading text-xl sm:text-2xl font-black text-[var(--ink)] mt-1 tracking-tight">{{ P::distance($maxDistance) }}</div>
    </div>
    <div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)] text-center transition-transform hover:-translate-y-0.5 flex flex-col justify-center min-h-[96px] sm:min-h-[112px]">
        <div class="text-[11px] font-black uppercase tracking-wider text-[var(--mute)]">Total Elevasi</div>
        <div class="font-heading text-xl sm:text-2xl font-black mt-1 tracking-tight" style="color: #DE7119 !important;">{{ $maxElevation === null ? '—' : '+'.P::angka((float) $maxElevation).' M' }}</div>
    </div>
    <div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)] text-center transition-transform hover:-translate-y-0.5 flex flex-col justify-center min-h-[96px] sm:min-h-[112px]">
        <div class="text-[11px] font-black uppercase tracking-wider text-[var(--mute)]">Cut-Off Time</div>
        <div class="font-heading text-xl sm:text-2xl font-black text-[var(--ink)] mt-1 tracking-tight leading-tight">
            {{ $maxCutOff === null ? '—' : P::jam((float) $maxCutOff) }}
            @if ($cutoffEnd !== '—')
                <span class="block text-xs sm:text-sm font-bold text-[var(--mute)] mt-0.5">({{ $cutoffEnd }} WIB)</span>
            @endif
        </div>
    </div>
    <div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)] text-center transition-transform hover:-translate-y-0.5 flex flex-col justify-center min-h-[96px] sm:min-h-[112px]">
        <div class="text-[11px] font-black uppercase tracking-wider text-[var(--mute)]">Kuota Tersisa</div>
        <div class="font-heading text-xl sm:text-2xl font-black mt-1 tracking-tight" style="color: #1F6F4A !important;">{{ $totalRemaining }} Orang</div>
    </div>
</div>
