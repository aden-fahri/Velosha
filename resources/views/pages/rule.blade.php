@extends('layouts.app', ['title' => 'Aturan Balap & Kode Etik — Velosha'])

@section('content')

@php
$content = \App\Models\RuleContent::current();
$groups = $content->groups ?? [];
$sanksiItems = $content->sanksi_items ?? [];

// Label styling helper
$getBadgeClass = function($statusOrLabel) {
    $text = strtoupper(trim($statusOrLabel ?? ''));
    if (str_contains($text, 'WAJIB') || $text === 'MANDATORY') return 'rule-badge--wajib';
    if (str_contains($text, 'DILARANG') || $text === 'FORBIDDEN') return 'rule-badge--dilarang';
    if (str_contains($text, 'DIANJURKAN') || str_contains($text, 'DIIZINKAN') || $text === 'ALLOWED') return 'rule-badge--dianjurkan';
    return 'rule-badge--default';
};

$statusLabels = [
    'mandatory' => 'WAJIB',
    'allowed'   => 'DIIZINKAN',
    'forbidden' => 'DILARANG',
    'note'      => 'INFORMASI',
];
@endphp

<style>
/* CSS Tokens & Custom Scoped Styles */
.rules-page-wrap {
    background-color: #FBF4E6;
    color: #10282E;
    font-family: 'Inter', sans-serif;
    padding-bottom: 96px;
    min-height: 100vh;
}

.font-bricolage {
    font-family: 'Bricolage Grotesque', sans-serif;
    font-optical-sizing: auto;
}

/* Keyboard Focus ring */
.rules-page-wrap a:focus-visible,
.rules-page-wrap button:focus-visible,
.rules-page-wrap summary:focus-visible {
    outline: 3px solid #F4BE3F !important;
    outline-offset: 3px !important;
}

/* Prefers reduced motion */
@media (prefers-reduced-motion: reduce) {
    .rules-page-wrap *,
    .rules-page-wrap *::before,
    .rules-page-wrap *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}

/* Sticky Jump Navigation */
.rules-jump-nav-bar {
    position: sticky;
    top: 76px;
    background-color: #FBF4E6;
    border-bottom: 2.5px solid #10282E;
    padding: 10px 0;
    z-index: 30;
}
.rules-jump-track {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.rules-jump-track::-webkit-scrollbar {
    display: none;
}
.rules-jump-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 44px;
    padding: 0 18px;
    border: 2.5px solid #10282E;
    border-radius: 10px;
    background-color: #FFFDF8;
    color: #10282E;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    scroll-snap-align: start;
    flex-shrink: 0;
    transition: background-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
}
.rules-jump-link:hover {
    background-color: #F8E3B5;
    transform: translateY(-1px);
}
.rules-jump-link.active {
    background-color: #10282E !important;
    color: #F8E3B5 !important;
}

/* Category Card */
.rules-category-card {
    background-color: #FFFDF8;
    border: 3px solid #10282E;
    border-radius: 22px;
    box-shadow: 8px 8px 0 #10282E;
    padding: 28px;
    margin-bottom: 40px;
    scroll-margin-top: 140px;
}
@media (max-width: 639.98px) {
    .rules-category-card {
        padding: 16px;
        margin-bottom: 24px;
        border-radius: 14px;
        box-shadow: 4px 4px 0 #10282E;
    }
}

.rules-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-bottom: 16px;
    border-bottom: 2px dashed #10282E;
    margin-bottom: 20px;
}
.rules-badge-circle {
    width: 44px;
    height: 44px;
    background-color: #DE7119;
    color: #FFFDF8;
    border: 2.5px solid #10282E;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Bricolage Grotesque', sans-serif;
    font-size: 18px;
    font-weight: 800;
    flex-shrink: 0;
}

/* Rule Grid */
.rules-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}
@media (min-width: 900px) {
    .rules-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }
}

/* Rule Box */
.rule-box-item {
    background-color: #FBF4E6;
    border: 2.5px solid #10282E;
    border-radius: 12px;
    padding: 16px;
    position: relative;
    display: flex;
    flex-direction: column;
}
.rule-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 26px;
    padding: 0 10px;
    border: 2px solid #10282E;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #10282E;
    flex-shrink: 0;
}
.rule-badge--wajib { background-color: #CFE6D6; }
.rule-badge--dilarang { background-color: #F6CFCF; }
.rule-badge--dianjurkan { background-color: #F4BE3F; }
.rule-badge--default { background-color: #FFFDF8; }
/* Desktop top-right badge */
@media (min-width: 640px) {
    .rule-badge-desktop {
        position: absolute;
        top: 12px;
        right: 12px;
    }
    .rule-title-desktop {
        padding-right: 90px;
    }
}
@media (max-width: 639.98px) {
    .rule-badge-mobile {
        align-self: flex-start;
        margin-bottom: 8px;
    }
}

/* Sanksi Section */
.sanksi-section {
    margin-top: 40px;
    scroll-margin-top: 140px;
}
.sanksi-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    align-items: stretch;
}
@media (min-width: 900px) {
    .sanksi-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }
}
.sanksi-card {
    background-color: #FFFDF8;
    border: 3px solid #10282E;
    border-radius: 18px;
    box-shadow: 6px 6px 0 #10282E;
    padding: 22px;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.sanksi-top-bar {
    height: 36px;
    width: 100%;
    padding: 0 14px;
    border: 2px solid #10282E;
    border-radius: 8px;
    display: flex;
    align-items: center;
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 14px;
    flex-shrink: 0;
}
.sanksi-top-bar--0 { background-color: #1F6F4A; color: #FFFFFF; }
.sanksi-top-bar--1 { background-color: #F4BE3F; color: #10282E; }
.sanksi-top-bar--2 { background-color: #9B2D2D; color: #FFFFFF; }
.sanksi-top-bar--default { background-color: #10282E; color: #FFFFFF; }
</style>

<div class="rules-page-wrap"
     x-data="{
        activeId: '',
        init() {
            const sections = document.querySelectorAll('.js-scroll-target');
            if (!sections.length) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.activeId = entry.target.id;
                    }
                });
            }, {
                rootMargin: '-40% 0px -55% 0px'
            });

            sections.forEach(s => observer.observe(s));
            this.activeId = sections[0].id;
        }
     }">

    <!-- 1. PAGE HEADER (Konsisten dengan Event, About & Bantuan) -->
    <section class="relative overflow-hidden py-10 sm:py-14" style="background: var(--butter, #F8E3B5); border-bottom: 3.5px solid var(--ink, #10282E);">
        {{-- Elemen Dekorasi Geometris Neobrutalism di Sisi Kanan --}}
        <svg aria-hidden="true" style="position:absolute; top:0; right:0; width:520px; max-width:45%; height:100%; z-index:1; pointer-events:none;" viewBox="0 0 520 280">
            <g stroke="#10282E" stroke-width="3" stroke-linejoin="round">
                <circle cx="90" cy="65" r="9" fill="#DE7119"></circle>
                <rect x="160" y="145" width="20" height="20" rx="3" fill="#1F6F4A" transform="rotate(20 170 155)"></rect>
                <path d="M250 215 l12 -20 l12 20z" fill="#F4BE3F"></path>
                <circle cx="230" cy="55" r="7" fill="#9B3D5A"></circle>
                <rect x="320" y="115" width="18" height="18" rx="3" fill="#F4BE3F" transform="rotate(-15 329 124)"></rect>
                <path d="M420 65 l12 -20 l12 20z" fill="#DE7119"></path>
                <circle cx="475" cy="165" r="8" fill="#1F6F4A"></circle>
            </g>
        </svg>

        <div class="wrap relative z-10 px-4 sm:px-6" style="max-width: 1200px;">
            <h1 class="h2" style="font-size: clamp(30px, 6.5vw, 48px) !important; line-height: 1.05 !important; margin-top: 0 !important; max-width: 680px;">
                {{ $content->header_title ?? 'Aturan balap & kode etik' }}
            </h1>
            <p class="lead" style="margin-top: 14px !important; max-width: 640px; font-size: clamp(15px, 3.5vw, 18px);">
                {{ $content->header_description ?? 'Standar keselamatan, perlengkapan, etika peloton, dan tata tertib resmi musim balap Velosha.' }}
            </p>
        </div>
    </section>

    <!-- 2. JUMP NAVIGATION (STICKY BAR) -->
    <nav class="rules-jump-nav-bar" aria-label="Navigasi Kategori Regulasi">
        <div class="wrap" style="max-width: 1200px;">
            <div class="rules-jump-track">
                @foreach($groups as $idx => $g)
                    @php $slug = \Illuminate\Support\Str::slug($g['title'] ?? 'kategori-' . $idx); @endphp
                    <a href="#{{ $slug }}"
                       class="rules-jump-link"
                       :class="{ 'active': activeId === '{{ $slug }}' }">
                        {{ $g['title'] }}
                    </a>
                @endforeach
                <a href="#sanksi"
                   class="rules-jump-link"
                   :class="{ 'active': activeId === 'sanksi' }">
                    Sanksi
                </a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="wrap" style="max-width: 1200px; padding-top: 40px;">
        <!-- C. FOUR CATEGORY CARDS -->
        @foreach($groups as $idx => $g)
            @php
                $slug = \Illuminate\Support\Str::slug($g['title'] ?? 'kategori-' . $idx);
                $badgeLetter = chr(65 + $idx); // A, B, C, D
            @endphp
            <section id="{{ $slug }}" class="rules-category-card js-scroll-target" aria-labelledby="heading-{{ $slug }}">
                <!-- Card Header -->
                <div class="rules-card-header">
                    <div class="rules-badge-circle">
                        {{ $badgeLetter }}
                    </div>
                    <div>
                        <h2 id="heading-{{ $slug }}" class="font-bricolage text-xl sm:text-2xl font-extrabold text-[#10282E] leading-tight">
                            {{ $g['title'] }}
                        </h2>
                        <p class="text-sm font-medium mt-0.5" style="color: #4B6268;">
                            Ketentuan teknis dan keselamatan kategori ini.
                        </p>
                    </div>
                </div>

                <!-- Rule Items Grid (desktop >= 900px: 2 cols, below 900px: 1 col) -->
                <div class="rules-grid">
                    @foreach($g['items'] ?? [] as $item)
                        @php
                            $rawStatus = $item['status'] ?? 'note';
                            $labelDisplay = $statusLabels[$rawStatus] ?? strtoupper($rawStatus);
                            $badgeClass = $getBadgeClass($labelDisplay);
                        @endphp
                        <article class="rule-box-item">
                            {{-- Mobile Badge (Above Title) --}}
                            <div class="block sm:hidden">
                                <span class="rule-badge rule-badge-mobile {{ $badgeClass }}">
                                    {{ $labelDisplay }}
                                </span>
                            </div>

                            <div class="flex items-start justify-between">
                                <h3 class="font-bricolage text-base font-bold text-[#10282E] leading-snug rule-title-desktop">
                                    {{ $item['label'] }}
                                </h3>

                                {{-- Desktop Badge (Top Right Absolute) --}}
                                <span class="hidden sm:inline-flex rule-badge rule-badge-desktop {{ $badgeClass }}">
                                    {{ $labelDisplay }}
                                </span>
                            </div>

                            <p class="text-[15px] text-[#10282E] leading-relaxed mt-2 flex-grow">
                                {{ $item['text'] }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <!-- D. SANKSI SECTION -->
        <section id="sanksi" class="sanksi-section js-scroll-target" aria-labelledby="heading-sanksi">
            <div class="mb-6">
                <h2 id="heading-sanksi" class="font-bricolage text-2xl sm:text-3xl md:text-[32px] font-extrabold text-[#10282E] leading-tight mb-2">
                    Sanksi & diskualifikasi
                </h2>
                <p class="text-base text-[#10282E] opacity-90 leading-relaxed max-w-2xl font-medium">
                    Penegakan aturan demi sportivitas dan keselamatan seluruh rider.
                </p>
            </div>

            <div class="sanksi-grid">
                @foreach($sanksiItems as $sIdx => $s)
                    <div class="sanksi-card">
                        <div class="sanksi-top-bar sanksi-top-bar--{{ $sIdx }}">
                            {{ $s['title'] }}
                        </div>
                        <p class="text-[15px] text-[#10282E] leading-relaxed flex-grow">
                            {{ $s['text'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            @if(!empty($content->sanksi_note))
                <div class="mt-6 p-4 sm:p-5 rounded-xl flex items-start gap-3"
                     style="background: #F4BE3F; border: 2.5px solid #10282E; box-shadow: 4px 4px 0 #10282E;">
                    <svg class="w-5 h-5 flex-none text-[#10282E] mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <p class="text-sm sm:text-base leading-relaxed text-[#10282E]">
                        <strong>Catatan Keputusan Commissaire:</strong> {{ $content->sanksi_note }}
                    </p>
                </div>
            @endif
        </section>
    </main>
</div>

@endsection
