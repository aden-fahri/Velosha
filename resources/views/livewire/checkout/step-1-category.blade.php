@use('App\Support\EventCardPresenter', 'P')
{{-- Langkah 1: pemilihan kategori jarak + ringkasan kategori terpilih (langkah 2 & 3) --}}
@if ($step === 1 && $event !== null)
<div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-6">
    <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-[var(--ink)]">
        <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider">
            Pilih Kategori Jarak
        </h3>
        <a href="{{ route('events.show', $event->slug) }}" wire:navigate class="text-xs font-bold text-[var(--orange)] hover:underline">Ganti Event &rarr;</a>
    </div>

    <div class="space-y-3">
        @forelse ($event->categories as $cat)
        @php
            $catDist = P::distance($cat->distance_km);
            $hasDist = \Illuminate\Support\Str::contains(strtolower($cat->name), [strtolower($catDist), (string)(int)$cat->distance_km . 'km', (string)(int)$cat->distance_km . ' km']);
            $displayName = $hasDist ? $cat->name : "{$cat->name} ({$catDist})";
        @endphp
        @if ($this->isCategoryPaid((int) $cat->id))
        <div class="w-full p-4 sm:p-5 rounded-2xl flex items-center justify-between gap-4 border-[2.5px] border-[#10282E] bg-[#E7F8EE] shadow-[3px_3px_0_var(--ink)]">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="px-3 py-1.5 min-w-[52px] max-w-[120px] h-9 flex items-center justify-center font-black text-xs rounded-lg shrink-0 border-2 border-[#10282E] bg-[#1F6F4A] text-white shadow-[2px_2px_0_var(--ink)] truncate">{{ $cat->code }}</div>
                <div class="min-w-0">
                    <div class="text-sm font-black text-[#10282E] truncate">{{ $displayName }}</div>
                    <div class="text-xs text-[#1F6F4A] font-bold mt-0.5">Anda sudah terdaftar di kategori ini</div>
                </div>
            </div>
            <div class="text-right shrink-0">
                <a href="/ticket/{{ $paidTicketMap[$cat->id] }}" wire:navigate class="inline-flex items-center bg-white hover:bg-[#F8E3B5] text-[#10282E] border-2 border-[#10282E] font-black px-3.5 py-2 text-xs rounded-lg shadow-[2px_2px_0_var(--ink)] transition">
                    <i data-lucide="ticket" class="w-3.5 h-3.5 mr-1.5"></i>
                    <span>Lihat E-Ticket</span>
                </a>
            </div>
        </div>
        @else
        <button type="button" wire:click="selectCategory({{ $cat->id }})"
            class="w-full text-left p-4 sm:p-5 rounded-2xl flex items-center justify-between gap-4 transition-all cursor-pointer {{ $categoryId === $cat->id ? 'border-2 border-[var(--ink)] bg-white shadow-[3.5px_3.5px_0_var(--ink)] ring-2 ring-[var(--mustard)]/40' : 'border-2 border-[var(--line)] bg-white/80 hover:border-[var(--ink)] hover:bg-white shadow-[1px_1px_0_var(--line)]' }}">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="px-3 py-1.5 min-w-[64px] max-w-[140px] h-9 rounded-lg flex items-center justify-center font-black text-xs shrink-0 border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] truncate tracking-wide {{ $categoryId === $cat->id ? 'bg-[#DE7119] text-white' : 'bg-[var(--paper)] text-[var(--ink)]' }}">{{ $cat->code }}</div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-black text-[var(--ink)] leading-snug">{{ $displayName }}</span>
                        @if ($cat->is_primary)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-[#F4BE3F] text-[var(--ink)] border border-[var(--ink)] shadow-[1px_1px_0_var(--ink)]">
                            ★ Utama
                        </span>
                        @endif
                    </div>
                    <div class="text-xs text-[var(--mute)] font-medium mt-1">Elevasi +{{ P::angka((float) $cat->elevation_gain_m) }}m &middot; COT {{ P::jam((float) $cat->cut_off_hours) }}</div>
                </div>
            </div>
            <div class="text-right shrink-0 pl-2">
                <div class="text-sm font-black text-[var(--ink)]">Rp {{ P::angka((float) $cat->price) }}</div>
                <span class="text-[11px] px-2.5 py-0.5 rounded-lg font-black inline-block mt-1 border-[1.5px] border-[var(--ink)] shadow-[1px_1px_0_var(--ink)] {{ $cat->remaining > 0 ? 'bg-[#E7F8EE] text-[#0D5B3A]' : 'bg-[#FEECEC] text-[#B91C1C]' }}">
                    {{ $cat->remaining > 0 ? 'Sisa slot '.$cat->remaining : 'Slot Penuh' }}
                </span>
            </div>
        </button>
        @endif
        @empty
        <div class="p-4 border-2 border-dashed border-[var(--ink)]/30 bg-[var(--paper)] rounded-xl text-xs text-[var(--mute)] font-semibold text-center">
            Kategori jarak belum tersedia untuk event ini.
        </div>
        @endforelse
    </div>

    @if ($event->categories->isNotEmpty())
    <div class="mt-5">
        <x-ui.submit-button
            type="button"
            wire:click="toStep2"
            target="toStep2"
            variant="orange"
            loadingText="Memuat data diri..."
            :fullWidth="true">
            <span>Lanjutkan ke Data Diri</span>
            <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
        </x-ui.submit-button>
    </div>
    @endif
</div>
@endif

@if ($step >= 2 && $category !== null)
<div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-6">
    <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-[var(--ink)]">
        <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider">
            Kategori Jarak Dipilih
        </h3>
        <button type="button" wire:click="toStep1" wire:loading.attr="disabled" class="text-xs font-bold text-[var(--orange)] hover:underline cursor-pointer">Ubah Kategori</button>
    </div>

    <div class="border-2 border-[var(--ink)] bg-[var(--paper)] p-4 rounded-xl shadow-[2px_2px_0_var(--ink)] flex items-center justify-between">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="px-3 py-1.5 min-w-[52px] max-w-[120px] h-9 bg-[#DE7119] text-white border-2 border-[var(--ink)] rounded-lg flex items-center justify-center font-black text-xs shrink-0 shadow-[2px_2px_0_var(--ink)] truncate">
                {{ $category->code }}
            </div>
            <div>
                @php
                    $catDist = P::distance($category->distance_km);
                    $hasDist = \Illuminate\Support\Str::contains(strtolower($category->name), [strtolower($catDist), (string)(int)$category->distance_km . 'km', (string)(int)$category->distance_km . ' km']);
                    $displayName = $hasDist ? $category->name : "{$category->name} ({$catDist})";
                @endphp
                <div class="flex items-center gap-1.5 flex-wrap">
                    <h4 class="text-sm font-black text-[var(--ink)]">{{ $displayName }}</h4>
                    @if ($category->is_primary)
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)] shadow-[1px_1px_0_var(--ink)]">
                        ★ Utama
                    </span>
                    @endif
                </div>
                <div class="text-xs text-[var(--mute)] font-medium">Elevasi +{{ P::angka((float) $category->elevation_gain_m) }}m &middot; COT {{ P::jam((float) $category->cut_off_hours) }}</div>
            </div>
        </div>
        <div class="text-right shrink-0">
            <div class="text-sm font-black text-[var(--ink)] mb-1">Rp {{ P::angka((float) $category->price) }}</div>
            <span class="inline-block text-[11px] px-2.5 py-0.5 font-black rounded-lg border-[1.5px] border-[var(--ink)] shadow-[1.5px_1.5px_0_var(--ink)] {{ $category->remaining > 0 ? 'bg-[#1F6F4A] text-white' : 'bg-[#DE3618] text-white' }}">
                {{ $category->remaining > 0 ? 'Tersedia' : 'Slot Penuh' }}
            </span>
        </div>
    </div>
</div>
@endif
