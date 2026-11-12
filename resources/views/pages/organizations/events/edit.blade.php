@extends('layouts.app', ['title' => 'Edit Event: ' . $event->title . ' — ' . $organization->name, 'nav' => false])

@section('content')
<div x-data="{
    sidebarOpen: false,
    showLogoutModal: false,
    categories: {{ json_encode($categories->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'distance_km' => (float)$c->distance_km,
        'price' => (float)$c->price,
        'quota' => (int)$c->quota,
        'cut_off_hours' => (float)$c->cut_off_hours,
        'is_primary' => (bool)$c->is_primary,
    ])->values()->all()) }} || [],
    primaryIndex: 0,
    init() {
        if (!this.categories || this.categories.length === 0) {
            this.categories = [{
                id: null,
                name: 'Gran Fondo 120 KM',
                distance_km: 120,
                price: 250000,
                quota: 500,
                cut_off_hours: 6.0,
                is_primary: true
            }];
            this.primaryIndex = 0;
        } else {
            const foundIdx = this.categories.findIndex(c => c.is_primary);
            this.primaryIndex = foundIdx >= 0 ? foundIdx : 0;
        }
    },
    addCategory() {
        this.categories.push({
            id: null,
            name: '',
            distance_km: 60,
            price: 150000,
            quota: 200,
            cut_off_hours: 4.0,
            is_primary: false
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

                {{-- 2. Buat Event --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}"
                   class="w-full flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    <span class="whitespace-nowrap font-bold text-sm">Buat Event</span>
                </a>

                {{-- 3. Event Saya (Aktif saat edit event) --}}
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
                   class="w-full flex items-center gap-2.5 on">
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

                {{-- 6. Tiket Bantuan / Chat Petugas --}}
                <a href="{{ route('helpdesk.create-ticket', ['from' => 'organizer']) }}" wire:navigate class="w-full flex items-center gap-2.5">
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

    {{-- ==================== MOBILE TOP BAR ==================== --}}
    <div class="md:hidden flex items-center justify-between p-4 bg-[#FBF4E6] border-b-[3px] border-[#10282E] sticky top-0 z-30">
        <a href="/" wire:navigate class="flex items-center gap-2">
            <img src="/images/logo-velosha.png" alt="Velosha" class="h-7 w-auto object-contain" />
            <span class="font-black text-xl tracking-tight text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
        </a>
        <button type="button" @click="sidebarOpen = true" class="p-2 border-2 border-[#10282E] rounded-lg bg-white shadow-[2px_2px_0_#10282E]">
            <svg class="w-5 h-5 text-[#10282E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
    </div>

    {{-- ==================== MOBILE SIDEBAR DRAWER ==================== --}}
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 md:hidden" role="dialog" aria-modal="true">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-[#10282E]/80 backdrop-blur-xs" @click="sidebarOpen = false"></div>

        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="side relative flex flex-col w-72 max-w-full h-full bg-[#FBF4E6] text-[var(--ink)] border-r-[3px] border-[#10282E] p-6 shadow-2xl overflow-y-auto">
            <div class="flex items-center justify-between pb-6 mb-4 border-b border-[#10282E]/15">
                <a href="/" wire:navigate class="flex items-center gap-2.5">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-8 w-auto object-contain" />
                    <span class="font-extrabold text-2xl tracking-tight text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
                </a>
                <button type="button" @click="sidebarOpen = false" class="p-1 rounded-md text-[var(--ink)]">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

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

            <nav class="space-y-1 text-sm font-semibold">
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'overview']) }}" class="w-full">Ringkasan EO</a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'create_event']) }}" class="w-full">Buat Event</a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}" class="w-full on">Event Saya</a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'kuota']) }}" class="w-full">Paket &amp; Kuota</a>
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'keuangan']) }}" class="w-full">Keuangan &amp; Payout</a>
                <a href="{{ route('helpdesk.create-ticket', ['from' => 'organizer']) }}" wire:navigate class="w-full">Chat / Bantuan</a>
                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">BERALIH</div>
                <a href="{{ route('dashboard') }}" wire:navigate class="w-full">Dashboard Rider</a>
                <a href="/" wire:navigate class="w-full">Kembali ke website</a>
            </nav>
        </div>
    </div>

    {{-- ==================== MAIN CONTENT AREA ==================== --}}
    <main class="flex-1 p-6 sm:p-8 lg:p-10 max-w-6xl w-full">
        <div class="space-y-6">

            {{-- Header Breadcrumb & Navigasi --}}
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
                   class="inline-flex items-center gap-2 text-xs font-black text-[var(--ink)] hover:text-[var(--orange)] transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>Kembali ke Daftar Event {{ $organization->name }}</span>
                </a>

                <div class="px-3.5 py-1.5 rounded-xl border-2 border-[var(--ink)] bg-white shadow-[2px_2px_0_var(--ink)] text-xs font-black">
                    Status: <span class="uppercase text-[var(--orange)]">{{ $event->status }}</span>
                </div>
            </div>

            {{-- FORM EDIT EVENT --}}
            <div class="card p-6 sm:p-10 bg-white shadow-[5px_5px_0_#10282E] space-y-6">
                <div class="border-b-2 border-[var(--line)] pb-5">
                    <span class="chip m">Edit Event Balap</span>
                    <h1 class="h3 font-heading text-2xl sm:text-3xl font-black text-[var(--ink)] tracking-tight mt-2" style="font-family: 'Bricolage Grotesque', sans-serif">
                        Perbarui Informasi: {{ $event->title }}
                    </h1>
                    <p class="text-xs sm:text-sm text-[var(--mute)] font-medium mt-1">
                        Sesuaikan detail rute, tanggal race, poster cover, serta tambahkan kategori tiket balap.
                    </p>
                </div>

                <form action="{{ route('organizations.events.update', ['organization' => $organization->slug, 'event' => $event->slug]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Judul Event --}}
                    <div class="space-y-2">
                        <label for="title" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                            Nama / Judul Event <span class="text-[var(--red)]">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $event->title) }}" required
                               class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]">
                        @error('title')
                            <p class="text-xs text-[var(--red)] font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid Tipe, Tanggal, Jam, Lokasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="space-y-2">
                            <label for="type" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                                Jenis Balap <span class="text-[var(--red)]">*</span>
                            </label>
                            <select id="type" name="type" required
                                    class="w-full px-3 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-xs text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                                <option value="gran_fondo" {{ old('type', $event->type) === 'gran_fondo' ? 'selected' : '' }}>Gran Fondo</option>
                                <option value="medio_fondo" {{ old('type', $event->type) === 'medio_fondo' ? 'selected' : '' }}>Medio Fondo</option>
                                <option value="fun_ride" {{ old('type', $event->type) === 'fun_ride' ? 'selected' : '' }}>Fun Ride</option>
                                <option value="climb" {{ old('type', $event->type) === 'climb' ? 'selected' : '' }}>KOM / Climb</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="event_date" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                                Tanggal <span class="text-[var(--red)]">*</span>
                            </label>
                            <input type="date" id="event_date" name="event_date" value="{{ old('event_date', $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : '') }}" required
                                   class="w-full px-3 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-xs text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                        </div>

                        <div class="space-y-2">
                            <label for="start_time" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                                Waktu Start (WIB) <span class="text-[var(--red)]">*</span>
                            </label>
                            <input type="time" id="start_time" name="start_time" value="{{ old('start_time', substr((string) $event->start_time, 0, 5)) }}" required
                                   class="w-full px-3 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-xs text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                        </div>

                        <div class="space-y-2">
                            <label for="location_name" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                                Lokasi Start <span class="text-[var(--red)]">*</span>
                            </label>
                            <input type="text" id="location_name" name="location_name" value="{{ old('location_name', $event->location_name) }}" required
                                   placeholder="Contoh: Alun-Alun Cianjur"
                                   class="w-full px-3 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-xs text-[var(--ink)] shadow-[2px_2px_0_var(--ink)]">
                        </div>
                    </div>

                    {{-- Foto / Poster Event --}}
                    <div class="space-y-2">
                        <label for="poster" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                            Foto / Poster Resmi Event (Tampil di Beranda &amp; Katalog)
                        </label>
                        <div class="p-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] space-y-3">
                            @php($coverPhoto = $event->photos()->orderBy('sort_order')->first())
                            @if ($coverPhoto)
                                <div class="flex items-center gap-3">
                                    <img src="{{ \App\Support\EventCardPresenter::photoUrl($coverPhoto) }}" alt="Poster Saat Ini" class="w-20 h-14 object-cover rounded-lg border-2 border-[var(--ink)]">
                                    <div class="text-xs text-[var(--mute)]">
                                        <strong class="text-[var(--ink)] block">Poster Saat Ini Aktif</strong>
                                        Unggah berkas baru di bawah untuk mengganti cover balap.
                                    </div>
                                </div>
                            @endif
                            <input type="file" id="poster" name="poster" accept="image/*"
                                   class="block w-full text-xs font-bold text-[var(--ink)] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-2 file:border-[var(--ink)] file:text-xs file:font-black file:bg-[var(--mustard)] file:text-[var(--ink)] file:shadow-[2px_2px_0_var(--ink)] file:cursor-pointer hover:file:bg-[var(--butter)]">
                            <span class="text-[11px] text-[var(--mute)] font-medium block">Format: JPG, PNG, atau WEBP (Maksimal 3 MB).</span>
                        </div>
                    </div>

                    {{-- Foto Peta Sirkuit / Rute Balap --}}
                    <div class="space-y-2">
                        <label for="map_image" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                            Foto Peta Sirkuit / Rute Balap (Tampil di Halaman Detail Event)
                        </label>
                        <div class="p-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] space-y-3">
                            @if ($event->map_image_path)
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('storage/' . ltrim($event->map_image_path, '/')) }}" alt="Peta Sirkuit Saat Ini" class="w-20 h-14 object-cover rounded-lg border-2 border-[var(--ink)]">
                                    <div class="text-xs text-[var(--mute)]">
                                        <strong class="text-[var(--ink)] block">Peta Sirkuit Aktif</strong>
                                        Unggah berkas gambar baru di bawah jika ingin mengganti peta sirkuit.
                                    </div>
                                </div>
                            @endif
                            <input type="file" id="map_image" name="map_image" accept="image/*"
                                   class="block w-full text-xs font-bold text-[var(--ink)] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-2 file:border-[var(--ink)] file:text-xs file:font-black file:bg-[var(--mustard)] file:text-[var(--ink)] file:shadow-[2px_2px_0_var(--ink)] file:cursor-pointer hover:file:bg-[var(--butter)]">
                            <span class="text-[11px] text-[var(--mute)] font-medium block">Format: JPG, PNG, atau WEBP (Maksimal 5 MB). Peta teknis sirkuit atau gambaran rute balap.</span>
                        </div>
                    </div>

                    {{-- Kategori Tiket & Repeater --}}
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center justify-between pb-2 border-b border-[var(--ink)]/15">
                            <div>
                                <h3 class="font-heading font-black text-base text-[var(--ink)]">Kategori Tiket Balap</h3>
                                <p class="text-xs text-[var(--mute)] font-medium">Kategori tiket utama wajib diisi. Anda dapat menambahkan beberapa kategori jarak/kelas balap lainnya.</p>
                            </div>
                            <button type="button" @click="addCategory()" class="btn btn-sm btn-mustard">
                                <span>+ Tambah Kategori Tiket</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(cat, idx) in categories" :key="cat.id || idx">
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

                                    <input type="hidden" :name="'categories[' + idx + '][id]'" :value="cat.id">

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                                        <div class="lg:col-span-2">
                                            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Nama Kategori <span class="text-red-600">*</span></label>
                                            <input type="text" :name="'categories[' + idx + '][name]'" x-model="cat.name" required
                                                   placeholder="Contoh: Gran Fondo 120 KM"
                                                   class="w-full px-3 py-2 text-xs font-bold rounded-lg border-2 border-[var(--ink)] bg-white text-[var(--ink)]">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-[var(--ink)] mb-1">Jarak (KM)</label>
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
                        </div>

                        <div>
                            <button type="button" @click="addCategory()" class="btn btn-sm btn-ghost border-2 border-dashed border-[var(--ink)] hover:border-solid w-full sm:w-auto">
                                + Tambah Kategori Tiket
                            </button>
                        </div>
                    </div>

                    {{-- Deskripsi Event --}}
                    <div class="space-y-2 pt-2">
                        <label for="description" class="label block font-bold text-xs uppercase tracking-wider text-[var(--ink)]">
                            Deskripsi &amp; Informasi Rute <span class="text-[var(--red)]">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" required
                                  class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-xs sm:text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]">{{ old('description', $event->description) }}</textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3 pt-4 border-t-2 border-[var(--line)]">
                        <button type="submit" class="btn">
                            Simpan Perubahan Event &rarr;
                        </button>
                        <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}" class="btn btn-ghost">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </main>

    {{-- Logout Modal Confirmation --}}
    <div x-cloak x-show="showLogoutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="showLogoutModal" x-transition.opacity class="fixed inset-0 bg-[#10282E]/80 backdrop-blur-xs" @click="showLogoutModal = false"></div>
        <div x-show="showLogoutModal" x-transition class="card p-6 bg-white shadow-[6px_6px_0_#10282E] max-w-sm w-full relative z-10 space-y-4">
            <h3 class="font-heading text-lg font-black text-[var(--ink)]">Konfirmasi Keluar</h3>
            <p class="text-xs text-[var(--mute)]">Apakah Anda yakin ingin keluar dari akun portal penyelenggara?</p>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="showLogoutModal = false" class="btn btn-ghost btn-sm">Batal</button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-pine">Ya, Keluar</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
