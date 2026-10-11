@use('App\Support\EventCardPresenter', 'P')
{{-- Strip sorotan spesifikasi cepat: jarak, elevasi, cut-off, sisa kuota --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 bg-velosurfacealt border border-veloborder velo-box-1 text-center">
    <div>
        <div class="text-[11px] text-velomuted uppercase font-semibold">Jarak Tempuh</div>
        <div class="text-base font-extrabold text-teal-900 mt-1">{{ P::distance($maxDistance) }}</div>
    </div>
    <div>
        <div class="text-[11px] text-velomuted uppercase font-semibold">Total Elevasi</div>
        <div class="text-base font-extrabold text-teal-900 mt-1">{{ $maxElevation === null ? '—' : '+'.P::angka((float) $maxElevation).' M' }}</div>
    </div>
    <div>
        <div class="text-[11px] text-velomuted uppercase font-semibold">Cut-Off Time</div>
        <div class="text-base font-extrabold text-teal-900 mt-1">{{ $maxCutOff === null ? '—' : P::jam((float) $maxCutOff).' ('.$cutoffEnd.')' }}</div>
    </div>
    <div>
        <div class="text-[11px] text-velomuted uppercase font-semibold">Kuota Tersisa</div>
        <div class="text-base font-extrabold text-teal-900 mt-1">{{ $totalRemaining }} Orang</div>
    </div>
</div>
