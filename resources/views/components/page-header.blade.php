@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'meta' => null,
    'size' => 'default',
    'chips' => [],
    'width' => 'max-w-7xl',
])

@php
if ($meta && str_contains($meta, 'Diperbarui')) {
    $meta = null;
}
@endphp

<div class="relative overflow-hidden bg-[#0A353E] text-white border-b border-teal-900 shadow-md">
    {{-- HANYA PAKAI GARIS STRIPE PATTERN DI KANAN (TANPA TOPO PATTERN) --}}
    <div class="absolute right-0 top-0 bottom-0 w-1/2 bg-right bg-cover bg-no-repeat opacity-30 pointer-events-none"
         style="background-image: url('/images/dashboard-stripe-pattern.png');"></div>

    {{-- Bottom accent line --}}
    <div class="absolute inset-x-0 bottom-0 h-1 bg-race-600" aria-hidden="true"></div>

    <div class="relative {{ $width }} mx-auto px-4 sm:px-6 lg:px-8 {{ $size === 'compact' ? 'py-8 sm:py-10' : 'py-12 sm:py-16' }}">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-2xl">
                @if ($eyebrow)
                    <span class="inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-[0.2em] text-[#EFA96A] mb-3">
                        @if (isset($statusDot) && $statusDot)
                            <span class="w-2 h-2 rounded-full bg-race-400" aria-hidden="true"></span>
                        @endif
                        {{ $eyebrow }}
                    </span>
                @endif
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white leading-[1.1]">{{ $title }}</h1>
                @if ($description)
                    <p class="text-sm sm:text-base text-teal-100/90 font-medium leading-relaxed mt-4">{{ $description }}</p>
                @endif

                @if (! empty($chips))
                    <div class="flex flex-wrap gap-2 mt-5">
                        @foreach ($chips as $chip)
                            @if (is_array($chip))
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold bg-[#051C22]/80 text-teal-100 px-3 py-1.5 velo-box-3 border border-teal-700/60 shadow-xs">
                                    @if (! empty($chip['icon']))
                                        <i data-lucide="{{ $chip['icon'] }}" class="w-3.5 h-3.5 text-[#EFA96A] shrink-0"></i>
                                    @endif
                                    {{ $chip['label'] }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold bg-[#051C22]/80 text-teal-100 px-3 py-1.5 velo-box-3 border border-teal-700/60 shadow-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-race-400 shrink-0" aria-hidden="true"></span>
                                    {{ $chip }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($meta)
                <div class="text-xs font-mono font-extrabold text-teal-300 md:text-right shrink-0 bg-[#051C22]/80 border border-teal-800 px-3.5 py-2 velo-box-3">{{ $meta }}</div>
            @endif
        </div>
    </div>
</div>
