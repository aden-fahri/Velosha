@use('App\Support\EventCardPresenter', 'P')
{{-- Langkah 1: pemilihan kategori jarak + ringkasan kategori terpilih (langkah 2 & 3) --}}
@if ($step === 1 && $event !== null)
<div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-6">
    <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-[var(--ink)]">
        <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-sm bg-[var(--orange)] border border-[var(--ink)]"></span>
            <span>Pilih Kategori Jarak</span>
        </h3>
        <a href="{{ route('events.show', $event->slug) }}" wire:navigate class="text-xs font-bold text-[var(--orange)] hover:underline">Ganti Event &rarr;</a>
    </div>

    <div class="space-y-3">
        @forelse ($event->categories as $cat)
        @if ($this->isCategoryPaid((int) $cat->id))
        <div class="w-full p-4 velo-box-3 flex items-center justify-between border-2 border-emerald-300 bg-emerald-50/50">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 flex items-center justify-center font-bold text-xs velo-box-3 bg-emerald-700 text-white">{{ $cat->code }}</div>
                <div>
                    <div class="text-sm font-bold text-teal-900">{{ $cat->name }} ({{ P::distance($cat->distance_km) }})</div>
                    <div class="text-xs text-emerald-800 font-semibold">Anda sudah terdaftar di kategori ini</div>
                </div>
            </div>
            <div class="text-right shrink-0">
                <a href="/ticket/{{ $paidTicketMap[$cat->id] }}" wire:navigate class="inline-flex items-center bg-white hover:bg-velosurfacealt text-emerald-800 border border-emerald-300 font-bold px-3 py-2 text-[11px] velo-box-3 shadow-xs transition">
                    <i data-lucide="ticket" class="w-3 h-3 mr-1.5"></i>
                    <span>Lihat E-Ticket</span>
                </a>
            </div>
        </div>
        @else
        <button type="button" wire:click="selectCategory({{ $cat->id }})"
            class="w-full text-left p-4 velo-box-3 flex items-center justify-between {{ $categoryId === $cat->id ? 'border-2 border-race-500 bg-race-50/40' : 'border border-veloborderstrong bg-white hover:border-[#93a0a1] cursor-pointer' }}">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 flex items-center justify-center font-bold text-xs velo-box-3 {{ $categoryId === $cat->id ? 'bg-[var(--orange)] text-[var(--ink)]' : 'bg-velosurfacealt text-teal-700' }}">{{ $cat->code }}</div>
                <div>
                    <div class="text-sm font-bold text-teal-900">{{ $cat->name }} ({{ P::distance($cat->distance_km) }})</div>
                    <div class="text-xs text-velomutedlight">Elevasi +{{ P::angka((float) $cat->elevation_gain_m) }}m &middot; COT {{ P::jam((float) $cat->cut_off_hours) }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-extrabold text-teal-900">Rp {{ P::angka((float) $cat->price) }}</div>
                <span class="text-[10px] px-1.5 py-0.5 velo-box-3 font-semibold {{ $cat->remaining > 0 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-red-700 bg-red-50 border border-red-200' }}">
                    {{ $cat->remaining > 0 ? 'Sisa slot '.$cat->remaining : 'Slot Penuh' }}
                </span>
            </div>
        </button>
        @endif
        @empty
        <div class="p-4 border border-veloborder bg-velosurfacealt velo-box-3 text-xs text-velomutedlight text-center">
            Kategori jarak belum tersedia untuk event ini.
        </div>
        @endforelse
    </div>

    @if ($event->categories->isNotEmpty())
    <button type="button" wire:click="toStep2" class="mt-5 w-full flex items-center justify-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white font-black py-3.5 text-xs rounded-xl border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
        <span>Lanjutkan ke Data Diri</span>
        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
    </button>
    @endif
</div>
@endif

@if ($step >= 2 && $category !== null)
<div class="rounded-2xl border-[2.5px] border-[var(--ink)] bg-white shadow-[4px_4px_0_var(--ink)] p-6">
    <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-[var(--ink)]">
        <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-sm bg-[var(--orange)] border border-[var(--ink)]"></span>
            <span>Kategori Jarak Dipilih</span>
        </h3>
        <button type="button" wire:click="toStep1" class="text-xs font-bold text-[var(--orange)] hover:underline cursor-pointer">Ubah Kategori</button>
    </div>

    <div class="border-2 border-[var(--ink)] bg-[var(--paper)] p-4 rounded-xl shadow-[2px_2px_0_var(--ink)] flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-[var(--orange)] text-white border-2 border-[var(--ink)] rounded-lg flex items-center justify-center font-black text-xs shadow-[2px_2px_0_var(--ink)]">
                {{ $category->code }}
            </div>
            <div>
                <h4 class="text-sm font-black text-[var(--ink)]">{{ $category->name }} ({{ P::distance($category->distance_km) }})</h4>
                <div class="text-xs text-[var(--mute)] font-medium">Elevasi +{{ P::angka((float) $category->elevation_gain_m) }}m &middot; COT {{ P::jam((float) $category->cut_off_hours) }}</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-sm font-black text-[var(--ink)]">Rp {{ P::angka((float) $category->price) }}</div>
            <span class="text-[10px] px-2 py-0.5 font-black rounded border border-[var(--ink)] {{ $category->remaining > 0 ? 'bg-[var(--pine)] text-white' : 'bg-red-500 text-white' }}">
                {{ $category->remaining > 0 ? 'Tersedia' : 'Slot Penuh' }}
            </span>
        </div>
    </div>
</div>
@endif
