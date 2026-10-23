@php($loggedInNonPeserta = Auth::check() && ! Auth::user()->isPeserta())
@if ($loggedInNonPeserta)
<!-- Peringatan: pendaftaran race khusus akun Peserta -->
<div x-cloak
     x-show="roleWarning"
     x-transition.opacity
     @click="roleWarning = false"
     class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full velo-box-1 border border-veloborderstrong shadow-2xl overflow-hidden"
         @click.stop>
        <div class="bg-[var(--orange)] text-[var(--ink)] px-5 py-3.5 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                <span class="font-extrabold text-sm tracking-tight">AKUN TIDAK SESUAI</span>
            </div>
            <button type="button" @click="roleWarning = false" class="text-white/70 hover:text-white" aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-velomutedlight leading-relaxed">
                Pendaftaran race hanya dapat dilakukan dengan akun <strong class="text-teal-900">Peserta</strong>.
                Anda sedang login sebagai <strong class="text-teal-900">{{ Auth::user()->isPenyelenggara() ? 'Penyelenggara' : 'Admin' }}</strong>,
                sehingga formulir pendaftaran tidak dapat dibuka.
            </p>
            <p class="text-xs text-velomutedlight">
                Untuk mendaftar race, silakan keluar dan masuk kembali dengan akun Peserta Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-2 pt-1">
                <button type="button" @click="roleWarning = false" class="flex-1 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold py-3 text-xs velo-box-1 shadow-sm transition">
                    Mengerti
                </button>
                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-white hover:bg-velosurfacealt text-teal-800 border border-veloborderstrong font-semibold py-3 text-xs velo-box-1 transition">
                        Keluar &amp; Ganti Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
