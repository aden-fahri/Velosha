@if ($paginator->hasPages())
<style>
.events-pagination-nav {
    margin-top: 56px;
    margin-bottom: 96px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
}
.page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    width: 44px;
    height: 44px;
    border: 2.5px solid #10282E;
    border-radius: 12px;
    font-weight: 700;
    line-height: 1;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    box-sizing: border-box;
    box-shadow: 4px 4px 0 #10282E;
    background: #FBF4E6;
    color: #10282E;
    cursor: pointer;
    text-decoration: none;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.page-btn:hover {
    transform: translate(-1px, -1px);
    box-shadow: 5px 5px 0 #10282E;
}
.page-btn.active {
    background: #DE7119;
    color: #FFFFFF;
    cursor: default;
}
.page-btn.active:hover {
    transform: none;
    box-shadow: 4px 4px 0 #10282E;
}
.page-ellipsis {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border: 2.5px solid #10282E;
    border-radius: 12px;
    font-weight: 700;
    font-size: 14px;
    color: rgba(16,40,46,0.5);
    background: #FBF4E6;
    box-shadow: 4px 4px 0 #10282E;
    user-select: none;
}
.page-mobile-indicator {
    display: none;
}
.page-numbers-group {
    display: flex;
    align-items: center;
    gap: 12px;
}
@media (max-width: 599.98px) {
    .page-numbers-group {
        display: none;
    }
    .page-mobile-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 44px;
        padding: 0 16px;
        border: 2.5px solid #10282E;
        border-radius: 12px;
        background: #FBF4E6;
        color: #10282E;
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 4px 4px 0 #10282E;
        user-select: none;
    }
}
</style>

<nav role="navigation" aria-label="Navigasi halaman" class="events-pagination-nav">
    {{-- Tombol Sebelumnya (<) --}}
    @if (!$paginator->onFirstPage())
        <button type="button" wire:click="previousPage" rel="prev"
                class="page-btn"
                aria-label="Halaman sebelumnya">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </button>
    @endif

    {{-- Indikator Mobile (< 600px): "2 / 5" --}}
    <span class="page-mobile-indicator">
        {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
    </span>

    {{-- Daftar Nomor Halaman Desktop (>= 600px) --}}
    <div class="page-numbers-group">
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-ellipsis">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page === $paginator->currentPage())
                        <span aria-current="page" class="page-btn active">{{ $page }}</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $page }})"
                                class="page-btn">{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>

    {{-- Tombol Berikutnya (>) --}}
    @if ($paginator->hasMorePages())
        <button type="button" wire:click="nextPage" rel="next"
                class="page-btn"
                aria-label="Halaman berikutnya">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </button>
    @endif
</nav>
@endif
