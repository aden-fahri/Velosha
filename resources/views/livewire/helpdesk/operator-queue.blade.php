<div class="min-h-screen bg-[var(--paper)] text-[var(--ink)] antialiased font-sans py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header Top Bar --}}
        <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-[3px] border-[var(--ink)] pb-5">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-3 h-3 bg-[var(--orange)] rotate-45 border border-[var(--ink)]"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--mute)]">Helpdesk Operator Panel</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-heading mt-1">Antrian Tiket Dukungan</h1>
                <p class="text-sm text-[var(--mute)]">Kelola dan selesaikan kendala rider, pembayaran, dan tiket event Velosha.</p>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border-[2px] border-[var(--ink)] rounded-lg text-xs font-bold shadow-[2px_2px_0_var(--ink)]">
                    <span class="w-2 h-2 rounded-full {{ auth()->user()->isOperator() ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    {{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})
                </span>

                <a href="{{ route('dashboard') }}" wire:navigate class="px-3.5 py-1.5 bg-[var(--paper2)] hover:bg-white text-[var(--ink)] border-[2px] border-[var(--ink)] rounded-lg text-xs font-extrabold shadow-[2px_2px_0_var(--ink)] transition">
                    Portal Rider
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 bg-[#C23B22] text-white border-[2px] border-[var(--ink)] rounded-lg text-xs font-extrabold shadow-[2px_2px_0_var(--ink)] hover:opacity-90 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

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

        {{-- 4 KPI Counter Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- KPI 1: Open --}}
            <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-[var(--mute)] uppercase tracking-wider">Tiket Baru (Open)</span>
                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                </div>
                <div class="mt-2 text-3xl sm:text-4xl font-black font-heading">{{ $kpiOpen }}</div>
                <div class="mt-1 text-xs text-[var(--mute)]">Menunggu respons awal</div>
            </div>

            {{-- KPI 2: In-Progress --}}
            <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-[var(--mute)] uppercase tracking-wider">Dalam Proses</span>
                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
                </div>
                <div class="mt-2 text-3xl sm:text-4xl font-black font-heading">{{ $kpiInProgress }}</div>
                <div class="mt-1 text-xs text-[var(--mute)]">Sedang ditangani operator</div>
            </div>

            {{-- KPI 3: Melewati SLA --}}
            <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)] {{ $kpiBreached > 0 ? 'bg-red-50' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-red-700 uppercase tracking-wider">Melewati SLA</span>
                    <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-pulse"></span>
                </div>
                <div class="mt-2 text-3xl sm:text-4xl font-black font-heading text-red-700">{{ $kpiBreached }}</div>
                <div class="mt-1 text-xs text-red-600 font-semibold">Perlu eskalasi segera</div>
            </div>

            {{-- KPI 4: Selesai Hari Ini --}}
            <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 sm:p-5 shadow-[4px_4px_0_var(--ink)]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider">Selesai Hari Ini</span>
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                </div>
                <div class="mt-2 text-3xl sm:text-4xl font-black font-heading text-emerald-800">{{ $kpiClosedToday }}</div>
                <div class="mt-1 text-xs text-[var(--mute)]">Tiket closed tanggal {{ date('d M') }}</div>
            </div>
        </div>

        {{-- Filter Tabs and Search Bar --}}
        <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl p-4 shadow-[4px_4px_0_var(--ink)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Filter Pills --}}
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="setFilter('all')"
                        class="px-3.5 py-1.5 rounded-lg border-[2px] border-[var(--ink)] text-xs font-bold transition {{ $filter === 'all' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-transparent hover:bg-[var(--paper2)]' }}">
                    Semua Antrian
                </button>
                <button type="button" wire:click="setFilter('open')"
                        class="px-3.5 py-1.5 rounded-lg border-[2px] border-[var(--ink)] text-xs font-bold transition {{ $filter === 'open' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-transparent hover:bg-[var(--paper2)]' }}">
                    Baru (Open)
                </button>
                <button type="button" wire:click="setFilter('in_progress')"
                        class="px-3.5 py-1.5 rounded-lg border-[2px] border-[var(--ink)] text-xs font-bold transition {{ $filter === 'in_progress' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-transparent hover:bg-[var(--paper2)]' }}">
                    Dalam Proses
                </button>
                <button type="button" wire:click="setFilter('my_tickets')"
                        class="px-3.5 py-1.5 rounded-lg border-[2px] border-[var(--ink)] text-xs font-bold transition {{ $filter === 'my_tickets' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-transparent hover:bg-[var(--paper2)]' }}">
                    Tiket Saya
                </button>
                <button type="button" wire:click="setFilter('closed')"
                        class="px-3.5 py-1.5 rounded-lg border-[2px] border-[var(--ink)] text-xs font-bold transition {{ $filter === 'closed' ? 'bg-[var(--mustard)] shadow-[2px_2px_0_var(--ink)]' : 'bg-transparent hover:bg-[var(--paper2)]' }}">
                    Selesai (Closed)
                </button>
            </div>

            {{-- Search Box --}}
            <div class="relative min-w-[260px]">
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Cari subjek, #ID, atau nama pelapor..."
                       class="w-full pl-3 pr-8 py-2 text-xs font-medium border-[2px] border-[var(--ink)] rounded-lg bg-[var(--paper)] focus:outline-none focus:bg-white shadow-[2px_2px_0_var(--ink)]">
                @if ($search !== '')
                    <button type="button" wire:click="$set('search', '')" class="absolute right-2.5 top-2.5 text-xs text-[var(--mute)] hover:text-[var(--ink)]">
                        ✕
                    </button>
                @endif
            </div>
        </div>

        {{-- Ticket List / Table --}}
        <div class="bg-white border-[2.5px] border-[var(--ink)] rounded-xl shadow-[4px_4px_0_var(--ink)] overflow-hidden">
            @if ($tickets->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-full bg-[var(--paper2)] border-[2px] border-[var(--ink)] flex items-center justify-center font-bold text-lg">
                        ☕
                    </div>
                    <h3 class="font-heading font-black text-lg">Tidak Ada Tiket di Antrian</h3>
                    <p class="text-xs text-[var(--mute)] max-w-sm mx-auto">
                        @if ($search !== '')
                            Tidak ada tiket yang cocok dengan kata kunci pencarian "{{ $search }}".
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
                                <th class="py-3 px-4 font-black">#ID / WAKTU</th>
                                <th class="py-3 px-4 font-black">PELAPOR</th>
                                <th class="py-3 px-4 font-black">SUBJEK TIKET</th>
                                <th class="py-3 px-4 font-black">PRIORITAS</th>
                                <th class="py-3 px-4 font-black">STATUS</th>
                                <th class="py-3 px-4 font-black">TENGGAT SLA</th>
                                <th class="py-3 px-4 font-black">OPERATOR</th>
                                <th class="py-3 px-4 font-black text-right">AKSI</th>
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
                                    {{-- ID / Time --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-extrabold text-[var(--ink)]">#{{ $t->id }}</div>
                                        <div class="text-[10px] text-[var(--mute)]">{{ $t->created_at->diffForHumans() }}</div>
                                    </td>

                                    {{-- Reporter --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-[var(--ink)] truncate max-w-[140px]">{{ $t->reporter?->name ?? 'Anonim' }}</div>
                                        <div class="text-[10px] text-[var(--mute)] truncate max-w-[140px]">{{ $t->reporter?->email }}</div>
                                    </td>

                                    {{-- Subject --}}
                                    <td class="py-3.5 px-4 min-w-[200px]">
                                        <a href="{{ route('helpdesk.detail', ['ticket' => $t->id]) }}" wire:navigate class="font-bold text-[var(--ink)] hover:underline line-clamp-1">
                                            {{ $t->subject }}
                                        </a>
                                        <div class="text-[10px] text-[var(--mute)] line-clamp-1 mt-0.5">
                                            {{ \Illuminate\Support\Str::limit($t->description, 60) }}
                                        </div>
                                    </td>

                                    {{-- Priority --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($t->priority === \App\Enums\TicketPriority::HIGH)
                                            <span class="inline-block px-2 py-0.5 rounded font-extrabold text-[10px] bg-red-100 text-red-800 border border-red-700">
                                                High
                                            </span>
                                        @elseif ($t->priority === \App\Enums\TicketPriority::MID)
                                            <span class="inline-block px-2 py-0.5 rounded font-extrabold text-[10px] bg-amber-100 text-amber-800 border border-amber-700">
                                                Mid
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 rounded font-extrabold text-[10px] bg-emerald-100 text-emerald-800 border border-emerald-700">
                                                Low
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($t->status === \App\Enums\TicketStatus::OPEN)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded font-extrabold text-[10px] bg-blue-100 text-blue-900 border border-blue-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Open
                                            </span>
                                        @elseif ($t->status === \App\Enums\TicketStatus::IN_PROGRESS)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded font-extrabold text-[10px] bg-amber-100 text-amber-900 border border-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> In Progress
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded font-extrabold text-[10px] bg-slate-100 text-slate-800 border border-slate-600">
                                                ✓ Closed
                                            </span>
                                        @endif
                                    </td>

                                    {{-- SLA Status --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($t->status === \App\Enums\TicketStatus::CLOSED)
                                            <span class="text-[11px] text-[var(--mute)]">Selesai ({{ $t->resolution_time ?? 0 }}m)</span>
                                        @elseif ($isOverdue)
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-[#C23B22] text-white animate-pulse">
                                                Overdue SLA
                                            </span>
                                        @else
                                            <span class="text-[11px] text-emerald-700 font-semibold">
                                                Target: {{ $t->resolution_due_at ? $t->resolution_due_at->format('H:i d/m') : '-' }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Operator --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($t->operator)
                                            <span class="font-bold text-[var(--ink)]">{{ $t->operator->name }}</span>
                                        @else
                                            <span class="text-[var(--mute)] italic">Belum Ditugaskan</span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center gap-2">
                                            @if ($t->status !== \App\Enums\TicketStatus::CLOSED && ! $t->operator_id)
                                                <button type="button" wire:click="claimTicket({{ $t->id }})"
                                                        class="px-2.5 py-1 bg-[var(--orange)] text-white hover:bg-[var(--orange-l)] border border-[var(--ink)] rounded text-[11px] font-extrabold shadow-[1.5px_1.5px_0_var(--ink)] transition">
                                                    Klaim
                                                </button>
                                            @endif

                                            <a href="{{ route('helpdesk.detail', ['ticket' => $t->id]) }}" wire:navigate
                                               class="px-2.5 py-1 bg-[var(--paper)] hover:bg-white text-[var(--ink)] border border-[var(--ink)] rounded text-[11px] font-bold shadow-[1.5px_1.5px_0_var(--ink)] transition">
                                                Detail →
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-t-[2px] border-[var(--ink)] bg-[var(--paper2)]">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
