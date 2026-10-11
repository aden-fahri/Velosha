@use('App\Support\EventCardPresenter', 'P')
{{-- Timeline race weekend: Race Pack Collection (H-2/H-1) & Race Day --}}
<div>
    <h2 class="text-xl font-extrabold text-teal-900 tracking-tight mb-4">Jadwal Race Weekend</h2>
    <div class="space-y-3">
        <div class="flex items-start space-x-4 p-4 border border-veloborder bg-white velo-box-1">
            <div class="w-16 text-center shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-velomuted block">{{ P::hari($rpcStart) }} - {{ P::hari($rpcEnd) }}</span>
                <span class="text-sm font-extrabold text-teal-900">{{ $rpcLabel }}</span>
            </div>
            <div class="border-l border-veloborder pl-4 flex-grow text-xs">
                <strong class="text-sm font-bold text-teal-900 block">Race Pack Collection (RPC) &amp; Bike Expo</strong>
                <p class="text-velomutedlight mt-1">10:00 - 20:00 WIB. Wajib membawa E-Ticket QR Code dan identitas diri.</p>
            </div>
        </div>

        <div class="flex items-start space-x-4 p-4 border border-veloborder bg-white velo-box-1">
            <div class="w-16 text-center shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">{{ P::hari($eventDate) }}</span>
                <span class="text-sm font-extrabold text-emerald-800">{{ $raceLabel }}</span>
            </div>
            <div class="border-l border-veloborder pl-4 flex-grow text-xs">
                <strong class="text-sm font-bold text-teal-900 block">Race Day — {{ $event->type_label }}</strong>
                <div class="text-velomutedlight mt-1 space-y-1">
                    <div><strong>04:45 WIB:</strong> Open Gate &amp; Loading Corral</div>
                    @foreach ($categories as $category)
                    <div>
                        @php
                            $waveParts = array_map('trim', explode(' - ', (string) $category->wave_start_time, 2));
                            $waveTime = isset($waveParts[1]) ? $waveParts[1] : $category->wave_start_time;
                            $waveName = $waveParts[0];
                        @endphp
                        <strong>{{ $waveTime }}:</strong> {{ $waveName }} ({{ $category->name }} {{ P::distance($category->distance_km) }})
                    </div>
                    @endforeach
                    @if ($maxCutOff !== null)
                    <div><strong>{{ $cutoffEnd }} WIB:</strong> Course Cut-Off Time &amp; Podium Ceremony</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
