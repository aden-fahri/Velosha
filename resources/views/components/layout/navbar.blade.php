<!-- Main Navigation Bar (Cartoon Screen-Print Style) -->
<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 bg-[var(--paper)] border-b-[3px] border-[var(--ink)]">
    <div class="wrap flex items-center justify-between h-[76px]">

        <!-- Logo & Brand -->
        <div class="flex items-center gap-8">
            <a href="/" wire:navigate class="flex items-center gap-2.5 transition-transform duration-150 hover:-translate-y-0.5" aria-label="Velosha — Beranda">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-10 w-auto object-contain" />
            </a>

            <!-- Nav Links (Desktop — 7 items) -->
            <nav class="hidden lg:flex items-center gap-1.5" aria-label="Utama">
                @foreach([
                    ['home', '/', 'Beranda'],
                    ['events.index', '/events', 'Kalender'],
                    ['ranking', '/ranking', 'Ranking'],
                    ['about', '/about', 'Tentang'],
                    ['rule', '/rule', 'Regulasi'],
                    ['help', '/help', 'Bantuan'],
                ] as [$key, $url, $label])
                @php($active = request()->routeIs($key . '*') || ($key === 'home' && request()->is('/')) || ($key === 'events.index' && request()->is('events*')))
                <a href="{{ $url }}"
                   wire:navigate
                   @if ($active) aria-current="page" @endif
                   class="px-4 py-2 rounded-lg font-bold text-[15px] border-[2.5px] transition-all duration-150 {{ $active ? 'bg-[var(--mustard)] border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]' : 'border-transparent text-[var(--ink)] hover:bg-[var(--paper2)]' }}">
                    {{ $label }}
                </a>
                @endforeach
            </nav>
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-3">
            @auth
                @if (Auth::user()->isAdmin() || Auth::user()->isPenyelenggara())
                    <a href="/admin" class="btn btn-sm btn-ghost">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-1 text-[var(--ink)]"></i>
                        <span>Panel Admin</span>
                    </a>
                @else
                    <a href="/dashboard" wire:navigate class="btn btn-sm btn-ghost">
                        <i data-lucide="gauge" class="w-4 h-4 mr-1 text-[var(--ink)]"></i>
                        <span>Portal Saya</span>
                    </a>
                @endif

                <a href="{{ route('ticket.show') }}" wire:navigate class="btn btn-sm btn-pine">
                    <i data-lucide="ticket" class="w-4 h-4 mr-1"></i>
                    <span>Tiket Saya</span>
                </a>
            @else
                <a href="/login" wire:navigate class="btn btn-sm">
                    <span>Masuk</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            @endauth

            <!-- Mobile Menu Button -->
            <button type="button" @click="mobileOpen = !mobileOpen" class="lg:hidden w-11 h-11 border-[2.5px] border-[var(--ink)] rounded-lg bg-[var(--card)] flex items-center justify-center cursor-pointer shadow-[3px_3px_0_var(--ink)]" aria-label="Menu navigasi">
                <i data-lucide="menu" class="w-5 h-5 text-[var(--ink)]"></i>
            </button>
        </div>

    </div>

    <!-- Mobile Dropdown Nav -->
    <div x-cloak x-show="mobileOpen" x-transition class="lg:hidden border-t-[3px] border-[var(--ink)] bg-[var(--paper)] px-4 py-4 space-y-2">
        @foreach([
            ['home', '/', 'Beranda'],
            ['events.index', '/events', 'Kalender'],
            ['ranking', '/ranking', 'Ranking'],
            ['about', '/about', 'Tentang'],
            ['rule', '/rule', 'Regulasi'],
            ['help', '/help', 'Bantuan'],
        ] as [$key, $url, $label])
        @php($active = request()->routeIs($key . '*') || ($key === 'home' && request()->is('/')) || ($key === 'events.index' && request()->is('events*')))
        <a href="{{ $url }}"
           wire:navigate
           @click="mobileOpen = false"
           class="flex items-center h-[52px] px-4 font-bold text-[16px] rounded-lg border-[2.5px] {{ $active ? 'bg-[var(--mustard)] border-[var(--ink)] shadow-[3px_3px_0_var(--ink)]' : 'border-[var(--ink)] bg-[var(--card)] text-[var(--ink)]' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</header>
