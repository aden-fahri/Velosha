<div>
    <!-- Category Tabs -->
    <section class="bg-white border-b border-veloborder sticky top-20 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center gap-2 overflow-x-auto" role="tablist" aria-label="Kategori race">
                @foreach($tabs as $tab)
                <button type="button"
                        wire:key="tab-{{ $tab['key'] }}"
                        wire:click="filter('{{ $tab['key'] }}')"
                        role="tab"
                        aria-selected="{{ $category === $tab['key'] ? 'true' : 'false' }}"
                        class="px-4 py-2 text-xs velo-box-1 whitespace-nowrap transition-colors {{ $category === $tab['key'] ? 'bg-race-300 text-race-900 font-bold' : 'bg-white text-teal-700 hover:text-race-600 font-medium border border-veloborderstrong' }}">{{ $tab['label'] }}</button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Races -->
    <section class="py-16 bg-velosurfacealt border-b border-veloborder">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-6">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight">Pendaftaran Terbuka &amp; Mendatang</h2>
                <a href="/events" wire:navigate class="inline-flex items-center text-xs font-semibold text-race-600 hover:gap-2 transition-all">
                    <span>Lihat Semua</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>

            {{-- Banner di atas kartu: gambar dari admin (Panel > Banner Race), fallback public/images/race-banner.*, lalu placeholder --}}
            @php
                $raceBanner = null;
                $adminBanner = \App\Models\RaceBannerContent::current()->image_path;
                if ($adminBanner && is_file(public_path('storage/' . ltrim($adminBanner, '/')))) {
                    $raceBanner = 'storage/' . ltrim($adminBanner, '/');
                } else {
                    foreach (['images/race-banner.jpg', 'images/race-banner.png', 'images/race-banner.webp'] as $rb) {
                        if (file_exists(public_path($rb))) { $raceBanner = $rb; break; }
                    }
                }
                $raceBannerDims = [1216, 192];
                if ($raceBanner) {
                    $dims = @getimagesize(public_path($raceBanner));
                    if ($dims) { $raceBannerDims = [$dims[0], $dims[1]]; }
                }
            @endphp
            {{-- Dengan gambar: proporsional ringkas; tanpa gambar: slot placeholder --}}
            <div class="relative w-full h-36 sm:h-40 md:h-44 mb-10 overflow-hidden velo-box-1 border border-veloborderstrong"
                 style="background-color:#0A353E;background-image:url('/images/patterns/topo.svg'),linear-gradient(135deg,#0E4B57 0%,#0A353E 100%);background-size:480px,auto;box-shadow:inset 0 -3px 0 #DE7119">
                @if ($raceBanner)
                    <img src="{{ asset($raceBanner) }}" alt="Banner race" width="{{ $raceBannerDims[0] }}" height="{{ $raceBannerDims[1] }}" loading="lazy"
                         class="block w-full h-full object-cover">
                @endif
                <div class="absolute inset-x-0 bottom-0 z-10 bg-race-500" style="height:3px" aria-hidden="true"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-150"
                 wire:loading.class="opacity-50"
                 wire:target="filter">

                @forelse($races as $race)
                <article wire:key="race-{{ $loop->index }}" class="group flex flex-col bg-white border border-veloborder velo-box-1 overflow-hidden hover:shadow-lg transition-all">
                    <div class="relative h-52 overflow-hidden bg-velosurface">
                        <img src="{{ $race['img'] }}" alt="{{ $race['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            <span class="{{ $race['catClass'] }} text-white text-[10px] font-bold uppercase tracking-wider px-2 py-1 velo-box-3">{{ $race['cat'] }}</span>
                            <span class="bg-white/95 text-teal-800 text-[10px] font-bold px-2 py-1 velo-box-3 border border-veloborder">{{ $race['dist'] }}</span>
                        </div>
                    </div>

                    <div class="p-5 flex-grow flex flex-col">
                        <div class="flex items-center text-[11px] text-velomutedlight gap-2 mb-2">
                            <span class="flex items-center">
                                <i data-lucide="calendar" class="w-3 h-3 mr-1 text-velomuted"></i>{{ $race['date'] }}
                            </span>
                            <span class="text-[#b8bfbe]">|</span>
                            <span class="truncate">{{ $race['loc'] }}</span>
                        </div>

                        <h3 class="text-base font-bold text-teal-900 group-hover:text-race-600 transition-colors">{{ $race['title'] }}</h3>

                        <div class="mt-4 pt-4 border-t border-velosurface grid grid-cols-3 gap-2 text-center">
                            @foreach($race['specs'] as $label => $value)
                            <div class="bg-velosurfacealt p-2 velo-box-3 border border-velosurface">
                                <div class="text-[10px] text-velomuted uppercase font-semibold">{{ $label }}</div>
                                <div class="text-[11px] font-bold text-teal-800 mt-0.5">{{ $value }}</div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-auto pt-5 flex items-end justify-between">
                            <div>
                                <div class="text-[10px] uppercase tracking-wider text-velomuted font-semibold">Mulai</div>
                                <div class="text-base font-extrabold text-teal-900">Rp {{ $race['price'] }}</div>
                            </div>
                            <a href="{{ $race['url'] }}" wire:navigate class="inline-flex items-center text-xs font-semibold text-white bg-race-600 hover:bg-race-700 px-4 py-2.5 velo-box-1 transition">
                                <span>Daftar</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                            </a>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full border border-veloborder velo-box-1 bg-white px-6 py-16 text-center">
                    <div class="w-12 h-12 mx-auto mb-4 bg-white text-velomuted border border-veloborder velo-box-1 flex items-center justify-center">
                        <i data-lucide="calendar-x" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-teal-900 mb-1.5">Belum ada race di kategori ini</h3>
                    <p class="text-xs text-velomutedlight max-w-sm mx-auto mb-6">Jadwal untuk kategori tersebut sedang disiapkan. Lihat kalender lengkap untuk race lainnya.</p>
                    <a href="/events" wire:navigate class="inline-flex items-center text-xs font-semibold text-white bg-race-600 hover:bg-race-700 px-4 py-2.5 velo-box-1 transition">
                        <span>Lihat Semua Race</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                    </a>
                </div>
                @endforelse

            </div>
        </div>
    </section>
</div>
