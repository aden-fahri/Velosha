{{-- Peta sirkuit + legenda pos hidrasi (aid stations) --}}
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="font-heading font-black text-2xl text-[var(--ink)] tracking-tight">Peta Sirkuit &amp; Pos Hidrasi</h2>
    </div>

    <div class="border-[2.5px] border-[var(--ink)] rounded-2xl shadow-[4px_4px_0_var(--ink)] overflow-hidden bg-white">
        @if($event->map_image_path)
            <div class="h-96 w-full bg-[var(--paper)]">
                <img src="{{ asset('storage/' . ltrim($event->map_image_path, '/')) }}"
                     alt="Peta sirkuit {{ $event->title }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            </div>
        @else
            <div class="h-80 w-full bg-[#FFFDF8] flex flex-col items-center justify-center text-center p-8">
                <div class="w-16 h-16 rounded-2xl bg-[var(--butter)] border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-[var(--ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon>
                        <line x1="9" y1="3" x2="9" y2="18"></line>
                        <line x1="15" y1="6" x2="15" y2="21"></line>
                    </svg>
                </div>
                <p class="font-heading font-black text-base text-[var(--ink)]">Peta rute belum tersedia</p>
                <p class="text-xs text-[var(--mute)] max-w-sm mt-1.5 font-medium leading-relaxed">
                    Panitia penyelenggara belum mengunggah berkas peta sirkuit teknis resmi untuk event ini.
                </p>
            </div>
        @endif
    </div>
</div>
