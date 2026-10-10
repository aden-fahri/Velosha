<div>
    <!-- Greeting -->
    <div class="bg-white border border-veloborder velo-box-1 shadow-sm p-6 sm:p-8 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-race-600 mb-2">Portal Rider</p>
                <h1 class="text-2xl sm:text-3xl font-black text-teal-900 tracking-tight">
                    Halo, {{ Auth::user()->name }}.
                </h1>
                <p class="text-xs text-[#556066] mt-2">
                    Satu akun untuk seluruh kalender balap: tiket, BIB, dan ranking RFID Anda.
                </p>
            </div>
            <div class="flex flex-col items-start sm:items-end gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-700/20 velo-box-1">
                    <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                    Peran: Peserta
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center text-xs font-semibold text-[#556066] hover:text-red-600 transition">
                        <i data-lucide="log-out" class="w-3.5 h-3.5 mr-1.5"></i>
                        Keluar dari akun
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Hitung Mundur Balap Berikutnya -->
    @if ($countdown !== null)
    <div class="bg-teal-900 border border-teal-900 velo-box-1 shadow-sm p-5 sm:p-6 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-amber-400 mb-1.5">Balap Berikutnya</p>
            <h2 class="text-lg sm:text-xl font-black text-white truncate">{{ $countdown['event']->title }}</h2>
            <p class="text-xs text-teal-100/80 mt-1">
                {{ \Illuminate\Support\Carbon::parse($countdown['event']->event_date)->translatedFormat('d M Y') }}
                &middot; {{ $countdown['event']->location_name ?? 'Lokasi menyusul' }}
            </p>
        </div>
        <div class="shrink-0 text-left sm:text-right">
            @if ($countdown['days'] <= 0)
                <span class="text-2xl sm:text-3xl font-black text-amber-400">HARI INI</span>
            @else
                <span class="text-2xl sm:text-3xl font-black text-amber-400">H-{{ $countdown['days'] }}</span>
            @endif
            <p class="text-[11px] text-teal-100/80 mt-1">menuju gun start</p>
        </div>
    </div>
    @endif

    <!-- Tiket Saya -->
    <div class="bg-white border border-veloborder velo-box-1 shadow-sm p-6 mb-6">
        <div class="flex flex-wrap items-center gap-2 mb-4 pb-3 border-b border-veloborder">
            <i data-lucide="ticket" class="w-4 h-4 text-race-600"></i>
            <h2 class="text-xs font-bold uppercase tracking-wider text-teal-900">Tiket Saya</h2>
            <span class="ml-auto text-[11px] text-[#556066]">
                {{ $statTotal }} pendaftaran &middot; {{ $statPaid }} lunas &middot; {{ $statPending }} menunggu pembayaran
            </span>
        </div>

        <div class="flex flex-wrap gap-2 mb-4">
            <button
                type="button"
                wire:click="filter('all')"
                class="px-3 py-1.5 text-[11px] font-bold border transition {{ $tab === 'all' ? 'bg-race-600 border-race-600 text-white' : 'bg-white border-veloborderstrong text-[#556066] hover:bg-velosurfacealt' }}"
            >
                Semua ({{ $statTotal }})
            </button>
            <button
                type="button"
                wire:click="filter('paid')"
                class="px-3 py-1.5 text-[11px] font-bold border transition {{ $tab === 'paid' ? 'bg-race-600 border-race-600 text-white' : 'bg-white border-veloborderstrong text-[#556066] hover:bg-velosurfacealt' }}"
            >
                Lunas ({{ $statPaid }})
            </button>
            <button
                type="button"
                wire:click="filter('pending')"
                class="px-3 py-1.5 text-[11px] font-bold border transition {{ $tab === 'pending' ? 'bg-race-600 border-race-600 text-white' : 'bg-white border-veloborderstrong text-[#556066] hover:bg-velosurfacealt' }}"
            >
                Menunggu ({{ $statPending }})
            </button>
            <button
                type="button"
                wire:click="filter('cancelled')"
                class="px-3 py-1.5 text-[11px] font-bold border transition {{ $tab === 'cancelled' ? 'bg-race-600 border-race-600 text-white' : 'bg-white border-veloborderstrong text-[#556066] hover:bg-velosurfacealt' }}"
            >
                Dibatalkan ({{ $statCancelled }})
            </button>
        </div>

        @if ($registrations->isEmpty())
        <div class="text-center py-6 border border-dashed border-veloborderstrong velo-box-3">
            <p class="text-xs text-[#556066] mb-4">Belum ada pendaftaran. Mulai dari kalender balap untuk menerbitkan E-Ticket pertama Anda.</p>
            <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-semibold px-5 py-2.5 text-xs velo-box-1 shadow-sm transition">
                <i data-lucide="calendar-days" class="w-3.5 h-3.5 mr-2"></i>
                <span>Jelajahi Kalender Balap</span>
            </a>
        </div>
        @elseif ($filtered->isEmpty())
        <div class="text-center py-6 border border-dashed border-veloborderstrong velo-box-3">
            <p class="text-xs text-[#556066]">Tidak ada pendaftaran dengan status yang dipilih.</p>
        </div>
        @else
        <div class="space-y-3" wire:loading.class="opacity-60">
            @foreach ($filtered as $registration)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border border-veloborderstrong velo-box-1 hover:bg-velosurfacealt transition">
                <div class="min-w-0">
                    <div class="text-sm font-bold text-teal-900 truncate">{{ $registration->category?->event?->title ?? 'Event Velosha' }}</div>
                    <div class="text-xs text-[#556066] mt-0.5 truncate">
                        {{ $registration->category?->name ?? '—' }}
                        &middot; BIB: <span class="font-mono">{{ $registration->bib_number ?? 'Belum terbit' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if ($registration->isPaid())
                    <span class="text-[10px] font-bold px-2 py-1 velo-box-3 bg-emerald-50 text-emerald-800 border border-emerald-200">
                        @if ($registration->status === \App\Models\Registration::STATUS_CHECKED_IN)
                        SUDAH CHECK-IN
                        @else
                        LUNAS
                        @endif
                    </span>
                    <a href="{{ route('ticket.show', $registration) }}" class="inline-flex items-center bg-race-600 hover:bg-race-700 text-white font-semibold px-3.5 py-2 text-xs velo-box-1 shadow-xs transition">
                        Lihat E-Ticket
                    </a>
                    @elseif ($registration->status === \App\Models\Registration::STATUS_PENDING)
                    <span class="text-[10px] font-bold px-2 py-1 velo-box-3 bg-amber-50 text-amber-800 border border-amber-300">MENUNGGU BAYAR</span>
                    <a href="{{ route('checkout', ['event' => $registration->category?->event?->slug, 'category' => $registration->event_category_id]) }}" wire:navigate class="inline-flex items-center bg-race-600 hover:bg-race-700 text-white font-semibold px-3.5 py-2 text-xs velo-box-1 shadow-xs transition">
                        Bayar Sekarang
                    </a>
                    @else
                    <span class="text-[10px] font-bold px-2 py-1 velo-box-3 bg-red-50 text-red-700 border border-red-200">DIBATALKAN</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Data Akun -->
        <div class="bg-white border border-veloborder velo-box-1 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-veloborder">
                <i data-lucide="id-card" class="w-4 h-4 text-race-600"></i>
                <h2 class="text-xs font-bold uppercase tracking-wider text-teal-900">Data Akun</h2>
            </div>
            <dl class="space-y-3 text-xs">
                <div class="flex justify-between gap-4">
                    <dt class="text-[#556066]">Nama Lengkap</dt>
                    <dd class="font-semibold text-teal-900 text-right">{{ Auth::user()->name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#556066]">Email</dt>
                    <dd class="font-semibold text-teal-900 text-right">{{ Auth::user()->email }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#556066]">WhatsApp</dt>
                    <dd class="font-semibold text-teal-900 text-right">{{ Auth::user()->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#556066]">Terdaftar</dt>
                    <dd class="font-semibold text-teal-900 text-right">{{ Auth::user()->created_at?->translatedFormat('d M Y') ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Langkah Berikutnya -->
        <div class="bg-white border border-veloborder velo-box-1 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-veloborder">
                <i data-lucide="route" class="w-4 h-4 text-race-600"></i>
                <h2 class="text-xs font-bold uppercase tracking-wider text-teal-900">Langkah Berikutnya</h2>
            </div>
            <div class="space-y-2">
                <a href="{{ route('events.index') }}" class="flex items-center justify-between px-3 py-3 border border-veloborderstrong velo-box-1 hover:bg-velosurfacealt transition group">
                    <span class="flex items-center gap-2 text-xs font-semibold text-teal-900">
                        <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-teal-700"></i>
                        Jelajahi Kalender Balap
                    </span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#556066] group-hover:text-race-600"></i>
                </a>
                <a href="{{ route('ticket.show') }}" class="flex items-center justify-between px-3 py-3 border border-veloborderstrong velo-box-1 hover:bg-velosurfacealt transition group">
                    <span class="flex items-center gap-2 text-xs font-semibold text-teal-900">
                        <i data-lucide="ticket" class="w-3.5 h-3.5 text-teal-700"></i>
                        Tiket &amp; E-Ticket QR Saya
                    </span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#556066] group-hover:text-race-600"></i>
                </a>
                <a href="{{ route('help') }}" class="flex items-center justify-between px-3 py-3 border border-veloborderstrong velo-box-1 hover:bg-velosurfacealt transition group">
                    <span class="flex items-center gap-2 text-xs font-semibold text-teal-900">
                        <i data-lucide="life-buoy" class="w-3.5 h-3.5 text-teal-700"></i>
                        Pusat Bantuan
                    </span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#556066] group-hover:text-race-600"></i>
                </a>
            </div>
        </div>
    </div>

    <p class="text-[11px] text-[#556066] mt-6 text-center">
        Dashboard ini akan dilengkapi dengan riwayat balap, status pembayaran, dan notifikasi race.
    </p>
</div>
