@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'meta' => null,
])

<div class="relative overflow-hidden bg-[#2E343B] text-white border-b border-white/10">
    {{-- Tekstur kontur topo vertikal di atas latar abu-abu gelap --}}
    <div class="absolute inset-0 pointer-events-none opacity-[0.55]"
         style="background-image:url('{{ asset('images/patterns/topo-vertical.svg') }}');background-size:480px 480px;background-repeat:repeat"
         aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-2xl">
                @if ($eyebrow)
                    <span class="inline-block text-[11px] font-bold uppercase tracking-[0.2em] text-race-400 mb-3">{{ $eyebrow }}</span>
                @endif
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white leading-[1.1]">{{ $title }}</h1>
                @if ($description)
                    <p class="text-sm sm:text-base text-[#d1d5db] font-normal leading-relaxed mt-4">{{ $description }}</p>
                @endif
            </div>
            @if ($meta)
                <div class="text-xs font-semibold text-[#9ca3af] md:text-right shrink-0">{{ $meta }}</div>
            @endif
        </div>
    </div>
</div>
