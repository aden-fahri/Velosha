@extends('layouts.app', ['title' => $event->title . ' — Detail Race & Registrasi'])
@use('App\Support\EventCardPresenter', 'P')

@section('content')
@php
    $categories = $event->categories;
    $photos = $event->photos;
    $maxDistance = $categories->max('distance_km');
    $maxElevation = $categories->max('elevation_gain_m');
    $maxCutOff = $categories->max('cut_off_hours');
    $lowestPrice = $categories->min('price');
    $totalRemaining = (int) $categories->sum(fn ($category) => $category->remaining);
    $startTime = $event->start_time ? substr((string) $event->start_time, 0, 5) : '05:45';
    $cutoffEnd = ($event->start_time && $maxCutOff !== null)
        ? \Illuminate\Support\Carbon::parse((string) $event->start_time)->addHours((float) $maxCutOff)->format('H:i')
        : '—';
    $eventDate = \Illuminate\Support\Carbon::parse($event->event_date);
    $rpcStart = $eventDate->copy()->subDays(2);
    $rpcEnd = $eventDate->copy()->subDays(1);
    $monthShort = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $rpcLabel = $rpcStart->format('j') . '-' . $rpcEnd->format('j') . ' ' . $monthShort[(int) $rpcEnd->format('n')];
    $raceLabel = $eventDate->format('j') . ' ' . $monthShort[(int) $eventDate->format('n')];
@endphp
<!-- Clean Page Header -->
<div class="bg-[#2E343B] text-white py-8 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <span class="inline-block text-[11px] font-bold uppercase tracking-[0.2em] text-race-400 mb-2">Detail Balap Resmi</span>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight">
                    {{ $event->title }}
                </h1>
                <div class="flex flex-wrap items-center gap-4 text-xs text-[#d1d5db] mt-2">
                    <span class="flex items-center"><i data-lucide="calendar" class="w-3.5 h-3.5 mr-1 text-[#9ca3af]"></i>{{ P::tanggalLengkap($eventDate) }}</span>
                    <span>&bull;</span>
                    <span class="flex items-center"><i data-lucide="clock" class="w-3.5 h-3.5 mr-1 text-[#9ca3af]"></i>Start: {{ $startTime }} WIB</span>
                    <span>&bull;</span>
                    <span class="flex items-center"><i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1 text-[#9ca3af]"></i>{{ $event->location_name }}</span>
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <span class="bg-race-500 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 velo-box-3">{{ $event->type_label }}</span>
                    <span class="{{ $eventDate->isFuture() && $totalRemaining > 0 ? 'bg-emerald-500/30 text-emerald-100 border border-emerald-300/40' : ($eventDate->isFuture() ? 'bg-white/20 text-white border border-white/30' : 'bg-black/20 text-white border border-white/20') }} text-[10px] font-bold px-2 py-0.5 velo-box-3">
                        {{ $eventDate->isFuture() ? ($totalRemaining > 0 ? 'Registrasi Dibuka' : 'Slot Penuh') : 'Event Selesai' }}
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3.5 py-2 text-xs font-medium velo-box-1 transition">
                    <i data-lucide="share-2" class="w-3.5 h-3.5 mr-1.5 text-[#9ca3af]"></i>
                    <span>Bagikan</span>
                </button>
                <button class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3.5 py-2 text-xs font-medium velo-box-1 transition">
                    <i data-lucide="calendar-plus" class="w-3.5 h-3.5 mr-1.5 text-[#9ca3af]"></i>
                    <span>Add to Calendar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Photo Gallery Grid (1 large + up to 4 small grid) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if ($photos->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 h-80 sm:h-96 md:h-[460px]">
        <!-- Big Photo (Col 1-2) -->
        <div class="md:col-span-2 h-full overflow-hidden bg-velosurface velo-box-1 relative group cursor-pointer">
            <img src="{{ P::photoUrl($photos->first()) }}"
                 alt="{{ $event->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <div class="absolute bottom-3 left-3 bg-race-600/80 backdrop-blur-sm text-white text-[11px] px-2.5 py-1 velo-box-3 border border-white/20">
                {{ $event->location_name }}
            </div>
        </div>

        <!-- Grid Photos (Col 3-4) -->
        <div class="md:col-span-2 grid grid-cols-2 gap-3 h-full">
            @foreach ($photos->slice(1, 4) as $photo)
            <div class="overflow-hidden bg-velosurface velo-box-1 relative group cursor-pointer">
                <img src="{{ P::photoUrl($photo) }}"
                     alt="Foto {{ $event->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute bottom-2 left-2 bg-black/60 text-white text-[10px] px-2 py-0.5 velo-box-3">
                    {{ $event->location_name }}
                </div>
            </div>
            @endforeach
            @if ($photos->count() > 5)
            <div class="overflow-hidden bg-velosurface velo-box-1 relative group cursor-pointer">
                <img src="{{ P::photoUrl($photos->get(5) ?? $photos->first()) }}"
                     alt="Foto {{ $event->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute bottom-2 right-2 bg-race-600 text-white text-[10px] font-semibold px-2 py-1 velo-box-3 flex items-center">
                    <i data-lucide="image" class="w-3 h-3 mr-1"></i>
                    <span>+{{ $photos->count() - 5 }} Foto Galeri</span>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="h-80 sm:h-96 overflow-hidden bg-velosurface velo-box-1 relative">
        <img src="/images/hero-peloton.jpg" alt="{{ $event->title }}" class="w-full h-full object-cover">
        <div class="absolute bottom-3 left-3 bg-race-600/80 backdrop-blur-sm text-white text-[11px] px-2.5 py-1 velo-box-3 border border-white/20">
            {{ $event->location_name }}
        </div>
    </div>
    @endif
</section>

<!-- Main Detail Content & Sticky Checkout Sidebar -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

        <!-- Left: Course Overview, Maps & Itinerary (8 Cols) -->
        <div class="lg:col-span-8 space-y-12">

            <!-- Quick Spec Highlights Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 bg-velosurfacealt border border-veloborder velo-box-1 text-center">
                <div>
                    <div class="text-[11px] text-[#5f6a70] uppercase font-semibold">Jarak Tempuh</div>
                    <div class="text-base font-extrabold text-teal-900 mt-1">{{ P::distance($maxDistance) }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-[#5f6a70] uppercase font-semibold">Total Elevasi</div>
                    <div class="text-base font-extrabold text-teal-900 mt-1">{{ $maxElevation === null ? '—' : '+'.P::angka((float) $maxElevation).' M' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-[#5f6a70] uppercase font-semibold">Cut-Off Time</div>
                    <div class="text-base font-extrabold text-teal-900 mt-1">{{ $maxCutOff === null ? '—' : P::jam((float) $maxCutOff).' ('.$cutoffEnd.')' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-[#5f6a70] uppercase font-semibold">Kuota Tersisa</div>
                    <div class="text-base font-extrabold text-teal-900 mt-1">{{ $totalRemaining }} Orang</div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h2 class="text-xl font-extrabold text-teal-900 tracking-tight mb-4">Tentang Rute &amp; Pengalaman</h2>
                <div class="text-sm text-[#4b555b] leading-relaxed space-y-4">
                    @if(strip_tags((string) $event->description) !== (string) $event->description)
                        <div class="prose max-w-none text-sm text-[#4b555b] leading-relaxed">
                            {!! $event->description !!}
                        </div>
                    @else
                        {!! nl2br(e($event->description)) !!}
                    @endif
                </div>
            </div>

            @if ($event->youtube_video_id)
            <!-- Official Video -->
            <div>
                <h2 class="text-xl font-extrabold text-teal-900 tracking-tight mb-4">Video Resmi Event</h2>
                <div class="border border-veloborderstrong velo-box-1 overflow-hidden">
                    <div class="aspect-video w-full">
                        <iframe src="https://www.youtube.com/embed/{{ $event->youtube_video_id }}"
                                class="w-full h-full" allowfullscreen loading="lazy"
                                title="Video {{ $event->title }}"></iframe>
                    </div>
                </div>
            </div>
            @endif

            <!-- Interactive GPS Map & Course Profile -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">Peta Sirkuit &amp; Pos Hidrasi</h2>
                </div>

                <div class="border border-veloborderstrong velo-box-1 overflow-hidden">
                    @if($event->map_image_path)
                        <div class="h-96 w-full bg-velosurfacealt">
                            <img src="{{ asset('storage/' . ltrim($event->map_image_path, '/')) }}"
                                 alt="Peta sirkuit {{ $event->title }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="h-96 w-full bg-velosurfacealt flex flex-col items-center justify-center text-center px-6">
                            <i data-lucide="map" class="w-10 h-10 text-[#9ca3af] mb-3"></i>
                            <p class="text-sm font-bold text-teal-900">Peta rute belum tersedia</p>
                            <p class="text-xs text-[#556066] mt-1">Panitia belum mengunggah gambar peta sirkuit untuk event ini.</p>
                        </div>
                    @endif
                    <!-- Aid Station Legend -->
                    <div class="p-4 bg-velosurfacealt border-t border-veloborder grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-teal-500 velo-box-3"></span>
                            <span><strong>KM 35:</strong> AS 1 (Hydration)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-teal-700 velo-box-3"></span>
                            <span><strong>KM 72:</strong> AS 2 (Energy Gel)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-race-500 velo-box-3"></span>
                            <span><strong>KM 98:</strong> AS 3 (KOM Summit)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-emerald-600 velo-box-3"></span>
                            <span><strong>KM 115:</strong> AS 4 (Final Push)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Race Weekend Schedule / Timeline -->
            <div>
                <h2 class="text-xl font-extrabold text-teal-900 tracking-tight mb-4">Jadwal Race Weekend</h2>
                <div class="space-y-3">
                    <div class="flex items-start space-x-4 p-4 border border-veloborder bg-white velo-box-1">
                        <div class="w-16 text-center shrink-0">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#5f6a70] block">{{ P::hari($rpcStart) }} - {{ P::hari($rpcEnd) }}</span>
                            <span class="text-sm font-extrabold text-teal-900">{{ $rpcLabel }}</span>
                        </div>
                        <div class="border-l border-veloborder pl-4 flex-grow text-xs">
                            <strong class="text-sm font-bold text-teal-900 block">Race Pack Collection (RPC) &amp; Bike Expo</strong>
                            <p class="text-[#556066] mt-1">10:00 - 20:00 WIB. Wajib membawa E-Ticket QR Code dan identitas diri.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 p-4 border border-veloborder bg-white velo-box-1">
                        <div class="w-16 text-center shrink-0">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">{{ P::hari($eventDate) }}</span>
                            <span class="text-sm font-extrabold text-emerald-800">{{ $raceLabel }}</span>
                        </div>
                        <div class="border-l border-veloborder pl-4 flex-grow text-xs">
                            <strong class="text-sm font-bold text-teal-900 block">Race Day — {{ $event->type_label }}</strong>
                            <div class="text-[#4b555b] mt-1 space-y-1">
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

        </div>

        <!-- Right: Sticky Booking Box (4 Cols) -->
        <div class="lg:col-span-4 sticky top-24">
            <div class="bg-white border border-veloborderstrong p-6 velo-box-1 shadow-md">

                <div class="pb-4 border-b border-veloborder flex justify-between items-baseline">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#5f6a70]">Pilih Kategori Jarak</span>
                        <div class="text-2xl font-extrabold text-teal-900 mt-0.5">Rp {{ $lowestPrice === null ? '—' : P::angka((float) $lowestPrice) }}</div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 velo-box-3 border {{ $totalRemaining > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                        {{ $totalRemaining > 0 ? 'Slot Tersisa '.$totalRemaining : 'Slot Penuh' }}
                    </span>
                </div>

                <!-- Select Category Radios -->
                <div class="my-5 space-y-2.5">
                    @forelse ($categories as $category)
                    <label class="flex items-center justify-between p-3.5 velo-box-3 cursor-pointer {{ $loop->first ? 'border-2 border-race-500 bg-race-50/50' : 'border border-veloborder hover:border-[#93a0a1] bg-white' }}">
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="race_category" value="{{ $category->code }}" data-category="{{ $category->id }}" {{ $loop->first ? 'checked' : '' }} class="text-teal-900 focus:ring-race-500">
                            <div>
                                <div class="text-xs font-bold text-teal-900">{{ $category->name }} ({{ P::distance($category->distance_km) }})</div>
                                <div class="text-[10px] text-[#556066]">Elevasi +{{ P::angka((float) $category->elevation_gain_m) }}m &middot; COT {{ P::jam((float) $category->cut_off_hours) }}</div>
                            </div>
                        </div>
                        <div class="text-xs font-extrabold text-teal-900">Rp {{ P::angka((float) $category->price) }}</div>
                    </label>
                    @empty
                    <div class="p-4 border border-veloborder bg-velosurfacealt velo-box-3 text-xs text-[#556066] text-center">
                        Kategori jarak belum tersedia untuk event ini.
                    </div>
                    @endforelse
                </div>

                <!-- Registration Inclusions Checklist -->
                <div class="py-4 border-t border-veloborder text-xs text-[#4b555b] space-y-2">
                    <div class="font-bold text-teal-900 text-xs mb-1">Paket Registrasi Termasuk:</div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Nomor BIB Resmi + RFID Timing Chip</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Medali Logam Finisher (Bagi yang lolos COT)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Akses Pos Hidrasi &amp; Nutrisi</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Asuransi Medis Kecelakaan Resmi</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Jamuan Makan Siang Finisher Meal</span>
                    </div>
                </div>

                @if ($event->gpx_file_path)
                <a href="{{ asset('storage/'.ltrim($event->gpx_file_path, '/')) }}"
                   class="mt-3 w-full flex items-center justify-center bg-white hover:bg-velosurfacealt text-teal-800 border border-veloborderstrong font-semibold py-2.5 text-xs velo-box-1 transition">
                    <i data-lucide="download" class="w-3.5 h-3.5 mr-1.5"></i>
                    <span>Unduh Berkas GPX Rute</span>
                </a>
                @endif

                <!-- Checkout CTA -->
                @php($loggedInNonPeserta = Auth::check() && ! Auth::user()->isPeserta())
                @if ($loggedInNonPeserta)
                <button type="button" onclick="openRoleWarning()" class="mt-4 w-full flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold py-3.5 text-sm velo-box-1 shadow-sm transition">
                    <span>Lanjut ke Formulir Pendaftaran</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </button>
                @else
                <a href="/checkout?event={{ $event->slug }}" id="checkout-cta" wire:navigate class="mt-4 w-full flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold py-3.5 text-sm velo-box-1 shadow-sm transition">
                    <span>Lanjut ke Formulir Pendaftaran</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
                @endif

                <div class="mt-3 text-center text-[11px] text-[#5f6a70]">
                    Konfirmasi instan via Midtrans &mdash; E-Ticket langsung terbit
                </div>

            </div>
        </div>

    </div>
</section>

@php($loggedInNonPeserta = Auth::check() && ! Auth::user()->isPeserta())
@if ($loggedInNonPeserta)
<!-- Peringatan: pendaftaran race khusus akun Peserta -->
<div id="roleWarningModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" onclick="closeRoleWarning()">
    <div class="bg-white max-w-md w-full velo-box-1 border border-veloborderstrong shadow-2xl overflow-hidden velo-modal-enter" onclick="event.stopPropagation()">
        <div class="bg-amber-600 text-white px-5 py-3.5 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                <span class="font-extrabold text-sm tracking-tight">AKUN TIDAK SESUAI</span>
            </div>
            <button type="button" onclick="closeRoleWarning()" class="text-white/70 hover:text-white" aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-[#4b555b] leading-relaxed">
                Pendaftaran race hanya dapat dilakukan dengan akun <strong class="text-teal-900">Peserta</strong>.
                Anda sedang login sebagai <strong class="text-teal-900">{{ Auth::user()->isPenyelenggara() ? 'Penyelenggara' : 'Admin' }}</strong>,
                sehingga formulir pendaftaran tidak dapat dibuka.
            </p>
            <p class="text-xs text-[#556066]">
                Untuk mendaftar race, silakan keluar dan masuk kembali dengan akun Peserta Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-2 pt-1">
                <button type="button" onclick="closeRoleWarning()" class="flex-1 bg-race-600 hover:bg-race-700 text-white font-semibold py-3 text-xs velo-box-1 shadow-sm transition">
                    Mengerti
                </button>
                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-white hover:bg-velosurfacealt text-teal-800 border border-veloborderstrong font-semibold py-3 text-xs velo-box-1 transition">
                        Keluar &amp; Ganti Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    @if ($loggedInNonPeserta)
    function openRoleWarning() {
        var modal = document.getElementById('roleWarningModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        var box = modal.querySelector('.velo-modal-enter');
        box.classList.remove('velo-modal-enter');
        void box.offsetWidth;
        box.classList.add('velo-modal-enter');
    }
    function closeRoleWarning() {
        var modal = document.getElementById('roleWarningModal');
        if (modal) modal.classList.add('hidden');
    }
    @endif

    (function () {
        var cta = document.getElementById('checkout-cta');
        if (!cta) return;
        var slug = @json($event->slug);

        function syncCta() {
            var checked = document.querySelector('input[name="race_category"]:checked');
            var id = checked ? checked.getAttribute('data-category') : null;
            if (cta) {
                cta.setAttribute('href', '/checkout?event=' + slug + (id ? '&category=' + id : ''));
            }

            document.querySelectorAll('input[name="race_category"]').forEach(function (radio) {
                var label = radio.closest('label');
                if (!label) return;
                if (radio.checked) {
                    label.classList.add('border-2', 'border-race-500', 'bg-race-50/50');
                    label.classList.remove('border-veloborder', 'bg-white');
                } else {
                    label.classList.remove('border-2', 'border-race-500', 'bg-race-50/50');
                    label.classList.add('border-veloborder', 'bg-white');
                }
            });
        }

        document.querySelectorAll('input[name="race_category"]').forEach(function (radio) {
            radio.addEventListener('change', syncCta);
        });

        syncCta();
    })();
</script>
@endsection
