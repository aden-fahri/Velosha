<div>
    <!-- Category Filter Bar (Cartoon Style) -->
    <section class="bg-[var(--paper)] border-b-[3px] border-[var(--ink)] sticky top-[76px] z-30 py-4">
        <div class="wrap">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-2.5 overflow-x-auto scrollbar-hide py-1" role="tablist" aria-label="Kategori race">
                    @foreach($tabs as $tab)
                    <button type="button"
                            wire:key="tab-{{ $tab['key'] }}"
                            wire:click="filter('{{ $tab['key'] }}')"
                            role="tab"
                            aria-selected="{{ $category === $tab['key'] ? 'true' : 'false' }}"
                            class="filter {{ $category === $tab['key'] ? 'on' : '' }}">
                        {{ $tab['label'] }}
                    </button>
                    @endforeach
                </div>

                <div class="flex items-center gap-3">
                    <a href="/events" wire:navigate.hover class="btn btn-sm btn-ghost">
                        <span>Lihat Semua Kalender</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Races Section -->
    <section class="py-14 bg-[var(--paper)]">
        <div class="wrap">

            @php
                $raceBanner = null;
                $adminBanner = \App\Models\RaceBannerContent::current()->image_path;
                if ($adminBanner) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($adminBanner) || is_file(public_path('storage/' . ltrim($adminBanner, '/')))) {
                        $raceBanner = 'storage/' . ltrim($adminBanner, '/');
                    }
                } else {
                    foreach (['images/race-banner.jpg', 'images/race-banner.png', 'images/race-banner.webp'] as $rb) {
                        if (file_exists(public_path($rb))) { $raceBanner = $rb; break; }
                    }
                }
            @endphp
            <!-- Banner Velosha Bergerak dengan Animasi Sepeda .bk Sesuai Mockup -->
            <div data-testid="race-banner-slot" class="card relative h-[220px] mb-12 overflow-hidden border-[3px] border-[var(--ink)] bg-[#F6B777]"
                 style="box-shadow: inset 0 -3px 0 #DE7119">
                @if ($raceBanner)
                    <img src="{{ asset($raceBanner) }}" alt="Banner race" class="w-full h-full object-cover">
                @else
                <!-- Background Panorama Kartun & Gunung SVG -->
                <svg viewBox="0 0 1136 220" preserveAspectRatio="xMidYMid slice" width="100%" height="100%" aria-hidden="true" class="absolute inset-0">
                    <rect width="1136" height="220" fill="#F6B777"></rect>
                    <circle cx="930" cy="76" r="46" fill="#F4BE3F" stroke="#10282E" stroke-width="4"></circle>
                    <path d="M560 190 L680 70 L800 190Z M740 190 L880 50 L1020 190Z M960 190 L1040 100 L1120 190Z" fill="#B2590F" stroke="#10282E" stroke-width="4" stroke-linejoin="round"></path>
                    <path d="M845 78 L880 50 L915 78 L898 72 L880 84 L862 72Z" fill="#FBF4E6" stroke="#10282E" stroke-width="3" stroke-linejoin="round"></path>
                    <path d="M0 170 Q200 130 420 160 T820 150 T1136 150 V220 H0Z" fill="#7DB88A" stroke="#10282E" stroke-width="4"></path>
                    <path d="M0 196 H1136 V220 H0Z" fill="#10282E"></path>
                    <path d="M0 208 H1136" stroke="#FBF4E6" stroke-width="3" stroke-dasharray="16 14"></path>
                </svg>

                <!-- Animasi Sepeda Kartun Melintas (3 Peloton .bk) -->
                <div class="bk" style="animation-delay: 0s">
                    <svg width="84" height="48" viewBox="0 0 70 40" aria-hidden="true">
                        <circle cx="14" cy="28" r="10" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <circle cx="56" cy="28" r="10" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <path d="M14 28 L28 12 H44 L56 28 M28 12 L34 28 H14 M34 28 L44 12" fill="none" stroke="#10282E" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"></path>
                        <circle cx="38" cy="4" r="4.5" fill="#DE7119" stroke="#10282E" stroke-width="2.5"></circle>
                        <path d="M30 12 L37 7 L47 11" fill="none" stroke="#DE7119" stroke-width="5" stroke-linecap="round"></path>
                    </svg>
                </div>
                <div class="bk" style="animation-delay: -5s">
                    <svg width="84" height="48" viewBox="0 0 70 40" aria-hidden="true">
                        <circle cx="14" cy="28" r="10" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <circle cx="56" cy="28" r="10" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <path d="M14 28 L28 12 H44 L56 28 M28 12 L34 28 H14 M34 28 L44 12" fill="none" stroke="#10282E" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"></path>
                        <circle cx="38" cy="4" r="4.5" fill="#1F6F4A" stroke="#10282E" stroke-width="2.5"></circle>
                        <path d="M30 12 L37 7 L47 11" fill="none" stroke="#1F6F4A" stroke-width="5" stroke-linecap="round"></path>
                    </svg>
                </div>
                <div class="bk" style="animation-delay: -10s">
                    <svg width="84" height="48" viewBox="0 0 70 40" aria-hidden="true">
                        <circle cx="14" cy="28" r="10" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <circle cx="56" cy="28" r="10" fill="#FFFDF8" stroke="#10282E" stroke-width="3"></circle>
                        <path d="M14 28 L28 12 H44 L56 28 M28 12 L34 28 H14 M34 28 L44 12" fill="none" stroke="#10282E" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"></path>
                        <circle cx="38" cy="4" r="4.5" fill="#9B3D5A" stroke="#10282E" stroke-width="2.5"></circle>
                        <path d="M30 12 L37 7 L47 11" fill="none" stroke="#9B3D5A" stroke-width="5" stroke-linecap="round"></path>
                    </svg>
                </div>
                @endif

                <!-- Teks Kartun Velosha Besar sesuai Mockup -->
                <div class="relative z-10 px-8 py-6 flex flex-col justify-center h-full">
                    <span class="chip chip-mustard w-max">Musim 2026/27</span>
                    <div class="font-heading font-black text-6xl sm:text-8xl md:text-9xl tracking-tight text-[#FBF4E6] mt-2 select-none"
                         style="-webkit-text-stroke: 3.5px #10282E; text-shadow: 6px 6px 0 #10282E;">
                        VELOSHA
                    </div>
                </div>
            </div>

            <!-- Kontrol Navigasi Horizontal (< - >) Sesuai Permintaan User -->
            <div x-data="{
                    scrollPrev() {
                        const el = this.$refs.slider;
                        if (!el) return;
                        const card = el.querySelector('.race-card');
                        const cardWidth = card ? card.offsetWidth : 320;
                        const gap = 24;
                        el.scrollBy({ left: -(cardWidth + gap), behavior: 'smooth' });
                    },
                    scrollNext() {
                        const el = this.$refs.slider;
                        if (!el) return;
                        const card = el.querySelector('.race-card');
                        const cardWidth = card ? card.offsetWidth : 320;
                        const gap = 24;
                        el.scrollBy({ left: (cardWidth + gap), behavior: 'smooth' });
                    }
                 }">

                <div class="flex items-center justify-between mb-5 px-1 gap-3">
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <h2 class="font-heading font-black text-lg sm:text-2xl text-[var(--ink)] tracking-tight m-0">
                            Pilihan Race Musim Ini
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-[var(--mustard)] border-[1.5px] border-[var(--ink)] font-heading font-black text-[10px] sm:text-[11px] uppercase tracking-wider text-[var(--ink)] shadow-[1.5px_1.5px_0_var(--ink)]">
                            Registrasi Buka
                        </span>
                    </div>

                    <!-- Tombol Panah < dan > Neobrutalist (Hanya Mobile/Tablet, Tersembunyi di Desktop) -->
                    <div class="flex lg:hidden items-center gap-2">
                        <button type="button"
                                @click="scrollPrev()"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg border-[2.5px] border-[var(--ink)] bg-white text-[var(--ink)] shadow-[2.5px_2.5px_0_var(--ink)] flex items-center justify-center font-black hover:-translate-x-0.5 active:translate-y-0.5 transition-all cursor-pointer"
                                aria-label="Event sebelumnya">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <button type="button"
                                @click="scrollNext()"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg border-[2.5px] border-[var(--ink)] bg-[var(--orange)] text-white shadow-[2.5px_2.5px_0_var(--ink)] flex items-center justify-center font-black hover:translate-x-0.5 active:translate-y-0.5 transition-all cursor-pointer"
                                aria-label="Event berikutnya">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>
                </div>

                <!-- Kartu Event Track Horizontal (< - >) Bebas Tabrakan & Snap Mulus -->
                <div x-ref="slider"
                     class="flex items-stretch gap-6 overflow-x-auto pb-6 pt-2 scroll-smooth scrollbar-none"
                     id="race-cards-grid"
                     style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;"
                     wire:loading.class="opacity-60"
                     wire:target="filter">

                    @forelse($races as $race)
                    <article wire:key="race-{{ $loop->index }}"
                             class="shrink-0 w-[290px] sm:w-[330px] md:w-[360px] snap-start race-card group"
                             data-c="{{ $race['cat'] }}"
                             style="scroll-snap-align: start;">

                        <div class="race-card-inner c flex flex-col h-full bg-[var(--card)] rounded-[14px] border-[3px] border-[var(--ink)] shadow-[6px_6px_0_var(--ink)] overflow-hidden transition-all duration-200">
                            {{-- Header Poster Image / Art --}}
                            <div class="race-card-thumb relative overflow-hidden border-b-[3px] border-[var(--ink)] bg-[var(--paper2)]"
                                 style="height: 200px; min-height: 200px; max-height: 200px; width: 100%; position: relative;">
                                <img src="{{ $race['img'] }}"
                                     alt="{{ $race['title'] }}"
                                     class="race-card-img w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-105"
                                     style="width: 100%; height: 100%; object-fit: cover; display: block;" />
                                <div class="absolute top-3 left-3 flex gap-2 z-10">
                                    <span class="chip chip-orange">{{ $race['cat'] }}</span>
                                    <span class="chip bg-[var(--card)]">{{ $race['dist'] }}</span>
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-5 flex flex-col flex-grow justify-between bg-[var(--card)]">
                                <div>
                                    <div class="flex items-center text-xs font-bold gap-2 text-[var(--mute)]">
                                        <span class="flex items-center">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1 text-[var(--orange)]"></i>{{ $race['date'] }}
                                        </span>
                                        <span>•</span>
                                        <span class="truncate flex items-center">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1 text-[var(--orange)]"></i>{{ $race['loc'] }}
                                        </span>
                                    </div>

                                    <h3 class="font-heading font-black text-xl text-[var(--ink)] mt-2.5 mb-2 leading-snug line-clamp-2 min-h-[54px] flex items-center">
                                        <a href="{{ $race['url'] }}" wire:navigate.hover class="hover:text-[var(--orange)] transition-colors">
                                            {{ $race['title'] }}
                                        </a>
                                    </h3>

                                    {{-- Stats 3-Col --}}
                                    <div class="spec" style="margin: 8px 0; padding: 8px 0;">
                                        @foreach($race['specs'] as $label => $value)
                                        <div>
                                            <span>{{ $label }}</span>
                                            <b>{{ $value }}</b>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- Kuota Progress Bar (380 ======------ 500) --}}
                                    @if(($race['totalQuota'] ?? 0) > 0)
                                    <div class="mt-3 mb-1.5 space-y-1.5">
                                        <div class="flex items-center justify-between text-xs font-bold">
                                            <span class="text-[var(--orange)] font-black">
                                                <b>{{ number_format($race['bookedQuota'] ?? 0, 0, ',', '.') }}</b> slot terisi
                                            </span>
                                            <span class="text-[var(--mute)] font-semibold text-[11px]">
                                                kuota <b>{{ number_format($race['totalQuota'] ?? 0, 0, ',', '.') }}</b>
                                            </span>
                                        </div>
                                        <div class="w-full h-3 bg-[#FAF8F3] border-2 border-[var(--ink)] rounded-lg overflow-hidden p-0.5 shadow-[1.5px_1.5px_0_var(--ink)]">
                                            <div class="h-full bg-[var(--orange)] rounded-[4px] border-r-2 border-[var(--ink)] transition-all duration-500"
                                                 style="width: {{ max(4, min(100, $race['percentBooked'] ?? 0)) }}%"></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                {{-- Footer with Price & CTA Button --}}
                                <div class="pt-3 border-t-2 border-[var(--ink)]/15 flex items-center justify-between gap-3">
                                    <div>
                                        <div class="text-[10px] uppercase tracking-wider text-[var(--mute)] font-bold">Mulai dari</div>
                                        <b class="font-heading font-black text-xl text-[var(--ink)]">Rp {{ $race['price'] }}</b>
                                    </div>
                                    <a href="{{ $race['url'] }}" wire:navigate.hover class="btn btn-sm">
                                        <span>Lihat detail</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="col-span-full py-16 text-center card bg-[var(--paper2)] p-8 w-full">
                        <p class="font-heading font-extrabold text-xl text-[var(--ink)]">Belum ada race pada kategori ini</p>
                        <p class="text-sm text-[var(--mute)] mt-1">Coba pilih kategori balap lainnya di atas.</p>
                    </div>
                    @endforelse

                </div>
            </div>

        </div>
    </section>
</div>
