<div>
    <!-- Search & Advanced Filters Bar -->
    <div class="bg-white border-b border-veloborder sticky top-20 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                <!-- Search Keyword -->
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-velomuted absolute left-3 top-3"></i>
                    <input type="text"
                           wire:model.live.debounce.400ms="search"
                           placeholder="Cari event, kota, atau provinsi..."
                           class="w-full pl-9 pr-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
                </div>

                <!-- Filter Kategori Jarak -->
                <div class="relative">
                    <select wire:model.live="type"
                            class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white text-teal-800">
                        @foreach ($typeOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Bulan / Waktu -->
                <div class="relative">
                    <select wire:model.live="month"
                            class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white text-teal-800">
                        <option value="">Semua Bulan</option>
                        @foreach ($monthOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sorting -->
                <div class="relative">
                    <select wire:model.live="sort"
                            class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white text-teal-800">
                        <option value="date-asc">Urutkan: tanggal terdekat</option>
                        <option value="popular">Paling Populer</option>
                        <option value="distance-desc">Jarak terjauh</option>
                        <option value="elevation-desc">Elevasi tertinggi</option>
                        <option value="price-asc">Biaya terendah</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <!-- Main Content Area: Catalog Grid -->
    <div class="py-12 bg-velosurfacealt">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Active Filter tags & Count -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-veloborder mb-8 gap-4">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    @if (count($activeFilters) > 0)
                        <span class="text-velomutedlight font-medium">Filter Aktif:</span>
                        @foreach ($activeFilters as $filter)
                            <span class="inline-flex items-center bg-white border border-veloborderstrong px-2 py-1 velo-box-3 text-teal-700">
                                {{ $filter['label'] }}
                                <button type="button"
                                        wire:click="clearFilter('{{ $filter['key'] }}')"
                                        class="ml-1.5 text-velomuted hover:text-teal-700"
                                        aria-label="Hapus filter">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </span>
                        @endforeach
                        <button type="button" wire:click="resetFilters" class="text-race-500 hover:underline font-semibold ml-2">Reset Semua</button>
                    @else
                        <span class="text-velomutedlight font-medium">Semua race resmi yang terjadwal</span>
                    @endif
                </div>
                <div class="text-xs text-velomutedlight">
                    Menampilkan <strong>{{ $cards->count() }}</strong> dari <strong>{{ $cards->total() }}</strong> hasil filter
                </div>
            </div>

            <!-- events Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-opacity duration-150"
                 wire:loading.class="opacity-50"
                 wire:target="search, type, month, sort, resetFilters, clearFilter">

                @forelse ($cards as $card)
                <article wire:key="event-{{ $card['slug'] }}" class="bg-white border border-veloborder velo-box-1 overflow-hidden hover:shadow-lg transition-all group flex flex-col">
                    <div class="relative h-56 overflow-hidden bg-velosurface">
                        <img src="{{ $card['img'] }}" alt="{{ $card['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                            <span class="{{ $card['catClass'] }} text-white text-[10px] font-bold uppercase tracking-wider px-2 py-1 velo-box-3">{{ $card['cat'] }}</span>
                            <span class="bg-white/95 text-teal-800 text-[10px] font-bold px-2 py-1 velo-box-3 border border-veloborder">{{ $card['dist'] }}</span>
                        </div>
                        <div class="absolute bottom-3 right-3 bg-white/95 text-[11px] font-bold px-2.5 py-0.5 velo-box-3 border {{ $card['slotsFull'] ? 'text-red-700 border-red-200' : 'text-emerald-800 border-emerald-200' }}">
                            {{ $card['slots'] }}
                        </div>
                    </div>
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="flex items-center text-xs text-velomutedlight space-x-3 mb-2">
                                <span class="flex items-center"><i data-lucide="calendar" class="w-3.5 h-3.5 mr-1 text-velomuted"></i>{{ $card['date'] }}</span>
                                <span>&bull;</span>
                                <span class="flex items-center truncate"><i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1 text-velomuted shrink-0"></i>{{ $card['loc'] }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-teal-900 group-hover:text-teal-900 transition-colors">
                                {{ $card['title'] }}
                            </h3>
                            <p class="text-xs text-velomutedlight mt-2 line-clamp-2 leading-relaxed">
                                {{ $card['excerpt'] }}
                            </p>
                            <div class="mt-4 pt-4 border-t border-velosurface grid grid-cols-3 gap-2 text-center">
                                @foreach ($card['specs'] as $label => $value)
                                <div class="bg-velosurfacealt p-2 velo-box-3 border border-velosurface">
                                    <div class="text-[10px] text-velomuted font-semibold">{{ $label }}</div>
                                    <div class="text-xs font-bold text-teal-800 mt-0.5">{{ $value }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-veloborder flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-velomuted font-semibold block">Mulai Dari</span>
                                <span class="text-base font-extrabold text-teal-900">Rp {{ $card['price'] }}</span>
                            </div>
                            <a href="{{ $card['url'] }}" wire:navigate class="inline-flex items-center text-xs font-semibold text-white bg-race-600 hover:bg-race-700 px-4 py-2.5 velo-box-1 transition">
                                <span>Detail &amp; Daftar</span>
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
                    <h3 class="text-base font-bold text-teal-900 mb-1.5">Tidak ada race yang cocok</h3>
                    <p class="text-xs text-velomutedlight max-w-sm mx-auto mb-6">Coba ubah kata kunci atau filter lain, atau lihat seluruh kalender balap.</p>
                    <button type="button" wire:click="resetFilters" class="inline-flex items-center text-xs font-semibold text-white bg-race-600 hover:bg-race-700 px-4 py-2.5 velo-box-1 transition">
                        <span>Reset Filter</span>
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 ml-1"></i>
                    </button>
                </div>
                @endforelse

            </div>

            <!-- Pagination -->
            @if ($cards->hasPages())
            <div class="mt-10">
                {{ $cards->links('velo-pagination') }}
            </div>
            @endif

        </div>
    </div>
</div>
