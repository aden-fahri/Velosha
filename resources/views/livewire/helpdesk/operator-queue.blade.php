<div x-data="{ sidebarOpen: false }" class="vs min-h-screen bg-[var(--paper)] text-[var(--ink)] flex flex-col md:flex-row font-sans" style="display: flex; min-height: 100vh;">

    {{-- ==================== OPERATOR SIDEBAR ==================== --}}
    <aside class="side hidden md:flex flex-col shrink-0 justify-between bg-[#FBF4E6] border-r-[3px] border-[#10282E] p-6 sticky top-0 h-screen overflow-y-auto overflow-x-hidden" style="width: 280px; min-width: 280px; max-width: 280px; flex-shrink: 0;">
        <div>
            {{-- Brand Logo Header (Official Brand Asset) --}}
            <div class="flex items-center pb-5 mb-4 border-b border-[#10282E]/15">
                <a href="/" wire:navigate class="flex items-center gap-3 transition-transform duration-150 hover:-translate-y-0.5" style="text-decoration: none" aria-label="Velosha — Beranda">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-8 w-auto object-contain" />
                    <span class="font-extrabold text-2xl tracking-tight text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
                </a>
            </div>

            {{-- Main Navigation Menu (Sesuai Gaya Peserta & EO) --}}
            <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 mb-2 mt-2">NAVIGASI OPERATOR</div>
            <nav class="space-y-1 text-sm font-semibold">
                {{-- 1. Dasbor Operator (Statistik Pribadi & Ringkasan) --}}
                <button type="button" wire:click="setTab('overview')"
                        class="w-full flex items-center gap-2.5 {{ $tab === 'overview' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Dasbor Operator</span>
                </button>

                {{-- 2. Antrian Semua Tiket --}}
                <button type="button" wire:click="setFilter('all')"
                        class="w-full flex items-center justify-between {{ $tab === 'queue' && $filter === 'all' ? 'on' : '' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Antrian Tiket</span>
                    </div>
                    @if ($kpiOpen > 0)
                        <span class="text-[10px] font-black px-2 py-0.5 rounded bg-[var(--orange)] text-white border border-[var(--ink)] shadow-[1px_1px_0_var(--ink)] shrink-0">
                            {{ $kpiOpen }}
                        </span>
                    @endif
                </button>

                {{-- 3. Tiket Selesai --}}
                <button type="button" wire:click="setFilter('closed')"
                        class="w-full flex items-center gap-2.5 {{ $tab === 'queue' && $filter === 'closed' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Tiket Selesai</span>
                </button>

                {{-- 4. Profil & Akun Petugas --}}
                <button type="button" wire:click="setTab('profile')"
                        class="w-full flex items-center gap-2.5 {{ $tab === 'profile' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1-5 15-5 16 0"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Profil &amp; Akun</span>
                </button>

                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">BERALIH</div>

                <a href="/" wire:navigate class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Kembali ke website</span>
                </a>

                @if(Auth::user()->isAdmin())
                <a href="/admin" class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Panel Admin</span>
                </a>
                @endif
            </nav>
        </div>

        {{-- Footer User Profile (Exact Same Style as Peserta & EO) --}}
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

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-ghost btn-block btn-sm text-[#C23B22] hover:bg-red-50">
                    Keluar akun
                </button>
            </form>
        </div>
    </aside>

    {{-- ==================== MOBILE SIDEBAR DRAWER ==================== --}}
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 md:hidden" role="dialog" aria-modal="true">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-[#10282E]/80 backdrop-blur-xs" @click="sidebarOpen = false"></div>

        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="side relative flex flex-col w-72 max-w-full h-full bg-[#FBF4E6] text-[var(--ink)] border-r-[3px] border-[#10282E] p-6 shadow-2xl overflow-y-auto">
            <div class="flex items-center justify-between pb-6 mb-4 border-b border-[#10282E]/15">
                <a href="/" wire:navigate class="flex items-center gap-2.5 transition-transform duration-150 hover:-translate-y-0.5" style="text-decoration: none" aria-label="Velosha — Beranda">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-8 w-auto object-contain" />
                    <span class="font-extrabold text-2xl tracking-tight text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
                </a>
                <button type="button" @click="sidebarOpen = false" class="p-2 text-[var(--ink)]">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 mb-2">NAVIGASI OPERATOR</div>
            <nav class="space-y-1 text-sm font-semibold">
                <button type="button" wire:click="setTab('overview'); sidebarOpen = false"
                        class="w-full flex items-center gap-2.5 {{ $tab === 'overview' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect></svg>
                    <span>Dasbor Operator</span>
                </button>

                <button type="button" wire:click="setFilter('all'); sidebarOpen = false"
                        class="w-full flex items-center justify-between {{ $tab === 'queue' && $filter === 'all' ? 'on' : '' }}">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"></path></svg>
                        <span>Antrian Tiket</span>
                    </div>
                    @if ($kpiOpen > 0)
                        <span class="text-[10px] font-black px-2 py-0.5 rounded bg-[var(--orange)] text-white border border-[var(--ink)]">{{ $kpiOpen }}</span>
                    @endif
                </button>

                <button type="button" wire:click="setFilter('closed'); sidebarOpen = false"
                        class="w-full flex items-center gap-2.5 {{ $tab === 'queue' && $filter === 'closed' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Tiket Selesai</span>
                </button>

                <button type="button" wire:click="setTab('profile'); sidebarOpen = false"
                        class="w-full flex items-center gap-2.5 {{ $tab === 'profile' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1-5 15-5 16 0"></path></svg>
                    <span>Profil &amp; Akun</span>
                </button>

                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">BERALIH</div>
                <a href="/" wire:navigate class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"></path></svg>
                    <span>Kembali ke website</span>
                </a>
            </nav>

            <div class="pt-6 border-t border-[#10282E]/15 mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-block btn-sm text-[#C23B22]">
                        Keluar akun
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== MAIN CONTENT CANVAS ==================== --}}
    <div class="flex-1 min-w-0 flex flex-col min-h-screen" style="flex: 1 1 0%; min-width: 0;">

        {{-- Top Bar for Mobile --}}
        <header class="md:hidden h-16 bg-[#FBF4E6] border-b-[2.5px] border-[#10282E] px-4 flex items-center justify-between sticky top-0 z-20">
            <button type="button" @click="sidebarOpen = true" class="p-2 text-[var(--ink)]">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <span class="font-heading font-black text-lg">Dashboard Operator</span>
            <span class="disp w-8 h-8 border-2 border-[#10282E] rounded-md bg-[var(--orange)] flex items-center justify-center font-bold text-xs text-[var(--ink)]">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
        </header>

        <main class="p-6 sm:p-10 min-w-0 flex-grow space-y-8">

            {{-- Flash Notification Messages --}}
            @if (session()->has('success'))
                <div class="p-4 bg-[#D5EBD9] border-2 border-[var(--ink)] text-[var(--ink)] font-bold rounded-xl shadow-[3px_3px_0_var(--ink)] flex items-center gap-2">
                    <span>✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 bg-red-100 border-2 border-[var(--ink)] text-red-900 font-bold rounded-xl shadow-[3px_3px_0_var(--ink)] flex items-center gap-2">
                    <span>✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- ========================================================= --}}
            {{-- TAB 1: DASBOR OPERATOR (OVERVIEW & STATISTIK)             --}}
            {{-- ========================================================= --}}
            @if ($tab === 'overview')

                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">DASHBOARD OPERATOR</div>
                    <h1 class="h3 font-heading text-3xl sm:text-4xl font-black mt-1 text-[var(--ink)]">Ringkasan Penanganan Bantuan</h1>
                </div>

                {{-- Sapaan Screen-Print Oranye Solid (#DE7119) Sesuai Dashboard Peserta & EO --}}
                <section class="card bg-[#DE7119] p-8 sm:p-9 relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6 min-h-[170px] border-[3px] border-[var(--ink)] rounded-2xl shadow-[6px_6px_0_var(--ink)]">
                    <div class="dots absolute inset-0 opacity-40 pointer-events-none"></div>

                    <div class="relative z-10">
                        <h2 class="h2 text-3xl sm:text-[40px] text-[var(--ink)] font-black" style="font-family: 'Bricolage Grotesque', sans-serif;">
                            Halo, Petugas {{ Auth::user()->name }}!
                        </h2>
                        <p class="mt-2 max-w-[520px] text-sm sm:text-base font-medium text-[var(--ink)]/90 leading-relaxed">
                            Pantau antrian aduan peserta, SLA respon awal, dan tiket kendala pembayaran yang memerlukan tindakan segera.
                        </p>
                    </div>

                    {{-- Jam Real-Time Box Hitam Sesuai Portal Peserta --}}
                    <div x-data="{ clock: '' }"
                         x-init="const updateClock = () => { const now = new Date(); clock = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Jakarta' }) + ' WIB'; }; updateClock(); setInterval(updateClock, 1000);"
                         class="relative z-10 px-5 py-3.5 border-[2.5px] border-[#10282E] rounded-[10px] bg-[#10282E] text-[#FBF4E6] text-center shrink-0 shadow-[3px_3px_0_#10282E]">
                        <div class="text-[11px] font-bold tracking-[.1em] text-[#F4BE3F] uppercase">WAKTU OPERASIONAL (WIB)</div>
                        <div class="mono text-2xl sm:text-[28px] font-bold mt-1 text-[#FBF4E6]" x-text="clock">--:--:-- WIB</div>
                        <div class="text-[11px] font-semibold text-[#D5EBD9] mt-0.5">SLA Monitoring Aktif</div>
                    </div>
                </section>

                {{-- 4 KPI Counter Cards (Sesuai Gaya Peserta & EO) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Menunggu Respons -->
                    <div wire:click="setFilter('open')" class="card p-5 flex justify-between shadow-[4px_4px_0_#10282E] bg-white cursor-pointer hover:-translate-y-0.5 transition-transform">
                        <div>
                            <div class="text-xs font-bold tracking-[.06em] text-[var(--mute)] uppercase">MENUNGGU RESPONS</div>
                            <div class="disp text-[40px] font-extrabold leading-[1.1] text-[var(--ink)] mt-1">{{ $kpiOpen }}</div>
                            <div class="text-[13px] text-[var(--mute)] font-medium mt-1">Tiket baru antrian</div>
                        </div>
                        <div class="ico bg-[#F3E7CF] w-12 h-12 flex items-center justify-center border-2 border-[#10282E] rounded-xl shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"></path></svg>
                        </div>
                    </div>

                    <!-- Dalam Proses -->
                    <div wire:click="setFilter('in_progress')" class="card p-5 flex justify-between shadow-[4px_4px_0_#10282E] bg-white cursor-pointer hover:-translate-y-0.5 transition-transform">
                        <div>
                            <div class="text-xs font-bold tracking-[.06em] text-[var(--mute)] uppercase">DALAM PROSES</div>
                            <div class="disp text-[40px] font-extrabold leading-[1.1] text-[var(--ink)] mt-1">{{ $kpiInProgress }}</div>
                            <div class="text-[13px] text-[var(--mute)] font-medium mt-1">Sedang ditangani</div>
                        </div>
                        <div class="ico bg-[#F8E3B5] w-12 h-12 flex items-center justify-center border-2 border-[#10282E] rounded-xl shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                    </div>

                    <!-- Mendesak / Overdue SLA -->
                    <div wire:click="setFilter('open')" class="card p-5 flex justify-between shadow-[4px_4px_0_#10282E] {{ $kpiBreached > 0 ? 'bg-red-50' : 'bg-white' }} cursor-pointer hover:-translate-y-0.5 transition-transform">
                        <div>
                            <div class="text-xs font-bold tracking-[.06em] {{ $kpiBreached > 0 ? 'text-red-700 font-black' : 'text-[var(--mute)]' }} uppercase">MENDESAK / SLA</div>
                            <div class="disp text-[40px] font-extrabold leading-[1.1] {{ $kpiBreached > 0 ? 'text-red-700' : 'text-[var(--ink)]' }} mt-1">{{ $kpiBreached }}</div>
                            <div class="text-[13px] {{ $kpiBreached > 0 ? 'text-red-700 font-bold' : 'text-[var(--mute)] font-medium' }} mt-1">Perlu eskalasi cepat</div>
                        </div>
                        <div class="ico {{ $kpiBreached > 0 ? 'bg-red-200 text-red-900' : 'bg-[#F2D3DC] text-[var(--ink)]' }} w-12 h-12 flex items-center justify-center border-2 border-[#10282E] rounded-xl shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        </div>
                    </div>

                    <!-- Selesai Hari Ini -->
                    <div wire:click="setFilter('closed')" class="card p-5 flex justify-between shadow-[4px_4px_0_#10282E] bg-white cursor-pointer hover:-translate-y-0.5 transition-transform">
                        <div>
                            <div class="text-xs font-bold tracking-[.06em] text-[var(--mute)] uppercase">SELESAI HARI INI</div>
                            <div class="disp text-[40px] font-extrabold leading-[1.1] text-[#1F6F4A] mt-1">{{ $kpiClosedToday }}</div>
                            <div class="text-[13px] text-[#1F6F4A] font-semibold mt-1">Closed tanggal {{ date('d M') }}</div>
                        </div>
                        <div class="ico bg-[#D5EBD9] w-12 h-12 flex items-center justify-center border-2 border-[#10282E] rounded-xl shrink-0">
                            <svg class="w-6 h-6 text-[#1F6F4A]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                    </div>
                </div>

                {{-- Card: Tiket Prioritas Mendesak --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-2xl shadow-[4px_4px_0_var(--ink)] overflow-hidden">
                    <div class="p-6 border-b-2 border-[var(--ink)] bg-[var(--butter)] flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-[var(--mute)]">PRIORITAS TINGGI</span>
                            <h3 class="font-heading font-black text-xl text-[var(--ink)]">Tiket Mendesak yang Membutuhkan Tindakan</h3>
                        </div>
                        <button type="button" wire:click="setTab('queue')" class="px-4 py-2 rounded-xl border-2 border-[var(--ink)] bg-white hover:bg-[var(--paper)] font-black text-xs shadow-[2px_2px_0_var(--ink)] transition-all">
                            Lihat Semua Antrian &rarr;
                        </button>
                    </div>

                    @if ($urgentTickets->isEmpty())
                        <div class="p-12 text-center space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-full bg-[var(--pine-l)] border-2 border-[var(--ink)] flex items-center justify-center font-bold text-lg text-[var(--pine)]">
                                ✓
                            </div>
                            <h4 class="font-heading font-black text-lg text-[var(--ink)]">Semua Tiket Terkendali</h4>
                            <p class="text-xs text-[var(--mute)] max-w-sm mx-auto font-medium">
                                Tidak ada tiket mendesak yang menunggu saat ini. Antrian bantuan dalam status aman.
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b-2 border-[var(--ink)] bg-[var(--paper2)]">
                                        <th class="py-3 px-4 font-black">#ID</th>
                                        <th class="py-3 px-4 font-black">PELAPOR</th>
                                        <th class="py-3 px-4 font-black">SUBJEK TIKET</th>
                                        <th class="py-3 px-4 font-black">PRIORITAS</th>
                                        <th class="py-3 px-4 font-black">STATUS</th>
                                        <th class="py-3 px-4 font-black">WAKTU</th>
                                        <th class="py-3 px-4 font-black text-right">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-[1.5px] divide-[var(--line)]">
                                    @foreach ($urgentTickets as $ut)
                                        <tr class="hover:bg-[var(--paper)] transition">
                                            <td class="py-3.5 px-4 font-mono font-black text-[var(--ink)]">
                                                #{{ $ut->id }}
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-[var(--ink)] truncate max-w-[140px]">{{ $ut->reporter?->name ?? 'Anonim' }}</div>
                                                <div class="text-[10px] text-[var(--mute)] truncate max-w-[140px]">{{ $ut->reporter?->email }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 min-w-[220px]">
                                                <a href="{{ route('helpdesk.detail', ['ticket' => $ut->id]) }}" wire:navigate class="font-bold text-[var(--ink)] hover:underline block truncate max-w-md">
                                                    {{ $ut->subject }}
                                                </a>
                                                <div class="text-[10px] text-[var(--mute)] truncate max-w-md mt-0.5">
                                                    {{ $ut->category }}
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if ($ut->priority === \App\Enums\TicketPriority::HIGH)
                                                    <span class="inline-block px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-red-100 text-red-900 border border-red-700 shadow-[1px_1px_0_var(--ink)]">
                                                        High
                                                    </span>
                                                @elseif ($ut->priority === \App\Enums\TicketPriority::MID)
                                                    <span class="inline-block px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)] shadow-[1px_1px_0_var(--ink)]">
                                                        Mid
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-[var(--pine-l)] text-[var(--ink)] border border-[var(--ink)]">
                                                        Low
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if ($ut->status === \App\Enums\TicketStatus::OPEN)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-blue-100 text-blue-900 border border-blue-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Open
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-amber-100 text-amber-900 border border-amber-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> In Progress
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-[11px] text-[var(--mute)]">
                                                {{ $ut->created_at->diffForHumans() }}
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                                <div class="inline-flex items-center gap-2">
                                                    @if (! $ut->operator_id)
                                                        <button type="button" wire:click="claimTicket({{ $ut->id }})"
                                                                class="px-3 py-1 bg-[var(--orange)] text-white hover:bg-[var(--orange-l)] border-2 border-[var(--ink)] rounded-lg text-xs font-black shadow-[2px_2px_0_var(--ink)] transition-all">
                                                            Klaim
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('helpdesk.detail', ['ticket' => $ut->id]) }}" wire:navigate
                                                       class="px-3 py-1 bg-white hover:bg-[var(--paper)] text-[var(--ink)] border-2 border-[var(--ink)] rounded-lg text-xs font-bold shadow-[2px_2px_0_var(--ink)] transition-all">
                                                        Buka &rarr;
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            {{-- ========================================================= --}}
            {{-- TAB 2: PROFIL & AKUN PETUGAS HELPDESK                     --}}
            {{-- ========================================================= --}}
            @elseif ($tab === 'profile')

                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">PENGATURAN OPERATOR</div>
                    <h1 class="h3 font-heading text-3xl sm:text-4xl font-black mt-1 text-[var(--ink)]">Profil &amp; Akun Petugas</h1>
                    <p class="text-[var(--mute)] text-sm sm:text-base mt-1 font-medium">Kelola informasi nama petugas, nomor kontak darurat, dan kata sandi akun helpdesk Anda.</p>
                </div>

                <div class="max-w-2xl card p-6 sm:p-8 bg-white shadow-[4px_4px_0_#10282E] rounded-2xl border-[2.5px] border-[var(--ink)] space-y-6">
                    <div>
                        <h3 class="h3 font-heading text-2xl font-black text-[var(--ink)]">Data Identitas Petugas Helpdesk</h3>
                        <p class="text-sm text-[var(--mute)] mt-1">Nama ini akan tercantum saat Anda merespons atau mengklaim tiket aduan peserta.</p>
                    </div>

                    @if($profileSaved)
                    <div class="p-4 bg-[#D5EBD9] border-[2.5px] border-[#10282E] text-[var(--ink)] rounded-xl text-xs font-extrabold flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#1F6F4A] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Profil petugas berhasil diperbarui!</span>
                    </div>
                    @endif

                    <form wire:submit.prevent="updateProfile" class="space-y-4">
                        <div>
                            <label class="label">Nama Lengkap Petugas</label>
                            <input type="text" wire:model="name" class="input">
                            @error('name') <span class="text-xs text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="label">Alamat Email (Login Resmi)</label>
                            <input type="email" value="{{ $email }}" disabled class="input opacity-60 cursor-not-allowed bg-[var(--paper)]">
                            <span class="text-[11px] text-[var(--mute)] mt-1 block">Email akun resmi tidak dapat diubah secara mandiri. Hubungi Administrator untuk mutasi email.</span>
                        </div>

                        <div>
                            <label class="label">Nomor WhatsApp / HP Petugas</label>
                            <input type="text" wire:model="phone" placeholder="081234567890" class="input">
                            @error('phone') <span class="text-xs text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 border-t-2 border-dashed border-[#E2D3B3] space-y-4">
                            <div>
                                <h4 class="font-heading font-black text-base text-[var(--ink)]">Ubah Kata Sandi (Opsional)</h4>
                                <p class="text-xs text-[var(--mute)]">Kosongkan jika Anda tidak ingin mengubah kata sandi akun login.</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Kata Sandi Baru</label>
                                    <input type="password" wire:model="new_password" placeholder="Minimal 8 karakter" class="input">
                                    @error('new_password') <span class="text-xs text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="label">Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" wire:model="new_password_confirmation" placeholder="Ulangi kata sandi baru" class="input">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t-2 border-dashed border-[#E2D3B3] flex justify-end">
                            <button type="submit" class="btn btn-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

            {{-- ========================================================= --}}
            {{-- TAB 3: ANTRIAN TIKET MASUK LENGKAP                        --}}
            {{-- ========================================================= --}}
            @else

                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">DASHBOARD OPERATOR</div>
                    <h1 class="h3 font-heading text-3xl sm:text-4xl font-black mt-1 text-[var(--ink)]">Antrian &amp; Manajemen Tiket</h1>
                    <p class="text-[var(--mute)] text-sm sm:text-base mt-1 font-medium">Filter berdasarkan status, prioritas, kategori masalah, atau cari tiket secara instan.</p>
                </div>

                {{-- Filter Toolbar: Horizontal Tabs & Search --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-2xl p-5 shadow-[4px_4px_0_var(--ink)] space-y-4">
                    {{-- Status Pills Row --}}
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" wire:click="setFilter('all')"
                                    class="px-3.5 py-1.5 rounded-xl border-2 border-[var(--ink)] text-xs font-black transition-all cursor-pointer {{ $filter === 'all' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-white hover:bg-[var(--paper)] text-[var(--ink)]' }}">
                                Semua Antrian
                            </button>
                            <button type="button" wire:click="setFilter('open')"
                                    class="px-3.5 py-1.5 rounded-xl border-2 border-[var(--ink)] text-xs font-black transition-all cursor-pointer {{ $filter === 'open' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-white hover:bg-[var(--paper)] text-[var(--ink)]' }}">
                                Menunggu Respons ({{ $kpiOpen }})
                            </button>
                            <button type="button" wire:click="setFilter('in_progress')"
                                    class="px-3.5 py-1.5 rounded-xl border-2 border-[var(--ink)] text-xs font-black transition-all cursor-pointer {{ $filter === 'in_progress' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-white hover:bg-[var(--paper)] text-[var(--ink)]' }}">
                                Sedang Diproses ({{ $kpiInProgress }})
                            </button>
                            <button type="button" wire:click="setFilter('my_tickets')"
                                    class="px-3.5 py-1.5 rounded-xl border-2 border-[var(--ink)] text-xs font-black transition-all cursor-pointer {{ $filter === 'my_tickets' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-white hover:bg-[var(--paper)] text-[var(--ink)]' }}">
                                Tiket Saya ({{ $myActiveTickets }})
                            </button>
                            <button type="button" wire:click="setFilter('closed')"
                                    class="px-3.5 py-1.5 rounded-xl border-2 border-[var(--ink)] text-xs font-black transition-all cursor-pointer {{ $filter === 'closed' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-white hover:bg-[var(--paper)] text-[var(--ink)]' }}">
                                Tiket Selesai
                            </button>
                        </div>

                        {{-- Search Box --}}
                        <div class="relative w-full sm:w-72">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   placeholder="Cari subjek, #ID, atau nama..."
                                   class="w-full pl-3.5 pr-8 py-2 text-xs font-bold border-2 border-[var(--ink)] rounded-xl bg-[var(--paper)] focus:outline-none focus:bg-white shadow-[2px_2px_0_var(--ink)]">
                            @if ($search !== '')
                                <button type="button" wire:click="$set('search', '')" class="absolute right-2.5 top-2.5 text-xs text-[var(--mute)] hover:text-[var(--ink)] font-black">
                                    ✕
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Category and Priority Horizontal Filter Row --}}
                    <div class="pt-3 border-t-2 border-[var(--line)] flex flex-wrap items-center justify-between gap-4 text-xs">
                        {{-- Kategori Masalah --}}
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-black text-[var(--mute)] uppercase text-[10px] mr-1">Kategori:</span>
                            @foreach([
                                ['all', 'Semua'],
                                ['Pendaftaran', 'Pendaftaran & BIB'],
                                ['Pembayaran', 'Pembayaran & Refund'],
                                ['Teknis', 'Akun & Teknis'],
                                ['Penyelenggara', 'Penyelenggara & Kuota'],
                            ] as [$catVal, $catLabel])
                                <button type="button" wire:click="setCategory('{{ $catVal }}')"
                                        class="px-2.5 py-1 rounded-lg border-2 border-[var(--ink)] transition-all cursor-pointer font-bold text-[11px] {{ $categoryFilter === $catVal ? 'bg-[var(--ink)] text-white shadow-[2px_2px_0_var(--ink)]' : 'bg-white text-[var(--ink)] hover:bg-[var(--paper)]' }}">
                                    {{ $catLabel }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Prioritas --}}
                        <div class="flex items-center gap-1.5">
                            <span class="font-black text-[var(--mute)] uppercase text-[10px] mr-1">Prioritas:</span>
                            @foreach([
                                ['all', 'Semua'],
                                ['High', 'High'],
                                ['Mid', 'Mid'],
                                ['Low', 'Low'],
                            ] as [$pVal, $pLabel])
                                <button type="button" wire:click="setPriority('{{ $pVal }}')"
                                        class="px-2.5 py-1 rounded-lg border-2 border-[var(--ink)] transition-all cursor-pointer font-black text-[11px] {{ $priorityFilter === $pVal ? 'bg-[var(--orange)] text-white shadow-[2px_2px_0_var(--ink)]' : 'bg-white text-[var(--ink)] hover:bg-[var(--paper)]' }}">
                                    {{ $pLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Full Ticket Queue Table --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-2xl shadow-[4px_4px_0_var(--ink)] overflow-hidden">
                    @if ($tickets->isEmpty())
                        <div class="p-12 text-center space-y-3">
                            <div class="w-12 h-12 mx-auto rounded-full bg-[var(--paper2)] border-2 border-[var(--ink)] flex items-center justify-center font-bold text-lg">
                                ☕
                            </div>
                            <h3 class="font-heading font-black text-lg">Tidak Ada Tiket di Antrian</h3>
                            <p class="text-xs text-[var(--mute)] max-w-sm mx-auto font-medium">
                                @if ($search !== '')
                                    Tidak ada tiket yang cocok dengan kata kunci "{{ $search }}".
                                @else
                                    Bagus! Tidak ada tiket yang membutuhkan tindakan pada filter ini saat ini.
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b-[2.5px] border-[var(--ink)] bg-[var(--paper2)]">
                                        <th class="py-3.5 px-4 font-black">#ID / WAKTU</th>
                                        <th class="py-3.5 px-4 font-black">PELAPOR</th>
                                        <th class="py-3.5 px-4 font-black">SUBJEK TIKET</th>
                                        <th class="py-3.5 px-4 font-black">PRIORITAS</th>
                                        <th class="py-3.5 px-4 font-black">STATUS</th>
                                        <th class="py-3.5 px-4 font-black">TENGGAT SLA</th>
                                        <th class="py-3.5 px-4 font-black">OPERATOR</th>
                                        <th class="py-3.5 px-4 font-black text-right">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-[1.5px] divide-[var(--line)]">
                                    @foreach ($tickets as $t)
                                        @php
                                            $isOverdue = false;
                                            if ($t->status !== \App\Enums\TicketStatus::CLOSED) {
                                                $now = \Carbon\Carbon::now();
                                                if (! $t->first_response_at && $t->response_due_at && $now->gt($t->response_due_at)) {
                                                    $isOverdue = true;
                                                } elseif ($t->resolution_due_at && $now->gt($t->resolution_due_at)) {
                                                    $isOverdue = true;
                                                }
                                            }
                                        @endphp
                                        <tr class="hover:bg-[#FBF8F1] transition">
                                            {{-- ID / Waktu --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-mono font-black text-[var(--ink)]">#{{ $t->id }}</div>
                                                <div class="text-[10px] text-[var(--mute)] font-semibold">{{ $t->created_at->diffForHumans() }}</div>
                                            </td>

                                            {{-- Pelapor --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-bold text-[var(--ink)] truncate max-w-[140px]">{{ $t->reporter?->name ?? 'Anonim' }}</div>
                                                <div class="text-[10px] text-[var(--mute)] truncate max-w-[140px]">{{ $t->reporter?->email }}</div>
                                            </td>

                                            {{-- Subjek & Kategori --}}
                                            <td class="py-3.5 px-4 min-w-[200px]">
                                                <a href="{{ route('helpdesk.detail', ['ticket' => $t->id]) }}" wire:navigate class="font-bold text-[var(--ink)] hover:underline block truncate max-w-sm">
                                                    {{ $t->subject }}
                                                </a>
                                                <div class="text-[10px] text-[var(--mute)] font-medium truncate max-w-sm mt-0.5">
                                                    {{ $t->category }}
                                                </div>
                                            </td>

                                            {{-- Prioritas --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if ($t->priority === \App\Enums\TicketPriority::HIGH)
                                                    <span class="inline-block px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-red-100 text-red-900 border border-red-700 shadow-[1px_1px_0_var(--ink)]">
                                                        High
                                                    </span>
                                                @elseif ($t->priority === \App\Enums\TicketPriority::MID)
                                                    <span class="inline-block px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)] shadow-[1px_1px_0_var(--ink)]">
                                                        Mid
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-[var(--pine-l)] text-[var(--ink)] border border-[var(--ink)]">
                                                        Low
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if ($t->status === \App\Enums\TicketStatus::OPEN)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-blue-100 text-blue-900 border border-blue-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Open
                                                    </span>
                                                @elseif ($t->status === \App\Enums\TicketStatus::IN_PROGRESS)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-amber-100 text-amber-900 border border-amber-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> In Progress
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg font-black text-[10px] bg-slate-100 text-slate-800 border border-slate-600">
                                                        ✓ Closed
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- SLA Status --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if ($t->status === \App\Enums\TicketStatus::CLOSED)
                                                    <span class="text-[11px] text-[var(--mute)]">Selesai</span>
                                                @elseif ($isOverdue)
                                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-[#C23B22] text-white animate-pulse">
                                                        Overdue SLA
                                                    </span>
                                                @else
                                                    <span class="text-[11px] text-emerald-700 font-bold">
                                                        Target: {{ $t->resolution_due_at ? $t->resolution_due_at->timezone('Asia/Jakarta')->format('H:i d/m') : '-' }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Operator --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if ($t->operator)
                                                    <span class="font-bold text-[var(--ink)]">{{ $t->operator->name }}</span>
                                                @else
                                                    <span class="text-[var(--mute)] italic font-medium">Belum Ditugaskan</span>
                                                @endif
                                            </td>

                                            {{-- Actions --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                                <div class="inline-flex items-center gap-2">
                                                    @if ($t->status !== \App\Enums\TicketStatus::CLOSED && ! $t->operator_id)
                                                        <button type="button" wire:click="claimTicket({{ $t->id }})"
                                                                class="px-3 py-1 bg-[var(--orange)] text-white hover:bg-[var(--orange-l)] border-2 border-[var(--ink)] rounded-lg text-xs font-black shadow-[2px_2px_0_var(--ink)] transition-all cursor-pointer">
                                                            Klaim
                                                        </button>
                                                    @endif

                                                    <a href="{{ route('helpdesk.detail', ['ticket' => $t->id]) }}" wire:navigate
                                                       class="px-3 py-1 bg-white hover:bg-[var(--paper)] text-[var(--ink)] border-2 border-[var(--ink)] rounded-lg text-xs font-bold shadow-[2px_2px_0_var(--ink)] transition-all">
                                                        Buka &rarr;
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Links --}}
                        <div class="p-4 border-t-2 border-[var(--ink)] bg-[var(--paper2)]">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>

            @endif

        </main>
    </div>
</div>
