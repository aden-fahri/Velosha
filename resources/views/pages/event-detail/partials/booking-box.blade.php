@use('App\Support\EventCardPresenter', 'P')
{{-- Sticky booking box: pilihan kategori, inklusi paket, CTA checkout --}}
<div id="daftar" class="lg:col-span-4 sticky top-24" x-data="{ selected: '{{ $categories->first()?->id }}', roleWarning: false }">
    <div class="bg-white border border-veloborderstrong p-6 velo-box-1 shadow-md">

        <div class="pb-4 border-b border-veloborder flex justify-between items-baseline">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-velomuted">Pilih Kategori Jarak</span>
                <div class="text-2xl font-extrabold text-teal-900 mt-0.5">Rp {{ $lowestPrice === null ? '—' : P::angka((float) $lowestPrice) }}</div>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 velo-box-3 border {{ $totalRemaining > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                {{ $totalRemaining > 0 ? 'Slot Tersisa '.$totalRemaining : 'Slot Penuh' }}
            </span>
        </div>

        <!-- Select Category Radios -->
        <div class="my-5 space-y-2.5">
            @forelse ($categories as $category)
            <label class="flex items-center justify-between p-3.5 velo-box-3 cursor-pointer transition"
                   :class="selected == '{{ $category->id }}' ? 'border-2 border-race-500 bg-race-50/50' : 'border border-veloborder hover:border-[#93a0a1] bg-white'">
                <div class="flex items-center space-x-3">
                    <input type="radio" name="race_category" x-model="selected" value="{{ $category->id }}" class="text-teal-900 focus:ring-race-500">
                    <div>
                        <div class="text-xs font-bold text-teal-900">{{ $category->name }} ({{ P::distance($category->distance_km) }})</div>
                        <div class="text-[10px] text-velomutedlight">Elevasi +{{ P::angka((float) $category->elevation_gain_m) }}m &middot; COT {{ P::jam((float) $category->cut_off_hours) }}</div>
                    </div>
                </div>
                <div class="text-xs font-extrabold text-teal-900">Rp {{ P::angka((float) $category->price) }}</div>
            </label>
            @empty
            <div class="p-4 border border-veloborder bg-velosurfacealt velo-box-3 text-xs text-velomutedlight text-center">
                Kategori jarak belum tersedia untuk event ini.
            </div>
            @endforelse
        </div>

        <!-- Registration Inclusions Checklist -->
        <div class="py-4 border-t border-veloborder text-xs text-velomutedlight space-y-2">
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
        <button type="button" @click="roleWarning = true" class="mt-4 w-full flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold py-3.5 text-sm velo-box-1 shadow-sm transition">
            <span>Lanjut ke Formulir Pendaftaran</span>
            <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
        </button>
        @else
        <a href="/checkout?event={{ $event->slug }}"
           id="checkout-cta"
           wire:navigate
           :href="selected ? '/checkout?event={{ $event->slug }}&category=' + selected : '/checkout?event={{ $event->slug }}'"
           class="mt-4 w-full flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold py-3.5 text-sm velo-box-1 shadow-sm transition">
            <span>Lanjut ke Formulir Pendaftaran</span>
            <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
        </a>
        @endif

        <div class="mt-3 text-center text-[11px] text-velomuted">
            Konfirmasi instan via Midtrans &mdash; E-Ticket langsung terbit
        </div>

    </div>

    @if ($loggedInNonPeserta)
    @include('pages.event-detail.partials.role-modal')
    @endif
</div>
