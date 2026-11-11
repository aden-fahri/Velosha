<div class="bg-[var(--paper)] min-h-[calc(100vh-140px)] py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center"
     wire:poll.3s="processQueue">

    <div class="max-w-lg w-full bg-[var(--card)] border-[3px] border-[var(--ink)] rounded-2xl p-7 sm:p-9 shadow-[6px_6px_0_var(--ink)] text-center space-y-6">

        @if ($isSoldOut)
            {{-- Status: Sold Out --}}
            <div class="w-16 h-16 mx-auto rounded-2xl bg-[var(--red-l)] border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] flex items-center justify-center text-[var(--red)]">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                </svg>
            </div>

            <div class="space-y-2">
                <span class="inline-block px-3 py-1 bg-[var(--red-l)] border-2 border-[var(--ink)] text-[var(--red)] font-black text-xs uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)]">
                    Tiket Terjual Habis
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-[var(--ink)] tracking-tight" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Kuota Resmi Penuh
                </h2>
                <p class="text-xs sm:text-sm text-[var(--mute)] font-medium leading-relaxed max-w-sm mx-auto">
                    Seluruh kuota untuk kategori <strong>{{ $category?->name }}</strong> pada event <strong>{{ $event?->title }}</strong> telah dipesan dan diselesaikan oleh peserta lain.
                </p>
            </div>

            <div class="pt-2">
                <a href="{{ route('events.show', ['slug' => $eventSlug]) }}"
                   class="inline-flex items-center justify-center gap-2 w-full bg-[var(--paper)] hover:bg-white text-[var(--ink)] border-2 border-[var(--ink)] font-black py-3.5 text-xs rounded-xl shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>Kembali ke Detail Race</span>
                </a>
            </div>

        @elseif ($slotSecured)
            {{-- Status: Slot Berhasil Didapat (Redirecting) --}}
            <div class="w-16 h-16 mx-auto rounded-2xl bg-[var(--pine-l)] border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] flex items-center justify-center text-[var(--pine)] animate-bounce">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <div class="space-y-2">
                <span class="inline-block px-3 py-1 bg-[var(--pine-l)] border-2 border-[var(--ink)] text-[var(--pine)] font-black text-xs uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)]">
                    Giliran Anda Tiba!
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-[var(--ink)] tracking-tight" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Slot Berhasil Diamankan
                </h2>
                <p class="text-xs sm:text-sm text-[var(--mute)] font-medium">
                    Mengalihkan Anda ke formulir pendaftaran resmi dalam sekejap...
                </p>
            </div>

            <div class="w-8 h-8 border-4 border-[var(--ink)] border-t-[var(--pine)] rounded-full animate-spin mx-auto"></div>

        @else
            {{-- Status: Sedang Mengantre di Ruang Tunggu --}}
            <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-[var(--ink)]/20 border-t-[var(--orange)] animate-spin"></div>
                <div class="w-12 h-12 bg-[var(--butter)] text-[var(--ink)] border-2 border-[var(--ink)] rounded-xl shadow-[2px_2px_0_var(--ink)] flex items-center justify-center font-bold text-lg">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>

            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-[var(--mustard)] border-2 border-[var(--ink)] text-[var(--ink)] font-black text-xs uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)]">
                    <span class="w-2 h-2 rounded-full bg-[var(--orange)] animate-ping"></span>
                    <span>Ruang Tunggu Antrean Resmi</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-[var(--ink)] tracking-tight pt-1" style="font-family: 'Bricolage Grotesque', sans-serif">
                    {{ $event?->title }}
                </h1>
                <div class="inline-block px-3 py-1 bg-[var(--paper)] border-2 border-[var(--ink)] rounded-lg text-xs font-bold text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                    Kategori: <span class="font-black text-[var(--orange)]">{{ $category?->name }}</span>
                </div>
            </div>

            <!-- Posisi Antrean Box -->
            <div class="bg-[var(--paper2)] border-2 border-[var(--ink)] rounded-xl p-5 shadow-[4px_4px_0_var(--ink)] space-y-2">
                <div class="text-[11px] font-black uppercase tracking-wider text-[var(--mute)]">
                    Posisi Antrean Anda
                </div>
                <div class="text-5xl font-black font-mono text-[var(--orange)] tracking-tight">
                    #{{ $queuePosition }}
                </div>
                <div class="text-xs text-[var(--ink)] font-bold flex items-center justify-center gap-1.5 pt-1">
                    <svg class="w-4 h-4 text-[var(--mute)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Estimasi waktu tunggu: <strong>&plusmn; {{ $estimatedWaitMinutes }} Menit</strong></span>
                </div>
            </div>

            <div class="text-xs text-[var(--ink)] font-medium leading-relaxed text-left bg-[var(--butter)] border-2 border-[var(--ink)] rounded-xl p-4 shadow-[2px_2px_0_var(--ink)] space-y-1">
                <div class="font-black text-[var(--ink)] flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[var(--orange)] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span>Mohon jangan menutup atau me-refresh halaman ini</span>
                </div>
                <p class="text-[11px] text-[var(--ink)]/80">
                    Saat peserta di depan Anda selesai checkout atau melepas slotnya (maks 15 menit), giliran Anda otomatis aktif dan berpindah langsung ke formulir pendaftaran.
                </p>
            </div>

            <!-- Tombol Keluar Antrean -->
            <div class="pt-2">
                <button type="button"
                        wire:click="leaveQueue"
                        wire:confirm="Yakin ingin keluar dari antrean? Nomor urut antrean Anda akan dilepas dan slot diberikan ke peserta berikutnya."
                        class="text-xs text-[var(--mute)] hover:text-[var(--red)] font-bold transition inline-flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Tinggalkan Antrean Balap</span>
                </button>
            </div>
        @endif

    </div>

</div>
