<div>
    <style>
        /* Card Container */
        .events-filter-card {
            background-color: var(--card, #FFFDF8);
            border: 2.5px solid #10282E;
            border-radius: 14px;
            box-shadow: 4px 4px 0 #10282E;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Grid Layout */
        .events-filter-grid {
            display: grid;
            gap: 12px;
            align-items: stretch;
        }

        /* Desktop (width >= 900px) */
        @media (min-width: 900px) {
            .events-filter-grid {
                grid-template-columns: minmax(0, 1fr) 220px 200px;
            }
            .events-filter-grid.has-reset {
                grid-template-columns: minmax(0, 1fr) 220px 200px auto;
            }
        }

        /* Tablet (600px - 899px) */
        @media (min-width: 600px) and (max-width: 899.98px) {
            .events-filter-grid {
                grid-template-columns: 1fr 1fr;
            }
            .events-filter-grid .search-wrap {
                grid-column: 1 / -1;
            }
            .events-filter-grid.has-reset {
                grid-template-columns: 1fr 1fr auto;
            }
            .events-filter-grid.has-reset .search-wrap {
                grid-column: 1 / -1;
            }
        }

        /* Mobile (< 600px) */
        @media (max-width: 599.98px) {
            .events-filter-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }

        /* Controls */
        .search-wrap {
            position: relative;
            height: 52px;
        }
        .search-wrap svg {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            pointer-events: none;
            color: #56666A;
        }
        .search-wrap input {
            width: 100%;
            height: 52px;
            padding: 0 16px 0 46px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            border: 2.5px solid #10282E;
            border-radius: 14px;
            background: #FBF4E6;
            color: #10282E;
            box-sizing: border-box;
            outline: none;
            transition: background-color 0.15s ease;
        }
        .search-wrap input:focus {
            background: #FFFFFF;
        }

        .events-select {
            width: 100%;
            height: 52px;
            box-sizing: border-box;
            padding: 0 40px 0 16px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            border: 2.5px solid #10282E;
            border-radius: 14px;
            background-color: #FBF4E6;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2310282E' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 14px;
            appearance: none;
            -webkit-appearance: none;
            color: #10282E;
            cursor: pointer;
            outline: none;
            transition: background-color 0.15s ease;
        }
        .events-select:focus {
            background-color: #FFFFFF;
        }

        .events-reset-btn {
            height: 52px;
            box-sizing: border-box;
            padding: 0 20px;
            border: 2.5px solid #10282E;
            border-radius: 14px;
            background: #FDE8E8;
            color: #9B1C1C;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            white-space: nowrap;
            transition: background-color 0.15s ease, transform 0.15s ease;
        }
        .events-reset-btn:hover {
            background: #FBD5D5;
            transform: translateY(-1px);
        }

        /* Result Bar */
        .events-result-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin: 28px 0 24px;
        }
        .events-count-text {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 800;
            font-size: 17px;
            color: #10282E;
        }
        .events-sort-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .events-sort-label {
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            color: #56666A;
            white-space: nowrap;
        }
        .events-sort-dropdown {
            min-width: 200px;
            height: 44px;
            box-sizing: border-box;
            border: 2.5px solid #10282E;
            border-radius: 12px;
            background-color: #FBF4E6;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2310282E' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 14px;
            appearance: none;
            -webkit-appearance: none;
            padding: 0 36px 0 14px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #10282E;
            cursor: pointer;
            outline: none;
        }
        .events-sort-dropdown:focus {
            background-color: #FFFFFF;
        }

        /* Promo Card Photo */
        .promo-image-box {
            width: 280px;
            height: 220px;
            max-width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .promo-cyclist-photo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(4px 4px 0 rgba(16, 40, 46, 0.25));
        }
    </style>

    @php
        $hasActiveFilter = ($search !== '' || $type !== 'all' || $month !== '' || $sort !== 'date-asc');
    @endphp

    <!-- Floating Search & Advanced Filters Bar (PROBLEM 1: ONE ROW) -->
    <div style="margin-top: -52px; position: relative; z-index: 20; margin-bottom: 24px;">
        <div class="wrap">
            <div class="events-filter-card">
                <div class="events-filter-grid {{ $hasActiveFilter ? 'has-reset' : '' }}">

                    <!-- Column 1: Search Input (Stretches) -->
                    <div class="search-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text"
                               wire:model.live.debounce.400ms="search"
                               placeholder="Cari event, rute, atau kota...">
                    </div>

                    <!-- Column 2: Category Select (220px on desktop) -->
                    <div>
                        <select wire:model.live="type" class="events-select">
                            @foreach ($typeOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Column 3: Month Select (200px on desktop) -->
                    <div>
                        <select wire:model.live="month" class="events-select">
                            <option value="">Semua Bulan</option>
                            @foreach ($monthOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Column 4: Reset Button (Only rendered when at least one filter or search is active) -->
                    @if ($hasActiveFilter)
                    <div>
                        <button type="button"
                                wire:click="resetFilters"
                                class="events-reset-btn"
                                aria-label="Reset semua filter">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                <path d="M3 3v5h5"></path>
                            </svg>
                            <span>Reset</span>
                        </button>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Catalog Grid (padding-bottom: 110px per PROBLEM 2) -->
    <div style="padding-bottom: 110px;">
        <div class="wrap">

            <!-- PROBLEM 2: Result Bar + Sort Dropdown on ONE row -->
            <div class="events-result-bar">
                <div class="events-count-text">
                    {{ $cards->total() }} event ditemukan
                </div>

                <div class="events-sort-wrapper">
                    <label for="sort-select" class="events-sort-label">Urutkan:</label>
                    <select id="sort-select"
                            wire:model.live="sort"
                            class="events-sort-dropdown">
                        <option value="date-asc">Tanggal terdekat</option>
                        <option value="popular">Paling Populer</option>
                        <option value="distance-desc">Jarak terjauh</option>
                        <option value="elevation-desc">Elevasi tertinggi</option>
                        <option value="price-asc">Biaya terendah</option>
                    </select>
                </div>
            </div>

            <!-- Active Filter tags (bila ada filter aktif) -->
            @if (count($activeFilters) > 0)
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <span class="text-xs font-bold text-[var(--mute)]">Filter Aktif:</span>
                @foreach ($activeFilters as $filter)
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 800; border: 2px solid var(--ink); background: var(--paper); color: var(--ink); box-shadow: 2px 2px 0 var(--ink);">
                        <span>{{ $filter['label'] }}</span>
                        <button type="button"
                                wire:click="clearFilter('{{ $filter['key'] }}')"
                                style="color: var(--mute); font-weight: 900; margin-left: 2px; cursor: pointer;"
                                class="hover:text-red-600"
                                aria-label="Hapus filter">×</button>
                    </span>
                @endforeach
                <button type="button" wire:click="resetFilters" style="font-size: 12px; color: var(--orange); font-weight: 800; margin-left: 6px; cursor: pointer; text-decoration: underline;">Reset Semua</button>
            </div>
            @endif

            <!-- Events Grid (Kartu Balap + SaaS Promo Card) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 transition-opacity duration-150"
                 wire:loading.class="opacity-50"
                 wire:target="search, type, month, sort, resetFilters, clearFilter">

                @forelse ($cards as $card)
                <article wire:key="event-{{ $card['slug'] }}"
                         style="background-color: var(--card); border: 3px solid var(--ink); border-radius: 20px; box-shadow: 6px 6px 0 var(--ink); overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;"
                         class="hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[8px_8px_0_var(--ink)] transition-all group">

                    {{-- Thumbnail & Badges --}}
                    <div class="relative h-52 overflow-hidden border-b-[3px] border-[var(--ink)] bg-[var(--paper2)]">
                        <img src="{{ $card['img'] }}"
                             alt="{{ $card['title'] }}"
                             class="w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-105">
                        <div class="absolute top-3 left-3 flex gap-2 z-10">
                            <span class="chip chip-orange">{{ $card['cat'] }}</span>
                            <span class="chip bg-[var(--card)]">{{ $card['dist'] }}</span>
                        </div>
                        @if($card['slotsFull'])
                            <div class="absolute bottom-3 right-3 bg-red-100 text-red-900 border-2 border-[var(--ink)] text-[11px] font-black px-2.5 py-0.5 rounded-md shadow-[2px_2px_0_var(--ink)]">
                                Penuh
                            </div>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="p-5 flex flex-col flex-grow justify-between bg-[var(--card)]">
                        <div>
                            <div class="flex items-center text-xs font-bold gap-2 text-[var(--mute)]">
                                <span class="flex items-center">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1 text-[var(--orange)]"></i>{{ $card['date'] }}
                                </span>
                                <span>•</span>
                                <span class="truncate flex items-center">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1 text-[var(--orange)]"></i>{{ $card['loc'] }}
                                </span>
                            </div>

                            <h3 class="font-heading font-black text-xl text-[var(--ink)] mt-2.5 mb-2 leading-snug line-clamp-2 min-h-[54px] flex items-center">
                                {{ $card['title'] }}
                            </h3>

                            {{-- 3-Col Stats: Elevasi, Cut-off, Kuota --}}
                            <div class="spec" style="margin: 8px 0; padding: 8px 0;">
                                @foreach ($card['specs'] as $label => $value)
                                <div>
                                    <span>{{ $label }}</span>
                                    <b>{{ $value }}</b>
                                </div>
                                @endforeach
                            </div>

                            {{-- Kuota Progress Bar (380 ======------ 500) --}}
                            @if(($card['totalQuota'] ?? 0) > 0)
                            <div class="mt-2.5 mb-1">
                                <div class="flex items-center justify-between text-[11px] font-extrabold text-[var(--ink)] mb-1">
                                    <span class="flex items-center gap-1.5 text-[var(--orange)]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--orange)] animate-pulse"></span>
                                        <b>{{ number_format($card['bookedQuota'] ?? 0, 0, ',', '.') }}</b> slot terisi
                                    </span>
                                    <span class="text-[var(--mute)] font-bold text-[11px]">
                                        kuota <b>{{ number_format($card['totalQuota'] ?? 0, 0, ',', '.') }}</b>
                                    </span>
                                </div>
                                <div class="w-full h-2.5 bg-[#F3E7CF] border-[1.5px] border-[var(--ink)] rounded-full overflow-hidden p-[1px] shadow-[inset_1px_1px_0_rgba(16,40,46,0.1)]">
                                    <div class="h-full bg-gradient-to-r from-[#F4BE3F] to-[#DE7119] rounded-full border-r-[1.5px] border-[var(--ink)] transition-all duration-500"
                                         style="width: {{ max(4, min(100, $card['percentBooked'] ?? 0)) }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Footer: Harga & Tombol Daftar --}}
                        <div class="pt-3 border-t-2 border-[var(--ink)]/15 flex items-center justify-between gap-3">
                            <div>
                                <div class="text-[10px] uppercase tracking-wider text-[var(--mute)] font-bold">Mulai dari</div>
                                <b class="font-heading font-black text-xl text-[var(--ink)]">Rp {{ $card['price'] }}</b>
                            </div>
                            <a href="{{ $card['url'] }}" wire:navigate
                               style="height: 40px; padding: 0 18px; border: 2px solid var(--ink); border-radius: 10px; background: var(--orange); color: var(--ink); box-shadow: 2.5px 2.5px 0 var(--ink); font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;"
                               class="hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all">
                                <span>Daftar</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full border-[3px] border-[var(--ink)] shadow-[6px_6px_0_var(--ink)] rounded-[20px] bg-[var(--card)] p-12 text-center">
                    <h3 class="font-heading font-black text-2xl text-[var(--ink)] mb-2">Tidak ada race yang cocok</h3>
                    <p class="text-sm text-[var(--mute)] max-w-sm mx-auto mb-6">Coba ubah kata kunci atau reset filter untuk melihat seluruh kalender balap.</p>
                    <button type="button" wire:click="resetFilters" class="btn btn-sm cursor-pointer">
                        <span>Reset Filter</span>
                    </button>
                </div>
                @endforelse

                {{-- SAAS PROMO CARD 2-KOLOM (Left: Teks & Info, Right: Gambar Pesepeda & Dashboard) --}}
                @if ($cards->currentPage() === 1)
                <div class="lg:col-span-2 col-span-1 md:col-span-2"
                     style="background-color: #DE7119; background-image: radial-gradient(rgba(16, 40, 46, 0.08) 1.5px, transparent 1.5px); background-size: 14px 14px; border: 3.5px solid #10282E; border-radius: 22px; box-shadow: 6px 6px 0 #10282E; padding: 32px 32px 46px; position: relative; overflow: hidden; box-sizing: border-box;">

                    <style>
                        .promo-two-columns-layout {
                            display: grid !important;
                            grid-template-columns: minmax(0, 1.25fr) minmax(240px, 1fr) !important;
                            gap: 20px !important;
                            align-items: end !important;
                            position: relative !important;
                            z-index: 10 !important;
                        }
                        @media (max-width: 680px) {
                            .promo-two-columns-layout {
                                grid-template-columns: 1fr !important;
                                gap: 24px !important;
                            }
                        }
                    </style>

                    <!-- Horizontal Grid: KIRI (Teks & Fitur) vs KANAN (Pesepeda & Dashboard) -->
                    <div class="promo-two-columns-layout">

                        <!-- KOLOM KIRI: Teks, Fitur, & Tombol -->
                        <div style="display: flex; flex-direction: column; justify-content: center; z-index: 10;">
                            <!-- Headline Sesuai Gambar 1 -->
                            <h3 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 28px; font-weight: 900; line-height: 1.15; color: #10282E; margin: 0 0 10px 0; letter-spacing: -0.02em;">
                                Punya event sepeda?<br>
                                Buka pendaftarannya di <span style="display: inline-block; background: #10282E; color: #FFFDF8; padding: 2px 9px; border-radius: 8px; box-shadow: 2px 2px 0 rgba(16,40,46,0.3); transform: rotate(-1deg);">Velosha.</span>
                            </h3>

                            <!-- Subtitle Sesuai Gambar 1 -->
                            <p style="font-size: 13.5px; font-weight: 600; line-height: 1.5; color: #10282E; opacity: 0.92; margin: 0 0 16px 0; max-width: 440px;">
                                Pendaftaran online, timing chip, dan race village dalam satu platform. Siap dipakai untuk Gran Fondo sampai Fun Ride.
                            </p>

                            <!-- 3 Mini Feature Pills Sesuai Gambar 1 (Rapi & Tidak Terlalu Ramai) -->
                            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin-bottom: 20px;">
                                <!-- Feature 1: Pendaftaran online -->
                                <div style="background: #FFFDF8; border: 2px solid #10282E; border-radius: 10px; padding: 8px 10px; box-shadow: 2px 2px 0 #10282E;">
                                    <div style="display: flex; align-items: center; gap: 4px; font-weight: 800; font-size: 11.5px; color: #10282E; margin-bottom: 2px; white-space: nowrap;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#DE7119" stroke-width="2.5"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg>
                                        <span>Pendaftaran</span>
                                    </div>
                                    <div style="font-size: 10px; font-weight: 600; color: #56666A; line-height: 1.25;">
                                        Tiket & nomor BIB
                                    </div>
                                </div>

                                <!-- Feature 2: Timing chip RFID -->
                                <div style="background: #FFFDF8; border: 2px solid #10282E; border-radius: 10px; padding: 8px 10px; box-shadow: 2px 2px 0 #10282E;">
                                    <div style="display: flex; align-items: center; gap: 4px; font-weight: 800; font-size: 11.5px; color: #10282E; margin-bottom: 2px; white-space: nowrap;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#DE7119" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span>Timing chip RFID</span>
                                    </div>
                                    <div style="font-size: 10px; font-weight: 600; color: #56666A; line-height: 1.25;">
                                        Real-time garis finis
                                    </div>
                                </div>

                                <!-- Feature 3: Race village -->
                                <div style="background: #FFFDF8; border: 2px solid #10282E; border-radius: 10px; padding: 8px 10px; box-shadow: 2px 2px 0 #10282E;">
                                    <div style="display: flex; align-items: center; gap: 4px; font-weight: 800; font-size: 11.5px; color: #10282E; margin-bottom: 2px; white-space: nowrap;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#DE7119" stroke-width="2.5"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16"/><path d="M2 21h20"/></svg>
                                        <span>Race village</span>
                                    </div>
                                    <div style="font-size: 10px; font-weight: 600; color: #56666A; line-height: 1.25;">
                                        Alur race pack rapi
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons (Daftar & Lihat Paket) -->
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                                @php
                                    $currentUser = auth()->user();
                                    $userOrg = $currentUser?->organizations()->first() ?? $currentUser?->ownedOrganizations()->first();
                                    $targetDaftar = $userOrg ? route('organizations.finance', $userOrg) : route('organizations.create');
                                    $targetPaket = $userOrg ? route('organizations.quota', $userOrg) : route('packages.index');
                                @endphp
                                <a href="{{ $targetDaftar }}" wire:navigate
                                   style="display: inline-flex; align-items: center; gap: 7px; background-color: #10282E; color: #FFFDF8; border: 2px solid #10282E; border-radius: 10px; box-shadow: 2.5px 2.5px 0 rgba(16,40,46,0.35); padding: 11px 18px; font-weight: 800; font-size: 13px; text-decoration: none;"
                                   class="hover:translate-x-[-1px] hover:translate-y-[-1px] hover:bg-[#1F6F4A] transition-all">
                                    <span>{{ $userOrg ? 'Buka Dasbor EO' : 'Daftar jadi penyelenggara' }}</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>

                                <a href="{{ $targetPaket }}" wire:navigate
                                   style="display: inline-flex; align-items: center; gap: 5px; background-color: #FFFDF8; color: #10282E; border: 2px solid #10282E; border-radius: 10px; box-shadow: 2.5px 2.5px 0 #10282E; padding: 11px 16px; font-weight: 800; font-size: 13px; text-decoration: none;"
                                   class="hover:translate-x-[-1px] hover:translate-y-[-1px] hover:bg-[#FBF4E6] transition-all">
                                    <span>Lihat paket & harga</span>
                                </a>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: Pesepeda No 2 & Floating Dashboard Card -->
                        <div style="position: relative; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; min-height: 260px; width: 100%;">
                            
                            @php
                                $realTotalRegs = \App\Models\Registration::count();
                                $realPaidRegs = \App\Models\Registration::whereIn('status', ['paid', 'checked_in'])->count();
                                $realCategoryBooked = (int) \App\Models\EventCategory::sum('registered_count');
                                $promoPesertaCount = max($realTotalRegs, $realCategoryBooked);
                                $promoRateLunas = $realTotalRegs > 0 ? (int) round(($realPaidRegs / $realTotalRegs) * 100) : 98;
                            @endphp

                            <!-- Floating Stat Card (Dashboard Penyelenggara) di Kanan Atas Pesepeda -->
                            <a href="{{ $targetDaftar }}" wire:navigate
                               style="position: absolute; top: -6px; right: 0; background: #FFFDF8; border: 2.5px solid #10282E; border-radius: 12px; box-shadow: 3px 3px 0 #10282E; padding: 8px 12px; z-index: 20; min-width: 150px; text-decoration: none; color: inherit;"
                               class="hidden sm:block hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all">
                                <div style="font-size: 8.5px; font-weight: 900; letter-spacing: 0.05em; color: #56666A; text-transform: uppercase;">
                                    DASHBOARD PENYELENGGARA
                                </div>
                                <div style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 20px; font-weight: 900; color: #10282E; line-height: 1.1; margin: 2px 0;">
                                    {{ number_format($promoPesertaCount > 0 ? $promoPesertaCount : 1284, 0, ',', '.') }}
                                </div>
                                <div style="font-size: 9.5px; font-weight: 700; color: #1F6F4A; margin-bottom: 5px;">
                                    peserta terdaftar · {{ $promoRateLunas }}% lunas
                                </div>
                                <!-- Mini Bar Chart -->
                                <div style="display: flex; align-items: flex-end; gap: 3px; height: 14px;">
                                    <div style="width: 6px; height: 5px; background: #1F6F4A; border-radius: 1px;"></div>
                                    <div style="width: 6px; height: 9px; background: #1F6F4A; border-radius: 1px;"></div>
                                    <div style="width: 6px; height: 7px; background: #1F6F4A; border-radius: 1px;"></div>
                                    <div style="width: 6px; height: 11px; background: #1F6F4A; border-radius: 1px;"></div>
                                    <div style="width: 6px; height: 14px; background: #1F6F4A; border-radius: 1px;"></div>
                                </div>
                            </a>

                            <!-- Pesepeda Gambar No 2 (Clean WebP Cutout) Berdiri di Sisi Kanan -->
                            <div style="width: 100%; max-width: 270px; height: 260px; display: flex; align-items: flex-end; justify-content: center; z-index: 10;">
                                <img src="/images/events/cyclist_organizer.webp"
                                     alt="Pesepeda Velosha Organizer"
                                     style="width: 100%; height: 100%; object-fit: contain; object-position: bottom center; filter: drop-shadow(4px 4px 0 rgba(16, 40, 46, 0.25)); display: block;">
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Asphalt Road Strip with Dashed Line & Checkered Finish (Sesuai Gambar 1) -->
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 26px; background: #10282E; display: flex; align-items: center; justify-content: space-between; overflow: hidden; z-index: 5;">
                        <!-- Dashed Road Markings -->
                        <div style="flex: 1; height: 4px; margin: 0 16px; background-image: repeating-linear-gradient(90deg, #FFFFFF 0px, #FFFFFF 22px, transparent 22px, transparent 38px); opacity: 0.85;"></div>
                        
                        <!-- Checkered Finish Line Flag -->
                        <div style="width: 26px; height: 26px; background: repeating-conic-gradient(#FFFFFF 0% 25%, #10282E 0% 50%) 50% / 10px 10px; border-left: 2px solid #FFFFFF;"
                             aria-label="Garis finis"></div>
                    </div>

                </div>
                @endif

            </div>

            <!-- PROBLEM 3: Pagination -->
            @if ($cards->hasPages())
            {{ $cards->links('velo-pagination') }}
            @endif

        </div>
    </div>
</div>
