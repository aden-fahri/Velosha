@php
$footer = \App\Models\FooterContent::current();

// Check existence of Organizer routes per instruction
$organizerLinks = [];
if (\Illuminate\Support\Facades\Route::has('organizer.register')) {
    $organizerLinks[] = ['label' => 'Jadi penyelenggara', 'url' => route('organizer.register')];
}
if (\Illuminate\Support\Facades\Route::has('organizer.packages')) {
    $organizerLinks[] = ['label' => 'Paket kuota event', 'url' => route('organizer.packages')];
}
if (\Illuminate\Support\Facades\Route::has('organizer.dashboard')) {
    $organizerLinks[] = ['label' => 'Masuk ke dasbor', 'url' => route('organizer.dashboard')];
}

$hasOrganizerLinks = count($organizerLinks) > 0;

// Auth-aware target for portal & e-ticket
$portalTarget = auth()->check() ? (Route::has('dashboard') ? route('dashboard') : '/dashboard') : (Route::has('login') ? route('login') : '/login');
@endphp

<footer id="footer" class="relative bg-[#10282E] text-[#E8DFCB] mt-20 md:mt-28 overflow-hidden">
    {{-- Top Strip: 14px tall, warna orange #DE7119 solid tanpa garis putih, bottom border 3px #0B1F25 --}}
    <div class="footer-top-strip" aria-hidden="true"></div>

    {{-- Watermark Kata VELOSHA: Ekstra Besar Membentang Mengikuti Footer, Samar Elegan --}}
    <div class="footer-velosha-watermark" aria-hidden="true">
        VELOSHA
    </div>

    <style>
        .footer-top-strip {
            height: 14px;
            width: 100%;
            background-color: #DE7119;
            border-bottom: 3px solid #0B1F25;
            position: relative;
            z-index: 2;
        }

        /* Watermark VELOSHA: Raksasa Membentang Penuh di Bawah Footer */
        .footer-velosha-watermark {
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            text-align: center;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: clamp(140px, 24vw, 360px);
            font-weight: 900;
            line-height: 0.75;
            letter-spacing: -0.04em;
            color: #FFFDF8;
            opacity: 0.022; /* Sangat halus, tidak terlalu kebaca, tekstur estetis latar belakang */
            user-select: none;
            pointer-events: none;
            white-space: nowrap;
            z-index: 1;
        }

        /* Container & Grid */
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 52px 24px 24px;
            position: relative;
            z-index: 2;
        }
        @media (max-width: 899.98px) {
            .footer-container {
                padding: 40px 20px 24px;
            }
        }
        @media (max-width: 639.98px) {
            .footer-container {
                padding-bottom: 96px !important;
            }
        }

        /* Desktop >= 900px */
        @media (min-width: 900px) {
            .footer-grid-layout {
                display: grid;
                grid-template-columns: 1.4fr 1fr 1fr 1.35fr;
                gap: 40px;
            }
        }

        /* Mobile & Tablet < 900px */
        @media (max-width: 899.98px) {
            .footer-grid-layout {
                display: flex;
                flex-direction: column;
                gap: 36px;
            }
            .footer-links-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 28px 20px;
            }
        }

        /* Column Heading */
        .footer-col-heading {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #F4BE3F;
            margin-bottom: 14px;
        }

        /* Links with orange chevron > */
        .footer-link {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            min-height: 38px;
            color: #E8DFCB;
            font-size: 15px;
            text-decoration: none;
            line-height: 1.4;
            transition: color 0.15s ease, transform 0.15s ease, text-decoration-color 0.15s ease;
        }
        .footer-link-chevron {
            color: #DE7119;
            font-weight: 900;
            font-size: 14px;
            line-height: 1;
            flex-shrink: 0;
            transition: transform 0.15s ease;
        }
        .footer-link:hover {
            color: #FFFFFF;
            transform: translateX(4px);
            text-decoration: underline 2px #DE7119;
            text-underline-offset: 5px;
        }
        .footer-link:hover .footer-link-chevron {
            transform: translateX(2px);
        }

        /* Social Icons 44x44 */
        .footer-social-btn {
            width: 44px;
            height: 44px;
            background-color: #FBF4E6;
            color: #10282E;
            border: 2.5px solid #10282E;
            border-radius: 12px;
            box-shadow: 3px 3px 0 #DE7119;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            text-decoration: none;
            flex-shrink: 0;
        }
        .footer-social-btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0 #DE7119;
        }

        /* Payment Column Chips Grid (di samping Bantuan & Info) */
        .footer-pay-chips-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .footer-pay-chip {
            height: 38px;
            padding: 0 12px;
            border: 2px solid #3A5860;
            border-radius: 8px;
            background-color: #17383F;
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        /* Bottom Bar */
        .footer-bottom-bar {
            margin-top: 44px;
            padding-top: 22px;
            border-top: 1px solid #2C474E;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            font-size: 13px;
            color: #E8DFCB;
        }
        @media (max-width: 899.98px) {
            .footer-bottom-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
        }

        /* Ke Atas Button */
        .footer-top-btn {
            height: 40px;
            padding: 0 14px;
            border: 2px solid #3A5860;
            border-radius: 10px;
            background-color: transparent;
            color: #E8DFCB;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }
        .footer-top-btn:hover {
            border-color: #F4BE3F;
            color: #F4BE3F;
            background-color: rgba(244, 190, 63, 0.08);
        }

        /* Keyboard Focus Ring */
        #footer a:focus-visible,
        #footer button:focus-visible {
            outline: 3px solid #F4BE3F !important;
            outline-offset: 3px !important;
        }

        /* Prefers reduced motion */
        @media (prefers-reduced-motion: reduce) {
            #footer *,
            #footer *::before,
            #footer *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    <div class="footer-container">
        {{-- Main Grid: Brand | Jelajahi | Bantuan & Info | Pembayaran --}}
        <div class="footer-grid-layout">

            {{-- Kolom 1: Brand Logo & Deskripsi Singkat --}}
            <div class="flex flex-col">
                <a href="/" wire:navigate.hover class="inline-flex items-center gap-3 w-max mb-3" aria-label="Beranda Velosha">
                    <img src="/images/logo-velosha.png" alt="Velosha Logo" class="h-10 w-auto object-contain brightness-0 invert opacity-95 hover:opacity-100 transition-opacity" />
                    <span class="font-bricolage text-2xl font-extrabold text-[#FFFDF8] tracking-tight">Velosha</span>
                </a>

                {{-- Deskripsi Singkat di Bawah Logo --}}
                <p style="color: #FFFFFF !important; font-size: 14.5px !important; line-height: 1.6 !important; max-width: 290px; margin-bottom: 24px; opacity: 1 !important; display: block !important;">
                    Platform resmi pendaftaran dan kurasi maraton sepeda endurance, Gran Fondo, dan cycling experience di Indonesia.
                </p>

                <div>
                    <h3 class="text-xs font-bold uppercase tracking-[0.1em] text-[#F4BE3F] mb-3">
                        IKUTI KOMUNITAS KAMI
                    </h3>
                    <div class="flex items-center gap-3">
                        <a href="https://strava.com" target="_blank" rel="noopener noreferrer"
                           class="footer-social-btn"
                           aria-label="Ikuti Komunitas Velosha di Strava">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M15.387 17.944l-2.089-4.116h-3.065L15.387 24l5.15-10.172h-3.066m-7.008-5.599l2.836 5.598h4.172L10.463 0l-7.925 15.771h4.172"/></svg>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"
                           class="footer-social-btn"
                           aria-label="Ikuti Komunitas Velosha di Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <a href="https://youtube.com" target="_blank" rel="noopener noreferrer"
                           class="footer-social-btn"
                           aria-label="Ikuti Komunitas Velosha di YouTube">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a href="https://x.com" target="_blank" rel="noopener noreferrer"
                           class="footer-social-btn"
                           aria-label="Ikuti Komunitas Velosha di X Twitter">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Wrapper untuk Navigasi Tengah di Mobile --}}
            <div class="footer-links-row contents md:contents">
                {{-- Kolom 2: Jelajahi --}}
                <div class="flex flex-col">
                    <h3 class="footer-col-heading">Jelajahi</h3>
                    <ul class="flex flex-col space-y-1">
                        <li>
                            <a href="/events" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Kalender balap</span>
                            </a>
                        </li>
                        <li>
                            <a href="/events?type=gran-fondo" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Gran Fondo</span>
                            </a>
                        </li>
                        <li>
                            <a href="/events?type=medio-fondo" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Medio Fondo</span>
                            </a>
                        </li>
                        <li>
                            <a href="/events?type=fun-ride" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Fun Ride</span>
                            </a>
                        </li>
                        <li>
                            <a href="/events?type=climb" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>KOM Climb</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Kolom 3: Bantuan & info --}}
                <div class="flex flex-col">
                    <h3 class="footer-col-heading">Bantuan & info</h3>
                    <ul class="flex flex-col space-y-1">
                        <li>
                            <a href="/help" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Pusat bantuan</span>
                            </a>
                        </li>
                        <li>
                            <a href="/rule" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Aturan & kode etik</span>
                            </a>
                        </li>
                        <li>
                            <a href="/about" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Tentang Velosha</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $portalTarget }}" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Cek e-ticket</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $portalTarget }}" wire:navigate.hover class="footer-link">
                                <span class="footer-link-chevron">&rsaquo;</span>
                                <span>Portal peserta</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Kolom 4: Pembayaran (Disimpan di Samping Bantuan & Info) --}}
            <div class="flex flex-col">
                <h3 class="footer-col-heading">Pembayaran</h3>
                <p class="text-xs text-[#E8DFCB] opacity-80 leading-relaxed mb-3">
                    Verifikasi instan, nomor BIB langsung terbit:
                </p>
                <div class="footer-pay-chips-grid">
                    <div class="footer-pay-chip">QRIS Instant</div>
                    <div class="footer-pay-chip">BCA Virtual</div>
                    <div class="footer-pay-chip">Mandiri VA</div>
                    <div class="footer-pay-chip">Bank BRI</div>
                    <div class="footer-pay-chip">Bank BNI</div>
                    <div class="footer-pay-chip">Visa / Master</div>
                </div>
            </div>

        </div>

        {{-- Bottom Subfooter Bar --}}
        <div class="footer-bottom-bar">
            <p class="font-normal opacity-85">
                {{ $footer->copyright ?? '© 2026 Velosha. Seluruh hak cipta dilindungi.' }}
            </p>

            <div class="flex flex-wrap items-center gap-6">
                <a href="/rule" wire:navigate.hover class="text-inherit hover:text-[#F4BE3F] transition-colors">Privasi</a>
                <a href="/rule" wire:navigate.hover class="text-inherit hover:text-[#F4BE3F] transition-colors">Ketentuan</a>

                <button type="button"
                        onclick="window.scrollTo({ top: 0, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' })"
                        class="footer-top-btn"
                        aria-label="Kembali ke atas">
                    <span>Ke atas</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 15-6-6-6 6"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</footer>
