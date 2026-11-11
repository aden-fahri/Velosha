<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Velosha' }} — Platform Maraton & Balap Sepeda Indonesia</title>
    <meta name="description" content="Velosha adalah platform kurasi dan registrasi maraton sepeda endurance, Gran Fondo, dan cycling experience terdepan di Indonesia.">

    <link rel="preload" href="{{ asset('fonts/velosha/bricolage-grotesque-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/velosha/inter-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- Lucide Icons -->
    <script src="{{ asset('js/lucide.min.js') }}"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="vs min-h-screen flex flex-col bg-[var(--paper)] text-[var(--ink)] selection:bg-[var(--mustard)] selection:text-[var(--ink)]">
<a href="#main-content" class="skip-link">Lewati ke konten</a>
@php($nav = $nav ?? true)

    @if ($nav)
    @include('components.layout.navbar')
    @endif

    <!-- Global Flash Banner (redirect error/sukses lintas halaman) -->
    @if (session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             x-transition.opacity
             class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="px-3 py-2 text-xs bg-red-50 border border-red-200 text-red-700 velo-box-1" role="alert">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content Yield -->
    <main id="main-content" tabindex="-1" class="flex-grow">
        @yield('content', $slot ?? '')
    </main>

    {{-- Tanya Mizu hanya tampil bila GEMINI_API_KEY sudah dikonfigurasi, bukan di halaman helpdesk*, dan bukan untuk operator/admin --}}
    @if (app(\App\Support\MizuAiService::class)->isConfigured()
         && ! request()->is('helpdesk*')
         && ! (auth()->check() && (auth()->user()->isOperator() || auth()->user()->isAdmin())))
        @livewire('mizu-chat-widget')
    @endif

    <!-- Neo-Brutalist Top Navigation Progress Bar -->
    <div id="velo-nav-progress"
         class="fixed top-0 left-0 right-0 h-[3.5px] bg-[#DE7119] z-[9999] pointer-events-none opacity-0 shadow-[0_1px_3px_rgba(16,40,46,0.3)]"
         style="transform: scaleX(0); transform-origin: 0% 50%;"
         aria-hidden="true"></div>

    @if ($nav)
    @include('components.layout.footer')
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

        // Lucide Icons init dengan throttling & scoping agar tidak membebani main thread
        window.__veloRenderIcons = function(rootEl = document) {
            if (!window.lucide || typeof window.lucide.createIcons !== 'function') return;
            window.requestAnimationFrame(() => {
                try {
                    window.lucide.createIcons({
                        root: rootEl instanceof HTMLElement ? rootEl : document
                    });
                } catch (e) {}
            });
        };

        try { window.__veloRenderIcons(); } catch (e) {}

        // Re-render Lucide icons inside regions morphed by Livewire
        if (!window.__veloLivewireInit) {
            window.__veloLivewireInit = true;
            document.addEventListener('livewire:init', () => {
                let rerenderTimer;
                Livewire.hook('morph.added', ({ el }) => {
                    clearTimeout(rerenderTimer);
                    rerenderTimer = setTimeout(() => {
                        window.__veloRenderIcons(el);
                    }, 16);
                });
            });
        }

        // Top progress bar & transisi halaman wire:navigate instan
        if (!window.__veloNavFx) {
            window.__veloNavFx = true;
            let enterTimer;
            document.addEventListener('livewire:navigating', () => {
                const bar = document.getElementById('velo-nav-progress');
                if (bar) {
                    bar.style.transition = 'transform 0.35s cubic-bezier(0.1, 0.9, 0.2, 1), opacity 0.1s ease';
                    bar.style.opacity = '1';
                    bar.style.transform = 'scaleX(0.75)';
                }
                document.body.classList.remove('velo-nav-entering');
                document.body.classList.add('velo-nav-leaving');
            });
            document.addEventListener('livewire:navigated', () => {
                const bar = document.getElementById('velo-nav-progress');
                if (bar) {
                    bar.style.transition = 'transform 0.15s ease-out, opacity 0.2s ease-out 0.15s';
                    bar.style.transform = 'scaleX(1)';
                    bar.style.opacity = '0';
                    setTimeout(() => {
                        bar.style.transform = 'scaleX(0)';
                    }, 350);
                }
                window.__veloRenderIcons();
                document.body.classList.remove('velo-nav-leaving');
                document.body.classList.add('velo-nav-entering');
                clearTimeout(enterTimer);
                enterTimer = setTimeout(() => document.body.classList.remove('velo-nav-entering'), 300);
            });
        }
    </script>
    <script type="module" src="{{ asset('js/intro-hero.js') }}"></script>
    <script type="module" src="{{ asset('js/race-deck.js') }}"></script>
    <script type="module" src="{{ asset('js/interactive-ticket.js') }}"></script>
</body>
</html>
