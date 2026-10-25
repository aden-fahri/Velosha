<div class="min-h-screen bg-[var(--paper)] text-[var(--ink)] antialiased font-sans py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Top Navigation & Breadcrumb --}}
        <div class="flex items-center justify-between border-b-[3px] border-[var(--ink)] pb-4">
            <a href="{{ route('helpdesk.index') }}" wire:navigate
               class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white border-[2px] border-[var(--ink)] rounded-lg text-xs font-black shadow-[2px_2px_0_var(--ink)] hover:bg-[var(--paper2)] transition">
                ← Kembali ke Antrian
            </a>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-[var(--mute)]">Operator Aktif:</span>
                <span class="px-2.5 py-1 bg-white border-[2px] border-[var(--ink)] rounded-lg text-xs font-extrabold shadow-[2px_2px_0_var(--ink)]">
                    {{ auth()->user()->name }}
                </span>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="p-4 bg-emerald-100 border-[2.5px] border-emerald-900 text-emerald-950 font-bold rounded-xl shadow-[3px_3px_0_var(--ink)] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="font-extrabold">✓ Berhasil:</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 bg-red-100 border-[2.5px] border-red-900 text-red-950 font-bold rounded-xl shadow-[3px_3px_0_var(--ink)] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="font-extrabold">✕ Kendala:</span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Main Ticket Header Card --}}
        <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-6 shadow-[4px_4px_0_var(--ink)] space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-xl font-black font-heading text-[var(--ink)]">#{{ $ticket->id }}</span>
                    
                    {{-- Priority Badge --}}
                    @if ($ticket->priority === \App\Enums\TicketPriority::HIGH)
                        <span class="px-2.5 py-1 rounded-md font-extrabold text-xs bg-red-100 text-red-800 border-[1.5px] border-red-700">
                            Prioritas: High
                        </span>
                    @elseif ($ticket->priority === \App\Enums\TicketPriority::MID)
                        <span class="px-2.5 py-1 rounded-md font-extrabold text-xs bg-amber-100 text-amber-800 border-[1.5px] border-amber-700">
                            Prioritas: Mid
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-md font-extrabold text-xs bg-emerald-100 text-emerald-800 border-[1.5px] border-emerald-700">
                            Prioritas: Low
                        </span>
                    @endif

                    {{-- Status Badge --}}
                    @if ($ticket->status === \App\Enums\TicketStatus::OPEN)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md font-extrabold text-xs bg-blue-100 text-blue-900 border-[1.5px] border-blue-700">
                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span> Open
                        </span>
                    @elseif ($ticket->status === \App\Enums\TicketStatus::IN_PROGRESS)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md font-extrabold text-xs bg-amber-100 text-amber-900 border-[1.5px] border-amber-700">
                            <span class="w-2 h-2 rounded-full bg-amber-600"></span> In Progress
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md font-extrabold text-xs bg-slate-100 text-slate-800 border-[1.5px] border-slate-700">
                            ✓ Closed
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md font-extrabold text-xs bg-purple-100 text-purple-900 border-[1.5px] border-purple-700">
                        {{ $ticket->category?->value ?? 'Lainnya' }}
                    </span>
                </div>

                <div class="text-xs text-[var(--mute)]">
                    Dibuat: <span class="font-bold text-[var(--ink)]">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                    ({{ $ticket->created_at->diffForHumans() }})
                </div>
            </div>

            <h2 class="text-xl sm:text-2xl font-black font-heading text-[var(--ink)]">
                {{ $ticket->subject }}
            </h2>

            {{-- SLA Monitoring Strip --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-3 border-t-[1.5px] border-[var(--line)] text-xs">
                <div class="bg-[var(--paper)] p-3 rounded-lg border border-[var(--ink)]">
                    <span class="text-[10px] font-bold text-[var(--mute)] uppercase block">Target Respons</span>
                    <span class="font-black text-[var(--ink)]">
                        {{ $ticket->response_due_at ? $ticket->response_due_at->format('H:i, d M') : '-' }}
                    </span>
                </div>

                <div class="bg-[var(--paper)] p-3 rounded-lg border border-[var(--ink)]">
                    <span class="text-[10px] font-bold text-[var(--mute)] uppercase block">Respons Pertama</span>
                    <span class="font-black text-[var(--ink)]">
                        {{ $ticket->first_response_at ? $ticket->first_response_at->format('H:i, d M') : 'Belum Dijawab' }}
                    </span>
                </div>

                <div class="bg-[var(--paper)] p-3 rounded-lg border border-[var(--ink)]">
                    <span class="text-[10px] font-bold text-[var(--mute)] uppercase block">Target Resolusi</span>
                    <span class="font-black text-[var(--ink)]">
                        {{ $ticket->resolution_due_at ? $ticket->resolution_due_at->format('H:i, d M') : '-' }}
                    </span>
                </div>

                <div class="bg-[var(--paper)] p-3 rounded-lg border border-[var(--ink)]">
                    <span class="text-[10px] font-bold text-[var(--mute)] uppercase block">Waktu Resolusi</span>
                    <span class="font-black text-[var(--ink)]">
                        @if ($ticket->closed_at)
                            {{ $ticket->resolution_time ?? 0 }} Menit
                        @else
                            Sedang Berjalan
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Two-Column Layout: Left Conversation, Right Operator Controls --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT (Col 1-2): Description & Responses --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Ticket Original Description --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 sm:p-6 shadow-[4px_4px_0_var(--ink)] space-y-2">
                    <div class="flex items-center justify-between border-b pb-2">
                        <span class="text-xs font-black uppercase tracking-wider text-[var(--mute)]">Uraian Masalah Pelapor</span>
                        <span class="text-xs font-bold text-[var(--ink)]">{{ $ticket->reporter?->name ?? 'Pelapor' }}</span>
                    </div>
                    <p class="text-sm text-[var(--ink)] whitespace-pre-line leading-relaxed pt-1">
                        {{ $ticket->description }}
                    </p>
                </div>

                {{-- Conversation Thread --}}
                <div class="space-y-4">
                    <h3 class="text-base font-black font-heading flex items-center gap-2">
                        <span>Riwayat Tanggapan &amp; Balasan</span>
                        <span class="px-2 py-0.5 text-xs bg-white border border-[var(--ink)] rounded-full font-bold">
                            {{ $ticket->responses->count() }}
                        </span>
                    </h3>

                    @if ($ticket->responses->isEmpty())
                        <div class="p-6 bg-white border-[2px] border-[var(--ink)] rounded-xl text-center text-xs text-[var(--mute)] shadow-[2px_2px_0_var(--ink)]">
                            Belum ada tanggapan pada tiket ini. Tulis balasan pertama di bawah.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($ticket->responses as $res)
                                @php
                                    $isStaff = $res->responder && ($res->responder->isOperator() || $res->responder->isAdmin());
                                @endphp
                                <div class="p-4 rounded-xl border-[2px] border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] {{ $isStaff ? 'bg-[var(--card)] ml-4' : 'bg-[#FFF8E7] mr-4' }}">
                                    <div class="flex items-center justify-between gap-2 border-b border-[var(--line)] pb-2 mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-xs text-[var(--ink)]">
                                                {{ $res->responder?->name ?? 'Sistem Velosha' }}
                                            </span>

                                            @if ($res->is_auto_reply)
                                                <span class="px-1.5 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-800 rounded border border-blue-600">
                                                    Otomatis
                                                </span>
                                            @elseif ($isStaff)
                                                <span class="px-1.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded border border-emerald-600">
                                                    Staf
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded border border-amber-600">
                                                    Pelapor
                                                </span>
                                            @endif
                                        </div>

                                        <span class="text-[10px] text-[var(--mute)]">
                                            {{ $res->created_at->format('H:i, d M Y') }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-[var(--ink)] leading-relaxed whitespace-pre-line">
                                        {{ $res->message }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Operator Reply Box --}}
                @if ($ticket->status !== \App\Enums\TicketStatus::CLOSED)
                    <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 shadow-[4px_4px_0_var(--ink)] space-y-3">
                        <label for="operator-reply" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                            Kirim Balasan Operator
                        </label>

                        {{-- Saran Balasan Operator (Templates) --}}
                        @if ($suggestions->isNotEmpty())
                            <div class="p-3 bg-[var(--paper)] border-[2px] border-[var(--ink)] rounded-lg space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-black uppercase tracking-wider text-[var(--ink)] flex items-center gap-1.5">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        Saran Balasan Cepat (Template)
                                    </span>
                                    <span class="text-[10px] font-bold text-[var(--mute)]">Pilih untuk isi otomatis</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    @foreach ($suggestions as $sug)
                                        <button type="button" wire:click="applyTemplate({{ $sug->id }})"
                                                class="text-left p-2.5 rounded-lg border-[1.5px] border-[var(--ink)] bg-white hover:bg-[var(--card)] transition shadow-[2px_2px_0_var(--ink)] flex flex-col justify-between">
                                            <div>
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="px-1.5 py-0.5 text-[9px] font-extrabold bg-amber-100 text-amber-900 rounded border border-amber-500">
                                                        {{ $sug->category?->value ?? 'Umum' }}
                                                    </span>
                                                    <span class="text-[9px] font-semibold text-[var(--mute)]">
                                                        {{ $sug->use_count }}x
                                                    </span>
                                                </div>
                                                <div class="text-[11px] font-bold text-[var(--ink)] line-clamp-1">
                                                    {{ $sug->title }}
                                                </div>
                                            </div>
                                            <div class="text-[10px] text-blue-800 font-extrabold mt-1.5 flex items-center gap-1">
                                                <span>Gunakan &rarr;</span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <textarea id="operator-reply" wire:model="replyMessage" rows="4"
                                  placeholder="Tulis instruksi atau informasi penyelesaian kendala di sini..."
                                  class="w-full p-3 text-xs border-[2px] border-[var(--ink)] rounded-lg bg-[var(--paper)] focus:outline-none focus:bg-white shadow-[2px_2px_0_var(--ink)]"></textarea>

                        @error('replyMessage')
                            <p class="text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[11px] text-[var(--mute)]">
                                Tanggapan manusia pertama akan otomatis mengubah status tiket menjadi <strong>In-Progress</strong>.
                            </span>

                            <button type="button" wire:click="sendReply"
                                    class="px-5 py-2 bg-[var(--orange)] text-white hover:bg-[var(--orange-l)] border-[2px] border-[var(--ink)] rounded-lg font-extrabold text-xs shadow-[2px_2px_0_var(--ink)] transition">
                                Kirim Balasan
                            </button>
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-slate-100 border-[2px] border-slate-600 rounded-xl text-center text-xs font-bold text-slate-700 shadow-[2px_2px_0_var(--ink)]">
                        Tiket ini telah ditutup (Closed). Tidak dapat menerima balasan tambahan sesuai kebijakan helpdesk.
                    </div>
                @endif

            </div>

            {{-- RIGHT (Col 3): Operator Action Sidebar --}}
            <div class="space-y-6">

                {{-- Operator Assignment Card --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 shadow-[4px_4px_0_var(--ink)] space-y-4">
                    <h3 class="font-black font-heading text-sm uppercase tracking-wider text-[var(--ink)]">
                        Penugasan Operator
                    </h3>

                    <div class="text-xs space-y-2">
                        <div>
                            <span class="text-[var(--mute)] block">Operator Bertugas:</span>
                            <span class="font-black text-sm text-[var(--ink)]">
                                {{ $ticket->operator?->name ?? 'Belum Ada (Unassigned)' }}
                            </span>
                        </div>

                        @if ($ticket->status !== \App\Enums\TicketStatus::CLOSED)
                            @if (! $ticket->operator_id)
                                <button type="button" wire:click="claimTicket"
                                        class="w-full py-2 bg-[var(--orange)] text-white font-extrabold rounded-lg border-[2px] border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] hover:bg-[var(--orange-l)] transition">
                                    Klaim Tiket Ini
                                </button>
                            @endif

                            {{-- Reassign Dropdown --}}
                            <div class="pt-2 border-t space-y-2">
                                <label for="assign-operator" class="text-[11px] font-bold text-[var(--mute)] block">
                                    Tugaskan Ke Operator Lain:
                                </label>
                                <select id="assign-operator" wire:model="assignOperatorId"
                                        class="w-full p-2 text-xs font-bold border-[2px] border-[var(--ink)] rounded-lg bg-[var(--paper)] focus:bg-white shadow-[2px_2px_0_var(--ink)]">
                                    <option value="">-- Pilih Staf --</option>
                                    @foreach ($operators as $op)
                                        <option value="{{ $op->id }}">{{ $op->name }} ({{ strtoupper($op->role) }})</option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="updateAssignment"
                                        class="w-full py-1.5 bg-white hover:bg-[var(--paper2)] font-bold text-xs rounded-lg border-[2px] border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] transition">
                                    Simpan Penugasan
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Status Management Card --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 shadow-[4px_4px_0_var(--ink)] space-y-4">
                    <h3 class="font-black font-heading text-sm uppercase tracking-wider text-[var(--ink)]">
                        Penyelesaian &amp; Status
                    </h3>

                    @if ($ticket->status !== \App\Enums\TicketStatus::CLOSED)
                        <div class="space-y-3 text-xs">
                            @if ($ticket->status === \App\Enums\TicketStatus::OPEN)
                                <button type="button" wire:click="reopenOrProgress"
                                        class="w-full py-2 bg-amber-400 font-extrabold text-[var(--ink)] rounded-lg border-[2px] border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] hover:bg-amber-300 transition">
                                    Tandai "In-Progress"
                                </button>
                            @endif

                            <div class="p-3 bg-[var(--paper2)] rounded-lg border-[1.5px] border-[var(--ink)] space-y-2.5">
                                <span class="font-extrabold block text-xs">Tutup Tiket (Closed):</span>

                                <div>
                                    <label for="solution-summary" class="text-[11px] font-bold text-[var(--mute)] block mb-1">
                                        Ringkasan Solusi (untuk rujukan):
                                    </label>
                                    <textarea id="solution-summary" wire:model="solutionSummary" rows="3"
                                              placeholder="Jelaskan langkah penyelesaian yang dilakukan..."
                                              class="w-full p-2 text-xs border border-[var(--ink)] rounded bg-white"></textarea>
                                </div>

                                <label class="flex items-center gap-2 cursor-pointer pt-1">
                                    <input type="checkbox" wire:model="isReference" class="w-4 h-4 rounded border-[2px] border-[var(--ink)]">
                                    <span class="text-[11px] font-bold text-[var(--ink)]">
                                        Jadikan Rujukan Resmi (Muncul di saran anti-duplikasi)
                                    </span>
                                </label>

                                <button type="button" wire:click="closeTicket"
                                        class="w-full py-2 bg-[#1B4D3E] text-white font-black rounded-lg border-[2px] border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] hover:opacity-90 transition">
                                    Selesaikan &amp; Tutup Tiket
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="space-y-2 text-xs">
                            <div class="p-3 bg-emerald-50 border border-emerald-700 rounded-lg">
                                <span class="font-extrabold text-emerald-900 block">Status: Selesai (Closed)</span>
                                <span class="text-[11px] text-emerald-800">
                                    Diselesaikan dalam <strong>{{ $ticket->resolution_time ?? 0 }} menit</strong>.
                                </span>
                            </div>

                            @if ($ticket->solution_summary)
                                <div class="p-3 bg-white border border-[var(--ink)] rounded-lg">
                                    <span class="font-bold text-[11px] text-[var(--mute)] block">Catatan Solusi:</span>
                                    <p class="text-xs font-medium text-[var(--ink)] mt-1">
                                        {{ $ticket->solution_summary }}
                                    </p>
                                    @if ($ticket->is_reference)
                                        <span class="inline-block mt-2 px-2 py-0.5 bg-blue-100 text-blue-900 border border-blue-700 rounded font-bold text-[10px]">
                                            ★ Rujukan Resmi
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Reporter Profile Card --}}
                <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-5 shadow-[4px_4px_0_var(--ink)] space-y-2 text-xs">
                    <h3 class="font-black font-heading text-sm uppercase tracking-wider text-[var(--ink)]">
                        Profil Pelapor
                    </h3>

                    <div>
                        <span class="text-[var(--mute)] block text-[11px]">Nama Lengkap:</span>
                        <span class="font-bold text-sm text-[var(--ink)]">{{ $ticket->reporter?->name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-[var(--mute)] block text-[11px]">Email Terdaftar:</span>
                        <span class="font-bold text-[var(--ink)]">{{ $ticket->reporter?->email ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-[var(--mute)] block text-[11px]">Peran Pengguna:</span>
                        <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-900 font-bold text-[10px] rounded border border-amber-600 mt-0.5">
                            {{ strtoupper($ticket->reporter?->role ?? 'PESERTA') }}
                        </span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
