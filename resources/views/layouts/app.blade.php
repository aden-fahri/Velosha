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

    <!-- Midtrans Snap SDK (Pop-up Payment Gateway) -->
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}" data-navigate-track></script>

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
            <div class="px-3.5 py-2.5 text-xs font-bold bg-red-100 border-2 border-red-500 text-red-900 rounded-lg shadow-[2px_2px_0_#991b1b]" role="alert">
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
        @persist('mizu-chat')
            @livewire('mizu-chat-widget')
        @endpersist
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
        window.__veloInitReveal = function() {
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
        };
        window.__veloInitReveal();

        // Global Lucide Icon MutationObserver (Single Source of Truth)
        let __veloIconDebounce = null;
        window.__veloRenderIcons = function() {
            if (!window.lucide || typeof window.lucide.createIcons !== 'function') return;
            clearTimeout(__veloIconDebounce);
            __veloIconDebounce = setTimeout(() => {
                try {
                    window.lucide.createIcons();
                } catch (e) {}
            }, 30);
        };

        try { window.__veloRenderIcons(); } catch (e) {}

        // Pasang MutationObserver di document.body untuk deteksi otomatis ikon baru
        if (!window.__veloIconObserver && typeof MutationObserver !== 'undefined') {
            window.__veloIconObserver = new MutationObserver((mutations) => {
                let hasIcons = false;
                for (let i = 0; i < mutations.length; i++) {
                    const m = mutations[i];
                    if (m.addedNodes.length > 0) {
                        for (let j = 0; j < m.addedNodes.length; j++) {
                            const node = m.addedNodes[j];
                            if (node.nodeType === 1) {
                                if (node.hasAttribute && node.hasAttribute('data-lucide')) { hasIcons = true; break; }
                                if (node.firstElementChild && node.querySelector('[data-lucide]')) { hasIcons = true; break; }
                            }
                        }
                    }
                    if (hasIcons) break;
                }
                if (hasIcons) {
                    window.__veloRenderIcons();
                }
            });
            window.__veloIconObserver.observe(document.body, { childList: true, subtree: true });
        }

        // Global Anti-Double-Submit Guard - ONLY untuk Form Standar Non-Livewire
        window.__veloResetFormButtons = function() {
            document.querySelectorAll('button[data-velo-submitting]').forEach((btn) => {
                btn.disabled = false;
                btn.classList.remove('opacity-60', 'cursor-not-allowed');
                delete btn.dataset.veloSubmitting;
                if (btn.dataset.veloOriginalText) {
                    const span = btn.querySelector('span');
                    if (span) span.textContent = btn.dataset.veloOriginalText;
                    delete btn.dataset.veloOriginalText;
                }
            });
        };

        if (!window.__veloFormSubmitGuard) {
            window.__veloFormSubmitGuard = true;
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!form || !(form instanceof HTMLFormElement)) return;
                if (e.defaultPrevented) return;

                // Jangan sentuh form Livewire (wire:submit) karena Livewire 3 mengelola wire:loading secara native
                const isLivewireForm = form.hasAttribute('wire:submit') ||
                    Array.from(form.attributes).some(attr => attr.name.startsWith('wire:submit'));
                if (isLivewireForm) return;

                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (!submitBtn || submitBtn.disabled) return;

                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    submitBtn.dataset.veloSubmitting = 'true';
                    const textSpan = submitBtn.querySelector('span');
                    if (textSpan && !submitBtn.querySelector('.animate-spin')) {
                        submitBtn.dataset.veloOriginalText = textSpan.textContent;
                        textSpan.textContent = 'Memproses...';
                    }
                }, 10);
            });
        }

        // Livewire morph hook fallback & reset form guard
        if (!window.__veloLivewireInit) {
            window.__veloLivewireInit = true;
            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', () => {
                    window.__veloRenderIcons();
                    window.__veloResetFormButtons();
                });
                Livewire.hook('morph.added', () => window.__veloRenderIcons());
                Livewire.hook('commit', ({ succeed, fail }) => {
                    succeed(() => window.__veloResetFormButtons());
                    fail(() => window.__veloResetFormButtons());
                });
                Livewire.on('open-midtrans-snap', (data) => window.__veloHandleSnap(data));
            });
        }

        // Global Midtrans Snap Pop-up Handler (Zero External Redirection)
        window.__veloHandleSnap = function(detail) {
            const payload = Array.isArray(detail) ? detail[0] : detail;
            const token = payload?.token;
            const regId = payload?.registrationId;
            if (!token) return;

            if (window.snap && typeof window.snap.pay === 'function') {
                window.snap.pay(token, {
                    onSuccess: function(result) {
                        if (regId) window.location.href = '/ticket/' + regId;
                        else window.location.reload();
                    },
                    onPending: function(result) {
                        if (regId) window.location.href = '/ticket/' + regId;
                        else window.location.reload();
                    },
                    onError: function(result) {
                        alert('Pembayaran tidak berhasil atau dibatalkan.');
                    },
                    onClose: function() {
                        console.log('Jendela Snap ditutup.');
                    }
                });
            } else if (payload?.redirectUrl) {
                window.location.href = payload.redirectUrl;
            }
        };
        window.addEventListener('open-midtrans-snap', (e) => window.__veloHandleSnap(e.detail));

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
                window.__veloInitReveal();
                if (typeof window.__veloResetFormButtons === 'function') window.__veloResetFormButtons();
                if (typeof window.initRaceDeck === 'function') window.initRaceDeck();
                if (typeof window.initInteractiveTicket === 'function') window.initInteractiveTicket();
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
