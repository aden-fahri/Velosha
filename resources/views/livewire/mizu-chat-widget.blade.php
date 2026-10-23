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
     class="fixed bottom-6 right-6 z-50 flex flex-col items-end w-fit">

    {{-- Panel obrolan: absolute agar tidak pernah menggeser tombol pemicu --}}
    <div x-cloak x-show="open"
         x-transition:enter="transition-all ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition-all ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         style="transform-origin: bottom right"
         class="absolute bottom-full right-0 mb-3 w-[calc(100vw-3rem)] max-w-sm bg-white border border-veloborderstrong velo-box-1 shadow-xl flex flex-col max-h-[70vh] sm:max-h-[32rem]"
         role="dialog" aria-label="Asisten Mizu">

        {{-- Header --}}
        <div class="bg-[#0A353E] text-white px-4 py-3 flex items-center justify-between border-b border-white/10 shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 bg-white/10 border border-white/15 flex items-center justify-center velo-box-1 shrink-0">
                    <img src="/images/logo-velosha.png" alt="" class="h-5 w-auto object-contain invert">
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold leading-tight">Tanya Mizu</div>
                    <div class="text-[10px] text-teal-100/70 leading-tight truncate">Asisten Balap Velosha</div>
                </div>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                @if (count($messages) > 0)
                <button type="button" wire:click="resetChat" title="Mulai ulang obrolan"
                        class="text-white/60 hover:text-white p-1 transition" aria-label="Mulai ulang obrolan">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </button>
                @endif
                <button type="button" @click="open = false" title="Tutup"
                        class="text-white/60 hover:text-white p-1 transition" aria-label="Tutup asisten">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        {{-- Area pesan --}}
        <div x-ref="scroller" class="flex-1 overflow-y-auto p-3.5 space-y-3 bg-velosurfacealt" aria-live="polite">

            @if ($notice !== '')
            <div class="text-[10px] text-center text-race-800 bg-race-50 border border-race-300 velo-box-3 px-2.5 py-1.5">
                {{ $notice }}
            </div>
            @endif

            {{-- Sapaan pembuka --}}
            <div class="flex justify-start">
                <div class="max-w-[85%] bg-white border border-veloborder p-2.5 velo-box-3 text-xs text-teal-800 leading-relaxed">
                    Halo! Aku Mizu, asisten Velosha. Tanya saja soal pendaftaran, Race Pack, regulasi, atau istilah balap yang bikin bingung.
                </div>
            </div>

            {{-- Riwayat percakapan --}}
            @foreach ($messages as $message)
                @if ($message['role'] === 'user')
                <div class="flex justify-end">
                    <div class="max-w-[85%] bg-[#DE7119] text-[var(--ink)] p-2.5 velo-box-3 text-xs leading-relaxed">
                        {{ $message['text'] }}
                    </div>
                </div>
                @else
                <div class="flex justify-start gap-2">
                    <div class="w-6 h-6 bg-[#0A353E] flex items-center justify-center velo-box-3 shrink-0 mt-0.5">
                        <img src="/images/logo-velosha.png" alt="" class="h-3.5 w-auto object-contain invert">
                    </div>
                    <div class="max-w-[85%] bg-white border border-veloborder p-2.5 velo-box-3 text-xs text-teal-800 leading-relaxed whitespace-pre-line">
                        {{ $message['text'] }}
                    </div>
                </div>
                @endif
            @endforeach

            {{-- Indikator Mizu sedang menjawab --}}
            <div wire:loading wire:target="ask,askQuick" class="flex justify-start gap-2">
                <div class="w-6 h-6 bg-[#0A353E] flex items-center justify-center velo-box-3 shrink-0 mt-0.5">
                    <img src="/images/logo-velosha.png" alt="" class="h-3.5 w-auto object-contain invert">
                </div>
                <div class="bg-white border border-veloborder px-3 py-2.5 velo-box-3 flex items-center gap-1" role="status">
                    <span class="w-1.5 h-1.5 rounded-full bg-velomutedlight animate-pulse"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-velomutedlight animate-pulse" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-velomutedlight animate-pulse" style="animation-delay:300ms"></span>
                    <span class="sr-only">Mizu sedang menjawab</span>
                </div>
            </div>
        </div>

        {{-- Quick prompt --}}
        @if (count($messages) === 0)
        <div class="px-3.5 pt-3 shrink-0">
            <div class="text-[10px] font-bold uppercase tracking-wider text-velomutedlight mb-2">Pertanyaan umum</div>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($quickPrompts as $prompt)
                <button type="button" wire:click="askQuick(@js($prompt))"
                        class="text-left text-[10px] font-semibold bg-white border border-veloborderstrong text-teal-800 hover:border-race-500 hover:text-race-700 px-2.5 py-1.5 velo-box-3 transition">
                    {{ $prompt }}
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Input --}}
        <form wire:submit="ask" class="p-2.5 flex items-center gap-2 bg-white border-t border-veloborder shrink-0">
            <label for="mizu-question" class="sr-only">Tulis pertanyaan untuk Mizu</label>
            <input id="mizu-question" type="text" wire:model="question" maxlength="500"
                   placeholder="Tanya seputar balapan…"
                   class="flex-grow px-3 py-2 text-xs border border-veloborderstrong bg-white velo-box-1 focus:outline-none focus:border-teal-600">
            <button type="submit" wire:loading.attr="disabled" wire:target="ask"
                    class="bg-teal-700 hover:bg-teal-800 text-white px-3.5 py-2 velo-box-1 disabled:opacity-50 transition shrink-0"
                    aria-label="Kirim pertanyaan">
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    {{-- Tombol pemicu: posisi & lebar terkunci (label tetap, hanya ikon yang berubah) --}}
    <button type="button" @click="open = !open; if (open) $dispatch('mizu-opened')"
            :aria-expanded="open.toString()"
            aria-label="Tanya Mizu"
            class="flex items-center gap-2 bg-teal-900 hover:bg-teal-800 text-white px-4 py-3 velo-box-1 shadow-lg transition hover:-translate-y-0.5 whitespace-nowrap">
        <i x-show="!open" data-lucide="bot" class="w-4 h-4"></i>
        <i x-show="open" data-lucide="x" class="w-4 h-4" x-cloak></i>
        <span>Tanya Mizu</span>
    </button>
</div>