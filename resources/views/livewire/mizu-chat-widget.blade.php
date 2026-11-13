<div x-data="{
        open: false,
        scrollBottom() {
            this.$nextTick(() => {
                const el = this.$refs.scroller;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },
    }"
     x-init="scrollBottom()"
     @mizu-opened.window="scrollBottom()"
     @mizu-answered.window="scrollBottom()"
     style="position: fixed; right: 24px; bottom: 24px; z-index: 50;"
     class="flex flex-col items-end w-fit">

    <style>
        .mizu-trigger-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: #10282E;
            color: #FFFFFF;
            border: 2.5px solid #10282E;
            border-radius: 9999px;
            box-shadow: 4px 4px 0 #10282E;
            padding: 9px 18px 9px 10px;
            font-family: 'Bricolage Grotesque', 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.15s cubic-bezier(0.2, 0.8, 0.2, 1);
            white-space: nowrap;
        }
        .mizu-trigger-btn:hover {
            background-color: #1F6F4A;
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 #10282E;
        }
        .mizu-trigger-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 #10282E;
        }
        @media (max-width: 639.98px) {
            .mizu-trigger-btn {
                width: 52px !important;
                height: 52px !important;
                border-radius: 9999px !important;
                padding: 0 !important;
                justify-content: center !important;
            }
            .mizu-trigger-btn .mizu-label {
                display: none !important;
            }
        }
    </style>

    {{-- Panel obrolan: absolute agar tidak pernah menggeser tombol pemicu --}}
    <div x-cloak x-show="open"
         x-transition:enter="transition-all ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition-all ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         style="transform-origin: bottom right"
         class="absolute bottom-full right-0 mb-3.5 w-[calc(100vw-2.5rem)] sm:w-[390px] bg-[#FFFDF8] border-[2.5px] border-[#10282E] rounded-2xl shadow-[6px_6px_0_#10282E] flex flex-col max-h-[75vh] sm:max-h-[34rem] overflow-hidden"
         role="dialog" aria-label="Asisten Mizu">

        {{-- Header Neobrutalist --}}
        <div class="bg-[#10282E] text-white px-4 py-3.5 flex items-center justify-between border-b-[2.5px] border-[#10282E] shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-[#F4BE3F] border-2 border-[#10282E] shadow-[2px_2px_0_rgba(0,0,0,0.35)] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#10282E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 8V4H8"/>
                        <rect width="16" height="12" x="4" y="8" rx="2"/>
                        <path d="M2 14h2"/>
                        <path d="M20 14h2"/>
                        <path d="M15 13v2"/>
                        <path d="M9 13v2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="font-['Bricolage_Grotesque',sans-serif] text-sm font-extrabold tracking-tight text-white leading-tight">Tanya Mizu</div>
                    <div class="flex items-center gap-1.5 text-[11px] text-[#F8E3B5] font-semibold leading-tight mt-0.5 truncate">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Asisten Balap Velosha
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                @if (count($messages) > 0)
                <button type="button" wire:click="resetChat" title="Mulai ulang obrolan"
                        class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition cursor-pointer" aria-label="Mulai ulang obrolan">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                </button>
                @endif
                <button type="button" @click="open = false" title="Tutup"
                        class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition cursor-pointer" aria-label="Tutup asisten">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Area pesan --}}
        <div x-ref="scroller" class="flex-1 overflow-y-auto p-4 space-y-3.5 bg-[#FBF4E6]" aria-live="polite">

            @if ($notice !== '')
            <div class="text-[11px] text-center font-bold text-[#10282E] bg-[#F8E3B5] border-2 border-[#10282E] rounded-xl shadow-[2px_2px_0_#10282E] px-3 py-2 leading-snug">
                {{ $notice }}
            </div>
            @endif

            {{-- Sapaan pembuka --}}
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#F4BE3F] border-[2px] border-[#10282E] shadow-[1.5px_1.5px_0_#10282E] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-[#10282E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 8V4H8"/>
                        <rect width="16" height="12" x="4" y="8" rx="2"/>
                        <path d="M2 14h2"/>
                        <path d="M20 14h2"/>
                        <path d="M15 13v2"/>
                        <path d="M9 13v2"/>
                    </svg>
                </div>
                <div class="max-w-[85%] bg-white border-[2px] border-[#10282E] rounded-2xl rounded-tl-sm p-3.5 shadow-[2.5px_2.5px_0_#10282E] text-xs text-[#10282E] font-medium leading-relaxed">
                    Halo! Aku <strong>Mizu</strong>, asisten Velosha 🚴‍♂️. Tanya saja soal pendaftaran, Race Pack, regulasi, atau istilah balap yang bikin bingung.
                </div>
            </div>

            {{-- Riwayat percakapan --}}
            @foreach ($messages as $message)
                @if ($message['role'] === 'user')
                <div class="flex justify-end">
                    <div class="max-w-[85%] bg-[#DE7119] text-white border-[2px] border-[#10282E] rounded-2xl rounded-tr-sm p-3 shadow-[2.5px_2.5px_0_#10282E] text-xs font-semibold leading-relaxed">
                        {{ $message['text'] }}
                    </div>
                </div>
                @else
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-[#F4BE3F] border-[2px] border-[#10282E] shadow-[1.5px_1.5px_0_#10282E] flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#10282E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 8V4H8"/>
                            <rect width="16" height="12" x="4" y="8" rx="2"/>
                            <path d="M2 14h2"/>
                            <path d="M20 14h2"/>
                            <path d="M15 13v2"/>
                            <path d="M9 13v2"/>
                        </svg>
                    </div>
                    <div class="max-w-[85%] bg-white border-[2px] border-[#10282E] rounded-2xl rounded-tl-sm p-3.5 shadow-[2.5px_2.5px_0_#10282E] text-xs text-[#10282E] font-medium leading-relaxed whitespace-pre-line">
                        {{ $message['text'] }}
                    </div>
                </div>
                @endif
            @endforeach

            {{-- Indikator Mizu sedang menjawab --}}
            <div wire:loading wire:target="ask,askQuick" class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#F4BE3F] border-[2px] border-[#10282E] shadow-[1.5px_1.5px_0_#10282E] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-[#10282E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 8V4H8"/>
                        <rect width="16" height="12" x="4" y="8" rx="2"/>
                        <path d="M2 14h2"/>
                        <path d="M20 14h2"/>
                        <path d="M15 13v2"/>
                        <path d="M9 13v2"/>
                    </svg>
                </div>
                <div class="bg-white border-[2px] border-[#10282E] rounded-2xl rounded-tl-sm px-4 py-3 shadow-[2.5px_2.5px_0_#10282E] flex items-center gap-1.5" role="status">
                    <span class="w-2 h-2 rounded-full bg-[#DE7119] animate-pulse"></span>
                    <span class="w-2 h-2 rounded-full bg-[#F4BE3F] animate-pulse" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 rounded-full bg-[#1F6F4A] animate-pulse" style="animation-delay:300ms"></span>
                    <span class="sr-only">Mizu sedang menjawab</span>
                </div>
            </div>
        </div>

        {{-- Quick prompt --}}
        @if (count($messages) === 0)
        <div class="px-4 py-3 bg-[#FFFDF8] border-t-2 border-[#10282E]/10 shrink-0">
            <div class="text-[10px] font-black uppercase tracking-wider text-[#56666A] mb-2 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#DE7119]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                </svg>
                Pertanyaan umum
            </div>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($quickPrompts as $prompt)
                <button type="button" wire:click="askQuick(@js($prompt))"
                        class="text-left text-[11px] font-bold bg-[#FFFDF8] hover:bg-[#F4BE3F] text-[#10282E] border-2 border-[#10282E] rounded-xl px-2.5 py-1.5 shadow-[2px_2px_0_#10282E] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer">
                    {{ $prompt }}
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Input --}}
        <form wire:submit="ask" class="p-3 flex items-center gap-2 bg-[#FFFDF8] border-t-[2.5px] border-[#10282E] shrink-0">
            <label for="mizu-question" class="sr-only">Tulis pertanyaan untuk Mizu</label>
            <input id="mizu-question" type="text" wire:model="question" maxlength="500"
                   placeholder="Tanya seputar balapan…"
                   class="flex-grow px-3.5 py-2.5 text-xs font-semibold border-2 border-[#10282E] rounded-xl bg-white text-[#10282E] placeholder-[#56666A] focus:outline-none focus:ring-2 focus:ring-[#F4BE3F] shadow-[inset_1.5px_1.5px_0_rgba(16,40,46,0.06)]">
            <button type="submit" wire:loading.attr="disabled" wire:target="ask"
                    class="bg-[#1F6F4A] hover:bg-[#18593B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none text-white px-3.5 py-2.5 border-2 border-[#10282E] rounded-xl shadow-[2.5px_2.5px_0_#10282E] disabled:opacity-50 transition-all shrink-0 flex items-center justify-center cursor-pointer"
                    aria-label="Kirim pertanyaan">
                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m22 2-7 20-4-9-9-4Z"/>
                    <path d="M22 2 11 13"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- Tombol pemicu: posisi & lebar terkunci (label tetap, hanya ikon yang berubah) --}}
    <button type="button" @click="open = !open; if (open) $dispatch('mizu-opened')"
            :aria-expanded="open.toString()"
            aria-label="Tanya Mizu"
            class="mizu-trigger-btn">
        <span class="w-7 h-7 rounded-full bg-[#F4BE3F] border-[2px] border-[#10282E] flex items-center justify-center shrink-0">
            <svg x-show="!open" class="w-4 h-4 text-[#10282E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 8V4H8"/>
                <rect width="16" height="12" x="4" y="8" rx="2"/>
                <path d="M2 14h2"/>
                <path d="M20 14h2"/>
                <path d="M15 13v2"/>
                <path d="M9 13v2"/>
            </svg>
            <svg x-show="open" x-cloak class="w-4 h-4 text-[#10282E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 6 6 18"/>
                <path d="m6 6 12 12"/>
            </svg>
        </span>
        <span class="mizu-label" x-show="!open">Tanya Mizu</span>
        <span class="mizu-label" x-show="open" x-cloak>Tutup</span>
    </button>
</div>
