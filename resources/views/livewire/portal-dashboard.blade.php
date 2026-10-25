<div x-data="{ sidebarOpen: false, showLogoutModal: false }" class="min-h-screen flex bg-[#f1f4f4] font-sans text-teal-900 selection:bg-teal-700 selection:text-white">

    <!-- ==================== DESKTOP SIDEBAR ==================== -->
    <aside class="hidden md:flex w-64 bg-white text-teal-900 shrink-0 flex-col justify-between border-r border-veloborder shadow-xs fixed inset-y-0 left-0 z-30">
        <div>
            <!-- Brand Logo Header -->
            <div class="h-20 px-5 flex items-center justify-between border-b border-veloborder bg-white">
                <a href="/" wire:navigate class="flex items-center gap-2 group">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-10 w-auto object-contain" />
                </a>
                <span class="text-[9px] font-extrabold uppercase tracking-wider bg-race-50 text-race-700 border border-race-300 px-2 py-0.5 velo-box-3">PORTAL RIDER</span>
            </div>

            <!-- Navigation Links -->
            <nav class="px-3 py-5 space-y-1 text-xs">
                <div class="px-3 pb-2 text-[10px] font-extrabold text-velomuted uppercase tracking-wider">Navigasi Utama</div>

                <button type="button" wire:click="setTab('overview')"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 transition-all {{ $activeTab === 'overview' ? 'bg-teal-50 text-teal-950 border-l-4 border-race-600 shadow-xs' : 'text-velomutedlight hover:bg-[#f1f4f4] hover:text-teal-950' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ $activeTab === 'overview' ? 'text-race-600' : 'text-teal-700' }}"></i>
                    <span>Ringkasan Musim</span>
                </button>

                <button type="button" wire:click="setTab('tickets')"
                        class="w-full flex items-center justify-between px-3.5 py-3 font-extrabold velo-box-1 transition-all {{ $activeTab === 'tickets' ? 'bg-teal-50 text-teal-950 border-l-4 border-race-600 shadow-xs' : 'text-velomutedlight hover:bg-[#f1f4f4] hover:text-teal-950' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="ticket" class="w-4 h-4 {{ $activeTab === 'tickets' ? 'text-race-600' : 'text-teal-700' }}"></i>
                        <span>Tiket &amp; BIB Saya</span>
                    </div>
                    @if($statTotal > 0)
                    <span class="text-[10px] font-extrabold px-2 py-0.5 velo-box-3 {{ $activeTab === 'tickets' ? 'bg-[var(--orange)] text-[var(--ink)]' : 'bg-teal-100 text-teal-900' }}">{{ $statTotal }}</span>
                    @endif
                </button>

                <button type="button" wire:click="setTab('events')"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 transition-all {{ $activeTab === 'events' ? 'bg-teal-50 text-teal-950 border-l-4 border-race-600 shadow-xs' : 'text-velomutedlight hover:bg-[#f1f4f4] hover:text-teal-950' }}">
                    <i data-lucide="calendar-days" class="w-4 h-4 {{ $activeTab === 'events' ? 'text-race-600' : 'text-teal-700' }}"></i>
                    <span>Jadwal Balap</span>
                </button>

                <button type="button" wire:click="setTab('profile')"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 transition-all {{ $activeTab === 'profile' ? 'bg-teal-50 text-teal-950 border-l-4 border-race-600 shadow-xs' : 'text-velomutedlight hover:bg-[#f1f4f4] hover:text-teal-950' }}">
                    <i data-lucide="user-cog" class="w-4 h-4 {{ $activeTab === 'profile' ? 'text-race-600' : 'text-teal-700' }}"></i>
                    <span>Profil &amp; Akun</span>
                </button>

                <button type="button" wire:click="setTab('help')"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 transition-all {{ $activeTab === 'help' ? 'bg-teal-50 text-teal-950 border-l-4 border-race-600 shadow-xs' : 'text-velomutedlight hover:bg-[#f1f4f4] hover:text-teal-950' }}">
                    <i data-lucide="life-buoy" class="w-4 h-4 {{ $activeTab === 'help' ? 'text-race-600' : 'text-teal-700' }}"></i>
                    <span>Pusat Bantuan</span>
                </button>

                <a href="{{ route('helpdesk.my-tickets') }}" wire:navigate
                   class="w-full flex items-center gap-3 px-3.5 py-2.5 font-bold text-teal-900 hover:bg-[#f1f4f4] velo-box-1 transition-all">
                    <i data-lucide="ticket-check" class="w-4 h-4 text-teal-700"></i>
                    <span>Tiket Bantuan Saya</span>
                </a>

                <div class="pt-6 px-3 pb-2 text-[10px] font-extrabold text-velomuted uppercase tracking-wider">Akses Publik</div>

                <a href="/" wire:navigate
                   class="w-full flex items-center gap-3 px-3.5 py-2.5 font-bold text-teal-900 hover:bg-[#f1f4f4] velo-box-1 transition-all">
                    <i data-lucide="globe" class="w-4 h-4 text-teal-700"></i>
                    <span>Kembali ke Website</span>
                </a>

                @if(Auth::user()->isAdmin() || Auth::user()->isPenyelenggara())
                <a href="/admin"
                   class="w-full flex items-center gap-3 px-3.5 py-2.5 font-bold text-amber-800 hover:bg-amber-50 velo-box-1 transition-all">
                    <i data-lucide="shield" class="w-4 h-4 text-amber-600"></i>
                    <span>Panel Pengelola</span>
                </a>
                @endif
            </nav>
        </div>

        <!-- Footer User Profile & Logout -->
        <div class="p-4 border-t border-veloborder bg-[#f1f4f4]">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 velo-box-3 bg-[var(--orange)] text-[var(--ink)] flex items-center justify-center font-black text-xs shadow-xs">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-black text-teal-900 truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-velomutedlight truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <button type="button" @click="showLogoutModal = true"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-extrabold text-red-700 bg-white hover:bg-red-50 border border-red-200 velo-box-3 transition shadow-xs">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Keluar Akun</span>
            </button>
        </div>
    </aside>

    <!-- ==================== MOBILE SIDEBAR DRAWER ==================== -->
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 md:hidden" role="dialog" aria-modal="true">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-teal-950/80 backdrop-blur-xs" @click="sidebarOpen = false"></div>

        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="relative flex flex-col w-72 max-w-full h-full bg-[#0A353E] text-white shadow-2xl">
            <div class="p-4 flex items-center justify-between border-b border-[#0E4B57]">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-9 w-auto" />
                <button type="button" @click="sidebarOpen = false" class="p-1.5 text-teal-300 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <nav class="p-4 space-y-1.5 text-xs flex-1 overflow-y-auto">
                <button type="button" wire:click="setTab('overview')" @click="sidebarOpen = false"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 {{ $activeTab === 'overview' ? 'bg-[#0E4B57] text-white' : 'text-teal-200' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Ringkasan Musim
                </button>
                <button type="button" wire:click="setTab('tickets')" @click="sidebarOpen = false"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 {{ $activeTab === 'tickets' ? 'bg-[#0E4B57] text-white' : 'text-teal-200' }}">
                    <i data-lucide="ticket" class="w-4 h-4"></i> Tiket &amp; BIB Saya
                </button>
                <button type="button" wire:click="setTab('events')" @click="sidebarOpen = false"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 {{ $activeTab === 'events' ? 'bg-[#0E4B57] text-white' : 'text-teal-200' }}">
                    <i data-lucide="calendar-days" class="w-4 h-4"></i> Jadwal Balap
                </button>
                <button type="button" wire:click="setTab('profile')" @click="sidebarOpen = false"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 {{ $activeTab === 'profile' ? 'bg-[#0E4B57] text-white' : 'text-teal-200' }}">
                    <i data-lucide="user-cog" class="w-4 h-4"></i> Profil &amp; Akun
                </button>
                <button type="button" wire:click="setTab('help')" @click="sidebarOpen = false"
                        class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold velo-box-1 {{ $activeTab === 'help' ? 'bg-[#0E4B57] text-white' : 'text-teal-200' }}">
                    <i data-lucide="life-buoy" class="w-4 h-4"></i> Pusat Bantuan
                </button>
                <a href="{{ route('helpdesk.my-tickets') }}" wire:navigate @click="sidebarOpen = false"
                   class="w-full flex items-center gap-3 px-3.5 py-3 font-extrabold text-teal-200 hover:text-white velo-box-1">
                    <i data-lucide="ticket-check" class="w-4 h-4"></i> Tiket Bantuan Saya
                </a>
                <a href="/" wire:navigate class="block px-3.5 py-3 font-bold text-teal-300 velo-box-1">🌐 Kembali ke Website</a>
            </nav>

            <div class="p-4 border-t border-[#0E4B57]">
                <button type="button" @click="showLogoutModal = true; sidebarOpen = false" class="w-full bg-red-950/60 text-red-200 py-2.5 text-xs font-bold velo-box-3 border border-red-800/40">
                    Keluar Akun
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MAIN APPLICATION CANVAS ==================== -->
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">

        <!-- TOP HEADER BAR -->
        <header class="h-20 bg-white border-b border-[#d5d9d8] px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <div class="flex items-center gap-4">
                <button type="button" @click="sidebarOpen = true" class="md:hidden p-2 text-teal-900 hover:bg-[#f1f4f4] velo-box-1">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <!-- Breadcrumb Title -->
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-[#5f6a70]">Portal Rider Velosha</div>
                    <h1 class="text-base sm:text-lg font-black text-teal-900 tracking-tight flex items-center gap-2">
                        @if($activeTab === 'overview') Ringkasan Musim
                        @elseif($activeTab === 'tickets') Tiket &amp; Nomor BIB Saya
                        @elseif($activeTab === 'events') Kalender Balap Terbuka
                        @elseif($activeTab === 'profile') Pengaturan Profil &amp; Data Rider
                        @elseif($activeTab === 'help') Pusat Bantuan &amp; Regulasi
                        @endif
                    </h1>
                </div>
            </div>

            <!-- Right Actions: User Avatar Only (Cari Balap Baru button removed per instruction) -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 velo-box-3 bg-teal-900 text-white flex items-center justify-center font-black text-xs shadow-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden lg:inline-block text-xs font-extrabold text-teal-900">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- MAIN WORKSPACE CONTENT -->
        <main class="p-4 sm:p-6 lg:p-8 flex-grow space-y-6">

            <!-- ================= TAB 1: OVERVIEW ================= -->
            @if($activeTab === 'overview')

                <!-- Welcome Greeting Banner (No Gradients, Solid Dark Teal, Striped Pattern on Right, Real-Time Clock) -->
                <div class="relative bg-[#0A353E] border border-teal-900 velo-box-1 p-6 sm:p-8 text-white shadow-md overflow-hidden">
                    {{-- Gambar Stripe Pattern di Sebelah Kanan --}}
                    <div class="absolute right-0 top-0 bottom-0 w-1/2 bg-right bg-cover bg-no-repeat opacity-30 pointer-events-none"
                         style="background-image: url('/images/dashboard-stripe-pattern.png');"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <div class="inline-block text-[10px] font-extrabold tracking-wider text-[var(--ink)] bg-[var(--orange)] px-2.5 py-1 velo-box-3 mb-3 uppercase">
                                MUSIM VELOSHA 2026/2027
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-black text-white mb-2 tracking-tight">
                                Halo, {{ Auth::user()->name }}! 👋
                            </h2>
                            <p class="text-xs sm:text-sm text-teal-100/90 font-medium max-w-xl leading-relaxed">
                                Satu pintu informasi peserta balap: pantau status pendaftaran, jadwal race terdekat, nomor BIB RFID, dan data akun resmi Anda.
                            </p>
                        </div>

                        {{-- Digantikan dengan JAM REAL-TIME --}}
                        <div x-data="{ clock: '' }"
                             x-init="const updateClock = () => { const now = new Date(); clock = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB'; }; updateClock(); setInterval(updateClock, 1000);"
                             class="shrink-0 bg-[#051C22] border border-teal-700/80 velo-box-3 px-5 py-3.5 text-left md:text-right shadow-sm">
                            <div class="text-[10px] font-extrabold uppercase tracking-widest text-[#EFA96A]">JAM REAL-TIME</div>
                            <div class="text-xl sm:text-2xl font-black text-white font-mono mt-0.5 tracking-wider" x-text="clock"></div>
                        </div>
                    </div>
                </div>

                <!-- KPI Metric Cards (4 Columns Grid — 1px Outer, 3px Inner Icons, Solid White) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

                    <!-- Total Pendaftaran -->
                    <div class="bg-white border border-veloborder velo-box-1 shadow-xs p-5 flex items-center justify-between hover:border-teal-700 transition">
                        <div>
                            <div class="text-[11px] font-extrabold text-[#556066] uppercase tracking-wider">Total Pendaftaran</div>
                            <div class="text-3xl font-black text-teal-900 mt-1">{{ $statTotal }}</div>
                            <div class="text-[11px] text-velomutedlight font-semibold mt-1">Balap terdaftar</div>
                        </div>
                        <div class="w-12 h-12 velo-box-3 bg-teal-50 text-teal-900 border border-teal-200 flex items-center justify-center">
                            <i data-lucide="ticket" class="w-6 h-6 text-teal-800"></i>
                        </div>
                    </div>

                    <!-- Tiket Lunas -->
                    <div class="bg-white border border-veloborder velo-box-1 shadow-xs p-5 flex items-center justify-between hover:border-emerald-600 transition">
                        <div>
                            <div class="text-[11px] font-extrabold text-[#556066] uppercase tracking-wider">Lunas &amp; Aktif</div>
                            <div class="text-3xl font-black text-emerald-700 mt-1">{{ $statPaid }}</div>
                            <div class="text-[11px] text-emerald-800 font-bold mt-1">E-Ticket QR Terbit</div>
                        </div>
                        <div class="w-12 h-12 velo-box-3 bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center">
                            <i data-lucide="badge-check" class="w-6 h-6"></i>
                        </div>
                    </div>

                    <!-- Menunggu Pembayaran -->
                    <div class="bg-white border border-veloborder velo-box-1 shadow-xs p-5 flex items-center justify-between hover:border-race-600 transition">
                        <div>
                            <div class="text-[11px] font-extrabold text-[#556066] uppercase tracking-wider">Menunggu Bayar</div>
                            <div class="text-3xl font-black text-race-600 mt-1">{{ $statPending }}</div>
                            <div class="text-[11px] text-race-700 font-bold mt-1">Segera selesaikan</div>
                        </div>
                        <div class="w-12 h-12 velo-box-3 bg-race-50 text-race-700 border border-race-200 flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                    </div>

                    <!-- BIB Terbit -->
                    <div class="bg-white border border-veloborder velo-box-1 shadow-xs p-5 flex items-center justify-between hover:border-indigo-600 transition">
                        <div>
                            <div class="text-[11px] font-extrabold text-[#556066] uppercase tracking-wider">BIB RFID Terbit</div>
                            <div class="text-3xl font-black text-indigo-800 mt-1">{{ $statBibs }}</div>
                            <div class="text-[11px] text-indigo-900 font-bold mt-1">Nomor dada resmi</div>
                        </div>
                        <div class="w-12 h-12 velo-box-3 bg-indigo-50 text-indigo-800 border border-indigo-200 flex items-center justify-center">
                            <i data-lucide="tag" class="w-6 h-6"></i>
                        </div>
                    </div>

                </div>

                <!-- Split Workspace Grid (Left 2 Columns, Right 1 Column) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- LEFT 2 COLS: Race Countdown & Active Tickets Table -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Hitung Mundur Balap Mendatang -->
                        @if($countdown)
                        <div class="bg-[#0A353E] text-white border border-teal-900 velo-box-1 p-6 relative overflow-hidden shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                                <div class="space-y-2 min-w-0">
                                    <div class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-[#EFA96A]">
                                        <i data-lucide="flame" class="w-3.5 h-3.5"></i>
                                        BALAP REKOMENDASI TERDEKAT
                                    </div>
                                    <h3 class="text-xl font-black truncate text-white">{{ $countdown['event']->title }}</h3>
                                    <div class="flex flex-wrap gap-2 text-xs text-teal-100 font-medium pt-1">
                                        <span class="inline-flex items-center gap-1 bg-teal-950/60 border border-teal-800 px-2.5 py-1 velo-box-3">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-race-400"></i>
                                            {{ \Illuminate\Support\Carbon::parse($countdown['event']->event_date)->translatedFormat('d M Y') }}
                                        </span>
                                        @if($countdown['event']->location_name)
                                        <span class="inline-flex items-center gap-1 bg-teal-950/60 border border-teal-800 px-2.5 py-1 velo-box-3">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-race-400"></i>
                                            {{ $countdown['event']->location_name }}
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="shrink-0 text-left sm:text-right">
                                    <div class="bg-[var(--orange)] text-[var(--ink)] font-black text-2xl sm:text-3xl px-5 py-2.5 velo-box-1 shadow-xs inline-block">
                                        {{ $countdown['days'] <= 0 ? 'HARI INI!' : 'H-'.$countdown['days'] }}
                                    </div>
                                    <div class="text-[10px] text-teal-300 font-extrabold uppercase tracking-wider mt-1.5">Menuju Gun Start</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Recent Registrations Card Table -->
                        <div class="bg-white border border-veloborder velo-box-1 shadow-xs overflow-hidden">
                            <div class="p-6 border-b border-veloborder flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-black text-teal-900 flex items-center gap-2">
                                        <i data-lucide="ticket" class="w-4 h-4 text-race-600"></i>
                                        Pendaftaran Balap Terbaru
                                    </h3>
                                    <p class="text-xs text-velomutedlight">Daftar race yang kamu daftarkan musim ini.</p>
                                </div>
                                <button type="button" wire:click="setTab('tickets')" class="text-xs font-black text-race-600 hover:underline">
                                    Lihat Semua Tiket &rarr;
                                </button>
                            </div>

                            @if($registrations->isEmpty())
                            <div class="p-12 text-center space-y-4">
                                <div class="w-16 h-16 bg-[#f1f4f4] text-velomutedlight velo-box-3 flex items-center justify-center mx-auto border border-veloborder">
                                    <i data-lucide="calendar-search" class="w-8 h-8"></i>
                                </div>
                                <p class="text-xs text-velomutedlight font-medium max-w-sm mx-auto">Kamu belum memiliki pendaftaran balap. Jelajahi kalender balap Velosha untuk mengamankan slot pertamamu!</p>
                                <a href="{{ route('events.index') }}" wire:navigate class="inline-flex items-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-extrabold text-xs px-5 py-2.5 velo-box-1 shadow-xs transition">
                                    <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                    Jelajahi Kalender Balap
                                </a>
                            </div>
                            @else
                            <div class="divide-y divide-veloborder">
                                @foreach($registrations->take(4) as $reg)
                                @php($regEvent = $reg->category?->event)
                                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-[#f1f4f4] transition">
                                    <div class="flex items-start gap-4 min-w-0">
                                        <div class="w-12 h-12 velo-box-3 bg-teal-900 text-white flex flex-col items-center justify-center shrink-0 shadow-xs">
                                            <span class="text-sm font-black leading-none">{{ \Illuminate\Support\Carbon::parse($regEvent?->event_date)->format('d') }}</span>
                                            <span class="text-[9px] font-bold text-teal-300 uppercase">{{ \Illuminate\Support\Carbon::parse($regEvent?->event_date)->translatedFormat('M') }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="text-sm font-black text-teal-900 truncate">{{ $regEvent?->title }}</h4>
                                                <span class="text-[10px] font-extrabold bg-teal-50 text-teal-800 border border-teal-200 px-2 py-0.5 velo-box-3">{{ $reg->category?->name }}</span>
                                            </div>
                                            <div class="text-xs text-velomutedlight flex flex-wrap items-center gap-3 mt-1 font-medium">
                                                <span><i data-lucide="map-pin" class="w-3 h-3 inline text-velomutedlight"></i> {{ $regEvent?->location_name }}</span>
                                                @if($reg->bib_number)
                                                <span class="font-mono font-black text-teal-900 bg-[#f1f4f4] px-2 py-0.5 velo-box-3 border border-veloborder">BIB #{{ $reg->bib_number }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                        @if($reg->isPaid())
                                            <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 px-2.5 py-1 velo-box-3 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> LUNAS
                                            </span>
                                            <a href="{{ route('ticket.show', $reg) }}" class="bg-white hover:bg-[#f1f4f4] text-teal-900 border border-veloborderstrong font-extrabold text-xs px-3.5 py-2 velo-box-1 transition shadow-xs">
                                                Lihat E-Ticket
                                            </a>
                                        @elseif($reg->status === \App\Models\Registration::STATUS_PENDING)
                                            <span class="text-[10px] font-extrabold bg-race-50 text-race-800 border border-race-200 px-2.5 py-1 velo-box-3 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-race-500"></span> MENUNGGU BAYAR
                                            </span>
                                            <a href="{{ route('checkout', ['event' => $regEvent?->slug, 'category' => $reg->event_category_id]) }}" wire:navigate class="bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-extrabold text-xs px-3.5 py-2 velo-box-1 transition shadow-xs">
                                                Bayar Sekarang
                                            </a>
                                        @else
                                            <span class="text-[10px] font-extrabold bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 velo-box-3">
                                                DIBATALKAN
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                    </div>

                    <!-- RIGHT 1 COL: Rider Quick Info & Guidelines -->
                    <div class="space-y-6">

                        <!-- Data Profil Peserta Card -->
                        <div class="bg-white border border-veloborder velo-box-1 shadow-xs p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-veloborder pb-3">
                                <h3 class="text-sm font-black text-teal-900 flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-race-600"></i>
                                    Profil Peserta
                                </h3>
                                <button type="button" wire:click="setTab('profile')" class="text-xs font-black text-race-600 hover:underline">
                                    Edit Profil
                                </button>
                            </div>

                            <dl class="space-y-3 text-xs">
                                <div class="flex justify-between">
                                    <dt class="text-velomutedlight font-semibold">Nama Lengkap</dt>
                                    <dd class="font-black text-teal-900">{{ Auth::user()->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-velomutedlight font-semibold">Email</dt>
                                    <dd class="font-black text-teal-900">{{ Auth::user()->email }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-velomutedlight font-semibold">Nomor Telepon</dt>
                                    <dd class="font-black text-teal-900">{{ Auth::user()->phone ?? 'Belum diisi' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-velomutedlight font-semibold">Golongan Darah</dt>
                                    <dd class="font-black text-teal-900 uppercase">{{ Auth::user()->blood_type ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Pengumuman Panitia Card -->
                        <div class="bg-[#0A353E] text-white border border-teal-900 velo-box-1 p-6 space-y-3 shadow-xs">
                            <div class="flex items-center gap-2 text-[#EFA96A] font-extrabold text-xs uppercase tracking-wider">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                PENGUMUMAN RESMI
                            </div>
                            <h4 class="text-sm font-black text-white">Panduan Pengambilan Race Pack &amp; BIB RFID</h4>
                            <p class="text-xs text-teal-100/90 leading-relaxed font-medium">
                                Pengambilan Race Pack wajib menunjukkan E-Ticket QR resmi dari portal ini dan kartu identitas asli (KTP/SIM/Paspor).
                            </p>
                            <button type="button" wire:click="setTab('help')" class="inline-block text-xs font-black text-[#EFA96A] hover:text-white pt-1">
                                Baca Regulasi Selengkapnya &rarr;
                            </button>
                        </div>

                    </div>

                </div>

            <!-- ================= TAB 2: TICKETS ================= -->
            @elseif($activeTab === 'tickets')

                <div class="bg-white border border-veloborder velo-box-1 shadow-xs p-6 space-y-6">

                    <!-- Filter Bar & Search -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-veloborder pb-5">
                        <!-- Filter Tabs -->
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="filterTickets('all')"
                                    class="px-4 py-2 text-xs font-extrabold velo-box-3 transition {{ $ticketStatus === 'all' ? 'bg-[var(--orange)] text-[var(--ink)] shadow-xs' : 'bg-white border border-veloborder text-velomutedlight hover:bg-[#f1f4f4]' }}">
                                Semua Tiket ({{ $statTotal }})
                            </button>
                            <button type="button" wire:click="filterTickets('paid')"
                                    class="px-4 py-2 text-xs font-extrabold velo-box-3 transition {{ $ticketStatus === 'paid' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white border border-veloborder text-velomutedlight hover:bg-[#f1f4f4]' }}">
                                Lunas ({{ $statPaid }})
                            </button>
                            <button type="button" wire:click="filterTickets('pending')"
                                    class="px-4 py-2 text-xs font-extrabold velo-box-3 transition {{ $ticketStatus === 'pending' ? 'bg-[var(--orange)] text-[var(--ink)] shadow-xs' : 'bg-white border border-veloborder text-velomutedlight hover:bg-[#f1f4f4]' }}">
                                Menunggu Bayar ({{ $statPending }})
                            </button>
                            <button type="button" wire:click="filterTickets('cancelled')"
                                    class="px-4 py-2 text-xs font-extrabold velo-box-3 transition {{ $ticketStatus === 'cancelled' ? 'bg-red-700 text-white shadow-xs' : 'bg-white border border-veloborder text-velomutedlight hover:bg-[#f1f4f4]' }}">
                                Dibatalkan ({{ $statCancelled }})
                            </button>
                        </div>

                        <!-- Search Input -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama balap / BIB..."
                                   class="w-full pl-9 pr-4 py-2 text-xs border border-veloborderstrong velo-box-3 focus:ring-2 focus:ring-teal-600 focus:outline-none bg-white text-teal-900 font-medium">
                            <i data-lucide="search" class="w-4 h-4 text-velomutedlight absolute left-3 top-2.5"></i>
                        </div>
                    </div>

                    <!-- Ticket Cards / Table Grid -->
                    @if($filteredRegistrations->isEmpty())
                        <div class="py-12 text-center space-y-3">
                            <div class="w-12 h-12 bg-[#f1f4f4] text-velomutedlight velo-box-3 flex items-center justify-center mx-auto border border-veloborder">
                                <i data-lucide="search-x" class="w-6 h-6"></i>
                            </div>
                            <p class="text-xs text-velomutedlight font-medium">Tidak ada pendaftaran tiket yang sesuai dengan filter ini.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($filteredRegistrations as $reg)
                            @php($regEvent = $reg->category?->event)
                            <div class="p-5 border border-veloborder velo-box-1 bg-white hover:border-teal-700 transition shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-5">
                                <div class="flex items-start gap-4 min-w-0">
                                    <div class="w-14 h-14 velo-box-3 bg-teal-900 text-white flex flex-col items-center justify-center shrink-0 shadow-xs">
                                        <span class="text-base font-black leading-none">{{ \Illuminate\Support\Carbon::parse($regEvent?->event_date)->format('d') }}</span>
                                        <span class="text-[10px] font-bold text-teal-300 uppercase">{{ \Illuminate\Support\Carbon::parse($regEvent?->event_date)->translatedFormat('M Y') }}</span>
                                    </div>
                                    <div class="space-y-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="text-base font-black text-teal-900 truncate">{{ $regEvent?->title }}</h4>
                                            <span class="text-xs font-extrabold bg-teal-50 text-teal-800 border border-teal-200 px-2.5 py-0.5 velo-box-3">{{ $reg->category?->name }}</span>
                                        </div>
                                        <div class="text-xs text-velomutedlight flex flex-wrap items-center gap-4 font-medium">
                                            <span><i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-velomutedlight"></i> {{ $regEvent?->location_name }}</span>
                                            @if($reg->bib_number)
                                            <span class="font-mono font-black text-teal-900 bg-[#f1f4f4] px-2 py-0.5 velo-box-3 border border-veloborder">BIB #{{ $reg->bib_number }}</span>
                                            @endif
                                            <span>Nomor Transaksi: <span class="font-mono text-teal-900 font-bold">#REG-{{ $reg->id }}</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between md:justify-end gap-4 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-veloborder">
                                    <div>
                                        @if($reg->isPaid())
                                            <span class="text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 px-3 py-1 velo-box-3 inline-flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> LUNAS &amp; AKTIF
                                            </span>
                                        @elseif($reg->status === \App\Models\Registration::STATUS_PENDING)
                                            <span class="text-xs font-extrabold bg-race-50 text-race-800 border border-race-200 px-3 py-1 velo-box-3 inline-flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-race-500"></span> MENUNGGU BAYAR
                                            </span>
                                        @else
                                            <span class="text-xs font-extrabold bg-red-50 text-red-700 border border-red-200 px-3 py-1 velo-box-3">
                                                DIBATALKAN
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        @if($reg->isPaid())
                                            <a href="{{ route('ticket.show', $reg) }}" class="bg-teal-900 hover:bg-teal-800 text-white font-extrabold text-xs px-4 py-2.5 velo-box-1 transition shadow-xs inline-flex items-center gap-2">
                                                <i data-lucide="qr-code" class="w-4 h-4 text-[#EFA96A]"></i>
                                                Lihat E-Ticket QR
                                            </a>
                                        @elseif($reg->status === \App\Models\Registration::STATUS_PENDING)
                                            <a href="{{ route('checkout', ['event' => $regEvent?->slug, 'category' => $reg->event_category_id]) }}" wire:navigate class="bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-extrabold text-xs px-4 py-2.5 velo-box-1 transition shadow-xs inline-flex items-center gap-2">
                                                <i data-lucide="credit-card" class="w-4 h-4"></i>
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
                            <h3 class="text-lg font-black text-teal-900">Kalender Balap Velosha</h3>
                            <p class="text-xs text-velomutedlight">Daftar event terbuka untuk musim balap endurance 2026/2027.</p>
                        </div>
                        <a href="{{ route('events.index') }}" wire:navigate class="text-xs font-black text-race-600 hover:underline">
                            Lihat Katalog Lengkap &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($openEvents as $event)
                        <div class="bg-white border border-veloborder velo-box-1 shadow-xs overflow-hidden flex flex-col hover:border-teal-700 transition">
                            <div class="h-2 bg-teal-900"></div>
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-race-600">
                                        {{ \Illuminate\Support\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}
                                    </div>
                                    <h4 class="text-base font-black text-teal-900 leading-snug">{{ $event->title }}</h4>
                                    <p class="text-xs text-velomutedlight flex items-center gap-1 font-medium">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-velomutedlight"></i>
                                        {{ $event->location_name }}
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-veloborder flex items-center justify-between">
                                    <div class="text-xs font-black text-teal-900">
                                        {{ $event->categories->count() }} Kategori Jarak
                                    </div>
                                    <a href="{{ route('events.show', $event->slug) }}" wire:navigate class="bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-extrabold text-xs px-4 py-2 velo-box-1 transition shadow-xs">
                                        Daftar Race
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            <!-- ================= TAB 4: PROFILE ================= -->
            @elseif($activeTab === 'profile')

                <div class="max-w-2xl bg-white border border-veloborder velo-box-1 shadow-xs p-6 sm:p-8 space-y-6">
                    <div>
                        <h3 class="text-lg font-black text-teal-900">Pengaturan Data Profil Peserta</h3>
                        <p class="text-xs text-velomutedlight">Perbarui data diri untuk keperluan registrasi &amp; asuransi keselamatan race.</p>
                    </div>

                    @if($profileSaved)
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 velo-box-3 text-xs font-extrabold flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        Profil berhasil diperbarui!
                    </div>
                    @endif

                    <form wire:submit.prevent="updateProfile" class="space-y-4">
                        <div>
                            <label class="block text-xs font-extrabold text-teal-900 mb-1">Nama Lengkap</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2.5 text-xs border border-veloborderstrong velo-box-3 focus:ring-2 focus:ring-teal-600 focus:outline-none text-teal-900">
                            @error('name') <span class="text-[10px] text-red-600 font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-teal-900 mb-1">Nomor WhatsApp / HP (Darurat)</label>
                            <input type="text" wire:model="phone" placeholder="081234567890" class="w-full px-4 py-2.5 text-xs border border-veloborderstrong velo-box-3 focus:ring-2 focus:ring-teal-600 focus:outline-none text-teal-900">
                            @error('phone') <span class="text-[10px] text-red-600 font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-teal-900 mb-1">Jenis Kelamin</label>
                                <select wire:model="gender" class="w-full px-4 py-2.5 text-xs border border-veloborderstrong velo-box-3 focus:ring-2 focus:ring-teal-600 focus:outline-none text-teal-900">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="male">Laki-Laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-teal-900 mb-1">Golongan Darah</label>
                                <select wire:model="blood_type" class="w-full px-4 py-2.5 text-xs border border-veloborderstrong velo-box-3 focus:ring-2 focus:ring-teal-600 focus:outline-none text-teal-900">
                                    <option value="">Pilih Golongan Darah</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-teal-900 mb-1">Tanggal Lahir</label>
                            <input type="date" wire:model="birth_date" class="w-full px-4 py-2.5 text-xs border border-veloborderstrong velo-box-3 focus:ring-2 focus:ring-teal-600 focus:outline-none text-teal-900">
                        </div>

                        <div class="pt-4 border-t border-veloborder flex justify-end">
                            <button type="submit" class="bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-extrabold text-xs px-6 py-3 velo-box-1 shadow-xs transition flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

            <!-- ================= TAB 5: HELP ================= -->
            @elseif($activeTab === 'help')

                <div class="max-w-3xl bg-white border border-veloborder velo-box-1 shadow-xs p-6 sm:p-8 space-y-6">
                    <div>
                        <h3 class="text-lg font-black text-teal-900">Pusat Bantuan &amp; Pertanyaan Peserta</h3>
                        <p class="text-xs text-velomutedlight">Informasi resmi seputar E-Ticket, Race Pack, dan Regulasi Balap Velosha.</p>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div class="p-4 bg-[#f1f4f4] border border-veloborder velo-box-3">
                            <h4 class="font-black text-teal-900 mb-1">Bagaimana cara mencetak E-Ticket?</h4>
                            <p class="text-velomutedlight leading-relaxed font-medium">Masuk ke menu Tiket &amp; BIB Saya, klik tombol "Lihat E-Ticket QR" pada race yang berstatus LUNAS. E-Ticket berbentuk QR Code resmi dapat disimpan ke ponsel atau dicetak.</p>
                        </div>

                        <div class="p-4 bg-[#f1f4f4] border border-veloborder velo-box-3">
                            <h4 class="font-black text-teal-900 mb-1">Kapan Nomor BIB RFID diterbitkan?</h4>
                            <p class="text-velomutedlight leading-relaxed font-medium">Nomor BIB RFID diterbitkan secara otomatis oleh sistem panitia setelah konfirmasi pembayaran diverifikasi.</p>
                        </div>

                        <div class="p-4 bg-[#f1f4f4] border border-veloborder velo-box-3">
                            <h4 class="font-black text-teal-900 mb-1">Apakah tiket yang sudah dibeli bisa dibatalkan / di-transfer?</h4>
                            <p class="text-velomutedlight leading-relaxed font-medium">Sesuai regulasi keselamatan balap Velosha, pendaftaran bersifat personal dan tidak dapat dipindahtangankan tanpa persetujuan resmi panitia.</p>
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
        <div class="absolute inset-0 bg-[#0A353E]/80 backdrop-blur-xs" @click="showLogoutModal = false"></div>

        <!-- Dialog Box -->
        <div x-show="showLogoutModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative bg-white border border-veloborder velo-box-1 shadow-2xl w-full max-w-sm overflow-hidden">

            <div class="h-1.5 bg-red-600 w-full"></div>

            <div class="p-6 text-center space-y-4">
                <div class="w-14 h-14 mx-auto bg-red-50 text-red-600 velo-box-3 border border-red-200 flex items-center justify-center">
                    <i data-lucide="log-out" class="w-7 h-7"></i>
                </div>

                <div>
                    <h3 class="text-base font-black text-teal-900">Konfirmasi Keluar</h3>
                    <p class="text-xs text-velomutedlight font-medium mt-1">
                        Apakah Anda yakin ingin keluar dari portal rider Velosha?
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" @click="showLogoutModal = false"
                            class="px-5 py-2.5 text-xs font-bold text-teal-900 bg-[#f1f4f4] hover:bg-velosurface velo-box-1 border border-veloborder transition">
                        Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-5 py-2.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 velo-box-1 shadow-xs transition flex items-center gap-2">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
