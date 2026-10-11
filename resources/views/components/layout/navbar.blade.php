<!-- Main Navigation Bar (Modern Glassmorphism with Active Nav Pills) -->
<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 bg-white/90 backdrop-blur-md backdrop-saturate-150 border-b border-veloborder shadow-xs transition-all duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

            <!-- Logo & Brand -->
            <div class="flex items-center gap-8">
                <a href="/" wire:navigate class="flex items-center group transition-transform duration-200 hover:scale-102" aria-label="Velosha — Beranda">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-12 w-auto object-contain" />
                </a>

                <!-- Nav Links (Desktop) -->
                <nav class="hidden lg:flex items-center space-x-1 text-xs font-bold">
                    @foreach([
                        ['home', '/', 'Home'],
                        ['about', '/about', 'About'],
                        ['rule', '/rule', 'Rule'],
                        ['riwayat', '/riwayat', 'History'],
                        ['ranking', '/ranking', 'Ranking'],
                        ['help', '/help', 'Help Center'],
                    ] as [$key, $url, $label])
                    @php($active = request()->routeIs($key . '*'))
                    <a href="{{ $url }}"
                       wire:navigate
                       @if ($active) aria-current="page" @endif
                       class="relative px-3.5 py-2 velo-box-3 transition-all duration-200 {{ $active ? 'bg-teal-900 text-white shadow-xs' : 'text-velomutedlight hover:text-teal-900 hover:bg-[#f1f4f4]' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </nav>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">
                @auth
                    @if (Auth::user()->isAdmin() || Auth::user()->isPenyelenggara())
                        <a href="/admin" class="hidden sm:inline-flex items-center text-xs font-extrabold text-teal-900 bg-white hover:bg-[#f1f4f4] border border-veloborderstrong px-4 py-2.5 velo-box-1 shadow-xs transition-all duration-200 hover:-translate-y-0.5">
                            <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 mr-1.5 text-teal-700"></i>
                            <span>Panel Pengelola</span>
                        </a>
                    @else
                        <a href="/dashboard" wire:navigate class="hidden sm:inline-flex items-center text-xs font-extrabold text-teal-900 bg-white hover:bg-[#f1f4f4] border border-veloborderstrong px-4 py-2.5 velo-box-1 shadow-xs transition-all duration-200 hover:-translate-y-0.5">
                            <i data-lucide="gauge" class="w-3.5 h-3.5 mr-1.5 text-teal-700"></i>
                            <span>Dashboard Peserta</span>
                        </a>
                    @endif

                    <a href="{{ route('ticket.show') }}" wire:navigate class="hidden sm:inline-flex items-center text-xs font-extrabold text-white bg-race-600 hover:bg-race-700 px-4 py-2.5 velo-box-1 shadow-xs transition-all duration-200 hover:-translate-y-0.5">
                        <i data-lucide="ticket" class="w-3.5 h-3.5 mr-1.5"></i>
                        <span>Tiket Saya</span>
                    </a>
                @else
                    <a href="/login" wire:navigate class="inline-flex items-center text-xs font-extrabold text-white bg-race-600 hover:bg-race-700 px-4.5 py-2.5 velo-box-1 shadow-xs transition-all duration-200 hover:-translate-y-0.5">
                        <span>Masuk</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5"></i>
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-teal-900 hover:bg-[#f1f4f4] velo-box-1" aria-label="Menu navigasi">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>

        </div>

        <!-- Mobile Dropdown Nav -->
        <div x-cloak x-show="mobileOpen" x-transition class="lg:hidden border-t border-veloborder py-3 space-y-1 bg-white/95 backdrop-blur-md -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
            @foreach([
                ['home', '/', 'Home'],
                ['about', '/about', 'About'],
                ['rule', '/rule', 'Rule'],
                ['riwayat', '/riwayat', 'History'],
                ['ranking', '/ranking', 'Ranking'],
                ['help', '/help', 'Help Center'],
            ] as [$key, $url, $label])
            <a href="{{ $url }}"
               wire:navigate
               @click="mobileOpen = false"
               class="block px-3.5 py-2.5 text-xs font-bold velo-box-3 {{ request()->routeIs($key . '*') ? 'bg-teal-900 text-white' : 'text-velomutedlight hover:bg-[#f1f4f4]' }}">
                {{ $label }}
            </a>
            @endforeach

            <div class="pt-2 mt-1 border-t border-veloborder space-y-2">
                @auth
                    <a href="{{ Auth::user()->isPeserta() ? '/dashboard' : '/admin' }}"
                       wire:navigate
                       @click="mobileOpen = false"
                       class="flex items-center gap-2.5 block px-3.5 py-2.5 text-xs font-extrabold text-teal-900 bg-teal-50 border border-teal-200 velo-box-1">
                        <i data-lucide="{{ Auth::user()->isPeserta() ? 'gauge' : 'layout-dashboard' }}" class="w-3.5 h-3.5 text-teal-700"></i>
                        {{ Auth::user()->isPeserta() ? 'Dashboard Peserta' : 'Panel Pengelola' }}
                    </a>
                    <a href="{{ route('ticket.show') }}"
                       wire:navigate
                       @click="mobileOpen = false"
                       class="flex items-center gap-2.5 block px-3.5 py-2.5 text-xs font-extrabold text-white bg-race-600 velo-box-1">
                        <i data-lucide="ticket" class="w-3.5 h-3.5"></i>
                        Tiket Saya
                    </a>
                @else
                    <a href="/login" wire:navigate @click="mobileOpen = false" class="block px-3.5 py-2.5 text-xs font-extrabold text-white bg-race-600 hover:bg-race-700 velo-box-1 text-center">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>

    </div>
</header>
