@extends('layouts.app', ['title' => 'Keuangan & Pencairan Dana — ' . $organization->name, 'nav' => false])

@section('content')
<x-layout.organizer-navbar :organization="$organization" />

<section class="py-8 px-4 sm:px-6 lg:px-8 bg-[var(--paper)] min-h-[calc(100vh-64px)]">
    <div class="max-w-6xl mx-auto space-y-8">

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

        {{-- Header Kartu Komunitas Penyelenggara --}}
        <div class="p-6 sm:p-8 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[6px_6px_0_var(--ink)] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="chip font-black text-xs uppercase tracking-wider">
                    Penyelenggara
                </span>

                <h1 class="text-3xl sm:text-4xl font-black text-[var(--ink)] tracking-tight" style="font-family: 'Bricolage Grotesque', sans-serif">
                    {{ $organization->name }}
                </h1>
                <p class="text-xs sm:text-sm text-[var(--mute)] max-w-2xl font-medium">
                    Kelola saldo hasil penjualan tiket race, rekening bank terdaftar, dan ajukan penarikan dana ke rekening komunitas.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('organizations.events.create', $organization) }}"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-[var(--orange)] text-white font-extrabold text-sm rounded-xl border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>+ Buat Event Baru</span>
                </a>
            </div>
        </div>

        {{-- 4 Tab Navigasi Neobrutalist --}}
        <div class="flex flex-wrap gap-2.5 border-b-2 border-[var(--line)] pb-2">
            <a href="{{ route('organizations.show', ['organization' => $organization->slug, 'tab' => 'events']) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Event Saya</span>
            </a>

            <a href="{{ route('organizations.quota', $organization) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path><path d="M12 18V6"></path></svg>
                <span>Paket &amp; Kuota</span>
            </a>

            <a href="{{ route('organizations.finance', $organization) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--mustard)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)] -translate-y-0.5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                <span>Keuangan &amp; Payout</span>
            </a>

            <a href="{{ route('organizations.verification', $organization) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Verifikasi Identitas</span>
            </a>
        </div>

        {{-- 4 Stat Cards Keuangan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl border-2 border-[var(--ink)] bg-[var(--card)] shadow-[3px_3px_0_var(--ink)]">
                <div class="text-[10px] font-black uppercase text-[var(--mute)]">Total Penjualan Kotor</div>
                <div class="text-2xl font-black text-[var(--ink)] mt-1" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Rp {{ number_format($totalSales, 0, ',', '.') }}
                </div>
                <div class="text-[10px] font-bold text-[var(--mute)] mt-0.5">Akumulasi tiket race terjual</div>
            </div>

            <div class="p-5 rounded-2xl border-2 border-[var(--ink)] bg-[var(--pine-l)] shadow-[3px_3px_0_var(--ink)]">
                <div class="text-[10px] font-black uppercase text-[var(--pine)]">Saldo Siap Tarik</div>
                <div class="text-2xl font-black text-[var(--pine)] mt-1" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </div>
                <div class="text-[10px] font-bold text-[var(--ink)] mt-0.5">Bisa diajukan penarikan</div>
            </div>

            <div class="p-5 rounded-2xl border-2 border-[var(--ink)] bg-[var(--butter)] shadow-[3px_3px_0_var(--ink)]">
                <div class="text-[10px] font-black uppercase text-[var(--ink)]">Dalam Proses Payout</div>
                <div class="text-2xl font-black text-[var(--ink)] mt-1" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Rp {{ number_format($pendingPayout, 0, ',', '.') }}
                </div>
                <div class="text-[10px] font-bold text-[var(--mute)] mt-0.5">Menunggu transfer admin</div>
            </div>

            <div class="p-5 rounded-2xl border-2 border-[var(--ink)] bg-[var(--card)] shadow-[3px_3px_0_var(--ink)]">
                <div class="text-[10px] font-black uppercase text-[var(--mute)]">Total Telah Dicairkan</div>
                <div class="text-2xl font-black text-[var(--ink)] mt-1" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Rp {{ number_format($totalPaidOut, 0, ',', '.') }}
                </div>
                <div class="text-[10px] font-bold text-[var(--mute)] mt-0.5">Berhasil masuk rekening</div>
            </div>
        </div>

        {{-- Rekening Bank & Formulir Payout --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Rekening Bank --}}
            <div class="p-6 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[5px_5px_0_var(--ink)] space-y-4">
                <h3 class="text-xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Rekening Bank Komunitas
                </h3>

                @if ($accounts->isEmpty())
                    <p class="text-xs text-[var(--mute)] font-medium">Belum ada rekening bank yang didaftarkan.</p>
                @else
                    <div class="space-y-2.5">
                        @foreach ($accounts as $acc)
                            <div class="p-3.5 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] flex items-center justify-between">
                                <div>
                                    <div class="font-black text-sm text-[var(--ink)]">{{ $acc->bank_name }} &bull; {{ $acc->account_number }}</div>
                                    <div class="text-xs text-[var(--mute)] font-semibold">a.n. {{ $acc->account_name }}</div>
                                </div>
                                @if ($acc->is_primary)
                                    <span class="px-2 py-0.5 bg-[var(--pine)] text-white text-[10px] font-black rounded-md border border-[var(--ink)]">Utama</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Formulir Tambah Rekening --}}
                <form action="{{ route('organizations.finance.account.store', $organization) }}" method="POST" class="pt-4 border-t-2 border-[var(--line)] space-y-3">
                    @csrf
                    <h4 class="text-xs font-black uppercase tracking-wider text-[var(--ink)]">+ Tambah Rekening Baru</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <input type="text" name="bank_name" placeholder="Bank (BCA/Mandiri)" required class="px-3 py-2 text-xs rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-[var(--ink)]">
                        <input type="text" name="account_number" placeholder="Nomor Rekening" required class="px-3 py-2 text-xs rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-[var(--ink)]">
                        <input type="text" name="account_name" placeholder="Nama Pemilik" required class="px-3 py-2 text-xs rounded-lg border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-[var(--ink)]">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-[var(--card)] hover:bg-[var(--paper2)] text-[var(--ink)] font-black text-xs rounded-lg border-2 border-[var(--ink)] shadow-[2px_2px_0_var(--ink)] transition">
                        Simpan Rekening
                    </button>
                </form>
            </div>

            {{-- Formulir Ajukan Payout --}}
            <div class="p-6 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[5px_5px_0_var(--ink)] space-y-4">
                <h3 class="text-xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Ajukan Pencairan Dana (Payout)
                </h3>

                @if ($accounts->isEmpty())
                    <div class="p-4 rounded-xl bg-[var(--butter)] border-2 border-[var(--ink)] text-xs font-bold text-[var(--ink)]">
                        Anda wajib mendaftarkan minimal 1 rekening bank sebelum dapat mengajukan pencairan dana.
                    </div>
                @else
                    <form action="{{ route('organizations.finance.payout', $organization) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-[var(--ink)] mb-1">
                                Pilih Rekening Tujuan
                            </label>
                            <select name="payout_account_id" required class="w-full px-3 py-2.5 text-xs font-bold rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] text-[var(--ink)]">
                                @foreach ($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_number }} (a.n. {{ $acc->account_name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-[var(--ink)] mb-1">
                                Nominal Penarikan (Rp)
                            </label>
                            <input type="number" name="amount" min="10000" max="{{ $availableBalance }}" required placeholder="Contoh: 500000" class="w-full px-3 py-2.5 text-xs font-bold rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] text-[var(--ink)]">
                            <div class="text-[11px] font-bold text-[var(--mute)] mt-1">
                                Maksimal saldo siap tarik: Rp {{ number_format($availableBalance, 0, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-[var(--ink)] mb-1">
                                Catatan Penarikan (Opsional)
                            </label>
                            <input type="text" name="notes" placeholder="Misal: Penarikan batch 1 pendaftaran race" class="w-full px-3 py-2.5 text-xs font-bold rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] text-[var(--ink)]">
                        </div>

                        <button type="submit"
                                {{ $availableBalance < 10000 ? 'disabled' : '' }}
                                class="w-full py-3 bg-[var(--pine)] text-white font-black text-xs uppercase tracking-wider rounded-xl border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            Kirim Pengajuan Pencairan Dana →
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Riwayat Pengajuan Payout --}}
        <div class="p-6 sm:p-8 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[5px_5px_0_var(--ink)] space-y-4">
            <h3 class="text-xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                Riwayat Pengajuan Pencairan Dana (Payout)
            </h3>

            @if ($payoutRequests->isEmpty())
                <p class="text-xs text-[var(--mute)] font-medium">Belum ada riwayat pengajuan pencairan dana.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b-2 border-[var(--ink)] font-black text-[var(--ink)]">
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">Rekening Tujuan</th>
                                <th class="py-2.5 px-3">Nominal</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--line)] font-medium">
                            @foreach ($payoutRequests as $pr)
                                <tr class="hover:bg-[var(--paper)]">
                                    <td class="py-3 px-3 text-[var(--mute)]">{{ $pr->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-3 font-bold text-[var(--ink)]">{{ $pr->account->bank_name ?? '-' }} ({{ $pr->account->account_number ?? '-' }})</td>
                                    <td class="py-3 px-3 font-black text-[var(--ink)]">Rp {{ number_format($pr->amount, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3">
                                        @if ($pr->status === 'approved')
                                            <span class="px-2 py-0.5 bg-[var(--pine)] text-white text-[10px] font-black rounded border border-[var(--ink)]">Selesai</span>
                                        @elseif ($pr->status === 'rejected')
                                            <span class="px-2 py-0.5 bg-[var(--red-l)] text-[var(--red)] text-[10px] font-black rounded border border-[var(--ink)]">Ditolak</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-[var(--mustard)] text-[var(--ink)] text-[10px] font-black rounded border border-[var(--ink)]">Menunggu Review</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</section>
@endsection
