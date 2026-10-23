@if ($paginator->hasPages())
<nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-center gap-1.5 text-xs">
    {{-- Sebelumnya --}}
    @if ($paginator->onFirstPage())
        <span aria-disabled="true" class="velo-box-1 border border-veloborder bg-velosurfacealt text-[#9ca3af] px-3 py-2 cursor-not-allowed select-none">Sebelumnya</span>
    @else
        <button type="button" wire:click="previousPage" rel="prev" class="velo-box-1 border border-veloborderstrong bg-white text-teal-700 hover:bg-velosurfacealt font-semibold px-3 py-2 transition">Sebelumnya</button>
    @endif

    {{-- Nomor halaman --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="velo-box-1 border border-veloborder bg-white text-[#9ca3af] px-3 py-2 select-none">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page === $paginator->currentPage())
                    <span aria-current="page" class="velo-box-1 border border-race-600 bg-[var(--orange)] text-[var(--ink)] font-bold px-3.5 py-2">{{ $page }}</span>
                @else
                    <button type="button" wire:click="gotoPage({{ $page }})" class="velo-box-1 border border-veloborderstrong bg-white text-teal-700 hover:bg-velosurfacealt px-3.5 py-2 transition">{{ $page }}</button>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Berikutnya --}}
    @if ($paginator->hasMorePages())
        <button type="button" wire:click="nextPage" rel="next" class="velo-box-1 border border-veloborderstrong bg-white text-teal-700 hover:bg-velosurfacealt font-semibold px-3 py-2 transition">Berikutnya</button>
    @else
        <span aria-disabled="true" class="velo-box-1 border border-veloborder bg-velosurfacealt text-[#9ca3af] px-3 py-2 cursor-not-allowed select-none">Berikutnya</span>
    @endif
</nav>
@endif
