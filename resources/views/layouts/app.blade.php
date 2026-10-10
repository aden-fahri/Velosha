<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Velosha' }} — Platform Maraton & Balap Sepeda Indonesia</title>
    <meta name="description" content="Velosha adalah platform kurasi dan registrasi maraton sepeda endurance, Gran Fondo, dan cycling experience terdepan di Indonesia.">

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite build — single source of truth) -->

    <!-- Lucide Icons (self-hosted — tanpa dependensi CDN saat render) -->
    <script src="{{ asset('js/lucide.min.js') }}"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #0A353E;
            background-color: #dee3e2;
            background-image: url("/images/patterns/topo.svg");
            background-size: 480px;
            -webkit-font-smoothing: antialiased;
        }

        /* Progress bar wire:navigate — lebih tebal, oranye brand (var di app.css) */
        html #nprogress .bar {
            height: 4px;
        }

        /* Strict Design Rules: Outer 1px, Nested Inner 3px, No Capsules */
        .velo-box-1 {
            border-radius: 1px !important;
        }
        .velo-box-3 {
            border-radius: 3px !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #dee3e2;
        }
        ::-webkit-scrollbar-thumb {
            background: #b8bfbe;
            border-radius: 1px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #93a0a1;
        }

        /* Reveal on scroll: fade + slide-up per section */
        .velo-reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s ease-out, transform 0.7s ease-out;
        }
        .velo-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Modal enter: fade + zoom (pengganti plugin tailwindcss-animate) */
        @keyframes veloModalEnter {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .velo-modal-enter {
            animation: veloModalEnter 0.15s ease-out;
        }

        /* Page transition: blur antar halaman (wire:navigate) */
        @keyframes veloPageLeave {
            from { opacity: 1; filter: blur(0); }
            to { opacity: 0.3; filter: blur(6px); }
        }
        @keyframes veloPageEnter {
            from { opacity: 0.3; filter: blur(6px); }
            to { opacity: 1; filter: blur(0); }
        }
        body.velo-nav-leaving > header,
        body.velo-nav-leaving > main,
        body.velo-nav-leaving > footer {
            animation: veloPageLeave 0.25s ease-in forwards;
        }
        body.velo-nav-entering > header,
        body.velo-nav-entering > main,
        body.velo-nav-entering > footer {
            animation: veloPageEnter 0.35s ease-out;
        }

        /* Korden banner abu bertekstur — muncul sesaat selama pindah halaman */
        body.velo-nav-leaving::before,
        body.velo-nav-entering::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 1000;
            pointer-events: none;
            background: #dee3e2 url("/images/patterns/topo.svg") repeat;
            background-size: 480px;
            opacity: 0;
        }
        body.velo-nav-leaving::before {
            animation: veloCurtainIn 0.25s ease-in forwards;
        }
        body.velo-nav-entering::before {
            animation: veloCurtainOut 0.35s ease-out forwards;
        }
        @keyframes veloCurtainIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes veloCurtainOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            .velo-reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }
            body.velo-nav-leaving > header,
            body.velo-nav-leaving > main,
            body.velo-nav-leaving > footer,
            body.velo-nav-entering > header,
            body.velo-nav-entering > main,
            body.velo-nav-entering > footer,
            .velo-modal-enter {
                animation: none;
            }
            body.velo-nav-leaving::before,
            body.velo-nav-entering::before {
                animation: none;
                opacity: 0;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-velosurface text-teal-900 selection:bg-teal-700 selection:text-white">
@php($nav = $nav ?? true)

    @if ($nav)
    <!-- Main Navigation Bar (White Glassmorphism) -->
    <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-xl backdrop-saturate-150 border-b border-veloborder shadow-[0_1px_16px_rgba(10,53,62,0.06)] supports-[backdrop-filter]:bg-white/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-8">
                    <a href="/" wire:navigate class="flex items-center group" aria-label="Velosha — Beranda">
                        <img src="/images/logo-velosha.png" alt="Velosha" class="h-14 w-auto object-contain" />
                    </a>

                    <!-- Nav Links (Desktop) -->
                    <nav class="hidden lg:flex items-center space-x-0.5 text-xs">
                        @foreach([
                            ['home', '/', 'Home'],
                            ['about', '/about', 'About'],
                            ['rule', '/rule', 'Rule'],
                            ['riwayat', '/riwayat', 'Riwayat'],
                            ['ranking', '/ranking', 'Ranking'],
                            ['help', '/help', 'Help Center'],
                        ] as [$key, $url, $label])
                        <a href="{{ $url }}"
                           wire:navigate
                           @if (request()->routeIs($key . '*')) aria-current="page" @endif
                           class="px-3 py-2 font-medium rounded-[1px] transition-colors {{ request()->routeIs($key . '*') ? 'bg-race-50 text-race-700' : 'text-[#4b555b] hover:text-teal-700 hover:bg-teal-50' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    </nav>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2.5">
                    @auth
                        @if (Auth::user()->isAdmin() || Auth::user()->isPenyelenggara())
                            <a href="/admin" wire:navigate class="hidden sm:inline-flex items-center text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-700 hover:text-white px-4 py-2.5 velo-box-1 transition">
                                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 mr-1.5"></i>
                                <span>Panel</span>
                            </a>
                        @else
                            <a href="/dashboard" wire:navigate class="hidden sm:inline-flex items-center text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-700 hover:text-white px-4 py-2.5 velo-box-1 transition">
                                <i data-lucide="gauge" class="w-3.5 h-3.5 mr-1.5"></i>
                                <span>Dashboard</span>
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="hidden sm:block">
                            @csrf
                            <button type="submit" class="inline-flex items-center text-xs font-bold text-white bg-race-600 hover:bg-race-700 px-4 py-2.5 velo-box-1 transition">
                                <span>Keluar</span>
                                <i data-lucide="log-out" class="w-3.5 h-3.5 ml-1.5"></i>
                            </button>
                        </form>
                    @else
                        <a href="/login" wire:navigate class="inline-flex items-center text-xs font-bold text-white bg-race-600 hover:bg-race-700 px-4 py-2.5 velo-box-1 transition">
                            <span>Masuk</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5"></i>
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 text-teal-900 hover:bg-teal-50 velo-box-1" aria-label="Menu navigasi">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>

            </div>

            <!-- Mobile Dropdown Nav -->
            <div id="mobileNav" class="hidden lg:hidden border-t border-veloborder py-3 space-y-1 bg-white/80 backdrop-blur-xl -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
                @foreach([
                    ['home', '/', 'Home'],
                    ['about', '/about', 'About'],
                    ['rule', '/rule', 'Rule'],
                    ['riwayat', '/riwayat', 'Riwayat'],
                    ['ranking', '/ranking', 'Ranking'],
                    ['help', '/help', 'Help Center'],
                ] as [$key, $url, $label])
                <a href="{{ $url }}"
                   wire:navigate
                   @if (request()->routeIs($key . '*')) aria-current="page" @endif
                   onclick="toggleMobileMenu()"
                   class="block px-3 py-2 text-xs font-semibold rounded-[1px] {{ request()->routeIs($key . '*') ? 'bg-race-50 text-race-700' : 'text-[#4b555b] hover:text-teal-700 hover:bg-teal-50' }}">
                    {{ $label }}
                </a>
                @endforeach

                <div class="pt-2 mt-1 border-t border-veloborder">
                    @auth
                        <a href="{{ Auth::user()->isPeserta() ? '/dashboard' : '/admin' }}"
                           wire:navigate
                           onclick="toggleMobileMenu()"
                           class="block px-3 py-2 text-xs font-semibold text-teal-700 hover:bg-teal-50 rounded-[1px]">
                            {{ Auth::user()->isPeserta() ? 'Dashboard Peserta' : 'Panel Pengelola' }}
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 text-xs font-semibold text-race-600 hover:bg-race-50 rounded-[1px]">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="/login" wire:navigate onclick="toggleMobileMenu()" class="block px-3 py-2 text-xs font-bold text-white bg-race-600 hover:bg-race-700 velo-box-1 text-center">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>

    </div>
    </header>
    @endif

    <!-- Global Flash Banner (redirect error/sukses lintas halaman) -->
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="px-3 py-2 text-xs bg-red-50 border border-red-200 text-red-700 velo-box-1" role="alert">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content Yield -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Floating Chatbot AI Widget -->
    <div id="veloChatWidget" class="fixed bottom-6 right-6 z-50">
        <!-- Chat Popup Panel (hidden by default) -->
        <div id="veloChatPanel" class="hidden mb-3 w-80 sm:w-96 bg-white border border-veloborder velo-box-1 shadow-xl overflow-hidden flex flex-col">
            <div class="bg-teal-700 px-4 py-3 text-white flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 bg-teal-500/40 flex items-center justify-center velo-box-3">
                        <i data-lucide="bot" class="w-3.5 h-3.5"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold leading-tight">Asisten Balap</div>
                        <div class="text-[10px] text-white/80">Siap membantu</div>
                    </div>
                </div>
                <button onclick="toggleChatbot()" class="text-white/70 hover:text-white p-1" aria-label="Tutup asisten">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <!-- Chat Messages -->
            <div class="p-3.5 h-64 overflow-y-auto space-y-3 bg-velosurfacealt text-xs">
                <div class="flex items-start gap-2">
                    <div class="bg-white border border-veloborder p-2.5 velo-box-3 text-teal-800 leading-relaxed max-w-[85%]">
                        Halo! Butuh bantuan pilih kategori jarak, tips nutrisi, atau info rute?
                    </div>
                </div>
                <div class="flex justify-end">
                    <div class="bg-race-600 text-white p-2.5 velo-box-3 leading-relaxed max-w-[85%]">
                        Bedanya Gran Fondo 130KM dan Medio Fondo 75KM?
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <div class="bg-white border border-veloborder p-2.5 velo-box-3 text-teal-800 leading-relaxed max-w-[85%]">
                        <strong>Gran Fondo</strong> 130KM, elevasi +1.450m, cut-off 7 jam. Untuk endurance.<br>
                        <strong>Medio Fondo</strong> 75KM, elevasi +680m, cut-off 4,5 jam. Lebih ringan.
                    </div>
                </div>
            </div>
            <!-- Input -->
            <div class="p-2.5 bg-white border-t border-veloborder flex items-center gap-2">
                <input type="text" placeholder="Tanya seputar rute atau BIB..." class="flex-grow px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-teal-600">
                <button class="bg-teal-700 hover:bg-teal-800 text-white px-3 py-2 velo-box-1" aria-label="Kirim">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        <!-- Chat Floating Trigger Button -->
        <button onclick="toggleChatbot()" class="flex items-center gap-2 bg-teal-700 hover:bg-teal-800 text-white px-4 py-3 velo-box-1 shadow-lg transition hover:-translate-y-0.5">
            <i data-lucide="message-square" class="w-4 h-4"></i>
            <span class="text-xs font-semibold tracking-wide">Tanya Aide</span>
        </button>
    </div>

    <!-- Footer -->
    @if ($nav)
    @php($footer = \App\Models\FooterContent::current())
    <footer class="bg-velosurfacealt border-t border-veloborder text-[#4b555b] mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-10">

                <!-- Brand -->
                <div class="col-span-2 md:col-span-1 space-y-4">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-20 w-auto object-contain" />
                    <p class="text-sm text-[#556066] leading-relaxed max-w-xs">
                        {{ $footer->brand_description }}
                    </p>
                </div>

                <!-- Kategori -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-teal-800 mb-4">{{ $footer->kategori_title }}</h4>
                    <ul class="space-y-2.5 text-sm">
                        @foreach (($footer->kategori_links ?? []) as $link)
                        <li><a href="{{ $link['url'] }}" wire:navigate class="hover:text-race-600 transition">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Bantuan -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-teal-800 mb-4">{{ $footer->bantuan_title }}</h4>
                    <ul class="space-y-2.5 text-sm">
                        @foreach (($footer->bantuan_links ?? []) as $link)
                            @unless (auth()->check() && (
                                ($link['url'] === '/dashboard' && (auth()->user()->isAdmin() || auth()->user()->isPenyelenggara()))
                                || ($link['url'] === '/admin' && auth()->user()->isPeserta())
                            ))
                            <li><a href="{{ $link['url'] }}" wire:navigate class="hover:text-race-600 transition">{{ $link['label'] }}</a></li>
                            @endunless
                        @endforeach
                    </ul>
                </div>

                <!-- Pembayaran -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-teal-800 mb-4">{{ $footer->payment_title }}</h4>
                    <div class="grid grid-cols-2 gap-2 text-[11px] font-semibold text-teal-700">
                        @foreach (($footer->payment_methods ?? []) as $method)
                        <div class="border border-veloborder bg-white p-2 text-center velo-box-3">{{ $method['label'] }}</div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Bottom Subfooter -->
            <div class="mt-10 pt-6 border-t border-veloborder flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-[#556066]">
                <p>{{ $footer->copyright }}</p>
                <div class="flex gap-6">
                    @foreach (($footer->bottom_links ?? []) as $link)
                    <a href="{{ $link['url'] }}" wire:navigate class="hover:text-teal-900">{{ $link['label'] }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>
    @endif

    <script>
        // Reveal on scroll (fade + slide-up) — dijalankan ulang tiap wire:navigate
        try {
            if (window.__veloRevealObserver) window.__veloRevealObserver.disconnect();
            window.__veloRevealObserver = new IntersectionObserver((entries, obs) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.05, rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('.velo-reveal:not(.is-visible)').forEach((el) => {
                window.__veloRevealObserver.observe(el);
            });
        } catch (e) {
            document.querySelectorAll('.velo-reveal').forEach((el) => el.classList.add('is-visible'));
        }

        // Lucide Icons init (aman walau CDN gagal — reveal di atas tidak bergantung ini)
        try { if (window.lucide) lucide.createIcons(); } catch (e) {}

        // Re-render Lucide icons inside regions morphed by Livewire
        if (!window.__veloLivewireInit) {
            window.__veloLivewireInit = true;
            document.addEventListener('livewire:init', () => {
                let rerender;
                Livewire.hook('morph.added', () => {
                    clearTimeout(rerender);
                    rerender = setTimeout(() => {
                        if (window.lucide) lucide.createIcons();
                    }, 0);
                });
            });
        }

        // Efek blur antar halaman wire:navigate (didaftarkan sekali per muat penuh)
        if (!window.__veloNavFx) {
            window.__veloNavFx = true;
            let enterTimer;
            document.addEventListener('livewire:navigating', () => {
                document.body.classList.remove('velo-nav-entering');
                document.body.classList.add('velo-nav-leaving');
            });
            document.addEventListener('livewire:navigated', () => {
                document.body.classList.remove('velo-nav-leaving');
                document.body.classList.add('velo-nav-entering');
                clearTimeout(enterTimer);
                enterTimer = setTimeout(() => document.body.classList.remove('velo-nav-entering'), 400);
            });
        }

        // Chatbot toggler
        function toggleChatbot() {
            const panel = document.getElementById('veloChatPanel');
            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        }

        // Mobile Nav toggler
        function toggleMobileMenu() {
            const nav = document.getElementById('mobileNav');
            if (nav) {
                nav.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
