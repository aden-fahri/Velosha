@props(['organization'])

<header class="sticky top-0 z-40 bg-[var(--paper)] border-b-[3px] border-[var(--ink)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-[72px]">
        {{-- Brand & Org Context --}}
        <div class="flex items-center gap-4">
            <a href="/" wire:navigate class="flex items-center gap-3 transition-transform duration-150 hover:-translate-y-0.5" aria-label="Velosha — Beranda">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-9 w-auto object-contain" />
                <span class="font-extrabold text-2xl tracking-tight text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
            </a>
            @if(isset($organization))
            <div class="hidden sm:flex items-center gap-2 pl-4 border-l-2 border-[var(--ink)]/20">
                <span class="font-bold text-sm text-[var(--ink)]">{{ $organization->name }}</span>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            @if(isset($organization))
            <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'overview']) }}" wire:navigate class="btn btn-sm btn-ghost">
                &larr; Dasbor Penyelenggara
            </a>
            @endif
            <a href="/" wire:navigate class="btn btn-sm btn-ghost">
                Website
            </a>
        </div>
    </div>
</header>

