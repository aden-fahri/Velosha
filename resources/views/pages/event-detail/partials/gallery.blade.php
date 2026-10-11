@use('App\Support\EventCardPresenter', 'P')
{{-- Galeri foto: 1 foto utama + grid thumbnail (format Airbnb 1+4) --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if ($photos->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 h-80 sm:h-96 md:h-[460px]">
        <!-- Big Photo (Col 1-2) -->
        <div class="md:col-span-2 h-full overflow-hidden bg-velosurface velo-box-1 relative group cursor-pointer">
            <img src="{{ P::photoUrl($photos->first()) }}"
                 alt="{{ $event->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <div class="absolute bottom-3 left-3 bg-race-600/80 backdrop-blur-sm text-white text-[11px] px-2.5 py-1 velo-box-3 border border-white/20">
                {{ $event->location_name }}
            </div>
        </div>

        <!-- Grid Photos (Col 3-4) -->
        <div class="md:col-span-2 grid grid-cols-2 gap-3 h-full">
            @foreach ($photos->slice(1, 4) as $photo)
            <div class="overflow-hidden bg-velosurface velo-box-1 relative group cursor-pointer">
                <img src="{{ P::photoUrl($photo) }}"
                     alt="Foto {{ $event->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute bottom-2 left-2 bg-black/60 text-white text-[10px] px-2 py-0.5 velo-box-3">
                    {{ $event->location_name }}
                </div>
            </div>
            @endforeach
            @if ($photos->count() > 5)
            <div class="overflow-hidden bg-velosurface velo-box-1 relative group cursor-pointer">
                <img src="{{ P::photoUrl($photos->get(5) ?? $photos->first()) }}"
                     alt="Foto {{ $event->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute bottom-2 right-2 bg-race-600 text-white text-[10px] font-semibold px-2 py-1 velo-box-3 flex items-center">
                    <i data-lucide="image" class="w-3 h-3 mr-1"></i>
                    <span>+{{ $photos->count() - 5 }} Foto Galeri</span>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="h-80 sm:h-96 overflow-hidden bg-velosurface velo-box-1 relative">
        <img src="/images/hero-peloton.jpg" alt="{{ $event->title }}" class="w-full h-full object-cover">
        <div class="absolute bottom-3 left-3 bg-race-600/80 backdrop-blur-sm text-white text-[11px] px-2.5 py-1 velo-box-3 border border-white/20">
            {{ $event->location_name }}
        </div>
    </div>
    @endif
</section>
