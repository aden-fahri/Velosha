<!-- Main Navigation Bar (Cartoon Screen-Print Style) -->
<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 bg-[var(--paper)] border-b-[3px] border-[var(--ink)]">
    <div class="wrap flex items-center justify-between h-[76px]">

        <!-- Logo & Brand -->
        <div class="flex items-center gap-8">
            <a href="/" wire:navigate.hover class="flex items-center gap-3 transition-transform duration-150 hover:-translate-y-0.5" aria-label="Velosha — Beranda">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-9 w-auto object-contain" />
                <span class="font-extrabold text-2xl tracking-tight text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
            </a>

            <!-- Nav Links (Desktop — 7 items) -->
            <nav class="hidden lg:flex items-center gap-1.5" aria-label="Utama">
                @foreach([
                    ['home', '/', 'Beranda'],
                    ['events.index', '/events', 'Kalender'],
                    ['about', '/about', 'Tentang'],
                    ['rule', '/rule', 'Regulasi'],
                    ['help', '/help', 'Bantuan'],
                ] as [$key, $url, $label])
                @php
                    $active = request()->routeIs($key . '*') || ($key === 'home' && request()->is('/')) || ($key === 'events.index' && request()->is('events*'));
                @endphp
                <a href="{{ $url }}"
                   wire:navigate.hover
                   @if ($active) aria-current="page" @endif
                   class="px-4 py-2 rounded-lg font-bold text-[15px] border-[2.5px] transition-all duration-150 {{ $active ? 'bg-[var(--mustard)] border-[var(--ink)] shadow-[2px_2px_0_var(--ink)]' : 'border-transparent text-[var(--ink)] hover:bg-[var(--paper2)] hover:border-[var(--ink)] hover:shadow-[2px_2px_0_var(--ink)] hover:-translate-y-0.5' }}">
                    {{ $label }}
                </a>
                @endforeach
            </nav>
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-3">
            <div class="hidden lg:flex items-center gap-3">
                @if (auth()->check())
                    @php
                        $u = auth()->user();
                        $homeUrl = $u->home();
                        if ($u->isOperator()) {
                            $homeLabel = 'Antrian Helpdesk';
                            $homeIcon = 'headset';
                        } elseif ($u->isAdmin() || $u->isPenyelenggara()) {
                            $homeLabel = 'Panel Admin';
                            $homeIcon = 'layout-dashboard';
                        } else {
                            $homeLabel = 'Portal Saya';
                            $homeIcon = 'layout-dashboard';
                        }
                    @endphp
                    <a href="{{ $homeUrl }}" {{ $u->isAdmin() ? '' : 'wire:navigate.hover' }}
                       class="btn btn-sm btn-ghost px-3 text-sm font-bold items-center gap-1.5"
                       aria-label="{{ $homeLabel }}">
                        <i data-lucide="{{ $homeIcon }}" class="w-4 h-4 text-[var(--ink)] shrink-0"></i>
                        <span>{{ $homeLabel }}</span>
                    </a>

                    <a href="{{ route('ticket.show') }}" wire:navigate.hover
                       class="btn btn-sm btn-pine px-3 text-sm font-bold items-center gap-1.5"
                       aria-label="Tiket Saya">
                        <i data-lucide="ticket" class="w-4 h-4 text-white shrink-0"></i>
                        <span>Tiket Saya</span>
                    </a>
                @else
                    <a href="/login" wire:navigate.hover class="btn btn-sm px-3 text-sm font-bold">
                        <span>Masuk</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                @endif
            </div>

            <!-- Mobile Menu Button -->
            <button type="button" @click="mobileOpen = !mobileOpen"
                    class="lg:hidden w-11 h-11 border-[2.5px] border-[var(--ink)] rounded-lg bg-[var(--card)] flex items-center justify-center cursor-pointer shadow-[3px_3px_0_var(--ink)] shrink-0"
                    aria-label="Menu navigasi">
                <i data-lucide="menu" class="w-5 h-5 text-[var(--ink)]"></i>
            </button>
        </div>

    </div>

    <!-- Mobile Dropdown Nav -->
    <div x-cloak x-show="mobileOpen" x-transition class="lg:hidden border-t-[3px] border-[var(--ink)] bg-[var(--paper)] px-4 py-4 space-y-2">
        @foreach([
            ['home', '/', 'Beranda'],
            ['events.index', '/events', 'Kalender'],
            ['about', '/about', 'Tentang'],
            ['rule', '/rule', 'Regulasi'],
            ['help', '/help', 'Bantuan'],
        ] as [$key, $url, $label])
        @php
            $active = request()->routeIs($key . '*') || ($key === 'home' && request()->is('/')) || ($key === 'events.index' && request()->is('events*'));
        @endphp
        <a href="{{ $url }}"
           wire:navigate.hover
           @click="mobileOpen = false"
           class="flex items-center h-[48px] px-4 font-bold text-[15px] rounded-lg border-[2.5px] transition-all duration-150 {{ $active ? 'bg-[var(--mustard)] border-[var(--ink)] shadow-[3px_3px_0_var(--ink)]' : 'border-[var(--ink)] bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)] hover:-translate-y-0.5' }}">
            {{ $label }}
        </a>
        @endforeach

        @if (auth()->check())
            @php
                $u = auth()->user();
                $homeUrl = $u->home();
            @endphp
            <div class="pt-2 border-t-2 border-dashed border-[var(--ink)] space-y-2">
                <a href="{{ $homeUrl }}"
                   {{ $u->isAdmin() ? '' : 'wire:navigate.hover' }}
                   @click="mobileOpen = false"
                   class="flex items-center justify-between h-[48px] px-4 font-bold text-[15px] rounded-lg border-[2.5px] border-[var(--ink)] bg-[var(--butter)] text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                    <span class="flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </span>
                    <span class="text-xs uppercase px-2 py-0.5 bg-[var(--mustard)] border border-[var(--ink)] rounded font-black">
                        {{ $u->role }}
                    </span>
                </a>

                <a href="{{ route('ticket.show') }}"
                   wire:navigate.hover
                   @click="mobileOpen = false"
                   class="flex items-center gap-2 h-[48px] px-4 font-bold text-[15px] rounded-lg border-[2.5px] border-[var(--ink)] bg-[#116979] text-white shadow-[2px_2px_0_var(--ink)]">
                    <i data-lucide="ticket" class="w-4 h-4"></i>
                    Tiket Saya
                </a>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 h-[44px] px-4 font-bold text-[14px] rounded-lg border-[2.5px] border-[var(--ink)] bg-[var(--card)] text-red-600 hover:bg-red-50 shadow-[2px_2px_0_var(--ink)] cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Keluar
                    </button>
                </form>
            </div>
        @else
            <div class="pt-2 border-t-2 border-dashed border-[var(--ink)]">
                <a href="/login"
                   wire:navigate.hover
                   @click="mobileOpen = false"
                   class="flex items-center justify-center gap-2 h-[48px] px-4 font-bold text-[15px] rounded-lg border-[2.5px] border-[var(--ink)] bg-[var(--orange)] text-white shadow-[3px_3px_0_var(--ink)]">
                    <span>Masuk ke Akun</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        @endif
    </div>
</header>
