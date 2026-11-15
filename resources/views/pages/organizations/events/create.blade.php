@extends('layouts.app', ['title' => 'Buat Event Baru — ' . $organization->name, 'nav' => false])

@section('content')
<div x-data="{
    sidebarOpen: false,
    showLogoutModal: false,
    categories: [
        {
            name: 'Gran Fondo 120 KM',
            distance_km: 120,
            price: 250000,
            quota: 500,
            cut_off_hours: 6.0
        }
    ],
    primaryIndex: 0,
    addCategory() {
        this.categories.push({
            name: '',
            distance_km: 60,
            price: 150000,
            quota: 200,
            cut_off_hours: 4.0
        });
    },
    removeCategory(index) {
        if (this.categories.length > 1) {
            this.categories.splice(index, 1);
            if (this.primaryIndex >= this.categories.length) {
                this.primaryIndex = 0;
            } else if (this.primaryIndex === index) {
                this.primaryIndex = 0;
            } else if (this.primaryIndex > index) {
                this.primaryIndex--;
            }
        }
    }
}" class="vs min-h-screen bg-[var(--paper)] text-[var(--ink)] flex flex-col md:flex-row" style="display: flex; min-height: 100vh;">

    {{-- ==================== DESKTOP SIDEBAR ==================== --}}
    <aside class="side hidden md:flex flex-col justify-between bg-[#FBF4E6] border-r-[3px] border-[#10282E] p-6 sticky top-0 h-screen overflow-y-auto overflow-x-hidden" style="width: 280px; min-width: 280px; max-width: 280px; flex-shrink: 0;">
        <div>
            {{-- Brand Logo Header --}}
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
                {{-- 1. Ringkasan EO --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'overview']) }}"
                   class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Ringkasan EO</span>
                </a>

                {{-- 2. Buat Event (Aktif) --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}"
                   class="w-full flex items-center gap-2.5 on">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Buat Event</span>
                </a>

                {{-- 3. Event Saya --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
                   class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Event Saya</span>
                </a>

                {{-- 4. Paket & Kuota --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']) }}"
                   class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path><path d="M12 18V6"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Paket &amp; Kuota</span>
                </a>

                {{-- 5. Keuangan & Payout --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'keuangan']) }}"
                   class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Keuangan &amp; Payout</span>
                </a>

                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">BERALIH</div>

                <a href="/" wire:navigate class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Kembali ke website</span>
                </a>

                @if(Auth::user()->isPeserta())
                <a href="/dashboard" wire:navigate class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1-5 15-5 16 0"></path></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Portal Rider</span>
                </a>
                @endif
            </nav>
        </div>

        {{-- Footer User Profile --}}
        <div class="pt-6 border-t border-[#10282E]/15 mt-auto">
            <div class="card-flat p-3.5 flex items-center gap-3 shadow-[3px_3px_0_#10282E] bg-white">
                <span class="disp w-11 h-11 border-[2.5px] border-[#10282E] rounded-lg bg-[var(--mustard)] flex items-center justify-center font-extrabold text-xl text-[var(--ink)] shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-sm truncate text-[var(--ink)]">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-[var(--mute)] truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <button type="button" @click="showLogoutModal = true" class="btn btn-ghost btn-block btn-sm mt-3 text-[#C23B22] hover:bg-red-50">
                Keluar akun
            </button>
        </div>
    </aside>

    {{-- ==================== MOBILE DRAWER ==================== --}}
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 md:hidden" role="dialog" aria-modal="true">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-[#10282E]/80 backdrop-blur-xs" @click="sidebarOpen = false"></div>

        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="side relative flex flex-col w-72 max-w-full h-full bg-[#FBF4E6] text-[var(--ink)] border-r-[3px] border-[#10282E] p-6 shadow-[6px_0_0_#10282E] overflow-y-auto">
            <div class="flex items-center justify-between pb-6 mb-4 border-b border-[#10282E]/15">
                <a href="/" wire:navigate class="flex items-center gap-2.5">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-8 w-auto object-contain" />
                    <span class="font-extrabold text-2xl tracking-tight text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
                </a>
                <button type="button" @click="sidebarOpen = false" class="p-2 text-[var(--ink)]">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <div class="px-3.5 py-3 rounded-xl bg-white border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] mb-4">
                <div class="text-[10px] font-black uppercase text-[var(--mute)] tracking-wider">KOMUNITAS</div>
                <div class="font-heading font-extrabold text-sm text-[var(--ink)] truncate mt-0.5">{{ $organization->name }}</div>
                <div class="text-[11px] font-bold text-[var(--pine)] mt-1">&bull; {{ $balance }} Kuota Event</div>
            </div>

            <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 mb-2">NAVIGASI</div>
            <nav class="space-y-1 text-sm font-semibold">
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'overview']) }}"
                   class="w-full flex items-center gap-2.5">Ringkasan EO</a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}"
                   class="w-full flex items-center gap-2.5 on">Buat Event</a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
                   class="w-full flex items-center gap-2.5">Event Saya</a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']) }}"
                   class="w-full flex items-center gap-2.5">Paket &amp; Kuota</a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'keuangan']) }}"
                   class="w-full flex items-center gap-2.5">Keuangan &amp; Payout</a>
            </nav>

            <div class="pt-6 border-t border-[#10282E]/15 mt-auto">
                <button type="button" @click="showLogoutModal = true" class="btn btn-ghost btn-block btn-sm text-[#C23B22]">
                    Keluar akun
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== MAIN CONTENT AREA ==================== --}}
    <div class="flex-1 min-w-0 flex flex-col min-h-screen" style="flex: 1 1 0%; min-width: 0;">

        {{-- Top Bar for Mobile --}}
        <header class="md:hidden h-16 bg-[#FBF4E6] border-b-[2.5px] border-[#10282E] px-4 flex items-center justify-between sticky top-0 z-20">
            <button type="button" @click="sidebarOpen = true" class="p-2 text-[var(--ink)]">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <span class="font-heading font-black text-lg">Buat Event Baru</span>
            <span class="disp w-8 h-8 border-2 border-[#10282E] rounded-md bg-[var(--mustard)] flex items-center justify-center font-bold text-xs text-[var(--ink)]">
                {{ strtoupper(substr($organization->name, 0, 1)) }}
            </span>
        </header>

        <main class="p-4 sm:p-8 md:p-10 min-w-0 flex-grow space-y-6 sm:space-y-8 max-w-5xl mx-auto w-full">

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="p-4 bg-[var(--pine-l)] border-2 border-[var(--ink)] text-[var(--ink)] rounded-xl shadow-[3px_3px_0_var(--ink)] flex items-center justify-between font-bold text-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-xs font-black">&check;</span>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-[var(--red-l)] border-2 border-[var(--ink)] text-[var(--ink)] rounded-xl shadow-[3px_3px_0_var(--ink)] flex items-center justify-between font-bold text-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-[var(--red)] text-white flex items-center justify-center text-xs font-black">&times;</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- JIKA KUOTA = 0: PANDUAN PILIH PAKET KUOTA LANGSUNG --}}
            @if ($balance <= 0)
                <div class="p-8 sm:p-10 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[6px_6px_0_var(--ink)] space-y-6 text-center sm:text-left">
                    <div class="space-y-2">
                        <span class="inline-block px-3 py-1 bg-[var(--mustard)] border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider rounded-md shadow-[2px_2px_0_var(--ink)]">
                            Langkah 1: Tambah Kuota Event
                        </span>
                        <h1 class="text-3xl sm:text-4xl font-black text-[var(--ink)] tracking-tight" style="font-family: 'Bricolage Grotesque', sans-serif">
                            Pilih Paket Kuota untuk Mulai Membuat Event
                        </h1>
                        <p class="text-xs sm:text-sm text-[var(--mute)] max-w-2xl font-medium leading-relaxed">
                            Anda membutuhkan minimal 1 kuota event untuk dapat membuat dan mempublikasikan race bersepeda di Velosha. Pilih salah satu paket di bawah ini; kuota aktif selamanya dan akun Anda langsung menjadi Penyelenggara.
                        </p>
                    </div>

                    {{-- 3 Paket Kuota Screen-Print --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch pt-4">
                        @foreach ($packages as $pkg)
                            @php($isPopular = $pkg->events_count === 3)
                            <div class="p-6 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--paper)] {{ $isPopular ? 'ring-2 ring-[var(--orange)] shadow-[6px_6px_0_var(--ink)]' : 'shadow-[4px_4px_0_var(--ink)]' }} flex flex-col justify-between text-left space-y-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-lg font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                                            {{ $pkg->name }}
                                        </h2>
                                        <span class="px-2 py-0.5 text-xs font-black border-2 border-[var(--ink)] rounded bg-[var(--card)]">
                                            {{ $pkg->events_count }} Race
                                        </span>
                                    </div>

                                    <p class="text-xs text-[var(--mute)] min-h-[36px]">
                                        {{ $pkg->description }}
                                    </p>

                                    <div class="border-y-2 border-[var(--line)] py-3">
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-xs font-bold text-[var(--mute)]">Rp</span>
                                            <span class="text-3xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                                                {{ number_format($pkg->price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] font-bold text-[var(--pine)] mt-0.5">
                                            Berlaku selamanya tanpa batas waktu
                                        </div>
                                    </div>

                                    <ul class="space-y-2 text-xs font-semibold text-[var(--ink)]">
                                        <li class="flex items-center gap-2">
                                            <span class="w-4 h-4 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black">&check;</span>
                                            <span>Bisa buat <strong>{{ $pkg->events_count }} Event</strong></span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span class="w-4 h-4 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black">&check;</span>
                                            <span>Tiket QR &amp; nomor BIB otomatis</span>
                                        </li>
                                    </ul>
                                </div>

                                <form action="{{ route('organizations.quota.purchase', $organization) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <input type="hidden" name="redirect_to" value="events.create">
                                    <button type="submit"
                                            class="w-full py-3 px-4 rounded-xl border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider {{ $isPopular ? 'bg-[var(--orange)] text-white hover:bg-[var(--orange-d)]' : 'bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]' }} shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        <span>Beli &amp; Lanjutkan Buat Event &rarr;</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- JIKA KUOTA > 0: FORMULIR INPUT EVENT LENGKAP --}}
                <div class="card p-5 sm:p-8 md:p-10 space-y-6 sm:space-y-8 bg-white border-[3px] border-[var(--ink)] rounded-2xl shadow-[4px_4px_0_var(--ink)] sm:shadow-[6px_6px_0_var(--ink)]">

                    <div class="flex items-start justify-between flex-wrap gap-4 border-b-2 border-[var(--ink)]/15 pb-6">
                        <div>
                            <div class="chip chip-mustard mb-2">Penyelenggara &bull; Buat Event Baru</div>
                            <h1 class="h3 font-heading text-3xl font-black text-[var(--ink)]">Informasi Balap Sepeda Baru</h1>
                            <p class="text-xs sm:text-sm text-[var(--mute)] mt-1 font-medium">Lengkapi rincian balap, unggah poster resmi, dan tentukan kategori tiket yang dibuka untuk pendaftaran rider.</p>
                        </div>
                        <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}" wire:navigate.hover class="btn btn-secondary btn-sm">
                            &larr; Batal &amp; Kembali
                        </a>
                    </div>

                    <form action="{{ route('organizations.events.store', $organization->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- 1. INFORMASI UTAMA EVENT --}}
                        <div class="space-y-4">
                            <h2 class="text-sm font-black uppercase tracking-wider text-[var(--ink)] border-b-2 border-[var(--line)] pb-2">
                                1. Informasi Utama Race
                            </h2>

                            <div>
                                <label for="title" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                    Judul / Nama Resmi Event <span class="text-[var(--red)]">*</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus
                                       placeholder="Contoh: Bromo Summit Challenge KOM 2026"
                                       class="input w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                @error('title')
                                    <p class="text-[var(--red)] text-xs font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="type" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        Jenis Event <span class="text-[var(--red)]">*</span>
                                    </label>
                                    <select id="type" name="type" required
                                            class="input w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
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
                                           class="input w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                </div>

                                <div>
                                    <label for="start_time" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                        Waktu Start (WIB) <span class="text-[var(--red)]">*</span>
                                    </label>
                                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '06:00') }}" required
                                           class="input w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                </div>
                            </div>

                            <div>
                                <label for="location_name" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                    Lokasi Start / Kota <span class="text-[var(--red)]">*</span>
                                </label>
                                <input type="text" id="location_name" name="location_name" value="{{ old('location_name') }}" required
                                       placeholder="Contoh: Pantai Pandawa, Badung, Bali"
                                       class="input w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                            </div>
                        </div>

                        {{-- FOTO / POSTER EVENT RESMI --}}
                        <div class="space-y-2 pt-2">
                            <label for="poster" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                                Foto / Poster Resmi Event (Tampil di Beranda &amp; Katalog)
                            </label>
                            <div class="p-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] space-y-2">
                                <input type="file" id="poster" name="poster" accept="image/*"
                                       class="block w-full text-xs font-bold text-[var(--ink)] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-2 file:border-[var(--ink)] file:text-xs file:font-black file:bg-[var(--mustard)] file:text-[var(--ink)] file:shadow-[2px_2px_0_var(--ink)] file:cursor-pointer hover:file:bg-[var(--butter)]">
                                <span class="text-[11px] text-[var(--mute)] font-medium block">Format yang didukung: JPG, PNG, atau WEBP (Maksimal 3 MB).</span>
                            </div>
                        </div>

                        {{-- FOTO PETA SIRKUIT / RUTE BALAP --}}
                        <div class="space-y-2 pt-2">
                            <label for="map_image" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                                Foto Peta Sirkuit / Rute Balap (Tampil di Halaman Detail Event)
                            </label>
                            <div class="p-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] space-y-2">
                                <input type="file" id="map_image" name="map_image" accept="image/*"
                                       class="block w-full text-xs font-bold text-[var(--ink)] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-2 file:border-[var(--ink)] file:text-xs file:font-black file:bg-[var(--mustard)] file:text-[var(--ink)] file:shadow-[2px_2px_0_var(--ink)] file:cursor-pointer hover:file:bg-[var(--butter)]">
                                <span class="text-[11px] text-[var(--mute)] font-medium block">Format yang didukung: JPG, PNG, atau WEBP (Maksimal 5 MB). Peta teknis sirkuit atau gambaran rute balap.</span>
                            </div>
                        </div>

                        {{-- 2. KATEGORI TIKET BALAP & REPEATER --}}
                        <div class="space-y-4 pt-4">
                            <div class="flex items-center justify-between pb-2 border-b-2 border-[var(--line)]">
                                <div>
                                    <h2 class="text-sm font-black uppercase tracking-wider text-[var(--ink)]">
                                        2. Kategori Tiket Balap
                                    </h2>
                                    <p class="text-xs text-[var(--mute)] font-medium mt-0.5">Tentukan kategori jarak dan harga pendaftaran. Anda dapat menambahkan beberapa kategori.</p>
                                </div>
                                <button type="button" @click="addCategory()" class="btn btn-sm btn-mustard font-black">
                                    <span>+ Tambah Kategori Tiket</span>
                                </button>
                            </div>

                            <template x-for="(cat, index) in categories" :key="index">
                                <div class="p-5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] shadow-[3px_3px_0_var(--ink)] space-y-4 relative">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="w-6 h-6 rounded-md bg-[var(--orange)] text-white font-black text-xs flex items-center justify-center border border-[var(--ink)]" x-text="index + 1"></span>
                                            <span class="font-heading font-black text-sm text-[var(--ink)]" x-text="'Kategori #' + (index + 1)"></span>

                                            {{-- Radio Button Kategori Utama --}}
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-black px-2.5 py-1 rounded-lg border-2 transition-all shadow-[2px_2px_0_var(--ink)]"
                                                   :class="primaryIndex === index ? 'bg-[var(--mustard)] border-[var(--ink)] text-[var(--ink)]' : 'bg-white border-[var(--ink)]/40 text-[var(--mute)] hover:border-[var(--ink)] hover:text-[var(--ink)]'">
                                                <input type="radio" 
                                                       name="primary_category_index" 
                                                       :value="index" 
                                                       :checked="primaryIndex === index" 
                                                       @change="primaryIndex = index" 
                                                       class="sr-only">
                                                <template x-if="primaryIndex === index">
                                                    <span class="flex items-center gap-1 text-[var(--ink)]">
                                                        <svg class="w-3.5 h-3.5 fill-[var(--ink)]" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                        ★ Kategori Tiket Utama
                                                    </span>
                                                </template>
                                                <template x-if="primaryIndex !== index">
                                                    <span class="flex items-center gap-1">
                                                        Jadikan Kategori Utama
                                                    </span>
                                                </template>
                                            </label>
                                            <input type="hidden" :name="'categories[' + index + '][is_primary]'" :value="primaryIndex === index ? '1' : '0'">
                                        </div>
                                        <button type="button"
                                                x-show="categories.length > 1"
                                                @click="removeCategory(index)"
                                                class="text-xs font-bold text-[var(--red)] hover:underline flex items-center gap-1 cursor-pointer">
                                            <span>&times; Hapus Kategori</span>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1">
                                                Nama Kategori <span class="text-[var(--red)]">*</span>
                                            </label>
                                            <input type="text"
                                                   :name="'categories[' + index + '][name]'"
                                                   x-model="cat.name"
                                                   required
                                                   placeholder="Misal: Gran Fondo 120 KM / Medio 60 KM"
                                                   class="input w-full px-3.5 py-2.5 rounded-lg border-2 border-[var(--ink)] bg-white font-bold text-xs text-[var(--ink)]">
                                        </div>

                                        <div>
                                            <label class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1">
                                                Jarak Tempuh (KM) <span class="text-[var(--red)]">*</span>
                                            </label>
                                            <input type="number" step="0.1"
                                                   :name="'categories[' + index + '][distance_km]'"
                                                   x-model="cat.distance_km"
                                                   required min="1"
                                                   placeholder="120"
                                                   class="input w-full px-3.5 py-2.5 rounded-lg border-2 border-[var(--ink)] bg-white font-bold text-xs text-[var(--ink)]">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div>
                                            <label class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1">
                                                Harga Tiket (Rp) <span class="text-[var(--red)]">*</span>
                                            </label>
                                            <input type="number"
                                                   :name="'categories[' + index + '][price]'"
                                                   x-model="cat.price"
                                                   required min="0"
                                                   placeholder="250000"
                                                   class="input w-full px-3.5 py-2.5 rounded-lg border-2 border-[var(--ink)] bg-white font-bold text-xs text-[var(--ink)]">
                                        </div>

                                        <div>
                                            <label class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1">
                                                Kuota Peserta <span class="text-[var(--red)]">*</span>
                                            </label>
                                            <input type="number"
                                                   :name="'categories[' + index + '][quota]'"
                                                   x-model="cat.quota"
                                                   required min="1"
                                                   placeholder="500"
                                                   class="input w-full px-3.5 py-2.5 rounded-lg border-2 border-[var(--ink)] bg-white font-bold text-xs text-[var(--ink)]">
                                        </div>

                                        <div>
                                            <label class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1">
                                                Batas Cut-Off (Jam)
                                            </label>
                                            <input type="number" step="0.5"
                                                   :name="'categories[' + index + '][cut_off_hours]'"
                                                   x-model="cat.cut_off_hours"
                                                   placeholder="6.0"
                                                   class="input w-full px-3.5 py-2.5 rounded-lg border-2 border-[var(--ink)] bg-white font-bold text-xs text-[var(--ink)]">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- 3. DESKRIPSI EVENT --}}
                        <div class="space-y-4 pt-4">
                            <h2 class="text-sm font-black uppercase tracking-wider text-[var(--ink)] border-b-2 border-[var(--line)] pb-2">
                                3. Deskripsi &amp; Informasi Rute
                            </h2>

                            <div>
                                <label for="description" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)] mb-1.5">
                                    Deskripsi Event <span class="text-[var(--red)]">*</span>
                                </label>
                                <textarea id="description" name="description" rows="5" required
                                          placeholder="Jelaskan mengenai rute, elevasi, fasilitas race pack, water station, ketentuan jersey, dan cut-off time..."
                                          class="input w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="pt-6 border-t-2 border-[var(--ink)]/15 flex items-center justify-between flex-wrap gap-4">
                            <div class="text-xs text-[var(--mute)] font-medium">
                                Mempublikasikan event ini akan memotong <strong>1 kuota race</strong> dari saldo Anda.
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg shadow-[4px_4px_0_var(--ink)]">
                                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Terbitkan Event &amp; Buka Pendaftaran</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </main>
    </div>

    {{-- Logout Confirmation Modal --}}
    <div x-cloak x-show="showLogoutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#10282E]/80 backdrop-blur-xs" role="dialog" aria-modal="true">
        <div class="bg-white border-[3px] border-[#10282E] rounded-2xl p-6 sm:p-7 max-w-sm w-full shadow-[8px_8px_0_#10282E] text-center space-y-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 text-[#C23B22] border-2 border-[#10282E] flex items-center justify-center mx-auto shadow-[2px_2px_0_#10282E]">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </div>
            <div>
                <h3 class="font-heading font-black text-xl text-[#10282E]">Keluar dari Akun?</h3>
                <p class="text-xs text-[var(--mute)] mt-1 font-medium">Anda harus login kembali untuk mengelola event.</p>
            </div>
            <div class="flex items-center gap-2 pt-2">
                <button type="button" @click="showLogoutModal = false" class="btn btn-ghost flex-1 text-xs">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full bg-[#C23B22] hover:bg-red-700 text-white text-xs border-2 border-[#10282E]">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
