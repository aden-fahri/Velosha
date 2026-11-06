<div class="bg-velosurfacealt min-h-[calc(100vh-140px)] py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center"
     wire:poll.3s="processQueue">

    <div class="max-w-md w-full bg-white border border-veloborderstrong velo-box-1 p-6 sm:p-8 shadow-xl text-center space-y-6">

        @if ($isSoldOut)
            {{-- Status: Sold Out --}}
            <div class="w-16 h-16 mx-auto bg-red-100 text-red-600 velo-box-1 flex items-center justify-center">
                <i data-lucide="slash" class="w-8 h-8"></i>
            </div>

            <div class="space-y-2">
                <span class="text-[10px] uppercase font-bold tracking-widest text-red-600 bg-red-50 border border-red-200 px-2.5 py-1 velo-box-3">
                    Tiket Terjual Habis
                </span>
                <h2 class="text-xl font-black text-teal-900 mt-2">
                    Kuota Resmi Penuh
                </h2>
                <p class="text-xs text-velomuted leading-relaxed">
                    Seluruh kuota untuk kategori <strong>{{ $category?->name }}</strong> pada <strong>{{ $event?->title }}</strong> telah berhasil dipesan dan dibayar oleh peserta lain.
                </p>
            </div>

            <div class="pt-4 border-t border-veloborder">
                <a href="{{ route('events.show', ['slug' => $eventSlug]) }}"
                   class="inline-flex items-center justify-center w-full bg-white hover:bg-velosurfacealt text-teal-900 border border-veloborder font-bold py-3 text-xs velo-box-1 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    <span>Kembali ke Detail Race</span>
                </a>
            </div>

        @elseif ($slotSecured)
            {{-- Status: Slot Berhasil Didapat (Redirecting) --}}
            <div class="w-16 h-16 mx-auto bg-emerald-100 text-emerald-600 velo-box-1 flex items-center justify-center animate-bounce">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
            </div>

            <div class="space-y-2">
                <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 velo-box-3">
                    Giliran Anda Tiba!
                </span>
                <h2 class="text-xl font-black text-teal-900 mt-2">
                    Slot Berhasil Diamankan
                </h2>
                <p class="text-xs text-velomuted">
                    Mengalihkan Anda ke formulir pendaftaran resmi dalam sekejap...
                </p>
            </div>

        @else
            {{-- Status: Sedang Mengantre di Ruang Tunggu --}}
            <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-race-200 border-t-race-600 animate-spin"></div>
                <div class="w-12 h-12 bg-race-50 text-race-600 velo-box-1 flex items-center justify-center font-bold text-lg">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 text-[10px] uppercase font-extrabold tracking-wider text-race-700 bg-race-50 border border-race-200 px-2.5 py-1 velo-box-3">
                    <span class="w-2 h-2 rounded-full bg-race-500 animate-ping"></span>
                    <span>Ruang Tunggu Antrean Resmi</span>
                </div>
                <h2 class="text-lg font-black text-teal-900 pt-2">
                    {{ $event?->title }}
                </h2>
                <div class="text-xs font-semibold text-velomuted">
                    Kategori: <span class="text-teal-800">{{ $category?->name }}</span>
                </div>
            </div>

            <!-- Posisi Antrean Box -->
            <div class="bg-velosurfacealt border border-veloborder p-5 velo-box-1 space-y-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-velomutedlight">
                    Posisi Antrean Anda
                </div>
                <div class="text-4xl font-black font-mono text-race-600 tracking-tight">
                    #{{ $queuePosition }}
                </div>
                <div class="text-xs text-velomuted flex items-center justify-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-velomutedlight"></i>
                    <span>Estimasi waktu tunggu: <strong>± {{ $estimatedWaitMinutes }} Menit</strong></span>
                </div>
            </div>

            <div class="text-[11px] text-velomutedlight leading-relaxed text-left bg-blue-50/60 border border-blue-200/60 p-3.5 velo-box-3 space-y-1">
                <div class="font-bold text-blue-900 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-blue-600 shrink-0"></i>
                    <span>Mohon jangan menutup halaman ini</span>
                </div>
                <p>
                    Saat peserta di depan Anda selesai atau melepas slotnya (maks 15 menit), giliran Anda akan langsung aktif dan dialihkan otomatis ke halaman formulir.
                </p>
            </div>

            <!-- Tombol Keluar Antrean -->
            <div class="pt-2">
                <button type="button"
                        wire:click="leaveQueue"
                        wire:confirm="Yakin ingin keluar dari antrean? Nomor urut antrean Anda akan dilepas."
                        class="text-xs text-velomuted hover:text-red-600 font-semibold transition inline-flex items-center gap-1">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Tinggalkan Antrean</span>
                </button>
            </div>
        @endif

    </div>

</div>

