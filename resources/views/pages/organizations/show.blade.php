@extends('layouts.app', ['title' => 'Portal Penyelenggara — ' . $organization->name, 'nav' => false])

@section('content')
<div x-data="{ sidebarOpen: false, showLogoutModal: false, editOrgModal: false }" class="vs min-h-screen bg-[var(--paper)] text-[var(--ink)] flex flex-col md:flex-row" style="display: flex; min-height: 100vh;">

    {{-- ==================== DESKTOP SIDEBAR ==================== --}}
    <aside class="side hidden md:flex flex-col justify-between bg-[#FBF4E6] border-r-[3px] border-[#10282E] p-6 sticky top-0 h-screen overflow-y-auto overflow-x-hidden" style="width: 280px; min-width: 280px; max-width: 280px; flex-shrink: 0;">
        <div>
            {{-- Brand Logo Header (Official Brand Asset) --}}
            <div class="flex items-center pb-5 mb-4 border-b border-[#10282E]/15">
                <a href="/" wire:navigate.hover class="flex items-center gap-3 transition-transform duration-150 hover:-translate-y-0.5" style="text-decoration: none" aria-label="Velosha — Beranda">
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

                {{-- 5. Laporan Penjualan Tiket --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'keuangan']) }}"
                   wire:navigate.hover
                   class="w-full flex items-center gap-2.5 {{ $activeTab === 'keuangan' ? 'on' : '' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Laporan Penjualan</span>
                </a>

                {{-- 6. Tiket Bantuan / Chat Petugas --}}
                <a href="{{ route('helpdesk.create-ticket', ['from' => 'organizer']) }}" wire:navigate.hover class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Chat / Bantuan</span>
                </a>

                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">BERALIH</div>

                <a href="{{ route('dashboard') }}" wire:navigate.hover class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1-5 15-5 16 0"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Dashboard Rider</span>
                </a>

                <a href="/" wire:navigate.hover class="w-full flex items-center gap-2.5">
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
             class="side relative flex flex-col w-72 max-w-full h-full bg-[#FBF4E6] text-[var(--ink)] border-r-[3px] border-[#10282E] p-6 shadow-[6px_0_0_#10282E] overflow-y-auto">
            <div class="flex items-center justify-between pb-6 mb-4 border-b border-[#10282E]/15">
                <a href="/" wire:navigate.hover class="flex items-center gap-2.5 transition-transform duration-150 hover:-translate-y-0.5" style="text-decoration: none" aria-label="Velosha — Beranda">
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
                    Ringkasan Kuota &amp; Penjualan
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
        <main class="p-4 sm:p-8 lg:p-12 flex-grow space-y-6 max-w-7xl">

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="card p-4 bg-[var(--pine-l)] border-2 border-[var(--ink)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)] font-bold text-sm flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg border border-[var(--ink)] bg-[var(--pine)] text-white flex items-center justify-center text-xs font-black">&check;</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="card p-4 bg-[var(--red-l)] border-2 border-[var(--ink)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)] font-bold text-sm flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg border border-[var(--ink)] bg-[var(--red)] text-white flex items-center justify-center text-xs font-black">&times;</span>
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
                <section class="card bg-[#DE7119] p-5 sm:p-8 relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-5 sm:gap-6 min-h-[170px]">
                    <div class="dots absolute inset-0 opacity-40 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="chip">{{ $organization->name }}</span>
                            <button type="button" @click="editOrgModal = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-[var(--butter)] text-[var(--ink)] font-black text-xs uppercase tracking-wider rounded-lg border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] cursor-pointer transition">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                <span>Edit Komunitas</span>
                            </button>
                        </div>
                        <h2 class="h2 text-2xl sm:text-[44px] mt-2 sm:mt-3.5 text-[var(--ink)]">
                            Halo, {{ Auth::user()->name }}!
                        </h2>
                        <p class="mt-2 max-w-[500px] text-sm sm:text-base font-medium text-[var(--ink)] leading-relaxed">
                            Kelola jadwal balap sepeda komunitasmu, pantau kuota aktif, dan pantau penjualan tiket resmi.
                        </p>
                    </div>

                    {{-- Jam Real-Time --}}
                    <div x-data="{ clock: '' }"
                         x-init="const updateClock = () => { const now = new Date(); clock = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB'; }; updateClock(); setInterval(updateClock, 1000);"
                         class="relative z-10 px-4 py-2.5 sm:px-5 sm:py-3.5 border-[2.5px] border-[#10282E] rounded-[10px] bg-[#10282E] text-[#FBF4E6] text-center shrink-0 shadow-[3px_3px_0_#10282E]">
                        <div class="text-[10px] sm:text-[11px] font-bold tracking-[.1em] text-[#F4BE3F] uppercase">JAM REAL-TIME</div>
                        <div class="mono text-xl sm:text-[28px] font-bold mt-0.5 sm:mt-1 text-[#FBF4E6]" x-text="clock">07.00.00 WIB</div>
                    </div>
                </section>

                {{-- Statistik 4 Box Utama Penyelenggara (Bento 2-Kolom di Mobile) --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                    {{-- 1. Total Event --}}
                    <div class="p-3.5 sm:p-6 bg-white border-[3px] border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] sm:shadow-[5px_5px_0_var(--ink)] rounded-2xl flex flex-col justify-between hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] sm:text-[11px] font-black tracking-wider text-[var(--mute)] uppercase">TOTAL EVENT</span>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[var(--butter)] border-2 border-[var(--ink)] flex items-center justify-center text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4">
                            <div class="font-heading font-black text-2xl sm:text-4xl text-[var(--ink)] tracking-tight">{{ $events->count() }}</div>
                            <div class="text-[11px] sm:text-xs text-[var(--mute)] font-bold mt-1">
                                Balap komunitas
                            </div>
                        </div>
                    </div>

                    {{-- 2. Sisa Kuota --}}
                    <div class="p-3.5 sm:p-6 bg-white border-[3px] border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] sm:shadow-[5px_5px_0_var(--ink)] rounded-2xl flex flex-col justify-between hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] sm:text-[11px] font-black tracking-wider text-[var(--mute)] uppercase">SISA KUOTA</span>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[var(--mustard)] border-2 border-[var(--ink)] flex items-center justify-center text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8M8 12h8"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4">
                            <div class="font-heading font-black text-2xl sm:text-4xl {{ $balance > 0 ? 'text-[var(--pine)]' : 'text-red-600' }} tracking-tight">
                                {{ $balance }}
                            </div>
                            <div class="text-[11px] sm:text-xs text-[var(--mute)] font-bold mt-1">
                                Slot publikasi
                            </div>
                        </div>
                    </div>

                    {{-- 3. Penjualan Kotor --}}
                    <div class="p-3.5 sm:p-6 bg-white border-[3px] border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] sm:shadow-[5px_5px_0_var(--ink)] rounded-2xl flex flex-col justify-between hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] sm:text-[11px] font-black tracking-wider text-[var(--mute)] uppercase">PENJUALAN</span>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[var(--orange)] border-2 border-[var(--ink)] flex items-center justify-center text-white shadow-[2px_2px_0_var(--ink)] shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4">
                            <div class="font-heading font-black text-lg sm:text-3xl text-[var(--ink)] tracking-tight truncate">
                                Rp {{ number_format($totalSales, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] sm:text-xs text-[var(--mute)] font-bold mt-1">
                                Total tiket
                            </div>
                        </div>
                    </div>

                    {{-- 4. Tiket Terjual --}}
                    @php
                        $totalTicketsSold = $events->sum(function($ev) {
                            return $ev->registrations()->whereIn('status', ['paid', 'checked_in'])->count();
                        });
                    @endphp
                    <div class="p-3.5 sm:p-6 bg-white border-[3px] border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] sm:shadow-[5px_5px_0_var(--ink)] rounded-2xl flex flex-col justify-between hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] sm:text-[11px] font-black tracking-wider text-[var(--mute)] uppercase">TERJUAL</span>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[#E6F4EA] border-2 border-[var(--ink)] flex items-center justify-center text-[var(--pine)] shadow-[2px_2px_0_var(--ink)] shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4">
                            <div class="font-heading font-black text-lg sm:text-3xl text-[var(--pine)] tracking-tight">
                                {{ number_format($totalTicketsSold, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] sm:text-xs text-[var(--mute)] font-bold mt-1">
                                Peserta
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
                           wire:navigate.hover
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
                           wire:navigate.hover
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
                                        <x-ui.submit-button :variant="$isPop ? 'orange' : 'mustard'" loadingText="Menghubungkan Midtrans..." :fullWidth="true">
                                            Beli &amp; Buat Event &rarr;
                                        </x-ui.submit-button>
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

                            {{-- Judul & Slug --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" x-data="{ title: '{{ old('title', '') }}', slug: '{{ old('slug', '') }}' }">
                                <div class="sm:col-span-2">
                                    <label for="title" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        Judul / Nama Event <span class="text-[var(--red)]">*</span>
                                    </label>
                                    <input type="text" id="title" name="title" x-model="title" required autofocus
                                           placeholder="Contoh: Tour de Merapi 2026"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                </div>
                                <div>
                                    <label for="slug" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        Slug URL (Opsional)
                                    </label>
                                    <input type="text" id="slug" name="slug" x-model="slug"
                                           placeholder="Otomatis jika kosong"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                </div>
                            </div>

                            {{-- Waktu & Tipe --}}
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

                            {{-- Lokasi & YouTube Video --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="location_name" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        Lokasi Start / Kota <span class="text-[var(--red)]">*</span>
                                    </label>
                                    <input type="text" id="location_name" name="location_name" value="{{ old('location_name') }}" required
                                           placeholder="Contoh: Alun-alun Kota Magelang, Jawa Tengah"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                </div>
                                <div>
                                    <label for="youtube_video_id" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        YouTube Video ID (Opsional)
                                    </label>
                                    <input type="text" id="youtube_video_id" name="youtube_video_id" value="{{ old('youtube_video_id') }}"
                                           placeholder="Contoh: dQw4w9WgXcQ"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                </div>
                            </div>

                            {{-- Media & Rute (Poster, Peta Rute, GPX File) --}}
                            <div class="p-5 rounded-2xl border-2 border-[var(--ink)] bg-[var(--paper)]/50 space-y-4">
                                <div class="border-b border-[var(--ink)]/15 pb-2">
                                    <h3 class="font-heading font-black text-sm text-[var(--ink)] uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[var(--orange)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        Media &amp; Navigasi Rute Balap
                                    </h3>
                                    <p class="text-xs text-[var(--mute)]">Upload poster resmi acara, peta sirkuit, dan file rute GPX untuk para rider.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    {{-- Poster Resmi --}}
                                    <div class="p-3.5 rounded-xl border-2 border-[var(--ink)] bg-white space-y-1.5">
                                        <label for="poster" class="block font-bold text-xs text-[var(--ink)]">
                                            Poster Resmi Cover <span class="text-red-500">*</span>
                                        </label>
                                        <input type="file" id="poster" name="poster" accept="image/*"
                                               class="block w-full text-[11px] font-bold text-[var(--ink)] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-[var(--ink)] file:text-[10px] file:font-black file:bg-[var(--mustard)] file:text-[var(--ink)] file:cursor-pointer hover:file:bg-[var(--butter)]">
                                        <span class="text-[10px] text-[var(--mute)] block">Tampil di beranda &amp; katalog event (Maks 3 MB).</span>
                                    </div>

                                    {{-- Peta Rute --}}
                                    <div class="p-3.5 rounded-xl border-2 border-[var(--ink)] bg-white space-y-1.5">
                                        <label for="map_image" class="block font-bold text-xs text-[var(--ink)]">
                                            Gambar Peta Sirkuit
                                        </label>
                                        <input type="file" id="map_image" name="map_image" accept="image/*"
                                               class="block w-full text-[11px] font-bold text-[var(--ink)] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-[var(--ink)] file:text-[10px] file:font-black file:bg-[var(--paper)] file:text-[var(--ink)] file:cursor-pointer hover:file:bg-[var(--butter)]">
                                        <span class="text-[10px] text-[var(--mute)] block">Gambar jalur balap di halaman detail (Maks 5 MB).</span>
                                    </div>

                                    {{-- File GPX --}}
                                    <div class="p-3.5 rounded-xl border-2 border-[var(--ink)] bg-white space-y-1.5">
                                        <label for="gpx_file" class="block font-bold text-xs text-[var(--ink)]">
                                            File Rute GPX (Bike Computer)
                                        </label>
                                        <input type="file" id="gpx_file" name="gpx_file" accept=".gpx,.xml"
                                               class="block w-full text-[11px] font-bold text-[var(--ink)] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-[var(--ink)] file:text-[10px] file:font-black file:bg-[var(--paper)] file:text-[var(--ink)] file:cursor-pointer hover:file:bg-[var(--butter)]">
                                        <span class="text-[10px] text-[var(--mute)] block">Format file .gpx untuk Garmin/Wahoo (Maks 10 MB).</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kategori Tiket Balap (Utama & Tambahan - Alpine.js Sesuai Skema Admin) --}}
                            <div x-data="{
                                categories: [
                                    { name: 'Gran Fondo 120 KM', code: 'GF', distance_km: 120, elevation_gain_m: 850, price: 250000, quota: 500, wave_start_time: 'Wave 1 - 05:45 WIB', cut_off_hours: 6.5 }
                                ],
                                primaryIndex: 0,
                                addCategory() {
                                    this.categories.push({ name: '', code: '', distance_km: 60, elevation_gain_m: 350, price: 150000, quota: 200, wave_start_time: 'Wave 2 - 06:15 WIB', cut_off_hours: 4.0 });
                                },
                                removeCategory(idx) {
                                    if (this.categories.length > 1) {
                                        this.categories.splice(idx, 1);
                                        if (this.primaryIndex >= this.categories.length) {
                                            this.primaryIndex = 0;
                                        } else if (this.primaryIndex === idx) {
                                            this.primaryIndex = 0;
                                        } else if (this.primaryIndex > idx) {
                                            this.primaryIndex--;
                                        }
                                    }
                                }
                            }" class="space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-[var(--ink)]/15">
                                    <div>
                                        <h3 class="font-heading font-black text-base text-[var(--ink)]">Kategori Tiket Balap (Sesuai Standar Race)</h3>
                                        <p class="text-xs text-[var(--mute)] font-medium">Atur kategori jarak, kode BIB peserta, elevasi, wave start, dan tentukan kategori tiket utama.</p>
                                    </div>
                                    <button type="button" @click="addCategory()" class="btn btn-sm btn-mustard">
                                        <span>+ Tambah Kategori Tiket</span>
                                    </button>
                                </div>

                                <template x-for="(cat, idx) in categories" :key="idx">
                                    <div class="p-5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] space-y-4">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="w-6 h-6 rounded-md bg-[var(--orange)] text-white font-black text-xs flex items-center justify-center border border-[var(--ink)]" x-text="idx + 1"></span>
                                                <span class="font-heading font-black text-sm text-[var(--ink)]" x-text="'Kategori #' + (idx + 1)"></span>

                                                {{-- Radio Button Kategori Utama --}}
                                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-black px-2.5 py-1 rounded-lg border-2 transition-all shadow-[2px_2px_0_var(--ink)]"
                                                       :class="primaryIndex === idx ? 'bg-[var(--mustard)] border-[var(--ink)] text-[var(--ink)]' : 'bg-white border-[var(--ink)]/40 text-[var(--mute)] hover:border-[var(--ink)] hover:text-[var(--ink)]'">
                                                    <input type="radio" 
                                                           name="primary_category_index" 
                                                           :value="idx" 
                                                           :checked="primaryIndex === idx" 
                                                           @change="primaryIndex = idx" 
                                                           class="sr-only">
                                                    <template x-if="primaryIndex === idx">
                                                        <span class="flex items-center gap-1 text-[var(--ink)]">
                                                            <svg class="w-3.5 h-3.5 fill-[var(--ink)]" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                            ★ Kategori Tiket Utama
                                                        </span>
                                                    </template>
                                                    <template x-if="primaryIndex !== idx">
                                                        <span class="flex items-center gap-1">
                                                            Jadikan Kategori Utama
                                                        </span>
                                                    </template>
                                                </label>
                                                <input type="hidden" :name="'categories[' + idx + '][is_primary]'" :value="primaryIndex === idx ? '1' : '0'">
                                            </div>
                                            <button type="button" x-show="categories.length > 1" @click="removeCategory(idx)"
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
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Kode BIB (Cth: GF, KOM)</label>
                                                <input type="text" :name="'categories[' + idx + '][code]'" x-model="cat.code" maxlength="10"
                                                       placeholder="GF"
                                                       class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)] uppercase">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Jarak Tempuh (KM) <span class="text-red-600">*</span></label>
                                                <input type="number" step="0.1" :name="'categories[' + idx + '][distance_km]'" x-model="cat.distance_km" required
                                                       class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)]">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Elevasi (+m)</label>
                                                <input type="number" :name="'categories[' + idx + '][elevation_gain_m]'" x-model="cat.elevation_gain_m"
                                                       placeholder="Contoh: 850"
                                                       class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)]">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
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
                                            <div>
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Wave Start</label>
                                                <input type="text" :name="'categories[' + idx + '][wave_start_time]'" x-model="cat.wave_start_time"
                                                       placeholder="Wave 1 - 05:45 WIB"
                                                       class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)]">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-[var(--ink)] mb-1">Cut-Off Time (Jam) <span class="text-red-600">*</span></label>
                                                <input type="number" step="0.5" :name="'categories[' + idx + '][cut_off_hours]'" x-model="cat.cut_off_hours" required min="1"
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

                            {{-- Deskripsi Event & Fasilitas --}}
                            <div>
                                <label for="description" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                    Deskripsi Event &amp; Fasilitas Race <span class="text-[var(--red)]">*</span>
                                </label>
                                <textarea id="description" name="description" rows="4" required
                                          placeholder="Tuliskan gambaran rute, water station, jersey finisher, dan regulasi balap..."
                                          class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">{{ old('description') }}</textarea>
                            </div>

                            <x-ui.submit-button variant="orange" loadingText="Menyimpan &amp; mempublikasikan..." size="md">
                                Simpan &amp; Publikasikan Event &rarr;
                            </x-ui.submit-button>
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
                    <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}" wire:navigate.hover class="btn btn-sm">
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
                        <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}" wire:navigate.hover class="btn">
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
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('organizations.events.scanner', ['organization' => $organization->slug, 'event' => $event->slug]) }}"
                                           wire:navigate.hover
                                           class="btn btn-pine btn-sm font-black flex items-center gap-1.5 shadow-[1px_1px_0_var(--ink)]"
                                           title="Buka Gate Scanner Hari-H untuk Check-in Racepack">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                            <span>Gate Scanner</span>
                                        </a>
                                        <a href="{{ route('organizations.events.edit', ['organization' => $organization->slug, 'event' => $event->slug]) }}"
                                           wire:navigate.hover
                                           class="btn btn-mustard btn-sm">
                                            Edit Event
                                        </a>
                                        <a href="{{ route('events.show', $event->slug) }}" wire:navigate.hover class="btn btn-ghost btn-sm">
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
                    <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}" wire:navigate.hover class="btn">
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
                                <x-ui.submit-button :variant="$isPop ? 'orange' : 'mustard'" loadingText="Menghubungkan Midtrans..." :fullWidth="true">
                                    Beli Kuota (Rp {{ number_format($pkg->price, 0, ',', '.') }})
                                </x-ui.submit-button>
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

            {{-- ==================== TAB: LAPORAN PENJUALAN TIKET ==================== --}}
            @if ($activeTab === 'keuangan')
                <div>
                    <div class="text-[13px] font-bold tracking-[.08em] text-[var(--mute)] uppercase">PORTAL PENYELENGGARA</div>
                    <h1 class="h3 font-heading text-3xl font-black mt-0.5">Laporan Penjualan Tiket Event</h1>
                    <p class="text-xs text-[var(--mute)] font-medium mt-1">Pantau performa pendaftaran peserta, tiket terjual, dan ringkasan omzet per kategori balap secara transparan.</p>
                </div>

                {{-- Ringkasan 3 Metrik Penjualan --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="card p-5 bg-white border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)]">
                        <div class="text-[11px] font-black uppercase text-[var(--mute)] tracking-wider">TOTAL OMZET TIKET</div>
                        <div class="font-heading font-black text-2xl sm:text-3xl text-[var(--ink)] mt-2">
                            Rp {{ number_format($totalSales, 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] text-[var(--mute)] font-semibold mt-1">Akumulasi seluruh transaksi lunas</div>
                    </div>

                    <div class="card p-5 bg-white border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)]">
                        <div class="text-[11px] font-black uppercase text-[var(--mute)] tracking-wider">PESERTA TERDAFTAR</div>
                        <div class="font-heading font-black text-2xl sm:text-3xl text-[var(--pine)] mt-2">
                            {{ number_format($totalTicketsSold, 0, ',', '.') }} Rider
                        </div>
                        <div class="text-[11px] text-[var(--mute)] font-semibold mt-1">Tiket aktif &amp; memiliki nomor BIB</div>
                    </div>

                    <div class="card p-5 bg-white border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)]">
                        <div class="text-[11px] font-black uppercase text-[var(--mute)] tracking-wider">EVENT DIPUBLIKASIKAN</div>
                        <div class="font-heading font-black text-2xl sm:text-3xl text-[var(--orange)] mt-2">
                            {{ $events->count() }} Event
                        </div>
                        <div class="text-[11px] text-[var(--mute)] font-semibold mt-1">Tersedia di kalender resmi Velosha</div>
                    </div>
                </div>

                {{-- Tabel Detail Penjualan per Event --}}
                <div class="card p-6 bg-white shadow-[4px_4px_0_#10282E] space-y-4">
                    <div class="flex items-center justify-between border-b-2 border-[var(--ink)] pb-3 flex-wrap gap-2">
                        <h3 class="font-heading text-xl font-black text-[var(--ink)]">Rincian Penjualan Tiket per Event</h3>
                        <span class="text-xs font-bold text-[var(--mute)]">Sinkronisasi otomatis real-time</span>
                    </div>

                    @if ($events->isEmpty())
                        <div class="p-8 text-center bg-[var(--paper)] rounded-xl border-2 border-dashed border-[var(--ink)]/30">
                            <p class="font-heading font-bold text-base text-[var(--ink)]">Belum ada event yang dipublikasikan</p>
                            <p class="text-xs text-[var(--mute)] mt-1">Buat event balap pertamamu untuk mulai menerima pendaftaran peserta.</p>
                            <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}" wire:navigate.hover class="btn btn-sm mt-3 inline-flex">
                                + Buat Event Sekarang
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b-2 border-[var(--ink)] font-black text-[var(--ink)]">
                                        <th class="py-3 px-3">Nama Event &amp; Lokasi</th>
                                        <th class="py-3 px-3">Tanggal Pelaksanaan</th>
                                        <th class="py-3 px-3">Kategori Tiket</th>
                                        <th class="py-3 px-3 text-center">Peserta</th>
                                        <th class="py-3 px-3 text-right">Pendapatan Kotor</th>
                                        <th class="py-3 px-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--line)]">
                                    @foreach ($events as $ev)
                                        @php
                                            $evRegistrations = $ev->registrations()->whereIn('status', ['paid', 'checked_in'])->get();
                                            $evSales = $evRegistrations->sum(fn($r) => (int) $r->amount);
                                            $evParticipants = $evRegistrations->count();
                                        @endphp
                                        <tr class="hover:bg-[var(--paper)]/60 transition">
                                            <td class="py-3.5 px-3">
                                                <div class="font-heading font-black text-sm text-[var(--ink)]">{{ $ev->title }}</div>
                                                <div class="text-[11px] text-[var(--mute)] font-semibold mt-0.5">{{ $ev->location_name }}</div>
                                            </td>
                                            <td class="py-3.5 px-3 font-semibold text-[var(--ink)]">
                                                {{ \Carbon\Carbon::parse($ev->event_date)->translatedFormat('d F Y') }}
                                            </td>
                                            <td class="py-3.5 px-3">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($ev->categories as $cat)
                                                        <span class="px-1.5 py-0.5 rounded bg-[var(--paper)] border border-[var(--ink)] text-[10px] font-bold">
                                                            {{ $cat->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-3 text-center font-black text-[var(--pine)] text-sm">
                                                {{ $evParticipants }}
                                            </td>
                                            <td class="py-3.5 px-3 text-right font-black text-sm text-[var(--ink)]">
                                                Rp {{ number_format($evSales, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3.5 px-3 text-center">
                                                <a href="{{ route('organizations.events.scanner', ['organization' => $organization->slug, 'event' => $ev->slug]) }}"
                                                   wire:navigate.hover
                                                   class="btn btn-sm btn-ghost text-[11px] py-1 px-2.5 font-bold"
                                                   title="Buka Scanner Check-in">
                                                    Gate Scanner &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Banner Informasi Integrasi Midtrans --}}
                <div class="p-5 rounded-2xl border-2 border-[var(--ink)] bg-[var(--butter)] shadow-[3px_3px_0_var(--ink)] flex items-start gap-4">
                    <div class="w-9 h-9 rounded-xl border-2 border-[var(--ink)] bg-white flex items-center justify-center shrink-0 shadow-[1.5px_1.5px_0_var(--ink)]">
                        <svg class="w-5 h-5 text-[var(--orange)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div>
                        <h4 class="font-heading font-black text-sm text-[var(--ink)]">Sistem Pembayaran Terpadu Midtrans</h4>
                        <p class="text-xs text-[var(--ink)]/85 leading-relaxed mt-0.5">
                            Setiap pendaftaran peserta diverifikasi secara otomatis melalui Midtrans (QRIS, VA Bank, &amp; Kartu). Pembayaran yang berhasil otomatis menerbitkan tiket dan mengunci nomor BIB RFID peserta tanpa perlu konfirmasi manual.
                        </p>
                    </div>
                </div>

                {{--
                ================================================================================
                PAYOUT WORKFLOW (DEACTIVATED TEMPORARILY AS REQUESTED):
                ================================================================================
                Alur penarikan dana (bank accounts + payout requests) dinonaktifkan sementara.
                Data mutasi & saldo tetap terjaga di database dan dapat diaktifkan kembali
                kapan saja saat dibutuhkan.
                ================================================================================
                --}}
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

    {{-- Modal Edit Nama & Profil Komunitas --}}
    <div x-cloak x-show="editOrgModal" class="fixed inset-0 z-50 bg-[#10282E]/75 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border-[3px] border-[var(--ink)] shadow-[6px_6px_0_var(--ink)] p-6 sm:p-7 space-y-5" style="max-width: 480px; width: 100%; margin: auto;" @click.outside="editOrgModal = false">
            <div class="flex items-center justify-between border-b-2 border-[var(--ink)] pb-3">
                <h3 class="font-heading font-black text-xl text-[var(--ink)]">Edit Profil Komunitas / EO</h3>
                <button type="button" @click="editOrgModal = false" class="w-8 h-8 rounded-lg border-2 border-[var(--ink)] bg-white hover:bg-[var(--butter)] text-[var(--ink)] font-black text-sm flex items-center justify-center shadow-[1px_1px_0_var(--ink)] cursor-pointer">✕</button>
            </div>
            <form action="{{ route('organizations.update', $organization) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_org_name" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">Nama Komunitas / Klub / EO *</label>
                    <input type="text" id="edit_org_name" name="name" value="{{ old('name', $organization->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)]">
                </div>
                <div>
                    <label for="edit_org_desc" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">Deskripsi Singkat (Opsional)</label>
                    <textarea id="edit_org_desc" name="description" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-xs text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)]">{{ old('description', $organization->description) }}</textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-3 border-t-2 border-[var(--ink)]">
                    <button type="button" @click="editOrgModal = false" class="btn btn-sm btn-ghost">Batal</button>
                    <x-ui.submit-button variant="orange" loadingText="Menyimpan..." size="sm">
                        Simpan Perubahan &rarr;
                    </x-ui.submit-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Midtrans Snap Popup with 100% Strong Blurred Backdrop --}}
    <div id="snap-embed-modal" class="fixed inset-0 z-[999999] hidden transition-all duration-300 flex items-center justify-center p-4" style="backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); background-color: rgba(16, 40, 46, 0.78);">
        <div class="relative bg-white rounded-2xl border-[3.5px] border-[var(--ink)] shadow-[8px_8px_0_var(--ink)] overflow-hidden" style="max-width: 460px; width: 100%; margin: auto;">
            <div class="flex items-center justify-between px-5 py-3.5 bg-[var(--butter)] border-b-[2.5px] border-[var(--ink)]">
                <span class="font-heading font-black text-sm text-[var(--ink)] tracking-tight">Pembayaran Paket Kuota Midtrans</span>
                <button type="button" id="close-snap-modal-btn" class="w-7 h-7 rounded-lg border-2 border-[var(--ink)] bg-white hover:bg-red-100 text-[var(--ink)] flex items-center justify-center text-xs font-black cursor-pointer shadow-[1.5px_1.5px_0_var(--ink)] transition">✕</button>
            </div>
            <div id="snap-container" class="w-full flex items-center justify-center bg-[#FAF9F5]" style="min-height: 540px;">
                <div id="snap-loading-state" class="text-xs font-bold text-[var(--mute)] p-8 text-center flex flex-col items-center gap-2">
                    <svg class="animate-spin h-6 w-6 text-[var(--orange)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Menyiapkan gerbang pembayaran Midtrans...</span>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        function buyQuotaSnap(e, form) {
            e.preventDefault();
            const modal = document.getElementById('snap-embed-modal');
            const container = document.getElementById('snap-container');
            const closeBtn = document.getElementById('close-snap-modal-btn');

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';

            const resetUi = () => {
                if (modal) modal.classList.add('hidden');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            };

            if (closeBtn) {
                closeBtn.onclick = resetUi;
            }

            if (container) {
                container.innerHTML = `
                    <div id="snap-loading-state" class="text-xs font-bold text-[var(--mute)] p-8 text-center flex flex-col items-center gap-2">
                        <svg class="animate-spin h-6 w-6 text-[var(--orange)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Menyiapkan gerbang pembayaran Midtrans...</span>
                    </div>
                `;
            }

            if (modal) modal.classList.remove('hidden');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Menyiapkan Pembayaran...</span>';
            }

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
            .then(async res => {
                const data = await res.json().catch(() => null);
                if (!res.ok) {
                    throw new Error((data && data.message) ? data.message : 'Gagal memproses pembayaran Midtrans.');
                }
                return data;
            })
            .then(data => {
                if (!data) {
                    resetUi();
                    alert('Gagal memproses respons server pembayaran.');
                    return;
                }

                if (data.is_mock && data.redirect_url) {
                    window.location.href = data.redirect_url;
                    return;
                }

                if (data.snap_token && window.snap) {
                    try {
                        if (typeof window.snap.embed === 'function') {
                            container.innerHTML = '';
                            window.snap.embed(data.snap_token, {
                                embedId: 'snap-container',
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
                                    resetUi();
                                    alert('Pembayaran Midtrans tidak berhasil atau dibatalkan.');
                                },
                                onClose: function() {
                                    resetUi();
                                }
                            });
                            return;
                        } else if (typeof window.snap.pay === 'function') {
                            // Alternatif jika snap.embed tidak aktif di lingkungan tertentu
                            if (modal) modal.classList.add('hidden');
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
                                onError: function() {
                                    resetUi();
                                    alert('Pembayaran Midtrans tidak berhasil atau dibatalkan.');
                                },
                                onClose: function() {
                                    resetUi();
                                }
                            });
                            return;
                        }
                    } catch (err) {
                        console.warn('snap.embed/pay failed, falling back to iframe', err);
                    }
                }

                // If snap.embed could not be run, load in iframe inside the blur modal!
                if (data.redirect_url) {
                    container.innerHTML = '<iframe src="' + data.redirect_url + '" style="width:100%;height:540px;border:none;border-radius:14px;" allow="payment"></iframe>';
                } else {
                    resetUi();
                    alert('Gagal memproses pembayaran. Silakan coba kembali.');
                }
            })
            .catch(err => {
                resetUi();
                alert(err.message || 'Terjadi kendala jaringan saat menghubungi gateway Midtrans.');
            });
        }
    </script>
</div>
@endsection
