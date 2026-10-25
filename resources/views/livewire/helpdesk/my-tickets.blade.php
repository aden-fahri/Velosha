<div wire:poll.10s class="vs min-h-screen bg-[var(--paper)] text-[var(--ink)] flex flex-col md:flex-row">
    <!-- SIDEBAR PORTAL RIDER -->
    <aside class="w-full md:w-64 bg-[#FBF4E6] border-b-2 md:border-b-0 md:border-r-[3px] border-[#10282E] p-6 flex flex-col shrink-0">
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
            <a href="{{ route('helpdesk.create-ticket') }}" wire:navigate class="btn bg-white hover:bg-[var(--paper2)] text-[var(--ink)] border-2 border-[var(--ink)] px-5 py-2 font-bold text-sm shadow-[2px_2px_0_#10282E]">
                Buat tiket
            </a>
            <a href="{{ route('helpdesk.my-tickets') }}" wire:navigate class="btn bg-[var(--mustard)] text-[var(--ink)] border-2 border-[var(--ink)] px-5 py-2 font-bold text-sm shadow-[3px_3px_0_#10282E]">
                Tiket saya
            </a>
        </div>

        <!-- FLASH MESSAGE -->
        @if (session('success'))
            <div class="mt-6 bg-[#D5EBD9] border-2 border-[var(--ink)] p-4 rounded-xl text-sm font-bold text-[var(--ink)] shadow-xs">
                {{ session('success') }}
            </div>
        @endif

        <!-- DUA KOLOM: LIST TIKET KIRI, THREAD KANAN -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mt-8">
            <!-- LIST TIKET (KIRI) -->
            <div class="lg:col-span-5 space-y-4">
                <!-- FILTER TIKET -->
                <div class="flex gap-2">
                    <button type="button" wire:click="setFilter('all')"
                            class="px-4 py-2 rounded-lg font-bold text-xs border-2 border-[var(--ink)] transition {{ $filter === 'all' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_#10282E]' : 'bg-white hover:bg-[var(--paper2)]' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="setFilter('Open')"
                            class="px-4 py-2 rounded-lg font-bold text-xs border-2 border-[var(--ink)] transition {{ $filter === 'Open' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_#10282E]' : 'bg-white hover:bg-[var(--paper2)]' }}">
                        Open
                    </button>
                    <button type="button" wire:click="setFilter('Closed')"
                            class="px-4 py-2 rounded-lg font-bold text-xs border-2 border-[var(--ink)] transition {{ $filter === 'Closed' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_#10282E]' : 'bg-white hover:bg-[var(--paper2)]' }}">
                        Closed
                    </button>
                </div>

                @forelse($tickets as $t)
                <div wire:click="selectTicket({{ $t->id }})"
                     class="cursor-pointer bg-white border-2 border-[var(--ink)] p-4 rounded-xl transition {{ $selectedTicketId === $t->id ? 'bg-[#FBF4E6] shadow-[4px_4px_0_#10282E] border-[2.5px]' : 'hover:bg-[var(--paper)] shadow-[2px_2px_0_#10282E]' }}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono text-xs font-bold text-[var(--mute)]">#TK-{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                        @if($t->isClosed())
                            <span class="chip text-[11px] font-bold bg-[var(--pine-l)] text-[var(--ink)] px-2 py-0.5 rounded border border-[var(--ink)]">Closed</span>
                        @elseif($t->isInProgress())
                            <span class="chip text-[11px] font-bold bg-[#A8DADC] text-[var(--ink)] px-2 py-0.5 rounded border border-[var(--ink)]">In-Progress</span>
                        @else
                            <span class="chip text-[11px] font-bold bg-[var(--mustard)] text-[var(--ink)] px-2 py-0.5 rounded border border-[var(--ink)]">Open</span>
                        @endif
                    </div>
                    <div class="font-heading font-extrabold text-base text-[var(--ink)] mt-2 leading-snug">{{ $t->subject }}</div>
                    <div class="flex items-center justify-between mt-3 text-xs">
                        @if($t->priority->value === 'High')
                            <span class="chip font-bold px-2 py-0.5 rounded bg-[var(--red-l)] text-[var(--ink)] border border-[var(--red)]">High</span>
                        @elseif($t->priority->value === 'Mid')
                            <span class="chip font-bold px-2 py-0.5 rounded bg-[var(--mustard)] text-[var(--ink)] border border-[var(--ink)]">Mid</span>
                        @else
                            <span class="chip font-bold px-2 py-0.5 rounded bg-[var(--pine-l)] text-[var(--ink)] border border-[var(--ink)]">Low</span>
                        @endif
                        <span class="text-[var(--mute)]">{{ $t->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="bg-white border-2 border-dashed border-[var(--line)] p-8 rounded-xl text-center">
                    <p class="text-sm font-bold text-[var(--mute)]">Belum ada tiket pada kategori ini.</p>
                    <a href="{{ route('helpdesk.create-ticket') }}" wire:navigate class="btn bg-[var(--orange)] text-[var(--ink)] border-2 border-[var(--ink)] px-4 py-2 font-bold text-xs mt-3 inline-block">
                        Buat tiket pertama
                    </a>
                </div>
                @endforelse
            </div>

            <!-- DETAIL & THREAD PERCAKAPAN (KANAN) -->
            <div class="lg:col-span-7">
                @if($selectedTicket)
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-2xl shadow-[4px_4px_0_#10282E] overflow-hidden">
                    <!-- HEADER TIKET -->
                    <div class="p-6 bg-[#F8E3B5] border-b-2 border-[var(--ink)] flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs font-black text-[var(--ink)]">#TK-{{ str_pad($selectedTicket->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <div class="flex gap-2">
                                @if($selectedTicket->isClosed())
                                    <span class="chip text-xs font-bold bg-[var(--pine-l)] text-[var(--ink)] px-2.5 py-0.5 rounded border border-[var(--ink)]">Closed</span>
                                @elseif($selectedTicket->isInProgress())
                                    <span class="chip text-xs font-bold bg-[#A8DADC] text-[var(--ink)] px-2.5 py-0.5 rounded border border-[var(--ink)]">In-Progress</span>
                                @else
                                    <span class="chip text-xs font-bold bg-[var(--mustard)] text-[var(--ink)] px-2.5 py-0.5 rounded border border-[var(--ink)]">Open</span>
                                @endif
                                <span class="chip text-xs font-bold px-2.5 py-0.5 rounded bg-white text-[var(--ink)] border border-[var(--ink)]">Prioritas: {{ $selectedTicket->priority->value }}</span>
                            </div>
                        </div>
                        <h2 class="font-heading font-black text-2xl text-[var(--ink)] mt-1">{{ $selectedTicket->subject }}</h2>
                        <div class="text-xs text-[var(--mute)] flex items-center gap-2">
                            <span class="font-bold text-[var(--ink)]">Operator: {{ $selectedTicket->operator ? $selectedTicket->operator->name : 'Belum ditugaskan' }}</span>
                            <span>•</span>
                            <span>Dibuka {{ $selectedTicket->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>

                    <!-- PROGRESS TIMELINE PELAPOR -->
                    <div class="px-6 py-4 bg-[#FBF4E6] border-b-2 border-[var(--ink)]" data-testid="ticket-timeline">
                        <div class="text-[11px] font-black uppercase tracking-wider text-[var(--mute)] mb-3">
                            Status &amp; Progres Penanganan
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            {{-- Step 1: Dibuat --}}
                            <div class="flex items-start gap-2.5 p-2.5 bg-white border-[1.5px] border-[var(--ink)] rounded-xl shadow-[2px_2px_0_var(--ink)]" data-testid="timeline-step-dibuat">
                                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white border-[1.5px] border-[var(--ink)] flex items-center justify-center font-bold text-xs shrink-0">
                                    ✓
                                </div>
                                <div class="min-w-0">
                                    <div class="font-black text-xs text-[var(--ink)]">Dibuat</div>
                                    <div class="text-[11px] text-[var(--mute)] font-medium">
                                        {{ $selectedTicket->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                    </div>
                                </div>
                            </div>

                            {{-- Step 2: Ditugaskan (Only if assigned) --}}
                            @if ($selectedTicket->operator_id && $selectedTicket->operator)
                                <div class="flex items-start gap-2.5 p-2.5 bg-white border-[1.5px] border-[var(--ink)] rounded-xl shadow-[2px_2px_0_var(--ink)]" data-testid="timeline-step-ditugaskan">
                                    <div class="w-6 h-6 rounded-full bg-emerald-500 text-white border-[1.5px] border-[var(--ink)] flex items-center justify-center font-bold text-xs shrink-0">
                                        ✓
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-black text-xs text-[var(--ink)] truncate">
                                            Ditugaskan ke {{ $selectedTicket->operator->name }}
                                        </div>
                                        <div class="text-[11px] text-[var(--mute)] font-medium">
                                            {{ ($selectedTicket->first_response_at ?? $selectedTicket->updated_at)->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Step 3: Diproses (first human reply or In-Progress) --}}
                            @php
                                $isDiproses = $selectedTicket->first_response_at !== null || $selectedTicket->isInProgress() || $selectedTicket->isClosed();
                            @endphp
                            <div class="flex items-start gap-2.5 p-2.5 {{ $isDiproses ? 'bg-white' : 'bg-[#F3E7CF]/50' }} border-[1.5px] border-[var(--ink)] rounded-xl shadow-[2px_2px_0_var(--ink)]" data-testid="timeline-step-diproses">
                                <div class="w-6 h-6 rounded-full {{ $isDiproses ? 'bg-amber-400 text-[var(--ink)]' : 'bg-white text-[var(--mute)] border-dashed' }} border-[1.5px] border-[var(--ink)] flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ $isDiproses ? '✓' : '•' }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-black text-xs {{ $isDiproses ? 'text-[var(--ink)]' : 'text-[var(--mute)]' }}">
                                        Diproses
                                    </div>
                                    <div class="text-[11px] text-[var(--mute)] font-medium">
                                        @if ($isDiproses)
                                            {{ ($selectedTicket->first_response_at ?? $selectedTicket->updated_at)->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                        @else
                                            Menunggu tanggapan
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Step 4: Selesai --}}
                            @php
                                $isClosed = $selectedTicket->isClosed();
                            @endphp
                            <div class="flex items-start gap-2.5 p-2.5 {{ $isClosed ? 'bg-white' : 'bg-[#F3E7CF]/50' }} border-[1.5px] border-[var(--ink)] rounded-xl shadow-[2px_2px_0_var(--ink)]" data-testid="timeline-step-selesai">
                                <div class="w-6 h-6 rounded-full {{ $isClosed ? 'bg-emerald-500 text-white' : 'bg-white text-[var(--mute)] border-dashed' }} border-[1.5px] border-[var(--ink)] flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ $isClosed ? '✓' : '•' }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-black text-xs {{ $isClosed ? 'text-[var(--ink)]' : 'text-[var(--mute)]' }}">
                                        Selesai
                                    </div>
                                    <div class="text-[11px] text-[var(--mute)] font-medium">
                                        @if ($isClosed)
                                            {{ ($selectedTicket->closed_at ?? $selectedTicket->updated_at)->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                        @else
                                            Menunggu penyelesaian
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DAFTAR PESAN (THREAD DUA ARAH) -->
                    <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto bg-[var(--paper)]">
                        <!-- Pesan Pembuka (Deskripsi Tiket oleh Pelapor) -->
                        <div class="msg me ml-auto max-w-[80%] bg-[var(--orange-l)] border-2 border-[var(--ink)] p-4 rounded-2xl rounded-br-xs shadow-xs">
                            <div class="flex items-center justify-between text-xs font-bold mb-1.5 text-[var(--ink)]">
                                <span>Kamu</span>
                                <span class="text-[11px] font-semibold text-[var(--ink)]/70">{{ $selectedTicket->created_at->format('H:i') }}</span>
                            </div>
                            <div class="text-sm leading-relaxed text-[var(--ink)] whitespace-pre-wrap">{{ $selectedTicket->description }}</div>
                        </div>

                        <!-- Balasan-Balasan Selanjutnya -->
                        @foreach($selectedTicket->responses as $resp)
                            @if($resp->is_auto_reply)
                                <!-- Balasan Otomatis -->
                                <div class="msg auto mr-auto max-w-[80%] bg-[var(--pine-l)] border-2 border-dashed border-[var(--ink)] p-4 rounded-2xl shadow-xs">
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5 text-[var(--ink)]">
                                        <div class="flex items-center gap-2">
                                            <span>Balasan Otomatis</span>
                                            <span class="chip text-[10px] bg-white border border-[var(--ink)] px-1.5 py-0.2 rounded font-bold">Bot</span>
                                        </div>
                                        <span class="text-[11px] text-[var(--ink)]/70">{{ $resp->created_at->format('H:i') }}</span>
                                    </div>
                                    <div class="text-sm leading-relaxed text-[var(--ink)] whitespace-pre-wrap">{{ $resp->message }}</div>
                                </div>
                            @elseif($resp->responder_id === Auth::id())
                                <!-- Balasan dari Pelapor (User) -->
                                <div class="msg me ml-auto max-w-[80%] bg-[var(--orange-l)] border-2 border-[var(--ink)] p-4 rounded-2xl rounded-br-xs shadow-xs">
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5 text-[var(--ink)]">
                                        <span>Kamu</span>
                                        <span class="text-[11px] font-semibold text-[var(--ink)]/70">{{ $resp->created_at->format('H:i') }}</span>
                                    </div>
                                    <div class="text-sm leading-relaxed text-[var(--ink)] whitespace-pre-wrap">{{ $resp->message }}</div>
                                </div>
                            @else
                                <!-- Balasan dari Operator / Admin -->
                                <div class="msg op mr-auto max-w-[80%] bg-white border-2 border-[var(--ink)] p-4 rounded-2xl rounded-bl-xs shadow-xs">
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5 text-[var(--ink)]">
                                        <span>{{ $resp->responder->name }} (Operator)</span>
                                        <span class="text-[11px] text-[var(--mute)]">{{ $resp->created_at->format('H:i') }}</span>
                                    </div>
                                    <div class="text-sm leading-relaxed text-[var(--ink)] whitespace-pre-wrap">{{ $resp->message }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- FORM KIRIM BALASAN ATAU BLOK CLOSED -->
                    @if(! $selectedTicket->isClosed())
                    <form wire:submit="sendReply" class="p-4 bg-white border-t-2 border-[var(--ink)]">
                        <label for="replyMsg" class="sr-only">Tulis balasan</label>
                        <textarea id="replyMsg" wire:model="replyMessage" rows="3"
                                  placeholder="Ketik tanggapan atau informasi tambahan..."
                                  class="w-full bg-[var(--paper)] border-2 border-[var(--ink)] p-3 rounded-xl text-sm font-semibold text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--mustard)] resize-none"></textarea>
                        @error('replyMessage') <span class="text-red-700 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror

                        <div class="mt-3 flex justify-end">
                            <button type="submit" wire:loading.attr="disabled"
                                    class="btn bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] border-2 border-[var(--ink)] px-5 py-2 font-heading font-black text-sm shadow-[2px_2px_0_#10282E] flex items-center gap-2">
                                <span>Kirim balasan</span>
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>
                    @else
                    <!-- TIKET CLOSED & SECTION RATING -->
                    <div class="p-6 bg-[#FBF4E6] border-t-2 border-dashed border-[var(--line)]">
                        <div class="text-sm font-bold text-[var(--ink)]">Tiket ini telah ditutup.</div>
                        <p class="text-xs text-[var(--mute)] mt-0.5">Balasan dinonaktifkan untuk tiket yang sudah Closed.</p>

                        <!-- FORM RATING JIKA BELUM DINILAI -->
                        @if(! $selectedTicket->rating && ! $ratingSubmitted)
                        <form wire:submit="submitRating" class="mt-4 pt-4 border-t border-[var(--ink)]/10">
                            <h3 class="font-heading font-black text-lg text-[var(--ink)]">Beri Penilaian Layanan</h3>
                            <p class="text-xs text-[var(--mute)]">Bagaimana pengalamanmu terhadap penanganan kendala ini?</p>

                            <!-- BINTANG RATING DENGAN RADIO INPUT AKSESIBEL KEYBOARD -->
                            <div class="flex items-center gap-3 mt-3" role="radiogroup" aria-label="Nilai kepuasan 1 sampai 5">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer p-1">
                                    <input type="radio" wire:model.live="ratingScore" value="{{ $i }}" class="sr-only">
                                    <svg class="w-8 h-8 transition-transform hover:scale-110" viewBox="0 0 24 24"
                                         fill="{{ $ratingScore >= $i ? '#F4BE3F' : '#FFFDF8' }}"
                                         stroke="#10282E" stroke-width="2" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"></path>
                                    </svg>
                                    <span class="sr-only">{{ $i }} Bintang</span>
                                </label>
                                @endfor
                                <span class="font-heading font-extrabold text-sm ml-2">{{ $ratingScore }} / 5</span>
                            </div>

                            <div class="mt-3">
                                <label for="fb" class="block font-bold text-xs text-[var(--ink)] mb-1">Masukan Tambahan (Opsional)</label>
                                <textarea id="fb" wire:model="ratingFeedback" rows="2"
                                          placeholder="Tulis kritik atau saran singkat..."
                                          class="w-full bg-white border-2 border-[var(--ink)] p-2.5 rounded-lg text-xs font-semibold text-[var(--ink)] focus:outline-none focus:ring-1 focus:ring-[var(--mustard)] resize-none"></textarea>
                            </div>

                            <button type="submit" class="btn bg-[var(--orange)] text-[var(--ink)] border-2 border-[var(--ink)] px-4 py-2 font-heading font-black text-xs shadow-[2px_2px_0_#10282E] mt-3">
                                Kirim Penilaian
                            </button>
                        </form>
                        @elseif($selectedTicket->rating || $ratingSubmitted)
                        <!-- RATING SUDAH DIBERIKAN -->
                        <div class="mt-4 p-4 bg-white border-2 border-[var(--ink)] rounded-xl flex items-center justify-between shadow-xs">
                            <div>
                                <div class="text-xs font-bold text-[var(--mute)]">Penilaian Anda</div>
                                <div class="flex items-center gap-1 mt-1">
                                    @php $score = $selectedTicket->rating ? $selectedTicket->rating->score : $ratingScore; @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5" viewBox="0 0 24 24"
                                         fill="{{ $score >= $i ? '#F4BE3F' : '#FFFDF8' }}"
                                         stroke="#10282E" stroke-width="2" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"></path>
                                    </svg>
                                    @endfor
                                    <span class="font-heading font-bold text-xs ml-1.5">{{ $score }} / 5</span>
                                </div>
                                @if($selectedTicket->rating && $selectedTicket->rating->feedback)
                                <p class="text-xs text-[var(--mute)] mt-1.5 italic">"{{ $selectedTicket->rating->feedback }}"</p>
                                @endif
                            </div>
                            <span class="chip text-[11px] font-bold bg-[var(--pine-l)] text-[var(--ink)] px-2.5 py-1 rounded border border-[var(--ink)]">Selesai Dinilai</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @else
                <div class="bg-white border-2 border-dashed border-[var(--line)] p-12 rounded-2xl text-center">
                    <div class="w-12 h-12 rounded-full bg-[var(--mustard)] border-2 border-[var(--ink)] flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="mouse-pointer-click" class="w-6 h-6 text-[var(--ink)]"></i>
                    </div>
                    <h3 class="font-heading font-black text-lg text-[var(--ink)]">Pilih Tiket Bantuan</h3>
                    <p class="text-xs text-[var(--mute)] max-w-sm mx-auto mt-1">
                        Pilih tiket di sebelah kiri untuk melihat riwayat percakapan dan status penanganan bantuan.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>
