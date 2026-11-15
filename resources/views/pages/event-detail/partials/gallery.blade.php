@use('App\Support\EventCardPresenter', 'P')
{{-- Galeri foto Neobrutalisme Velosha: 1 foto utama + grid thumbnail dengan border radius konsisten --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-4">
    @if ($photos->isNotEmpty())
    <div class="bg-white border-[2.5px] sm:border-[3px] border-[var(--ink)] rounded-2xl sm:rounded-3xl p-3 sm:p-4 shadow-[4px_4px_0_var(--ink)] sm:shadow-[5px_5px_0_var(--ink)]">
        <div class="flex flex-col md:grid md:grid-cols-4 gap-3 sm:gap-4 h-auto md:h-[460px]">
            <!-- Big Photo (Col 1-2 on Desktop, Full Width on Mobile) -->
            <div class="w-full h-56 sm:h-72 md:h-full md:col-span-2 rounded-xl sm:rounded-2xl overflow-hidden border-2 border-[var(--ink)] bg-[var(--paper)] relative group cursor-pointer shadow-xs shrink-0">
                <img src="{{ P::photoUrl($photos->first()) }}"
                     alt="{{ $event->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute bottom-2.5 left-2.5 sm:bottom-3 sm:left-3 bg-[var(--orange)] text-white text-[11px] sm:text-xs font-bold px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span>{{ $event->location_name }}</span>
                </div>
            </div>

            <!-- Thumbnail Photos: Horizontal Scroll Track on Mobile, 2x2 Grid on Desktop -->
            <div class="md:col-span-2 flex overflow-x-auto md:grid md:grid-cols-2 gap-2.5 sm:gap-4 md:h-full pb-1 md:pb-0 scrollbar-none snap-x">
                @foreach ($photos->slice(1, 4) as $photo)
                <div class="w-36 h-24 sm:w-44 sm:h-28 md:w-full md:h-full shrink-0 snap-start rounded-xl overflow-hidden border-2 border-[var(--ink)] bg-[var(--paper)] relative group cursor-pointer shadow-xs">
                    <img src="{{ P::photoUrl($photo) }}"
                         alt="Foto {{ $event->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute bottom-1.5 left-1.5 sm:bottom-2 sm:left-2 bg-[#10282E]/85 backdrop-blur-xs text-white text-[9px] sm:text-[10px] font-semibold px-1.5 sm:px-2 py-0.5 rounded border border-white/20 truncate max-w-[90%]">
                        {{ $event->location_name }}
                    </div>
                </div>
                @endforeach

                @if ($photos->count() > 5)
                <div class="w-36 h-24 sm:w-44 sm:h-28 md:w-full md:h-full shrink-0 snap-start rounded-xl overflow-hidden border-2 border-[var(--ink)] bg-[var(--paper)] relative group cursor-pointer shadow-xs">
                    <img src="{{ P::photoUrl($photos->get(5) ?? $photos->first()) }}"
                         alt="Foto {{ $event->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute bottom-1.5 right-1.5 sm:bottom-2 sm:right-2 bg-[var(--mustard)] text-[var(--ink)] text-[10px] sm:text-[11px] font-black px-2 py-0.5 sm:py-1 rounded-lg border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] flex items-center gap-1">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <span>+{{ $photos->count() - 5 }} Foto</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="bg-white border-[2.5px] sm:border-[3px] border-[var(--ink)] rounded-2xl sm:rounded-3xl p-3 sm:p-4 shadow-[4px_4px_0_var(--ink)] sm:shadow-[5px_5px_0_var(--ink)]">
        <div class="h-60 sm:h-80 md:h-96 rounded-xl sm:rounded-2xl overflow-hidden border-2 border-[var(--ink)] bg-[var(--paper)] relative">
            <img src="/images/hero-peloton.jpg" alt="{{ $event->title }}" class="w-full h-full object-cover">
            <div class="absolute bottom-3 left-3 bg-[var(--orange)] text-white text-xs font-bold px-3 py-1.5 rounded-lg border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>{{ $event->location_name }}</span>
            </div>
        </div>
    </div>
    @endif
</section>
