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

                <a href="/events" wire:navigate class="btn btn-sm btn-ghost hidden sm:inline-flex">
                    <span>Lihat Semua Kalender</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Races Section -->
    <section class="py-14 bg-[var(--paper)]">
        <div class="wrap">

            <!-- Banner Velosha Bergerak dengan Animasi Sepeda .bk Sesuai Mockup -->
            <div class="card relative h-[220px] mb-12 overflow-hidden border-[3px] border-[var(--ink)] bg-[#F6B777]">
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

                <!-- Teks Kartun Velosha Besar sesuai Mockup -->
                <div class="relative z-10 px-8 py-6 flex flex-col justify-center h-full">
                    <span class="chip chip-mustard w-max">Musim 2026/27</span>
                    <div class="font-heading font-black text-6xl sm:text-8xl md:text-9xl tracking-tight text-[#FBF4E6] mt-2 select-none"
                         style="-webkit-text-stroke: 3.5px #10282E; text-shadow: 6px 6px 0 #10282E;">
                        VELOSHA
                    </div>
                </div>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-opacity duration-150"
                 wire:loading.class="opacity-50"
                 wire:target="filter">

                @forelse($races as $race)
                <article wire:key="race-{{ $loop->index }}" class="card card-hover flex flex-col overflow-hidden bg-[var(--card)]">
                    {{-- Header Poster Image --}}
                    <div class="relative h-52 overflow-hidden border-b-[2.5px] border-[var(--ink)] bg-[var(--paper2)]">
                        <img src="{{ $race['img'] }}" alt="{{ $race['title'] }}" class="w-full h-full object-cover">
                        <div class="absolute top-3 left-3 flex gap-2">
                            <span class="chip chip-orange">{{ $race['cat'] }}</span>
                            <span class="chip bg-[var(--card)]">{{ $race['dist'] }}</span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-5 flex-grow flex flex-col">
                        <div class="flex items-center text-xs font-bold text-[var(--mute)] gap-2 mb-2">
                            <span class="flex items-center">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1 text-[var(--orange)]"></i>{{ $race['date'] }}
                            </span>
                            <span>•</span>
                            <span class="truncate flex items-center">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1 text-[var(--orange)]"></i>{{ $race['loc'] }}
                            </span>
                        </div>

                        <h3 class="text-lg font-heading font-extrabold text-[var(--ink)] line-clamp-2 leading-tight">
                            {{ $race['title'] }}
                        </h3>

                        {{-- Stats 3-Col --}}
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            @foreach($race['specs'] as $label => $value)
                            <div class="bg-[var(--paper2)] p-2 rounded-lg border-[2px] border-[var(--ink)]">
                                <div class="text-[10px] text-[var(--mute)] uppercase font-bold tracking-wider">{{ $label }}</div>
                                <div class="text-xs font-heading font-extrabold text-[var(--ink)] mt-0.5">{{ $value }}</div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Footer with Price & CTA Button --}}
                        <div class="mt-auto pt-6 flex items-end justify-between border-t-[2px] border-dashed border-[var(--line)]">
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-[var(--mute)] font-bold">Mulai dari</div>
                                <div class="text-xl font-heading font-extrabold text-[var(--ink)]">Rp {{ $race['price'] }}</div>
                            </div>
                            <a href="{{ $race['url'] }}" wire:navigate class="btn btn-sm">
                                <span>Lihat detail</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                            </a>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full py-16 text-center card bg-[var(--paper2)] p-8">
                    <p class="font-heading font-extrabold text-xl text-[var(--ink)]">Belum ada race pada kategori ini</p>
                    <p class="text-sm text-[var(--mute)] mt-1">Coba pilih kategori balap lainnya di atas.</p>
                </div>
                @endforelse

            </div>

        </div>
    </section>
</div>
