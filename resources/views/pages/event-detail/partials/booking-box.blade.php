@use('App\Support\EventCardPresenter', 'P')
{{-- Sticky booking box: pilihan kategori, inklusi paket, CTA checkout (Neobrutalisme Velosha) --}}
<div id="daftar" class="lg:col-span-4 sticky top-24" x-data="{ selected: '{{ ($categories->firstWhere('is_primary', true) ?? $categories->first())?->id }}' }">
    <div class="bg-white border-[3px] border-[var(--ink)] rounded-2xl p-6 sm:p-7 shadow-[6px_6px_0_var(--ink)]">

        <div class="pb-4 border-b-2 border-[var(--ink)] flex justify-between items-start gap-3">
            <div>
                <span class="text-[11px] uppercase font-black tracking-wider text-[var(--mute)]">Pilih Kategori Jarak</span>
                <div class="font-heading text-3xl font-black text-[var(--ink)] mt-0.5 tracking-tight">Rp {{ $lowestPrice === null ? '—' : P::angka((float) $lowestPrice) }}</div>
                <div class="flex items-center gap-1.5 text-xs font-bold text-[#1F6F4A] mt-1">
                    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span>{{ P::tanggalLengkap($eventDate) }}</span>
                </div>
            </div>
            <span class="text-xs font-black px-3 py-1.5 rounded-lg border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] shrink-0 {{ $totalRemaining > 0 ? 'bg-[#1F6F4A] text-white' : 'bg-[#A02F1A] text-white' }}"
                  style="background-color: {{ $totalRemaining > 0 ? '#1F6F4A' : '#A02F1A' }} !important; color: #ffffff !important;">
                {{ $totalRemaining > 0 ? 'Sisa '.$totalRemaining.' Slot' : 'Slot Penuh' }}
            </span>
        </div>

        <!-- Select Category Radios -->
        <div class="my-5 space-y-3">
            @forelse ($categories as $category)
            @php
                $catDist = P::distance($category->distance_km);
                $hasDist = \Illuminate\Support\Str::contains(strtolower($category->name), [strtolower($catDist), (string)(int)$category->distance_km . 'km', (string)(int)$category->distance_km . ' km']);
                $displayName = $hasDist ? $category->name : "{$category->name} ({$catDist})";
            @endphp
            <label class="group relative flex items-center justify-between p-4 rounded-xl border-2 transition-all cursor-pointer"
                   :class="selected == '{{ $category->id }}'
                       ? 'bg-white border-[var(--ink)] shadow-[3.5px_3.5px_0_var(--ink)] ring-2 ring-[var(--mustard)]/40'
                       : 'bg-white/80 border-[var(--line)] hover:border-[var(--ink)] hover:bg-white shadow-[1px_1px_0_var(--line)]'">
                <div class="flex items-start gap-3.5 min-w-0">
                    <div class="pt-0.5">
                        <input type="radio" name="race_category" x-model="selected" value="{{ $category->id }}"
                               class="w-4 h-4 text-[var(--orange)] focus:ring-[var(--orange)] border-2 border-[var(--ink)] cursor-pointer">
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-heading font-black text-sm text-[var(--ink)] leading-snug">
                                {{ $displayName }}
                            </span>
                            @if ($category->is_primary)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)] shadow-[1px_1px_0_var(--ink)]">
                                ★ Kategori Utama
                            </span>
                            @endif
                        </div>
                        <div class="text-[11px] text-[var(--mute)] font-medium mt-1 flex items-center gap-1.5 flex-wrap">
                            <span>Elevasi +{{ P::angka((float) $category->elevation_gain_m) }}m</span>
                            <span>&bull;</span>
                            <span>COT {{ P::jam((float) $category->cut_off_hours) }}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded {{ $category->remaining > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-red-50 text-red-800 border border-red-300' }}">
                                {{ $category->remaining > 0 ? 'Sisa ' . $category->remaining . ' Slot' : 'Slot Penuh' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right shrink-0 pl-3">
                    <div class="text-[10px] uppercase font-bold text-[var(--mute)] tracking-wider">Harga</div>
                    <div class="font-heading font-black text-base text-[var(--ink)]">Rp {{ P::angka((float) $category->price) }}</div>
                </div>
            </label>
            @empty
            <div class="p-4 border-2 border-dashed border-[var(--ink)]/30 rounded-xl bg-[var(--paper)] text-xs text-[var(--mute)] text-center font-bold">
                Kategori jarak belum tersedia untuk event ini.
            </div>
            @endforelse
        </div>

        <!-- Registration Inclusions Checklist -->
        <div class="py-4 border-t-2 border-[var(--ink)] text-xs space-y-2.5">
            <div class="font-black text-[var(--ink)] text-xs mb-1.5 uppercase tracking-wider">Paket Registrasi Termasuk:</div>
            <div class="flex items-center space-x-2.5">
                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                <span class="font-bold text-[var(--ink)]">Nomor BIB Resmi + RFID Timing Chip</span>
            </div>
            <div class="flex items-center space-x-2.5">
                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                <span class="font-bold text-[var(--ink)]">Medali Logam Finisher (Bagi yang lolos COT)</span>
            </div>
            <div class="flex items-center space-x-2.5">
                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                <span class="font-bold text-[var(--ink)]">Akses Pos Hidrasi &amp; Nutrisi</span>
            </div>
            <div class="flex items-center space-x-2.5">
                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                <span class="font-bold text-[var(--ink)]">Asuransi Medis Kecelakaan Resmi</span>
            </div>
            <div class="flex items-center space-x-2.5">
                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                <span class="font-bold text-[var(--ink)]">Jamuan Makan Siang Finisher Meal</span>
            </div>
        </div>

        @if ($event->gpx_file_path)
        <a href="{{ asset('storage/'.ltrim($event->gpx_file_path, '/')) }}"
           class="mt-3 w-full flex items-center justify-center gap-2 bg-white hover:bg-[var(--paper)] text-[var(--ink)] border-2 border-[var(--ink)] font-bold py-2.5 text-xs rounded-xl shadow-[2px_2px_0_var(--ink)] transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Unduh Berkas GPX Rute</span>
        </a>
        @endif

        <!-- Checkout CTA -->
        @if ($event->status !== \App\Models\Event::STATUS_PUBLISHED)
        <div class="mt-4 p-3 bg-[var(--paper)] border-2 border-[var(--ink)] rounded-xl text-center text-xs font-bold text-[var(--mute)]">
            Mode Pratinjau &mdash; Pendaftaran dibuka saat event resmi dipublikasikan.
        </div>
        @else
        <a href="/waiting-room?event={{ $event->slug }}"
           id="checkout-cta"
           wire:navigate
           :href="selected ? '/waiting-room?event={{ $event->slug }}&category=' + selected : '/waiting-room?event={{ $event->slug }}'"
           class="mt-4 w-full flex items-center justify-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white font-black py-4 text-sm rounded-xl border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all text-center cursor-pointer">
            <span>Lanjut ke Formulir Pendaftaran</span>
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        @endif

        <div class="mt-3 text-center text-[11px] text-[var(--mute)] font-semibold">
            Konfirmasi instan via Midtrans &mdash; E-Ticket langsung terbit
        </div>

    </div>
</div>
