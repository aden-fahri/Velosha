@extends('layouts.app', ['title' => 'Portal Penyelenggara — ' . $organization->name, 'nav' => false])

@section('content')
<div x-data="{ sidebarOpen: false, showLogoutModal: false }" class="vs min-h-screen bg-[var(--paper)] text-[var(--ink)] flex flex-col md:flex-row" style="display: flex; min-height: 100vh;">

    {{-- ==================== DESKTOP SIDEBAR ==================== --}}
    <aside class="side hidden md:flex flex-col justify-between bg-[#FBF4E6] border-r-[3px] border-[#10282E] p-6 sticky top-0 h-screen overflow-y-auto overflow-x-hidden" style="width: 280px; min-width: 280px; max-width: 280px; flex-shrink: 0;">
        <div>
            {{-- Brand Logo Header (Official Brand Asset) --}}
            <div class="flex items-center pb-5 mb-4 border-b border-[#10282E]/15">
                <a href="/" wire:navigate class="flex items-center gap-3 transition-transform duration-150 hover:-translate-y-0.5" style="text-decoration: none" aria-label="Velosha — Beranda">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-8 w-auto object-contain" />
                    <span class="font-extrabold text-2xl tracking-tight text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
                </a>
            </div>

            {{-- Community Card --}}
            <div class="px-3.5 py-3 rounded-xl bg-white border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] mb-4">
                <div class="text-[10px] font-black uppercase text-[var(--mute)] tracking-wider">KOMUNITAS</div>
                <div class="font-heading font-extrabold text-sm text-[var(--ink)] truncate mt-0.5">
                    {{ $organization->name }}
                </div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold text-[var(--pine)] mt-1">
                    <span class="w-2 h-2 rounded-full bg-[var(--pine)] inline-block"></span>
                    <span>{{ $balance }} Kuota Event</span>
                </div>
            </div>

            {{-- Navigation Links --}}
            <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 mb-2 mt-2">NAVIGASI</div>
            <nav class="space-y-1 text-sm font-semibold">
                {{-- 1. Ringkasan & Statistik --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'overview']) }}"
                   wire:navigate.hover
                   class="w-full flex items-center justify-between {{ in_array($activeTab, ['overview', '']) ? 'on' : '' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Ringkasan EO</span>
                    </div>
                </a>

                {{-- 2. Buat Event Baru --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}"
                   wire:navigate.hover
                   class="w-full flex items-center justify-between {{ $activeTab === 'create_event' ? 'on' : '' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Buat Event</span>
                    </div>
                </a>

                {{-- 3. Event Saya --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
                   wire:navigate.hover
                   class="w-full flex items-center gap-2.5 {{ $activeTab === 'events' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Event Saya</span>
                </a>

                {{-- 4. Paket & Kuota --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']) }}"
                   wire:navigate.hover
                   class="w-full flex items-center gap-2.5 {{ $activeTab === 'kuota' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path><path d="M12 18V6"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Paket &amp; Kuota</span>
                </a>

                {{-- 5. Keuangan & Payout --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'keuangan']) }}"
                   wire:navigate.hover
                   class="w-full flex items-center gap-2.5 {{ $activeTab === 'keuangan' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Keuangan &amp; Payout</span>
                </a>

                {{-- 6. Tiket Bantuan / Chat Petugas --}}
                <a href="{{ route('helpdesk.create-ticket', ['from' => 'organizer']) }}" wire:navigate.hover class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Chat / Bantuan</span>
                </a>

                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">BERALIH</div>

                <a href="{{ route('dashboard') }}" wire:navigate class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1-5 15-5 16 0"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Dashboard Rider</span>
                </a>

                <a href="/" wire:navigate class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Kembali ke website</span>
                </a>
            </nav>
        </div>

        {{-- Footer User Profile & Logout --}}
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
                <button type="button" @click="sidebarOpen = false" class="p-1.5 text-[var(--ink)]">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <nav class="space-y-1.5 text-sm flex-1">
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'overview']) }}"
                   wire:navigate.hover
                   class="w-full {{ in_array($activeTab, ['overview', '']) ? 'on' : '' }}">
                    Ringkasan EO
                </a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}"
                   wire:navigate.hover
                   class="w-full {{ $activeTab === 'create_event' ? 'on' : '' }}">
                    Buat Event
                </a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
                   wire:navigate.hover
                   class="w-full {{ $activeTab === 'events' ? 'on' : '' }}">
                    Event Saya
                </a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']) }}"
                   wire:navigate.hover
                   class="w-full {{ $activeTab === 'kuota' ? 'on' : '' }}">
                    Paket &amp; Kuota
                </a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'keuangan']) }}"
                   wire:navigate.hover
                   class="w-full {{ $activeTab === 'keuangan' ? 'on' : '' }}">
                    Keuangan &amp; Payout
                </a>
                <a href="{{ route('helpdesk.create-ticket', ['from' => 'organizer']) }}" wire:navigate.hover class="w-full">
                    Chat / Bantuan CS
                </a>
                <a href="{{ route('dashboard') }}" wire:navigate.hover class="w-full mt-4">
                    Kembali ke Dashboard Rider
                </a>
            </nav>

            <div class="pt-6 border-t border-[#10282E]/15 mt-auto">
                <button type="button" @click="showLogoutModal = true; sidebarOpen = false" class="btn btn-ghost btn-block btn-sm text-[#C23B22]">
                    Keluar akun
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== MAIN APPLICATION CANVAS ==================== --}}
    <div class="flex-1 min-w-0 flex flex-col min-h-screen" style="flex: 1 1 0%; min-width: 0;">

        {{-- Mobile Top Bar --}}
        <header class="md:hidden h-16 bg-[#FBF4E6] border-b-[2.5px] border-[#10282E] px-4 flex items-center justify-between sticky top-0 z-20">
            <button type="button" @click="sidebarOpen = true" class="p-2 text-[var(--ink)]">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <span class="font-heading font-black text-lg">{{ $organization->name }}</span>
            <span class="disp w-8 h-8 border-2 border-[#10282E] rounded-md bg-[var(--orange)] flex items-center justify-center font-bold text-xs text-[var(--ink)]">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
        </header>

        {{-- Main Workspace Content --}}
        <main class="p-6 sm:p-10 lg:p-12 flex-grow space-y-6 max-w-7xl">

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="card p-4 bg-[var(--pine-l)] border-2 border-[var(--ink)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)] font-bold text-sm flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-xs font-black">&check;</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="card p-4 bg-[var(--red-l)] border-2 border-[var(--ink)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)] font-bold text-sm flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[var(--red)] text-white flex items-center justify-center text-xs font-black">&times;</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- ==================== TAB: OVERVIEW (RINGKASAN EO) ==================== --}}
            @if (in_array($activeTab, ['overview', '']))
                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">PORTAL PENYELENGGARA</div>
                    <h1 class="h3 font-heading text-3xl font-black mt-0.5">Ringkasan Komunitas &amp; Event</h1>
                </div>

                {{-- Sapaan Kartun Screen-Print Oranye Solid (#DE7119) Sesuai Desain Asli --}}
                <section class="card bg-[#DE7119] p-8 sm:p-9 relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6 min-h-[190px]">
                    <div class="dots absolute inset-0 opacity-40 pointer-events-none"></div>

                    <div class="relative z-10">
                        <span class="chip">{{ $organization->name }}</span>
                        <h2 class="h2 text-3xl sm:text-[44px] mt-3.5 text-[var(--ink)]">
                            Halo, {{ Auth::user()->name }}!
                        </h2>
                        <p class="mt-2.5 max-w-[500px] text-base font-medium text-[var(--ink)] leading-relaxed">
                            Kelola jadwal balap sepeda komunitasmu, pantau kuota aktif, dan ajukan pencairan dana tiket langsung.
                        </p>
                    </div>

                    {{-- Jam Real-Time --}}
                    <div x-data="{ clock: '' }"
                         x-init="const updateClock = () => { const now = new Date(); clock = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB'; }; updateClock(); setInterval(updateClock, 1000);"
                         class="relative z-10 px-5 py-3.5 border-[2.5px] border-[#10282E] rounded-[10px] bg-[#10282E] text-[#FBF4E6] text-center shrink-0 shadow-[3px_3px_0_#10282E]">
                        <div class="text-[11px] font-bold tracking-[.1em] text-[#F4BE3F] uppercase">JAM REAL-TIME</div>
                        <div class="mono text-2xl sm:text-[28px] font-bold mt-1 text-[#FBF4E6]" x-text="clock">07.00.00 WIB</div>
                    </div>
                </section>

                {{-- Statistik 4 Box Utama Penyelenggara (Redesain Bersih & Tegas Neobrutalism) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- 1. Total Event --}}
                    <div class="p-6 bg-white border-[3px] border-[var(--ink)] shadow-[5px_5px_0_var(--ink)] rounded-2xl flex flex-col justify-between hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black tracking-wider text-[var(--mute)] uppercase">TOTAL EVENT</span>
                            <div class="w-10 h-10 rounded-xl bg-[var(--butter)] border-2 border-[var(--ink)] flex items-center justify-center text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="font-heading font-black text-4xl text-[var(--ink)] tracking-tight">{{ $events->count() }}</div>
                            <div class="text-xs text-[var(--mute)] font-bold mt-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[var(--pine)] inline-block"></span>
                                <span>Balap dikelola komunitas</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Sisa Kuota --}}
                    <div class="p-6 bg-white border-[3px] border-[var(--ink)] shadow-[5px_5px_0_var(--ink)] rounded-2xl flex flex-col justify-between hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black tracking-wider text-[var(--mute)] uppercase">SISA KUOTA</span>
                            <div class="w-10 h-10 rounded-xl bg-[var(--mustard)] border-2 border-[var(--ink)] flex items-center justify-center text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8M8 12h8"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="font-heading font-black text-4xl {{ $balance > 0 ? 'text-[var(--pine)]' : 'text-red-600' }} tracking-tight">
                                {{ $balance }}
                            </div>
                            <div class="text-xs text-[var(--mute)] font-bold mt-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $balance > 0 ? 'bg-[var(--pine)]' : 'bg-red-500' }} inline-block"></span>
                                <span>Race siap dipublikasikan</span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Penjualan Kotor --}}
                    <div class="p-6 bg-white border-[3px] border-[var(--ink)] shadow-[5px_5px_0_var(--ink)] rounded-2xl flex flex-col justify-between hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black tracking-wider text-[var(--mute)] uppercase">PENJUALAN KOTOR</span>
                            <div class="w-10 h-10 rounded-xl bg-[var(--orange)] border-2 border-[var(--ink)] flex items-center justify-center text-white shadow-[2px_2px_0_var(--ink)]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="font-heading font-black text-2xl sm:text-3xl text-[var(--ink)] tracking-tight">
                                Rp {{ number_format($totalSales, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-[var(--mute)] font-bold mt-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[var(--mustard)] inline-block"></span>
                                <span>Total transaksi tiket</span>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Saldo Siap Tarik --}}
                    <div class="p-6 bg-white border-[3px] border-[var(--ink)] shadow-[5px_5px_0_var(--ink)] rounded-2xl flex flex-col justify-between hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black tracking-wider text-[var(--mute)] uppercase">SALDO SIAP TARIK</span>
                            <div class="w-10 h-10 rounded-xl bg-[#E6F4EA] border-2 border-[var(--ink)] flex items-center justify-center text-[var(--pine)] shadow-[2px_2px_0_var(--ink)]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="font-heading font-black text-2xl sm:text-3xl text-[var(--pine)] tracking-tight">
                                Rp {{ number_format($availableBalance, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-[var(--mute)] font-bold mt-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[var(--pine)] inline-block"></span>
                                <span>Bebas dicairkan ke rekening</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Quick Links --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] flex flex-col justify-between space-y-4">
                        <div>
                            <span class="chip m">Aksi Cepat</span>
                            <h3 class="font-heading text-xl font-black text-[var(--ink)] mt-2">Buat &amp; Publikasikan Event Baru</h3>
                            <p class="text-xs sm:text-sm text-[var(--mute)] font-medium mt-1">
                                Siapkan nama event, tanggal pelaksanaan, rute balap, dan kategori tiket untuk membuka pendaftaran peserta rider.
                            </p>
                        </div>
                        <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}"
                           class="btn w-fit">
                            <span>+ Buat Event Sekarang</span>
                        </a>
                    </div>

                    <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] flex flex-col justify-between space-y-4">
                        <div>
                            <span class="chip p">Manajemen Kuota</span>
                            <h3 class="font-heading text-xl font-black text-[var(--ink)] mt-2">Paket Kuota Event Tanpa Batas</h3>
                            <p class="text-xs sm:text-sm text-[var(--mute)] font-medium mt-1">
                                Kuota berlaku selamanya tanpa kedaluwarsa. Dapatkan paket Single (1 race), Komunitas (3 race), atau Pro (10 race).
                            </p>
                        </div>
                        <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']) }}"
                           class="btn btn-mustard w-fit">
                            <span>Beli Kuota Event &rarr;</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- ==================== TAB: BUAT EVENT BARU ==================== --}}
            @if ($activeTab === 'create_event')
                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">PORTAL PENYELENGGARA</div>
                    <h1 class="h3 font-heading text-3xl font-black mt-0.5">Buat Event Balap Sepeda</h1>
                </div>

                @if ($balance <= 0)
                    {{-- Kuota 0: Tampilkan Panduan Pembelian Paket Kuota --}}
                    <div class="card p-8 bg-white shadow-[5px_5px_0_#10282E] space-y-6">
                        <div>
                            <span class="chip o">Kuota Event Kosong</span>
                            <h2 class="font-heading text-2xl font-black text-[var(--ink)] mt-2">
                                Beli Kuota Event untuk Membuka Formulir
                            </h2>
                            <p class="text-xs sm:text-sm text-[var(--mute)] font-medium mt-1 max-w-xl">
                                Anda membutuhkan minimal 1 kuota event untuk dapat mempublikasikan balap sepeda di Velosha. Pilih salah satu paket di bawah ini:
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                            @foreach ($packages as $pkg)
                                @php($isPop = $pkg->events_count === 3)
                                <div class="card p-6 {{ $isPop ? 'border-[3px] border-[var(--orange)] shadow-[6px_6px_0_#10282E]' : 'bg-[var(--paper)]' }} flex flex-col justify-between space-y-5">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-heading font-black text-lg text-[var(--ink)]">{{ $pkg->name }}</h3>
                                            <span class="chip m text-[10px]">{{ $pkg->events_count }} Race</span>
                                        </div>
                                        <div class="disp text-3xl font-black text-[var(--ink)]">
                                            Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                        </div>
                                        <p class="text-xs text-[var(--mute)] leading-relaxed min-h-[48px] pb-3">{{ $pkg->description }}</p>
                                    </div>

                                    <form action="{{ route('organizations.quota.purchase', $organization) }}" method="POST" onsubmit="buyQuotaSnap(event, this)" class="mt-6 pt-2">
                                        @csrf
                                        <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                        <input type="hidden" name="redirect_to" value="events.create">
                                        <button type="submit" class="btn btn-block {{ $isPop ? '' : 'btn-ghost' }}">
                                            Beli &amp; Buat Event &rarr;
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Kuota > 0: Formulir Input Event Lengkap --}}
                    <div class="card p-8 sm:p-10 bg-white shadow-[5px_5px_0_#10282E] space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-[var(--ink)]/15">
                            <div>
                                <h2 class="font-heading text-2xl font-black text-[var(--ink)]">Informasi Balap Sepeda Baru</h2>
                                <p class="text-xs sm:text-sm text-[var(--mute)] font-medium">Pembuatan event akan langsung memotong 1 kuota Anda (Sisa kuota: {{ $balance }} Race).</p>
                            </div>
                            <span class="chip p">{{ $balance }} Kuota Siap Pakai</span>
                        </div>

                        <form action="{{ route('organizations.events.store', $organization) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <div>
                                <label for="title" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                    Judul / Nama Event <span class="text-[var(--red)]">*</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus
                                       placeholder="Contoh: Tour de Merapi 2026"
                                       class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="type" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        Jenis Event <span class="text-[var(--red)]">*</span>
                                    </label>
                                    <select id="type" name="type" required class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                        <option value="gran_fondo" {{ old('type') === 'gran_fondo' ? 'selected' : '' }}>Gran Fondo</option>
                                        <option value="medio_fondo" {{ old('type') === 'medio_fondo' ? 'selected' : '' }}>Medio Fondo</option>
                                        <option value="fun_ride" {{ old('type') === 'fun_ride' ? 'selected' : '' }}>Fun Ride</option>
                                        <option value="climb" {{ old('type') === 'climb' ? 'selected' : '' }}>King of Mountain / Climb</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="event_date" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        Tanggal Pelaksanaan <span class="text-[var(--red)]">*</span>
                                    </label>
                                    <input type="date" id="event_date" name="event_date" value="{{ old('event_date') }}" required
                                           class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                </div>

                                <div>
                                    <label for="start_time" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        Waktu Start (WIB) <span class="text-[var(--red)]">*</span>
                                    </label>
                                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '06:00') }}" required
                                           class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                </div>
                            </div>

                            <div>
                                <label for="location_name" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                    Lokasi Start / Kota <span class="text-[var(--red)]">*</span>
                                </label>
                                <input type="text" id="location_name" name="location_name" value="{{ old('location_name') }}" required
                                       placeholder="Contoh: Alun-alun Kota Magelang, Jawa Tengah"
                                       class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                            </div>

                            <div>
                                <label for="poster" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                    Foto / Poster Resmi Event (Tampil di Beranda &amp; Katalog)
                                </label>
                                <div class="p-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)]">
                                    <input type="file" id="poster" name="poster" accept="image/*"
                                           class="block w-full text-xs font-bold text-[var(--ink)] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-2 file:border-[var(--ink)] file:text-xs file:font-black file:bg-[var(--mustard)] file:text-[var(--ink)] file:shadow-[2px_2px_0_var(--ink)] file:cursor-pointer hover:file:bg-[var(--butter)]">
                                    <span class="text-[11px] text-[var(--mute)] font-medium mt-1.5 block">Format: JPG, PNG, atau WEBP (Maksimal 3 MB). Gambar ini akan otomatis muncul sebagai cover balap di landing page Velosha.</span>
                                </div>
                            </div>

                            {{-- Kategori Tiket Balap (Utama & Tambahan) --}}
                            <div x-data="{
                                categories: [
                                    { name: 'Gran Fondo 120 KM', distance_km: 120, price: 250000, quota: 500 }
                                ],
                                addCategory() {
                                    this.categories.push({ name: '', distance_km: 60, price: 150000, quota: 200 });
                                },
                                removeCategory(idx) {
                                    if (idx > 0) this.categories.splice(idx, 1);
                                }
                            }" class="space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-[var(--ink)]/15">
                                    <div>
                                        <h3 class="font-heading font-black text-base text-[var(--ink)]">Kategori Tiket Balap</h3>
                                        <p class="text-xs text-[var(--mute)] font-medium">Tentukan kategori tiket utama dan Anda dapat menambahkan beberapa kategori jarak/kelas balap lainnya.</p>
                                    </div>
                                    <button type="button" @click="addCategory()" class="btn btn-sm btn-mustard">
                                        <span>+ Tambah Kategori Tiket</span>
                                    </button>
                                </div>

                                <template x-for="(cat, idx) in categories" :key="idx">
                                    <div class="p-5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span x-show="idx === 0" class="px-2.5 py-1 bg-[var(--mustard)] border-2 border-[var(--ink)] font-black text-[11px] uppercase tracking-wider rounded-md shadow-[1px_1px_0_var(--ink)]">
                                                Kategori Tiket Utama
                                            </span>
                                            <span x-show="idx > 0" class="px-2.5 py-1 bg-[var(--butter)] border-2 border-[var(--ink)] font-black text-[11px] uppercase tracking-wider rounded-md shadow-[1px_1px_0_var(--ink)]">
                                                Kategori Tambahan #<span x-text="idx + 1"></span>
                                            </span>
                                            <button type="button" x-show="idx > 0" @click="removeCategory(idx)"
                                                    class="px-2.5 py-1 rounded-lg border-2 border-[var(--ink)] bg-red-100 text-red-700 hover:bg-red-200 text-xs font-black shadow-[1px_1px_0_var(--ink)]">
                                                &times; Hapus Kategori
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Nama Kategori <span class="text-red-600">*</span></label>
                                                <input type="text" :name="'categories[' + idx + '][name]'" x-model="cat.name" required
                                                       placeholder="Contoh: Gran Fondo 120 KM"
                                                       class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)]">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Jarak Tempuh (KM)</label>
                                                <input type="number" step="0.1" :name="'categories[' + idx + '][distance_km]'" x-model="cat.distance_km"
                                                       class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)]">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Harga Tiket (Rp) <span class="text-red-600">*</span></label>
                                                <input type="number" :name="'categories[' + idx + '][price]'" x-model="cat.price" required min="0"
                                                       class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)]">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Kuota Peserta <span class="text-red-600">*</span></label>
                                                <input type="number" :name="'categories[' + idx + '][quota]'" x-model="cat.quota" required min="1"
                                                       class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)]">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div>
                                    <button type="button" @click="addCategory()" class="btn btn-sm btn-ghost border-2 border-dashed border-[var(--ink)] hover:border-solid w-full sm:w-auto">
                                        + Tambah Kategori Tiket
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                    Deskripsi Event &amp; Fasilitas Race <span class="text-[var(--red)]">*</span>
                                </label>
                                <textarea id="description" name="description" rows="3" required
                                          placeholder="Tuliskan gambaran rute, water station, jersey finisher, dan regulasi balap..."
                                          class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">{{ old('description') }}</textarea>
                            </div>

                            <button type="submit" class="btn w-full sm:w-auto">
                                Simpan &amp; Publikasikan Event &rarr;
                            </button>
                        </form>
                    </div>
                @endif
            @endif

            {{-- ==================== TAB: EVENT SAYA ==================== --}}
            @if ($activeTab === 'events')
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">PORTAL PENYELENGGARA</div>
                        <h1 class="h3 font-heading text-3xl font-black mt-0.5">Daftar Event Dikelola</h1>
                    </div>
                    <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}" class="btn btn-sm">
                        + Buat Event
                    </a>
                </div>

                @if ($events->isEmpty())
                    <div class="card p-10 bg-white shadow-[4px_4px_0_#10282E] text-center space-y-4 max-w-xl mx-auto my-6">
                        <div class="w-16 h-16 rounded-2xl bg-[var(--butter)] border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] flex items-center justify-center mx-auto text-[var(--orange)]">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"></circle><circle cx="18.5" cy="17.5" r="3.5"></circle><path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 9l4.5 3.5H18M12 17.5V14l-3-3"></path></svg>
                        </div>
                        <h3 class="font-heading text-xl font-black text-[var(--ink)]">Belum Ada Event Dibuat</h3>
                        <p class="text-xs sm:text-sm text-[var(--mute)] font-medium">
                            Komunitas {{ $organization->name }} belum memiliki event balap sepeda. Buat event pertamamu sekarang!
                        </p>
                        <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}" class="btn">
                            + Buat Event Sekarang
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($events as $event)
                            <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] flex flex-col justify-between space-y-4">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="chip m text-[10px]">{{ strtoupper(str_replace('_', ' ', $event->type)) }}</span>
                                        <span class="chip p text-[10px]">DITAYANGKAN</span>
                                    </div>
                                    <h3 class="font-heading text-2xl font-black text-[var(--ink)]">
                                        {{ $event->title }}
                                    </h3>
                                    <div class="text-xs text-[var(--mute)] font-semibold space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 shrink-0 text-[var(--ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                            <span>{{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') }} &bull; {{ substr($event->start_time, 0, 5) }} WIB</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 shrink-0 text-[var(--ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>{{ $event->location_name }}</span>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-[var(--ink)]/15 flex flex-wrap gap-1.5">
                                        @foreach ($event->categories as $c)
                                            <span class="px-2 py-0.5 rounded bg-[var(--paper)] border border-[var(--ink)] text-[10px] font-bold">
                                                {{ $c->name }} &bull; Rp {{ number_format($c->price, 0, ',', '.') }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-[var(--ink)]/15 flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-xs text-[var(--mute)] font-bold">/events/{{ $event->slug }}</span>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('organizations.events.edit', ['organization' => $organization->slug, 'event' => $event->slug]) }}"
                                           class="btn btn-mustard btn-sm">
                                            Edit Event
                                        </a>
                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-ghost btn-sm">
                                            Lihat Publik &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            {{-- ==================== TAB: PAKET & KUOTA ==================== --}}
            @if ($activeTab === 'kuota')
                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">PORTAL PENYELENGGARA</div>
                    <h1 class="h3 font-heading text-3xl font-black mt-0.5">Paket &amp; Saldo Kuota Event</h1>
                </div>

                {{-- Card Saldo --}}
                <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-bold text-[var(--mute)] uppercase">SALDO KUOTA AKTIF</div>
                        <div class="disp text-4xl font-black text-[var(--pine)] mt-1">{{ $balance }} Kuota Race</div>
                        <div class="text-xs text-[var(--mute)] font-medium mt-1">1 kuota = 1 publikasi event balap sepeda di Velosha.</div>
                    </div>
                    <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}" class="btn">
                        + Buat Event Dengan Kuota Ini
                    </a>
                </div>

                {{-- Pilihan 3 Paket --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    @foreach ($packages as $pkg)
                        @php($isPop = $pkg->events_count === 3)
                        <div class="card p-6 {{ $isPop ? 'border-[3px] border-[var(--orange)] shadow-[6px_6px_0_#10282E]' : 'bg-white shadow-[4px_4px_0_#10282E]' }} flex flex-col justify-between space-y-5">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-heading font-black text-lg text-[var(--ink)]">{{ $pkg->name }}</h3>
                                    <span class="chip m text-[10px]">{{ $pkg->events_count }} Race</span>
                                </div>
                                <div class="disp text-3xl font-black text-[var(--ink)]">
                                    Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                </div>
                                <p class="text-xs text-[var(--mute)] leading-relaxed min-h-[48px] pb-3">{{ $pkg->description }}</p>
                            </div>

                            <form action="{{ route('organizations.quota.purchase', $organization) }}" method="POST" onsubmit="buyQuotaSnap(event, this)" class="mt-6 pt-2">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                <button type="submit" class="btn btn-block {{ $isPop ? '' : 'btn-ghost' }}">
                                    Beli Kuota (Rp {{ number_format($pkg->price, 0, ',', '.') }})
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                {{-- Riwayat Mutasi Kuota --}}
                <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] space-y-4">
                    <h3 class="font-heading text-xl font-black text-[var(--ink)]">Riwayat Mutasi Kuota</h3>
                    @if ($ledger->isEmpty())
                        <p class="text-xs text-[var(--mute)] font-medium">Belum ada riwayat mutasi kuota.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b-2 border-[var(--ink)] font-black text-[var(--ink)]">
                                        <th class="py-2.5 px-3">Tanggal</th>
                                        <th class="py-2.5 px-3">Aktivitas</th>
                                        <th class="py-2.5 px-3">Perubahan</th>
                                        <th class="py-2.5 px-3">Referensi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--line)]">
                                    @foreach ($ledger as $l)
                                        <tr>
                                            <td class="py-3 px-3 text-[var(--mute)]">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="py-3 px-3 font-bold text-[var(--ink)]">{{ $l->reasonLabel() }}</td>
                                            <td class="py-3 px-3 font-black {{ $l->delta > 0 ? 'text-[var(--pine)]' : 'text-[var(--red)]' }}">
                                                {{ $l->delta > 0 ? "+{$l->delta}" : $l->delta }} Event
                                            </td>
                                            <td class="py-3 px-3 text-[var(--mute)]">{{ $l->reference() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ==================== TAB: KEUANGAN & PAYOUT ==================== --}}
            @if ($activeTab === 'keuangan')
                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">PORTAL PENYELENGGARA</div>
                    <h1 class="h3 font-heading text-3xl font-black mt-0.5">Keuangan &amp; Penarikan Dana</h1>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Rekening Bank --}}
                    <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] space-y-4">
                        <h3 class="font-heading text-xl font-black text-[var(--ink)]">Rekening Bank Komunitas</h3>
                        @if ($bankAccounts->isEmpty())
                            <p class="text-xs text-[var(--mute)]">Belum ada rekening bank yang didaftarkan.</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($bankAccounts as $acc)
                                    <div class="p-3 rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)] flex items-center justify-between">
                                        <div>
                                            <div class="font-black text-xs text-[var(--ink)]">{{ $acc->bank_name }} - {{ $acc->account_number }}</div>
                                            <div class="text-[11px] text-[var(--mute)]">a.n. {{ $acc->account_name }}</div>
                                        </div>
                                        @if ($acc->is_primary)
                                            <span class="chip p text-[9px]">Utama</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('organizations.finance.account.store', $organization) }}" method="POST" class="pt-4 border-t border-[var(--ink)]/15 space-y-3">
                            @csrf
                            <div class="text-xs font-black uppercase text-[var(--ink)]">+ Tambah Rekening</div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <input type="text" name="bank_name" placeholder="Bank" required class="px-2.5 py-1.5 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)]">
                                <input type="text" name="account_number" placeholder="No. Rekening" required class="px-2.5 py-1.5 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)]">
                                <input type="text" name="account_name" placeholder="Nama Pemilik" required class="px-2.5 py-1.5 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)]">
                            </div>
                            <button type="submit" class="btn btn-sm btn-ghost">Simpan Rekening</button>
                        </form>
                    </div>

                    {{-- Form Penarikan Dana --}}
                    <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] space-y-4">
                        <h3 class="font-heading text-xl font-black text-[var(--ink)]">Ajukan Penarikan Dana</h3>
                        @if ($bankAccounts->isEmpty())
                            <div class="p-4 rounded-xl bg-[var(--butter)] text-xs font-bold text-[var(--ink)]">
                                Daftarkan minimal 1 rekening bank untuk mencairkan saldo.
                            </div>
                        @else
                            <form action="{{ route('organizations.finance.payout', $organization) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-[var(--ink)] mb-1">Rekening Tujuan</label>
                                    <select name="payout_account_id" required class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)]">
                                        @foreach ($bankAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_number }} (a.n. {{ $acc->account_name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-[var(--ink)] mb-1">Nominal (Rp) &bull; Maks: Rp {{ number_format($availableBalance, 0, ',', '.') }}</label>
                                    <input type="number" name="amount" min="10000" max="{{ $availableBalance }}" required placeholder="Contoh: 500000"
                                           class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)]">
                                </div>
                                <button type="submit" {{ $availableBalance < 10000 ? 'disabled' : '' }} class="btn w-full btn-sm">
                                    Ajukan Pencairan Dana &rarr;
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Riwayat Payout --}}
                <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] space-y-4">
                    <h3 class="font-heading text-xl font-black text-[var(--ink)]">Riwayat Pencairan Dana</h3>
                    @if ($payoutRequests->isEmpty())
                        <p class="text-xs text-[var(--mute)] font-medium">Belum ada pengajuan pencairan dana.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b-2 border-[var(--ink)] font-black text-[var(--ink)]">
                                        <th class="py-2.5 px-3">Tanggal</th>
                                        <th class="py-2.5 px-3">Rekening</th>
                                        <th class="py-2.5 px-3">Nominal</th>
                                        <th class="py-2.5 px-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--line)]">
                                    @foreach ($payoutRequests as $pr)
                                        <tr>
                                            <td class="py-3 px-3 text-[var(--mute)]">{{ $pr->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="py-3 px-3 font-bold text-[var(--ink)]">{{ $pr->account->bank_name ?? '-' }} ({{ $pr->account->account_number ?? '-' }})</td>
                                            <td class="py-3 px-3 font-black text-[var(--ink)]">Rp {{ number_format($pr->amount, 0, ',', '.') }}</td>
                                            <td class="py-3 px-3">
                                                @if ($pr->status === 'approved')
                                                    <span class="chip p text-[9px]">Selesai</span>
                                                @elseif ($pr->status === 'rejected')
                                                    <span class="chip r text-[9px]">Ditolak</span>
                                                @else
                                                    <span class="chip m text-[9px]">Menunggu Review</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

        </main>
    </div>

    {{-- Logout Confirmation Modal --}}
    <div x-cloak x-show="showLogoutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#10282E]/80 backdrop-blur-xs">
        <div class="card p-6 bg-white max-w-sm w-full space-y-4 shadow-[6px_6px_0_#10282E]" @click.outside="showLogoutModal = false">
            <h3 class="font-heading font-black text-lg text-[var(--ink)]">Konfirmasi Keluar</h3>
            <p class="text-xs text-[var(--mute)] leading-relaxed">Apakah kamu yakin ingin keluar dari akun Velosha?</p>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="showLogoutModal = false" class="btn btn-ghost btn-sm">Batal</button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm bg-[var(--red)] text-white hover:bg-[var(--red-d)]">Ya, Keluar</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap Backdrop Blur Overlay --}}
    <div id="midtrans-blur-overlay" class="fixed inset-0 z-50 bg-[#10282E]/60 backdrop-blur-md hidden transition-all duration-300 flex items-center justify-center pointer-events-none"></div>

    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        function buyQuotaSnap(e, form) {
            e.preventDefault();
            const overlay = document.getElementById('midtrans-blur-overlay');
            if (overlay) overlay.classList.remove('hidden');

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.is_mock && data.redirect_url) {
                    window.location.href = data.redirect_url;
                    return;
                }

                if (data.snap_token && window.snap) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            const finishBase = data.finish_url || window.location.href;
                            const sep = finishBase.includes('?') ? '&' : '?';
                            window.location.href = finishBase + sep +
                                'order_id=' + (result.order_id || data.order_id) +
                                '&transaction_status=' + (result.transaction_status || 'settlement');
                        },
                        onPending: function(result) {
                            const finishBase = data.finish_url || window.location.href;
                            const sep = finishBase.includes('?') ? '&' : '?';
                            window.location.href = finishBase + sep +
                                'order_id=' + (result.order_id || data.order_id) +
                                '&transaction_status=' + (result.transaction_status || 'pending');
                        },
                        onError: function(result) {
                            if (overlay) overlay.classList.add('hidden');
                            alert('Pembayaran Midtrans tidak berhasil atau dibatalkan.');
                        },
                        onClose: function() {
                            if (overlay) overlay.classList.add('hidden');
                        }
                    });
                } else if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    if (overlay) overlay.classList.add('hidden');
                    form.submit();
                }
            })
            .catch(err => {
                console.error(err);
                if (overlay) overlay.classList.add('hidden');
                form.submit();
            });
        }
    </script>
</div>
@endsection
