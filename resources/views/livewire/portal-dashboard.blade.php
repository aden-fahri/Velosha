<div x-data="{ sidebarOpen: false, showLogoutModal: false }" class="vs min-h-screen flex bg-[var(--paper)] text-[var(--ink)]">

    <!-- ==================== DESKTOP SIDEBAR ==================== -->
    <aside class="side hidden md:flex w-[272px] bg-[#FBF4E6] text-[var(--ink)] shrink-0 flex-col justify-between border-r-[3px] border-[#10282E] p-6 fixed inset-y-0 left-0 z-30 overflow-y-auto">
        <div>
            <!-- Brand Logo Header -->
            <div class="flex items-center justify-between pb-6 mb-2 border-b border-[#10282E]/15">
                <a href="/" wire:navigate class="font-heading font-extrabold text-2xl text-[var(--ink)] tracking-tight">
                    Velosha
                </a>
                <span class="chip m">Portal</span>
            </div>

            <!-- Navigation Links -->
            <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3.5 mb-2 mt-4">NAVIGASI</div>
            <nav class="space-y-1 text-sm font-semibold">
                <button type="button" wire:click="setTab('overview')"
                        class="w-full {{ $activeTab === 'overview' ? 'on' : '' }}">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect></svg>
                    <span>Ringkasan musim</span>
                </button>

                <button type="button" wire:click="setTab('tickets')"
                        class="w-full flex items-center justify-between {{ $activeTab === 'tickets' ? 'on' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 1 0-6V6H3z"></path><path d="M14 6v12" stroke-dasharray="2 3"></path></svg>
                        <span>Tiket &amp; BIB Saya</span>
                    </div>
                    @if($statTotal > 0)
                    <span class="chip p ml-auto">{{ $statTotal }}</span>
                    @endif
                </button>

                <button type="button" wire:click="setTab('events')"
                        class="w-full {{ $activeTab === 'events' ? 'on' : '' }}">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"></rect><path d="M8 3v4M16 3v4M3 11h18"></path></svg>
                    <span>Jadwal balap</span>
                </button>

                <button type="button" wire:click="setTab('profile')"
                        class="w-full {{ $activeTab === 'profile' ? 'on' : '' }}">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1-5 15-5 16 0"></path></svg>
                    <span>Profil &amp; akun</span>
                </button>

                <button type="button" wire:click="setTab('help')"
                        class="w-full {{ $activeTab === 'help' ? 'on' : '' }}">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M9.500 9.500a2.500 2.500 0 1 1 3.500 2.300c-.7.4-1 .9-1 1.700M12 17v.01"></path></svg>
                    <span>Pusat bantuan</span>
                </button>

                <a href="{{ route('helpdesk.my-tickets') }}" wire:navigate class="w-full flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H9l-5 4z"></path></svg>
                        <span>Tiket Bantuan</span>
                    </div>
                    @php
                        $userTicketCount = \App\Models\HelpdeskTicket::where('reporter_id', Auth::id())
                            ->whereIn('status', [\App\Enums\TicketStatus::OPEN, \App\Enums\TicketStatus::IN_PROGRESS])
                            ->count();
                    @endphp
                    @if($userTicketCount > 0)
                    <span class="chip p ml-auto">{{ $userTicketCount }}</span>
                    @endif
                </a>

                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3.5 pt-5 pb-1">PUBLIK</div>

                <a href="/" wire:navigate class="w-full">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"></path></svg>
                    <span>Kembali ke website</span>
                </a>

                @if(Auth::user()->isAdmin() || Auth::user()->isPenyelenggara())
                <a href="/admin" class="w-full">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <span>Panel Pengelola</span>
                </a>
                @endif
            </nav>
        </div>

        <!-- Footer User Profile & Logout -->
        <div class="pt-6 mt-auto">
            <div class="card-flat p-3.5 flex items-center gap-3 shadow-[3px_3px_0_#10282E] bg-white">
                <span class="disp w-11 h-11 border-[2.5px] border-[#10282E] rounded-lg bg-[var(--orange)] flex items-center justify-center font-extrabold text-xl text-[var(--ink)] shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-sm truncate text-[var(--ink)]">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-[var(--mute)] truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <button type="button" @click="showLogoutModal = true"
                    class="btn btn-ghost btn-block btn-sm mt-3 text-[#C23B22] hover:bg-red-50">
                Keluar akun
            </button>
        </div>
    </aside>

    <!-- ==================== MOBILE SIDEBAR DRAWER ==================== -->
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 md:hidden" role="dialog" aria-modal="true">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-[#10282E]/80 backdrop-blur-xs" @click="sidebarOpen = false"></div>

        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="side relative flex flex-col w-72 max-w-full h-full bg-[#FBF4E6] text-[var(--ink)] border-r-[3px] border-[#10282E] p-6 shadow-2xl overflow-y-auto">
            <div class="flex items-center justify-between pb-6 mb-4 border-b border-[#10282E]/15">
                <a href="/" class="font-heading font-extrabold text-2xl text-[var(--ink)]">Velosha</a>
                <button type="button" @click="sidebarOpen = false" class="p-1.5 text-[var(--ink)]">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <nav class="space-y-1.5 text-sm flex-1">
                <button type="button" wire:click="setTab('overview')" @click="sidebarOpen = false"
                        class="w-full {{ $activeTab === 'overview' ? 'on' : '' }}">
                    Ringkasan musim
                </button>
                <button type="button" wire:click="setTab('tickets')" @click="sidebarOpen = false"
                        class="w-full {{ $activeTab === 'tickets' ? 'on' : '' }}">
                    Tiket &amp; BIB
                </button>
                <button type="button" wire:click="setTab('events')" @click="sidebarOpen = false"
                        class="w-full {{ $activeTab === 'events' ? 'on' : '' }}">
                    Jadwal balap
                </button>
                <button type="button" wire:click="setTab('profile')" @click="sidebarOpen = false"
                        class="w-full {{ $activeTab === 'profile' ? 'on' : '' }}">
                    Profil &amp; akun
                </button>
                <button type="button" wire:click="setTab('help')" @click="sidebarOpen = false"
                        class="w-full {{ $activeTab === 'help' ? 'on' : '' }}">
                    Pusat bantuan
                </button>
                <a href="{{ route('helpdesk.my-tickets') }}" wire:navigate class="w-full">
                    Tiket bantuan
                </a>
                <a href="/" wire:navigate class="w-full mt-4">
                    Kembali ke website
                </a>
            </nav>

            <div class="pt-6 border-t border-[#10282E]/15 mt-auto">
                <button type="button" @click="showLogoutModal = true; sidebarOpen = false" class="btn btn-ghost btn-block btn-sm text-[#C23B22]">
                    Keluar akun
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MAIN APPLICATION CANVAS ==================== -->
    <div class="flex-1 md:ml-[272px] flex flex-col min-h-screen">

        <!-- TOP BAR FOR MOBILE -->
        <header class="md:hidden h-16 bg-[#FBF4E6] border-b-[2.5px] border-[#10282E] px-4 flex items-center justify-between sticky top-0 z-20">
            <button type="button" @click="sidebarOpen = true" class="p-2 text-[var(--ink)]">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <span class="font-heading font-black text-lg">Portal Rider</span>
            <span class="disp w-8 h-8 border-2 border-[#10282E] rounded-md bg-[var(--orange)] flex items-center justify-center font-bold text-xs text-[var(--ink)]">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
        </header>

        <!-- MAIN WORKSPACE CONTENT -->
        <main class="p-6 sm:p-10 lg:p-12 flex-grow space-y-6 max-w-7xl">

            <!-- ================= TAB 1: OVERVIEW ================= -->
            @if($activeTab === 'overview')

                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">PORTAL RIDER</div>
                    <h1 class="h3 font-heading text-3xl font-black mt-0.5">Ringkasan musim</h1>
                </div>

                <!-- Sapaan Kartun Screen-Print Oranye Solid (#DE7119) Sesuai Portal.dc.html -->
                <section class="card bg-[#DE7119] p-8 sm:p-9 relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6 min-h-[190px]">
                    <div class="dots absolute inset-0 opacity-40 pointer-events-none"></div>

                    <div class="relative z-10">
                        <span class="chip">Musim 2026/2027</span>
                        <h2 class="h2 text-3xl sm:text-[44px] mt-3.5 text-[var(--ink)]">
                            Halo, {{ Auth::user()->name }}!
                        </h2>
                        <p class="mt-2.5 max-w-[480px] text-base font-medium text-[var(--ink)] leading-relaxed">
                            Pantau status pendaftaran, jadwal race terdekat, dan nomor BIB RFID kamu di sini.
                        </p>
                    </div>

                    <!-- Jam Real-Time Box Hitam Sesuai Portal.dc.html -->
                    <div x-data="{ clock: '' }"
                         x-init="const updateClock = () => { const now = new Date(); clock = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB'; }; updateClock(); setInterval(updateClock, 1000);"
                         class="relative z-10 px-5 py-3.5 border-[2.5px] border-[#10282E] rounded-[10px] bg-[#10282E] text-[#FBF4E6] text-center shrink-0 shadow-[3px_3px_0_#10282E]">
                        <div class="text-[11px] font-bold tracking-[.1em] text-[#F4BE3F] uppercase">JAM REAL-TIME</div>
                        <div class="mono text-2xl sm:text-[28px] font-bold mt-1 text-[#FBF4E6]" x-text="clock">06.13.30 WIB</div>
                    </div>
                </section>

                <!-- Statistik 4 Box Sesuai Portal.dc.html -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Total Pendaftaran -->
                    <div class="card p-5 flex justify-between shadow-[4px_4px_0_#10282E] bg-white">
                        <div>
                            <div class="text-xs font-bold tracking-[.06em] text-[var(--mute)] uppercase">TOTAL PENDAFTARAN</div>
                            <div class="disp text-[44px] font-extrabold leading-[1.1] text-[var(--ink)] mt-1">{{ $statTotal }}</div>
                            <div class="text-[13px] text-[var(--mute)] font-medium mt-1">Balap terdaftar</div>
                        </div>
                        <div class="ico bg-[#F3E7CF] w-12 h-12 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 1 0-6V6H3z"></path><path d="M14 6v12" stroke-dasharray="2 3"></path></svg>
                        </div>
                    </div>

                    <!-- Lunas & Aktif -->
                    <div class="card p-5 flex justify-between shadow-[4px_4px_0_#10282E] bg-white">
                        <div>
                            <div class="text-xs font-bold tracking-[.06em] text-[var(--mute)] uppercase">Lunas &amp; Aktif</div>
                            <div class="disp text-[44px] font-extrabold leading-[1.1] text-[#1F6F4A] mt-1">{{ $statPaid }}</div>
                            <div class="text-[13px] font-semibold text-[#1F6F4A] mt-1">E-ticket terbit</div>
                        </div>
                        <div class="ico bg-[#D5EBD9] w-12 h-12 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </div>
                    </div>

                    <!-- Menunggu Bayar -->
                    <div class="card p-5 flex justify-between shadow-[4px_4px_0_#10282E] bg-white">
                        <div>
                            <div class="text-xs font-bold tracking-[.06em] text-[var(--mute)] uppercase">MENUNGGU BAYAR</div>
                            <div class="disp text-[44px] font-extrabold leading-[1.1] text-[#B2590F] mt-1">{{ $statPending }}</div>
                            <div class="text-[13px] font-semibold text-[#B2590F] mt-1">Segera selesaikan</div>
                        </div>
                        <div class="ico bg-[#F6B777] w-12 h-12 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                    </div>

                    <!-- BIB RFID Terbit -->
                    <div class="card p-5 flex justify-between shadow-[4px_4px_0_#10282E] bg-white">
                        <div>
                            <div class="text-xs font-bold tracking-[.06em] text-[var(--mute)] uppercase">BIB RFID TERBIT</div>
                            <div class="disp text-[44px] font-extrabold leading-[1.1] text-[#9B3D5A] mt-1">{{ $statBibs }}</div>
                            <div class="text-[13px] font-semibold text-[#9B3D5A] mt-1">Nomor dada resmi</div>
                        </div>
                        <div class="ico bg-[#F2D3DC] w-12 h-12 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                        </div>
                    </div>
                </div>

                <!-- Dua Kolom: Kiri (Race Terdekat & Pendaftaran Terbaru), Kanan (Profil Peserta & Panduan) Sesuai Portal.dc.html -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <!-- KOLOM KIRI (7/12) -->
                    <div class="lg:col-span-8 space-y-6">

                        <!-- Balap Rekomendasi Terdekat (Hitam + Badge Rotasi 4deg) Sesuai Portal.dc.html -->
                        @if($countdown)
                        <section class="card bg-[#10282E] text-[#FBF4E6] p-7 sm:p-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 shadow-[5px_5px_0_#DE7119]">
                            <div>
                                <span class="chip m">Balap rekomendasi terdekat</span>
                                <h2 class="h3 text-2xl sm:text-[30px] mt-3.5 text-[#FBF4E6] font-heading font-extrabold">
                                    {{ $countdown['event']->title }}
                                </h2>
                                <div class="flex flex-wrap gap-2.5 mt-4">
                                    <span class="chip bg-white text-[var(--ink)]">
                                        {{ \Illuminate\Support\Carbon::parse($countdown['event']->event_date)->translatedFormat('d M Y') }}
                                    </span>
                                    @if($countdown['event']->location_name)
                                    <span class="chip bg-white text-[var(--ink)]">
                                        {{ $countdown['event']->location_name }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-center transform rotate-4 self-center sm:self-auto shrink-0">
                                <div class="disp px-6 py-3.5 bg-[#DE7119] text-[#10282E] border-[3px] border-[#FBF4E6] rounded-[10px] text-4xl sm:text-[44px] font-extrabold shadow-sm">
                                    {{ $countdown['days'] <= 0 ? 'HARI INI' : 'H-'.$countdown['days'] }}
                                </div>
                                <div class="text-xs font-bold tracking-[.08em] text-[#F4BE3F] mt-2.5 uppercase">MENUJU GUN START</div>
                            </div>
                        </section>
                        @endif

                        <!-- Daftar Pendaftaran Terbaru Sesuai Portal.dc.html -->
                        <section class="card overflow-hidden bg-white">
                            <div class="p-5 sm:p-7 border-b-[2.5px] border-[#10282E] flex justify-between items-baseline">
                                <div>
                                    <h2 class="h3 font-heading text-xl sm:text-2xl font-black">Pendaftaran terbaru</h2>
                                    <div class="text-sm text-[var(--mute)] mt-0.5">Race yang kamu daftar musim ini.</div>
                                </div>
                                <button type="button" wire:click="setTab('tickets')" class="font-bold text-sm text-[#B2590F] hover:underline">
                                    Lihat semua tiket
                                </button>
                            </div>

                            @if($registrations->isEmpty())
                            <div class="p-10 text-center space-y-3">
                                <p class="text-sm text-[var(--mute)]">Kamu belum memiliki pendaftaran balap musim ini.</p>
                                <a href="{{ route('events.index') }}" wire:navigate class="btn btn-sm">
                                    Cari Balap Terbuka
                                </a>
                            </div>
                            @else
                            <div class="divide-y-2 divide-dashed divide-[#E2D3B3]">
                                @foreach($registrations->take(4) as $reg)
                                @php($regEvent = $reg->category?->event)
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-5 sm:px-7 hover:bg-[#F3E7CF]/40 transition">
                                    <!-- Tanggal Kotak Kuning / Hijau Sesuai Portal.dc.html -->
                                    <div class="disp w-[60px] h-[64px] border-[2.5px] border-[#10282E] rounded-[10px] {{ $reg->isPaid() ? 'bg-[#D5EBD9]' : 'bg-[#F4BE3F]' }} flex flex-col items-center justify-center leading-none shrink-0">
                                        <b class="text-2xl">{{ \Illuminate\Support\Carbon::parse($regEvent?->event_date)->format('d') }}</b>
                                        <span class="text-xs font-bold uppercase mt-1">{{ \Illuminate\Support\Carbon::parse($regEvent?->event_date)->translatedFormat('M') }}</span>
                                    </div>

                                    <!-- Nama Event & Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex gap-2.5 items-center flex-wrap">
                                            <b class="text-base sm:text-[17px] text-[var(--ink)] truncate">{{ $regEvent?->title }}</b>
                                            <span class="chip {{ $reg->isPaid() ? 'o' : 'b' }}">{{ $reg->category?->name }}</span>
                                        </div>
                                        <div class="text-sm text-[var(--mute)] mt-1 truncate">
                                            {{ $regEvent?->location_name }}
                                            @if($reg->bib_number)
                                            · <span class="font-mono font-bold text-[var(--ink)]">BIB #{{ $reg->bib_number }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Status & CTA Sesuai Portal.dc.html -->
                                    <div class="flex items-center gap-3 self-end sm:self-center shrink-0">
                                        @if($reg->isPaid())
                                            <span class="chip g">Lunas</span>
                                            <a href="{{ route('ticket.show', $reg) }}" class="btn btn-sm btn-ghost">E-ticket</a>
                                        @elseif($reg->status === \App\Models\Registration::STATUS_PENDING)
                                            <span class="chip m">Menunggu bayar</span>
                                            <a href="{{ route('checkout', ['event' => $regEvent?->slug, 'category' => $reg->event_category_id]) }}" wire:navigate class="btn btn-sm">Bayar sekarang</a>
                                        @else
                                            <span class="chip r">Dibatalkan</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </section>
                    </div>

                    <!-- KOLOM KANAN (4/12) Sesuai Portal.dc.html -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Profil Peserta Card -->
                        <section class="card p-6 bg-white">
                            <div class="flex justify-between items-baseline pb-3 border-b border-[#10282E]/10">
                                <h2 class="h3 text-xl font-bold font-heading">Profil peserta</h2>
                                <button type="button" wire:click="setTab('profile')" class="font-bold text-sm text-[#B2590F] hover:underline">
                                    Edit
                                </button>
                            </div>
                            <div class="mt-4 grid gap-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-[var(--mute)]">Nama</span>
                                    <b class="text-[var(--ink)]">{{ Auth::user()->name }}</b>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[var(--mute)]">Email</span>
                                    <b class="text-[var(--ink)] truncate max-w-[180px]">{{ Auth::user()->email }}</b>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[var(--mute)]">Telepon</span>
                                    <b class="text-[var(--ink)]">{{ Auth::user()->phone ?? 'Belum diisi' }}</b>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[var(--mute)]">Gol. darah</span>
                                    <b class="text-[var(--ink)]">{{ Auth::user()->blood_type ?? '—' }}</b>
                                </div>
                            </div>
                        </section>

                        <!-- Pengumuman Resmi Card (#1F6F4A) Sesuai Portal.dc.html -->
                        <section class="card p-6 bg-[#1F6F4A] text-white">
                            <span class="chip m">Pengumuman resmi</span>
                            <h2 class="h3 text-xl mt-3.5 text-white font-heading font-extrabold">
                                Panduan ambil race pack &amp; BIB RFID
                            </h2>
                            <p class="mt-2.5 text-sm leading-[1.55] text-white/95">
                                Bawa e-ticket resmi dari portal ini dan kartu identitas asli (KTP/SIM/Paspor).
                            </p>
                            <button type="button" wire:click="setTab('help')" class="inline-block mt-3.5 font-bold text-[#F4BE3F] hover:underline text-sm">
                                Baca regulasi lengkap &rarr;
                            </button>
                        </section>
                    </div>
                </div>

            <!-- ================= TAB 2: TICKETS ================= -->
            @elseif($activeTab === 'tickets')

                <div class="card p-6 space-y-6 bg-white shadow-[4px_4px_0_#10282E]">

                    <!-- Filter Bar & Search -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-dashed border-[#E2D3B3] pb-5">
                        <!-- Filter Tabs -->
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="filterTickets('all')"
                                    class="filter text-xs {{ $ticketStatus === 'all' ? 'on' : '' }}">
                                Semua Tiket ({{ $statTotal }})
                            </button>
                            <button type="button" wire:click="filterTickets('paid')"
                                    class="filter text-xs {{ $ticketStatus === 'paid' ? 'on' : '' }}">
                                Lunas ({{ $statPaid }})
                            </button>
                            <button type="button" wire:click="filterTickets('pending')"
                                    class="filter text-xs {{ $ticketStatus === 'pending' ? 'on' : '' }}">
                                Menunggu Bayar ({{ $statPending }})
                            </button>
                            <button type="button" wire:click="filterTickets('cancelled')"
                                    class="filter text-xs {{ $ticketStatus === 'cancelled' ? 'on' : '' }}">
                                Dibatalkan ({{ $statCancelled }})
                            </button>
                        </div>

                        <!-- Search Input -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama balap / BIB..."
                                   class="input text-xs h-11 pr-4 pl-9">
                            <svg class="w-4 h-4 text-[var(--mute)] absolute left-3 top-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                    </div>

                    <!-- Ticket Cards / Table Grid -->
                    @if($filteredRegistrations->isEmpty())
                        <div class="py-12 text-center space-y-3">
                            <p class="text-sm text-[var(--mute)]">Tidak ada pendaftaran tiket yang sesuai dengan filter ini.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($filteredRegistrations as $reg)
                            @php($regEvent = $reg->category?->event)
                            <div class="card p-5 bg-white shadow-[4px_4px_0_#10282E] flex flex-col md:flex-row md:items-center justify-between gap-5">
                                <div class="flex items-start gap-4 min-w-0">
                                    <div class="disp w-14 h-14 border-[2.5px] border-[#10282E] rounded-[10px] {{ $reg->isPaid() ? 'bg-[#D5EBD9]' : 'bg-[#F4BE3F]' }} flex flex-col items-center justify-center shrink-0 leading-none">
                                        <b class="text-lg">{{ \Illuminate\Support\Carbon::parse($regEvent?->event_date)->format('d') }}</b>
                                        <span class="text-[10px] font-bold uppercase mt-0.5">{{ \Illuminate\Support\Carbon::parse($regEvent?->event_date)->translatedFormat('M Y') }}</span>
                                    </div>
                                    <div class="space-y-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="text-base font-black text-[var(--ink)] truncate">{{ $regEvent?->title }}</h4>
                                            <span class="chip {{ $reg->isPaid() ? 'o' : 'b' }}">{{ $reg->category?->name }}</span>
                                        </div>
                                        <div class="text-xs text-[var(--mute)] flex flex-wrap items-center gap-3 font-medium">
                                            <span>{{ $regEvent?->location_name }}</span>
                                            @if($reg->bib_number)
                                            <span class="font-mono font-bold text-[var(--ink)]">BIB #{{ $reg->bib_number }}</span>
                                            @endif
                                            <span>Nomor Transaksi: <span class="font-mono text-[var(--ink)] font-bold">#REG-{{ $reg->id }}</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between md:justify-end gap-3 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-[#10282E]/10">
                                    <div>
                                        @if($reg->isPaid())
                                            <span class="chip g">Lunas &amp; Aktif</span>
                                        @elseif($reg->status === \App\Models\Registration::STATUS_PENDING)
                                            <span class="chip m">Menunggu Bayar</span>
                                        @else
                                            <span class="chip r">Dibatalkan</span>
                                        @endif
                                    </div>

                                    <div>
                                        @if($reg->isPaid())
                                            <a href="{{ route('ticket.show', $reg) }}" class="btn btn-sm btn-ghost">
                                                Lihat E-Ticket
                                            </a>
                                        @elseif($reg->status === \App\Models\Registration::STATUS_PENDING)
                                            <a href="{{ route('checkout', ['event' => $regEvent?->slug, 'category' => $reg->event_category_id]) }}" wire:navigate class="btn btn-sm">
                                                Bayar Sekarang
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif

                </div>

            <!-- ================= TAB 3: EVENTS CATALOG ================= -->
            @elseif($activeTab === 'events')

                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="h3 font-heading text-2xl font-black">Kalender Balap Velosha</h3>
                            <p class="text-sm text-[var(--mute)]">Daftar event terbuka untuk musim balap endurance 2026/2027.</p>
                        </div>
                        <a href="{{ route('events.index') }}" wire:navigate class="font-bold text-sm text-[#B2590F] hover:underline">
                            Lihat Katalog Lengkap &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($openEvents as $event)
                        <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <span class="chip o">
                                    {{ \Illuminate\Support\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}
                                </span>
                                <h4 class="text-lg font-black text-[var(--ink)] leading-snug">{{ $event->title }}</h4>
                                <p class="text-xs text-[var(--mute)]">
                                    {{ $event->location_name }}
                                </p>
                            </div>

                            <div class="pt-4 border-t-2 border-dashed border-[#E2D3B3] flex items-center justify-between">
                                <div class="text-xs font-bold text-[var(--ink)]">
                                    {{ $event->categories->count() }} Kategori Jarak
                                </div>
                                <a href="{{ route('events.show', $event->slug) }}" wire:navigate class="btn btn-sm">
                                    Daftar Race
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            <!-- ================= TAB 4: PROFILE ================= -->
            @elseif($activeTab === 'profile')

                <div class="max-w-2xl card p-6 sm:p-8 bg-white shadow-[4px_4px_0_#10282E] space-y-6">
                    <div>
                        <h3 class="h3 font-heading text-2xl font-black">Pengaturan Data Profil Peserta</h3>
                        <p class="text-sm text-[var(--mute)] mt-1">Perbarui data diri untuk keperluan registrasi &amp; asuransi keselamatan race.</p>
                    </div>

                    @if($profileSaved)
                    <div class="p-4 bg-[#D5EBD9] border-[2.5px] border-[#10282E] text-[var(--ink)] rounded-xl text-xs font-extrabold flex items-center gap-2">
                        Profil berhasil diperbarui!
                    </div>
                    @endif

                    <form wire:submit.prevent="updateProfile" class="space-y-4">
                        <div>
                            <label class="label">Nama Lengkap</label>
                            <input type="text" wire:model="name" class="input">
                            @error('name') <span class="text-xs text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="label">Nomor WhatsApp / HP (Darurat)</label>
                            <input type="text" wire:model="phone" placeholder="081234567890" class="input">
                            @error('phone') <span class="text-xs text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="label">Jenis Kelamin</label>
                                <select wire:model="gender" class="input">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="male">Laki-Laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="label">Golongan Darah</label>
                                <select wire:model="blood_type" class="input">
                                    <option value="">Pilih Golongan Darah</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="label">Tanggal Lahir</label>
                            <input type="date" wire:model="birth_date" class="input">
                        </div>

                        <div class="pt-4 border-t-2 border-dashed border-[#E2D3B3] flex justify-end">
                            <button type="submit" class="btn btn-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

            <!-- ================= TAB 5: HELP ================= -->
            @elseif($activeTab === 'help')

                <div class="max-w-3xl card p-6 sm:p-8 bg-white shadow-[4px_4px_0_#10282E] space-y-6">
                    <div>
                        <h3 class="h3 font-heading text-2xl font-black">Pusat Bantuan &amp; Pertanyaan Peserta</h3>
                        <p class="text-sm text-[var(--mute)] mt-1">Informasi resmi seputar E-Ticket, Race Pack, dan Regulasi Balap Velosha.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="card-flat p-4 bg-[#FBF4E6]">
                            <h4 class="font-bold text-base text-[var(--ink)] mb-1">Bagaimana cara mencetak E-Ticket?</h4>
                            <p class="text-sm text-[var(--mute)] leading-relaxed">Masuk ke menu Tiket &amp; BIB Saya, klik tombol "Lihat E-Ticket" pada race yang berstatus LUNAS. E-Ticket berbentuk QR Code resmi dapat disimpan ke ponsel atau dicetak.</p>
                        </div>

                        <div class="card-flat p-4 bg-[#FBF4E6]">
                            <h4 class="font-bold text-base text-[var(--ink)] mb-1">Kapan Nomor BIB RFID diterbitkan?</h4>
                            <p class="text-sm text-[var(--mute)] leading-relaxed">Nomor BIB RFID diterbitkan secara otomatis oleh sistem panitia setelah konfirmasi pembayaran diverifikasi.</p>
                        </div>

                        <div class="card-flat p-4 bg-[#FBF4E6]">
                            <h4 class="font-bold text-base text-[var(--ink)] mb-1">Apakah tiket yang sudah dibeli bisa dibatalkan / di-transfer?</h4>
                            <p class="text-sm text-[var(--mute)] leading-relaxed">Sesuai regulasi keselamatan balap Velosha, pendaftaran bersifat personal dan tidak dapat dipindahtangankan tanpa persetujuan resmi panitia.</p>
                        </div>
                    </div>
                </div>

            @endif

        </main>
    </div>

    <!-- ==================== LOGOUT MODAL OVERLAY (Alpine.js) ==================== -->
    <div x-cloak
         x-show="showLogoutModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showLogoutModal = false">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-[#10282E]/80 backdrop-blur-xs" @click="showLogoutModal = false"></div>

        <!-- Dialog Box -->
        <div x-show="showLogoutModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="card relative bg-white shadow-[6px_6px_0_#10282E] w-full max-w-sm overflow-hidden p-6 text-center space-y-4">

            <div>
                <h3 class="h3 font-heading text-xl font-black text-[var(--ink)]">Konfirmasi Keluar</h3>
                <p class="text-sm text-[var(--mute)] mt-1">
                    Apakah Anda yakin ingin keluar dari portal rider Velosha?
                </p>
            </div>

            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" @click="showLogoutModal = false"
                        class="btn btn-ghost btn-sm">
                    Batal
                </button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm bg-[#C23B22] text-white">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

