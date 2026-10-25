<div class="vs min-h-screen bg-[var(--paper)] text-[var(--ink)] flex flex-col md:flex-row">
    <!-- SIDEBAR PORTAL RIDER -->
    <aside class="w-full md:w-68 bg-[#FBF4E6] border-b-2 md:border-b-0 md:border-r-[3px] border-[#10282E] p-6 flex flex-col shrink-0">
        <div class="flex items-center justify-between pb-6 mb-2 border-b border-[#10282E]/10">
            <a href="/" wire:navigate class="font-heading font-extrabold text-2xl text-[var(--ink)] tracking-tight">Velosha</a>
            <span class="chip font-bold text-xs bg-[var(--mustard)] border border-[var(--ink)] px-2 py-0.5 rounded">Portal</span>
        </div>

        <div class="text-[11px] font-extrabold tracking-wider text-[var(--mute)] uppercase px-2 mb-2">NAVIGASI</div>
        <nav class="space-y-1 text-sm font-semibold">
            <a href="/dashboard" wire:navigate class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-[var(--ink)] hover:bg-[#F3E7CF] transition">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>Ringkasan musim</span>
            </a>
            <a href="/dashboard" wire:navigate class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-[var(--ink)] hover:bg-[#F3E7CF] transition">
                <div class="flex items-center gap-3">
                    <i data-lucide="ticket" class="w-4 h-4"></i>
                    <span>Tiket &amp; BIB</span>
                </div>
            </a>
            <a href="/events" wire:navigate class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-[var(--ink)] hover:bg-[#F3E7CF] transition">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                <span>Jadwal balap</span>
            </a>
            <a href="/dashboard" wire:navigate class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-[var(--ink)] hover:bg-[#F3E7CF] transition">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span>Profil &amp; akun</span>
            </a>
            <a href="{{ route('helpdesk.my-tickets') }}" wire:navigate class="flex items-center justify-between px-3.5 py-2.5 rounded-lg bg-[var(--mustard)] border-2 border-[var(--ink)] font-bold shadow-xs">
                <div class="flex items-center gap-3">
                    <i data-lucide="life-buoy" class="w-4 h-4 text-[var(--ink)]"></i>
                    <span>Tiket bantuan</span>
                </div>
                @if($activeTicketCount > 0)
                <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-[var(--orange)] text-[var(--ink)] border border-[var(--ink)]">{{ $activeTicketCount }}</span>
                @endif
            </a>
        </nav>

        <div class="text-[11px] font-extrabold tracking-wider text-[var(--mute)] uppercase px-2 mt-6 mb-2">PUBLIK</div>
        <nav class="space-y-1 text-sm font-semibold">
            <a href="/" wire:navigate class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-[var(--ink)] hover:bg-[#F3E7CF] transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke website</span>
            </a>
        </nav>

        <div class="mt-auto pt-6">
            <div class="bg-white border-2 border-[var(--ink)] p-3 rounded-xl flex items-center gap-3 shadow-[3px_3px_0_#10282E]">
                <div class="w-10 h-10 rounded-lg bg-[var(--orange)] text-[var(--ink)] flex items-center justify-center font-extrabold text-sm border-2 border-[var(--ink)]">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-sm truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-[var(--mute)] truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 p-6 sm:p-10 min-w-0">
        <div class="text-xs font-extrabold tracking-wider text-[var(--mute)] uppercase">PORTAL RIDER</div>
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
                    <label for="sub" class="block font-bold text-sm text-[var(--ink)] mb-1.5">Subjek</label>
                    <input id="sub" type="text" wire:model.live.debounce.400ms="subject"
                           placeholder="Contoh: E-ticket belum muncul setelah bayar"
                           class="w-full bg-[var(--paper)] border-2 border-[var(--ink)] p-3 rounded-lg text-sm font-semibold text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)]">
                    @error('subject') <span class="text-red-700 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    <div class="text-xs text-[var(--mute)] mt-1.5">Saat kamu mengetik, sistem mencari aduan serupa di samping.</div>
                </div>

                <div class="mt-5">
                    <label for="desc" class="block font-bold text-sm text-[var(--ink)] mb-1.5">Deskripsi</label>
                    <textarea id="desc" wire:model="description" rows="5"
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
                            <span class="chip text-[11px] font-bold bg-[var(--pine-l)] text-[var(--ink)] px-2 py-0.5 rounded border border-[var(--ink)]">Rujukan Resmi</span>
                            <span class="chip text-[11px] font-bold {{ $sim['is_closed'] ? 'bg-[var(--pine-l)]' : 'bg-[var(--mustard)]' }} text-[var(--ink)] px-2 py-0.5 rounded border border-[var(--ink)]">{{ $sim['is_closed'] ? 'Selesai' : 'Aktif' }}</span>
                        </div>
                        <h3 class="font-heading font-bold text-base text-[var(--ink)] mt-2.5">{{ $sim['subject'] }}</h3>
                        <div class="text-xs text-[var(--ink)]/90 mt-2 p-2.5 bg-[#FFFDF8] border border-[#10282E]/20 rounded-md">
                            <b class="text-[var(--pine)] block mb-1">Solusi Pengelola:</b>
                            <p class="leading-relaxed">{{ $sim['solution_summary'] }}</p>
                        </div>
                    </article>
                    @endforeach
                </div>
                @else
                <div class="bg-white/60 border-2 border-dashed border-[var(--line)] p-6 rounded-xl text-center text-xs text-[var(--mute)]">
                    Ketik subjek di samping untuk melihat aduan dan jawaban serupa secara otomatis.
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
