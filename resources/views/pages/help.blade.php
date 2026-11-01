@extends('layouts.app', ['title' => 'Pusat Bantuan — Velosha'])

@section('content')

@php
$content = \App\Models\HelpContent::current();
$allFaqGroups = $content->faq_groups ?? [];

// Pisahkan FAQ groups biasa dan Glossary "Istilah Sepeda"
$faqGroups = array_values(array_filter($allFaqGroups, fn($g) => ($g['title'] ?? '') !== 'Istilah Sepeda'));
$glossaryGroup = collect($allFaqGroups)->firstWhere('title', 'Istilah Sepeda');
$glossaryItems = $glossaryGroup['items'] ?? [];

$contacts = $content->contacts ?? [];
$hasRiderImage = file_exists(public_path('images/art/rider-standing.png'));
@endphp

<style>
/* CSS Tokens & Custom Scoped Styles */
.help-page-wrap {
    background-color: #FBF4E6;
    color: #10282E;
    font-family: 'Inter', sans-serif;
    padding-bottom: 96px;
    min-height: 100vh;
}

/* Headings font */
.font-bricolage {
    font-family: 'Bricolage Grotesque', sans-serif;
    font-optical-sizing: auto;
}

/* Keyboard Focus ring per ground rules */
.help-page-wrap a:focus-visible,
.help-page-wrap button:focus-visible,
.help-page-wrap input:focus-visible,
.help-page-wrap summary:focus-visible,
.help-page-wrap details:focus-visible {
    outline: 3px solid #F4BE3F !important;
    outline-offset: 3px !important;
}

/* Prefers reduced motion */
@media (prefers-reduced-motion: reduce) {
    .help-page-wrap *,
    .help-page-wrap *::before,
    .help-page-wrap *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}

/* Section Spacing: 56px desktop / 40px mobile */
.help-section {
    margin-top: 56px;
}
@media (max-width: 639.98px) {
    .help-section {
        margin-top: 40px;
    }
}

/* Contact Cards Grid */
.contact-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
}
.contact-card-item {
    background: #FFFDF8;
    border: 2.5px solid #10282E;
    border-radius: 14px;
    box-shadow: 4px 4px 0 #10282E;
    padding: 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-decoration: none;
    color: #10282E;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    min-height: 44px;
}
.contact-card-item:hover {
    transform: translate(-2px, -2px);
    box-shadow: 6px 6px 0 #10282E;
}

@media (max-width: 639.98px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    .contact-card-item {
        flex-direction: row;
        align-items: center;
        gap: 14px;
        min-height: 72px;
        padding: 14px 16px;
    }
}

/* FAQ Groups Grid: Desktop >= 900px 2 columns, tablet/mobile 1 col */
.faq-grid-2col {
    display: grid;
    grid-template-columns: 1fr;
    gap: 32px;
}
@media (min-width: 900px) {
    .faq-grid-2col {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 32px;
    }
}

/* Accordion details / summary */
.faq-details {
    background: #FFFDF8;
    border: 2.5px solid #10282E;
    border-radius: 14px;
    box-shadow: 4px 4px 0 #10282E;
    margin-bottom: 12px;
    overflow: hidden;
    transition: box-shadow 0.15s ease;
}
.faq-summary {
    min-height: 56px;
    padding: 14px 16px;
    font-size: 16px;
    line-height: 1.4;
    font-weight: 700;
    color: #10282E;
    background: #FFFDF8;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    cursor: pointer;
    list-style: none;
    user-select: none;
}
.faq-summary::-webkit-details-marker {
    display: none;
}
.faq-chevron {
    width: 18px;
    height: 18px;
    flex: none;
    transition: transform 0.2s ease;
}
.faq-details[open] .faq-chevron {
    transform: rotate(180deg);
}
.faq-answer {
    padding: 0 16px 16px 16px;
    font-size: 15px;
    line-height: 1.6;
    color: #10282E;
}

/* Final Banner */
.final-banner {
    background-color: #DE7119;
    border: 3px solid #10282E;
    border-radius: 22px;
    box-shadow: 8px 8px 0 #10282E;
    padding: 32px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}
@media (max-width: 639.98px) {
    .final-banner {
        flex-direction: column;
        align-items: flex-start;
        padding: 24px;
        gap: 20px;
    }
}
</style>

<div class="help-page-wrap"
     x-data="{
        query: '',
        showAllGlossary: false,
        totalFaqCount: {{ count($allFaqGroups) }},
        matches(text) {
            if (!this.query || !this.query.trim()) return true;
            return text.toLowerCase().includes(this.query.toLowerCase().trim());
        },
        hasMatchInGroup(items) {
            if (!this.query || !this.query.trim()) return true;
            return items.some(item => this.matches(item.q) || this.matches(item.a));
        },
        isFirstMatchInGroup(items, currentIndex) {
            if (!this.query || !this.query.trim()) return false;
            const firstIdx = items.findIndex(item => this.matches(item.q) || this.matches(item.a));
            return firstIdx === currentIndex;
        },
        hasAnyGlobalMatch(allGroups) {
            if (!this.query || !this.query.trim()) return true;
            for (let g of allGroups) {
                if (g.items && g.items.some(i => this.matches(i.q) || this.matches(i.a))) {
                    return true;
                }
            }
            return false;
        }
     }">

    <!-- 1. PAGE HEADER -->
    <header class="relative overflow-hidden" style="background-color: #F8E3B5; border-bottom: 3.5px solid #10282E; padding: 48px 0 44px 0;">
        {{-- Neobrutalist Geometric Background Ornaments --}}
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

        <div class="wrap relative z-10">
            <div style="max-width: 720px; width: 100%;">
                <h1 class="font-bricolage text-3xl sm:text-4xl md:text-5xl font-extrabold text-[#10282E] tracking-tight leading-none mb-3">
                    {{ $content->header_title ?? 'Help Center' }}
                </h1>
                <p class="text-base sm:text-lg text-[#10282E] opacity-90 leading-relaxed mb-6">
                    {{ $content->header_description ?? 'Semua yang perlu kamu tahu soal registrasi, pembayaran, pengambilan race pack, dan persiapan hari-H. Kalau jawabannya tidak ada di sini, hubungi WhatsApp kami.' }}
                </p>

                <!-- D. SEARCH BAR -->
                <div class="relative w-full max-w-xl">
                    {{-- Search Icon Inside Left --}}
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-4 text-[#10282E]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </div>

                    {{-- Search Input --}}
                    <input type="text"
                           x-model="query"
                           @keydown.escape="query = ''"
                           placeholder="Cari pertanyaan…"
                           aria-label="Cari pertanyaan"
                           style="height: 56px; border-radius: 14px; border: 2.5px solid #10282E; padding-left: 46px; padding-right: 48px; font-size: 16px; background-color: #FFFDF8; color: #10282E; box-shadow: 4px 4px 0 #10282E;"
                           class="w-full focus:outline-none placeholder:text-stone-500 font-medium">

                    {{-- Clear Button --}}
                    <button type="button"
                            x-show="query.length > 0"
                            x-cloak
                            @click="query = ''"
                            aria-label="Hapus pencarian"
                            style="width: 44px; height: 44px; right: 6px; top: 6px;"
                            class="absolute flex items-center justify-center text-lg font-bold text-[#10282E] rounded-xl hover:bg-[#F8E3B5] transition-colors">
                        &times;
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="wrap">
        <!-- B. CONTACT CARDS -->
        <section class="help-section" aria-label="Kontak Bantuan">
            <div class="contact-grid">
                @foreach($contacts as $c)
                    @php
                        $iconName = $c['icon'] ?? 'message-circle';
                        $isMizu = ($iconName === 'bot' || str_contains(strtolower($c['name'] ?? ''), 'mizu'));
                        $isEmail = ($iconName === 'mail' || str_contains(strtolower($c['name'] ?? ''), 'email'));
                        $href = $isEmail ? 'mailto:' . ($c['value'] ?? 'cs@velosha.id') : ($isMizu ? 'javascript:void(0)' : 'https://wa.me/' . preg_replace('/[^0-9]/', '', $c['value'] ?? ''));
                    @endphp
                    <a href="{{ $href }}"
                       @if($isMizu) @click.prevent="$dispatch('mizu-opened')" @endif
                       @if(!$isMizu && !$isEmail) target="_blank" rel="noopener noreferrer" @endif
                       class="contact-card-item">
                        <div class="flex items-center gap-4">
                            <div style="width: 44px; height: 44px; background-color: #F8E3B5; border: 2px solid #10282E; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex: none;">
                                @if($iconName === 'message-circle')
                                    <svg class="w-5 h-5 text-[#10282E]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                                @elseif($iconName === 'mail')
                                    <svg class="w-5 h-5 text-[#10282E]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-[#10282E]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>
                                @endif
                            </div>
                            <div>
                                <h2 class="font-bricolage text-base font-bold text-[#10282E] leading-snug">{{ $c['name'] }}</h2>
                                <p class="text-sm text-stone-600 mt-0.5">{{ $c['info'] }}</p>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 text-sm font-bold underline text-[#DE7119] flex items-center justify-between">
                            <span>{{ $c['value'] }}</span>
                            <span class="text-xs">&rarr;</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- NO-MATCH STATE (when search has no matches across all FAQ groups + Glossary) -->
        <div x-cloak
             x-show="query.trim().length > 0 && !hasAnyGlobalMatch({{ json_encode($allFaqGroups) }})"
             class="help-section text-center py-12 px-6 rounded-2xl"
             style="background: #FFFDF8; border: 2.5px solid #10282E; box-shadow: 4px 4px 0 #10282E;">
            <p class="font-bricolage text-xl sm:text-2xl font-bold text-[#10282E] mb-2">
                Tidak ada hasil untuk &ldquo;<span x-text="query"></span>&rdquo;.
            </p>
            <p class="text-base text-stone-600 mb-6 max-w-md mx-auto">
                Pertanyaan yang kamu cari belum terdaftar. Kamu bisa langsung membuat tiket bantuan agar tim kami dapat membantu kendalamu.
            </p>
            <a href="/dashboard/bantuan/buat"
               wire:navigate
               style="height: 52px; background-color: #10282E; color: #FFFDF8; border: 2.5px solid #10282E; border-radius: 14px; box-shadow: 4px 4px 0 #DE7119; padding: 0 28px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 800; font-size: 15px; text-decoration: none;">
                <span>Buat tiket bantuan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>

        <!-- C. FAQ GROUPS (2 columns desktop >= 900px, 1 column mobile/tablet) -->
        <section class="help-section"
                 aria-label="Grup Pertanyaan Umum"
                 x-show="hasAnyGlobalMatch({{ json_encode($allFaqGroups) }})">
            <div class="faq-grid-2col">
                @foreach($faqGroups as $gIndex => $group)
                    @php
                        $groupItems = $group['items'] ?? [];
                        $groupCount = count($groupItems);
                        $groupItemsJson = json_encode($groupItems);
                    @endphp
                    <div x-show="hasMatchInGroup({{ $groupItemsJson }})" class="flex flex-col">
                        {{-- Group Header --}}
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <h2 class="font-bricolage text-2xl font-extrabold text-[#10282E]">
                                {{ $group['title'] }}
                            </h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-md"
                                  style="background: #F8E3B5; color: #10282E; border: 1.5px solid #10282E;">
                                {{ $groupCount }} pertanyaan
                            </span>
                        </div>

                        {{-- Group Items --}}
                        <div class="flex flex-col">
                            @foreach($groupItems as $qIndex => $q)
                                @php
                                    $itemText = addslashes($q['q'] . ' ' . $q['a']);
                                @endphp
                                <details name="faq-group-{{ $gIndex }}"
                                         class="faq-details"
                                         x-show="matches('{{ $itemText }}')"
                                         :open="query.trim().length > 0 && isFirstMatchInGroup({{ $groupItemsJson }}, {{ $qIndex }})">
                                    <summary class="faq-summary">
                                        <span>{{ $q['q'] }}</span>
                                        <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m6 9 6 6 6-6"/>
                                        </svg>
                                    </summary>
                                    <div class="faq-answer">
                                        {{ $q['a'] }}
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- E. GLOSSARY "Istilah Sepeda" -->
        @if($glossaryGroup && count($glossaryItems) > 0)
            @php
                $glossaryCount = count($glossaryItems);
                $glossaryJson = json_encode($glossaryItems);
            @endphp
            <section class="help-section"
                     aria-label="Istilah Sepeda"
                     x-show="hasMatchInGroup({{ $glossaryJson }})">
                {{-- Glossary Header --}}
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div>
                        <h2 class="font-bricolage text-2xl sm:text-3xl font-extrabold text-[#10282E]">
                            {{ $glossaryGroup['title'] }}
                        </h2>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-md"
                          style="background: #F8E3B5; color: #10282E; border: 1.5px solid #10282E;">
                        {{ $glossaryCount }} istilah
                    </span>
                </div>

                {{-- Glossary Items Grid (2 cols desktop, 1 col mobile) --}}
                <div class="faq-grid-2col">
                    @foreach($glossaryItems as $idx => $item)
                        @php
                            $glossText = addslashes($item['q'] . ' ' . $item['a']);
                        @endphp
                        <div x-show="matches('{{ $glossText }}') && (showAllGlossary || query.trim().length > 0 || {{ $idx }} < 6)">
                            <details name="faq-glossary"
                                     class="faq-details"
                                     :open="query.trim().length > 0 && isFirstMatchInGroup({{ $glossaryJson }}, {{ $idx }})">
                                <summary class="faq-summary">
                                    <span>{{ $item['q'] }}</span>
                                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                </summary>
                                <div class="faq-answer">
                                    {{ $item['a'] }}
                                </div>
                            </details>
                        </div>
                    @endforeach
                </div>

                {{-- Expand / Collapse Button (hidden when search query is typed) --}}
                @if($glossaryCount > 6)
                    <div class="mt-6 text-center" x-show="query.trim().length === 0">
                        <button type="button"
                                @click="showAllGlossary = !showAllGlossary"
                                :aria-expanded="showAllGlossary.toString()"
                                style="min-height: 48px; border: 2.5px solid #10282E; border-radius: 14px; background: #FFFDF8; box-shadow: 4px 4px 0 #10282E; padding: 0 24px; font-weight: 800; font-size: 15px; color: #10282E; cursor: pointer;"
                                class="inline-flex items-center gap-2 hover:bg-[#F8E3B5] transition-colors">
                            <span x-text="showAllGlossary ? 'Sembunyikan' : 'Tampilkan semua istilah ({{ $glossaryCount }})'"></span>
                            <svg class="w-4 h-4 transition-transform duration-200"
                                 :class="showAllGlossary ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </section>
        @endif

        <!-- F. FINAL BANNER -->
        <section class="help-section" aria-label="Tiket Bantuan">
            <div class="final-banner">
                <div style="max-width: 640px;">
                    <span class="inline-block px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider mb-2"
                          style="background: #F8E3B5; color: #10282E; border: 1.5px solid #10282E; border-radius: 6px;">
                        Bantuan Langsung
                    </span>
                    <h2 class="font-bricolage text-2xl sm:text-3xl font-extrabold text-[#FFFDF8] leading-tight mb-2">
                        Belum ketemu jawabannya? Buat tiket bantuan
                    </h2>
                    <p class="text-base text-[#FFFDF8] opacity-95 leading-relaxed">
                        Sampaikan kendala pendaftaran, pembayaran, atau nomor BIB. Tim pengelola Velosha akan menangani aduanmu langsung di Portal.
                    </p>
                </div>

                <a href="/dashboard/bantuan/buat"
                   wire:navigate
                   style="height: 52px; background-color: #10282E; color: #FFFDF8; border: 2.5px solid #10282E; border-radius: 14px; box-shadow: 4px 4px 0 #FFFDF8; padding: 0 24px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 800; font-size: 15px; text-decoration: none; flex: none;"
                   class="hover:translate-x-0.5 hover:translate-y-0.5 transition-transform">
                    <span>Buat tiket bantuan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </section>
    </main>
</div>

<!-- SR-ONLY / SEO PRESERVATION -->
<div class="sr-only" aria-hidden="true">
    <h2>{{ $content->cta_title ?? 'Masih ada yang mengganjal?' }}</h2>
    <p>{{ $content->cta_desc ?? 'Tim kami siap membantu setiap hari kerja, 09.00–18.00 WIB.' }}</p>
    <a href="{{ $content->cta_url ?? '/events' }}">{{ $content->cta_label ?? 'Lihat Semua Race' }}</a>
</div>

@endsection
