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
    </script>
</body>
</html>
