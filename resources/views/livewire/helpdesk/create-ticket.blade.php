<div class="vs min-h-screen bg-[var(--paper)] text-[var(--ink)] flex flex-col md:flex-row" style="display: flex; min-height: 100vh;">
    <!-- SIDEBAR PORTAL RIDER -->
    <aside class="side hidden md:flex flex-col justify-between bg-[#FBF4E6] border-r-[3px] border-[#10282E] p-6 sticky top-0 h-screen overflow-y-auto overflow-x-hidden" style="width: 280px; min-width: 280px; max-width: 280px; flex-shrink: 0;">
        <div>
            <!-- Brand Logo Header (Official Brand Asset) -->
            <div class="flex items-center pb-5 mb-4 border-b border-[#10282E]/15">
                <a href="/" wire:navigate class="flex items-center gap-3 transition-transform duration-150 hover:-translate-y-0.5" style="text-decoration: none" aria-label="Velosha — Beranda">
                    <img src="/images/logo-velosha.png" alt="Velosha" class="h-8 w-auto object-contain" />
                    <span class="font-extrabold text-2xl tracking-tight text-[#10282E]" style="font-family: 'Bricolage Grotesque', sans-serif;">Velosha</span>
                </a>
            </div>

            @php
                $sidebarOrg = Auth::user()->organizations()->first() ?? Auth::user()->ownedOrganizations()->first();
                $hasActiveQuota = $sidebarOrg && $sidebarOrg->quotaBalance() > 0;
                $canAccessOrganizer = (Auth::user()->isPenyelenggara() && $hasActiveQuota) || Auth::user()->isAdmin();
                $isOrganizerContext = (request('from') === 'organizer' || session('helpdesk_context') === 'organizer' || (Auth::user()->isPenyelenggara() && request('from') !== 'rider')) && $sidebarOrg;
            @endphp

            @if ($isOrganizerContext)
                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 mb-2 mt-2">KOMUNITAS / EO</div>
                <div class="px-3 py-2 rounded-xl border-2 border-[var(--ink)] bg-white shadow-[2px_2px_0_var(--ink)] mb-3">
                    <div class="font-black text-sm text-[var(--ink)] truncate">{{ $sidebarOrg->name }}</div>
                    <div class="text-[11px] font-bold text-[var(--pine)]">{{ $sidebarOrg->quotaBalance() }} Kuota Tersedia</div>
                </div>

                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 mb-2">NAVIGASI PENYELENGGARA</div>
                <nav class="space-y-1 text-sm font-semibold">
                    <a href="{{ route('organizations.show', ['organization' => $sidebarOrg->slug, 'tab' => 'overview']) }}" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Ringkasan EO</span>
                    </a>
                    <a href="{{ route('organizations.show', ['organization' => $sidebarOrg->slug, 'tab' => 'create_event']) }}" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Buat Event</span>
                    </a>
                    <a href="{{ route('organizations.show', ['organization' => $sidebarOrg->slug, 'tab' => 'events']) }}" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Event Saya</span>
                    </a>
                    <a href="{{ route('organizations.show', ['organization' => $sidebarOrg->slug, 'tab' => 'kuota']) }}" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path><path d="M12 18V6"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Paket &amp; Kuota</span>
                    </a>
                    <a href="{{ route('organizations.show', ['organization' => $sidebarOrg->slug, 'tab' => 'keuangan']) }}" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Keuangan &amp; Payout</span>
                    </a>
                    <a href="{{ route('helpdesk.create-ticket', ['from' => 'organizer']) }}" wire:navigate class="w-full flex items-center gap-2.5 on">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Chat / Bantuan</span>
                    </a>

                    <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">BERALIH</div>
                    <a href="/dashboard" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1-5 15-5 16 0"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Dashboard Rider</span>
                    </a>
                    <a href="/" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Kembali ke website</span>
                    </a>
                </nav>
            @else
                <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 mb-2 mt-2">NAVIGASI</div>
                <nav class="space-y-1 text-sm font-semibold">
                    <a href="/dashboard" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Ringkasan Balap</span>
                    </a>

                    <a href="/dashboard" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9a3 3 0 0 0 0 6v3h18v-3a3 3 0 0 1 0-6V6H3z"></path><path d="M14 6v12" stroke-dasharray="2 3"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Tiket &amp; BIB Saya</span>
                    </a>

                    <a href="/events" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"></rect><path d="M8 3v4M16 3v4M3 11h18"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Jadwal balap</span>
                    </a>

                    <a href="/dashboard" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c1-5 15-5 16 0"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Profil &amp; akun</span>
                    </a>

                    <a href="{{ route('helpdesk.my-tickets', ['from' => 'rider']) }}" wire:navigate class="w-full flex items-center gap-2.5 on">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H9l-5 4z"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Tiket Bantuan</span>
                    </a>

                    <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">PUBLIK</div>

                    <a href="/" wire:navigate class="w-full flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"></path></svg>
                        <span class="whitespace-nowrap font-bold text-sm">Kembali ke website</span>
                    </a>

                    <div class="text-[11px] font-bold tracking-wider text-[var(--mute)] uppercase px-3 pt-5 pb-1">PENYELENGGARA</div>

                    @if ($canAccessOrganizer && $sidebarOrg)
                        <a href="{{ route('organizations.show', $sidebarOrg) }}" wire:navigate class="w-full flex items-center gap-2.5">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span class="whitespace-nowrap font-bold text-sm">Dasbor Penyelenggara</span>
                        </a>
                    @else
                        <a href="{{ route('packages.index') }}" wire:navigate class="w-full flex items-center gap-2.5 group" title="Beli kuota paket untuk membuka dasbor penyelenggara">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="#DE7119" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span class="whitespace-nowrap font-bold text-sm text-[var(--ink)]">Penyelenggara</span>
                        </a>
                    @endif
                </nav>
            @endif
        </div>

        <!-- Footer User Profile -->
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

    <!-- KONTEN UTAMA -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen" style="flex: 1 1 0%; min-width: 0;">
        <main class="p-6 sm:p-10 min-w-0 flex-grow">
        <div class="text-xs font-extrabold tracking-wider text-[var(--mute)] uppercase">DASHBOARD RIDER</div>
        <h1 class="font-heading font-black text-3xl sm:text-4xl text-[var(--ink)] mt-1">Tiket bantuan</h1>
        <p class="text-[var(--mute)] text-sm sm:text-base mt-1">Laporkan kendala dan pantau penanganannya di sini.</p>

        <!-- SUB NAV FILTER -->
        <div class="flex gap-2.5 mt-6 border-b-2 border-[var(--line)] pb-4">
            <a href="{{ route('helpdesk.create-ticket') }}" wire:navigate class="btn bg-[var(--mustard)] text-[var(--ink)] border-2 border-[var(--ink)] px-5 py-2 font-bold text-sm shadow-[3px_3px_0_#10282E]">
                Buat tiket
            </a>
            <a href="{{ route('helpdesk.my-tickets') }}" wire:navigate class="btn bg-white hover:bg-[var(--paper2)] text-[var(--ink)] border-2 border-[var(--ink)] px-5 py-2 font-bold text-sm shadow-[2px_2px_0_#10282E]">
                Tiket saya
            </a>
        </div>

        <!-- GRID DUA KOLOM: FORM KIRI, ADUAN SERUPA KANAN -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mt-8">
            <!-- FORMULIR -->
            <form wire:submit="submit" class="lg:col-span-7 bg-white border-[2.5px] border-[var(--ink)] p-6 sm:p-8 rounded-2xl shadow-[4px_4px_0_#10282E]">
                <h2 class="font-heading font-extrabold text-2xl text-[var(--ink)]">Ceritakan kendalamu</h2>

                <div class="mt-5">
                    <label for="cat" class="block font-bold text-sm text-[var(--ink)] mb-1.5">Kategori Kendala</label>
                    <select id="cat" wire:model="category"
                            class="w-full bg-[var(--paper)] border-2 border-[var(--ink)] p-3 rounded-lg text-sm font-semibold text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)]">
                        @foreach(\App\Enums\Helpdesk\TicketCategory::cases() as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->value }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="text-red-700 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="mt-5">
                    <label for="sub" class="block font-bold text-sm text-[var(--ink)] mb-1.5">Subjek</label>
                    <input id="sub" type="text" wire:model.live.debounce.400ms="subject"
                           placeholder="Contoh: E-ticket belum muncul setelah bayar"
                           class="w-full bg-[var(--paper)] border-2 border-[var(--ink)] p-3 rounded-lg text-sm font-semibold text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)]">
                    @error('subject') <span class="text-red-700 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    <div class="text-xs text-[var(--mute)] mt-1.5">Saat kamu mengetik, sistem mencari aduan serupa di samping.</div>
                </div>

                <div class="mt-5">
                    <label for="desc" class="block font-bold text-sm text-[var(--ink)] mb-1.5">Deskripsi</label>
                    <textarea id="desc" wire:model.blur="description" rows="5"
                              placeholder="Jelaskan detail kendala, nomor invoice/BIB, atau kendala transaksi..."
                              class="w-full bg-[var(--paper)] border-2 border-[var(--ink)] p-3.5 rounded-lg text-sm font-semibold text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] resize-none"></textarea>
                    @error('description') <span class="text-red-700 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="mt-5">
                    <label class="block font-bold text-sm text-[var(--ink)] mb-2">Tingkat Prioritas</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer border-2 border-[var(--ink)] rounded-xl p-3 flex flex-col gap-1 transition {{ $priority === 'Low' ? 'bg-[var(--orange)] shadow-[3px_3px_0_#10282E]' : 'bg-[var(--card)] hover:bg-[var(--paper2)]' }}">
                            <input type="radio" wire:model.live="priority" value="Low" class="sr-only">
                            <span class="font-heading font-bold text-base {{ $priority === 'Low' ? 'text-[var(--ink)]' : 'text-[var(--ink)]' }}">Low</span>
                            <small class="text-xs {{ $priority === 'Low' ? 'text-[var(--ink)] font-semibold' : 'text-[var(--mute)]' }}">Pertanyaan umum</small>
                        </label>
                        <label class="cursor-pointer border-2 border-[var(--ink)] rounded-xl p-3 flex flex-col gap-1 transition {{ $priority === 'Mid' ? 'bg-[var(--orange)] shadow-[3px_3px_0_#10282E]' : 'bg-[var(--card)] hover:bg-[var(--paper2)]' }}">
                            <input type="radio" wire:model.live="priority" value="Mid" class="sr-only">
                            <span class="font-heading font-bold text-base {{ $priority === 'Mid' ? 'text-[var(--ink)]' : 'text-[var(--ink)]' }}">Mid</span>
                            <small class="text-xs {{ $priority === 'Mid' ? 'text-[var(--ink)] font-semibold' : 'text-[var(--mute)]' }}">Mengganggu</small>
                        </label>
                        <label class="cursor-pointer border-2 border-[var(--ink)] rounded-xl p-3 flex flex-col gap-1 transition {{ $priority === 'High' ? 'bg-[var(--orange)] shadow-[3px_3px_0_#10282E]' : 'bg-[var(--card)] hover:bg-[var(--paper2)]' }}">
                            <input type="radio" wire:model.live="priority" value="High" class="sr-only">
                            <span class="font-heading font-bold text-base {{ $priority === 'High' ? 'text-[var(--ink)]' : 'text-[var(--ink)]' }}">High</span>
                            <small class="text-xs {{ $priority === 'High' ? 'text-[var(--ink)] font-semibold' : 'text-[var(--mute)]' }}">Mendesak</small>
                        </label>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" wire:loading.attr="disabled"
                            class="btn bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] border-2 border-[var(--ink)] px-6 py-3 font-heading font-black text-base shadow-[3px_3px_0_#10282E] flex items-center gap-2">
                        <span>Kirim tiket</span>
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>

            <!-- PANEL ADUAN SERUPA -->
            <aside class="lg:col-span-5 space-y-4" aria-label="Aduan serupa">
                @if($feedbackMessage)
                <div class="bg-[var(--pine-l)] border-2 border-[var(--ink)] p-4 rounded-xl shadow-xs">
                    <div class="flex items-center gap-2 text-sm font-bold text-[var(--ink)]">
                        <i data-lucide="check-circle" class="w-4 h-4 text-[var(--pine)]"></i>
                        <span>{{ $feedbackMessage }}</span>
                    </div>
                </div>
                @endif

                <div class="bg-[#F8E3B5] border-2 border-[var(--ink)] p-4 rounded-xl flex items-center gap-3.5 shadow-xs">
                    <div class="w-10 h-10 rounded-lg bg-[#F4BE3F] border border-[var(--ink)] flex items-center justify-center shrink-0">
                        <i data-lucide="copy" class="w-5 h-5 text-[var(--ink)]"></i>
                    </div>
                    <div>
                        <div class="font-heading font-black text-base text-[var(--ink)]">
                            {{ count($similarTickets) > 0 ? 'Ada ' . count($similarTickets) . ' aduan serupa' : 'Pencarian Aduan Serupa' }}
                        </div>
                        <div class="text-xs text-[var(--ink)]/80 mt-0.5">Cek dulu, mungkin solusi kendalamu sudah tersedia.</div>
                    </div>
                </div>

                @if(count($similarTickets) > 0)
                <div class="space-y-3">
                    @foreach($similarTickets as $sim)
                    <article class="bg-white border-2 border-[var(--ink)] p-4 rounded-xl shadow-xs">
                        <div class="flex items-center justify-between gap-2">
                            @if($sim['is_own_ticket'])
                                <span class="chip text-[11px] font-bold bg-[var(--mustard)] text-[var(--ink)] px-2 py-0.5 rounded border border-[var(--ink)]">Tiket Anda</span>
                            @else
                                <span class="chip text-[11px] font-bold bg-[var(--pine-l)] text-[var(--ink)] px-2 py-0.5 rounded border border-[var(--ink)]">Rujukan Resmi</span>
                            @endif
                            <span class="chip text-[11px] font-bold {{ $sim['is_closed'] ? 'bg-[var(--pine-l)]' : 'bg-[var(--mustard)]' }} text-[var(--ink)] px-2 py-0.5 rounded border border-[var(--ink)]">{{ $sim['status'] }}</span>
                        </div>
                        <h3 class="font-heading font-bold text-base text-[var(--ink)] mt-2.5">{{ $sim['subject'] }}</h3>
                        @if(!empty($sim['solution_summary']))
                        <div class="text-xs text-[var(--ink)]/90 mt-2 p-2.5 bg-[#FFFDF8] border border-[#10282E]/20 rounded-md">
                            <b class="text-[var(--pine)] block mb-1">Solusi Pengelola:</b>
                            <p class="leading-relaxed">{{ $sim['solution_summary'] }}</p>
                        </div>
                        @endif
                        <div class="flex items-center gap-2 mt-3 pt-2.5 border-t border-[var(--line)]">
                            <button type="button" wire:click="markAnswered({{ $sim['id'] }})"
                                    class="btn bg-[var(--pine-l)] hover:bg-[#bfe2c6] text-[var(--ink)] text-xs font-bold px-3 py-1.5 rounded-lg border border-[var(--ink)] transition">
                                Ini menjawab
                            </button>
                            <button type="button" wire:click="rejectAnswerAndSubmit({{ $sim['id'] }})"
                                    class="btn bg-white hover:bg-[var(--paper2)] text-[var(--ink)] text-xs font-bold px-3 py-1.5 rounded-lg border border-[var(--ink)] transition">
                                Tetap kirim
                            </button>
                        </div>
                    </article>
                    @endforeach
                </div>
                @else
                <div class="bg-white/60 border-2 border-dashed border-[var(--line)] p-6 rounded-xl text-center text-xs text-[var(--mute)]">
                    Ketik subjek atau deskripsi di samping untuk melihat aduan dan jawaban serupa secara otomatis.
                </div>
                @endif

                <div class="bg-[#D5EBD9] border-2 border-[var(--ink)] p-5 rounded-xl shadow-xs">
                    <div class="font-heading font-extrabold text-sm text-[var(--ink)]">Saran jawaban otomatis</div>
                    <p class="text-xs text-[var(--ink)]/90 mt-1.5 leading-relaxed">
                        E-ticket terbit otomatis setelah pembayaran terkonfirmasi. Pastikan cek tab Tiket &amp; BIB Saya di Portal.
                    </p>
                </div>
            </aside>
        </div>
    </main>
    </div>
</div>
