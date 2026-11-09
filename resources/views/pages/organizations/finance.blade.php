@extends('layouts.app', ['title' => 'Keuangan & Pencairan Dana — ' . $organization->name])

@section('content')
<div class="bg-velosurfacealt min-h-[calc(100vh-140px)] py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Notifikasi Status & Error --}}
        @if (session('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-semibold velo-box-1 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 text-xs font-semibold velo-box-1 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

        {{-- Header Organisasi --}}
        <div class="bg-white border border-veloborderstrong p-6 sm:p-8 velo-box-1 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-sm">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-velomuted">SaaS &bull; Pembayaran &amp; Pencairan</span>
                <h1 class="text-2xl font-black text-teal-900 mt-1 flex items-center gap-2">
                    <span>{{ $organization->name }}</span>
                    @if ($organization->isVerified())
                    <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <i data-lucide="badge-check" class="w-3.5 h-3.5 mr-1 text-emerald-600"></i>
                        VERIFIED
                    </span>
                    @endif
                </h1>
                <p class="text-xs text-velomuted mt-1">
                    Kelola saldo hasil penjualan tiket race, rekening bank, dan pengajuan penarikan dana ke rekening komunitas.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('organizations.quota', ['organization' => $organization->slug]) }}"
                   class="inline-flex items-center text-xs font-bold px-3 py-2 border border-veloborder hover:bg-velosurfacealt text-teal-900 velo-box-3 transition">
                    <i data-lucide="coins" class="w-3.5 h-3.5 mr-1.5 text-race-600"></i>
                    <span>Kuota Event</span>
                </a>
                <a href="{{ route('organizations.verification', ['organization' => $organization->slug]) }}"
                   class="inline-flex items-center text-xs font-bold px-3 py-2 border border-veloborder hover:bg-velosurfacealt text-teal-900 velo-box-3 transition">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 mr-1.5 text-emerald-600"></i>
                    <span>Verifikasi</span>
                </a>
            </div>
        </div>

        {{-- Stat Cards Keuangan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-veloborderstrong p-5 velo-box-1 shadow-xs">
                <div class="text-[10px] uppercase font-bold text-velomutedlight">Saldo Siap Tarik</div>
                <div class="text-2xl font-black font-mono text-emerald-700 mt-1">
                    Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-velomuted mt-1">Bebas ditarik kapan saja</div>
            </div>

            <div class="bg-white border border-veloborderstrong p-5 velo-box-1 shadow-xs">
                <div class="text-[10px] uppercase font-bold text-velomutedlight">Dalam Proses Payout</div>
                <div class="text-2xl font-black font-mono text-amber-600 mt-1">
                    Rp {{ number_format($pendingPayout, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-velomuted mt-1">Menunggu transfer admin</div>
            </div>

            <div class="bg-white border border-veloborderstrong p-5 velo-box-1 shadow-xs">
                <div class="text-[10px] uppercase font-bold text-velomutedlight">Total Penjualan Kotor</div>
                <div class="text-2xl font-black font-mono text-teal-900 mt-1">
                    Rp {{ number_format($totalSales, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-velomuted mt-1">Akumulasi tiket race terjual</div>
            </div>

            <div class="bg-white border border-veloborderstrong p-5 velo-box-1 shadow-xs">
                <div class="text-[10px] uppercase font-bold text-velomutedlight">Telah Dicairkan</div>
                <div class="text-2xl font-black font-mono text-blue-700 mt-1">
                    Rp {{ number_format($totalPaidOut, 0, ',', '.') }}
                </div>
                <div class="text-[10px] text-velomuted mt-1">Berhasil masuk rekening</div>
            </div>
        </div>

        {{-- Bagian Penarikan Dana & Rekening Bank --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Kolom Kiri: Form Pengajuan Pencairan (7 Cols) --}}
            <div class="lg:col-span-7 bg-white border border-veloborderstrong p-6 velo-box-1 space-y-6 shadow-sm">
                <div class="border-b border-veloborder pb-3">
                    <h2 class="text-sm font-bold text-teal-900 uppercase tracking-wider">
                        Ajukan Penarikan Dana (Payout)
                    </h2>
                    <p class="text-xs text-velomuted mt-0.5">
                        Dana akan ditransfer oleh tim finance ke rekening bank komunitas terdaftar.
                    </p>
                </div>

                @if (! $organization->isVerified())
                    <div class="p-4 bg-amber-50 border border-amber-300 text-amber-900 text-xs velo-box-3 flex items-start space-x-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                        <div>
                            <strong>Verifikasi Diperlukan:</strong>
                            Organisasi Anda harus terverifikasi terlebih dahulu sebelum dapat mencairkan saldo. Silakan lengkapi dokumen di halaman verifikasi.
                        </div>
                    </div>
                @elseif ($accounts->isEmpty())
                    <div class="p-4 bg-blue-50 border border-blue-300 text-blue-900 text-xs velo-box-3 flex items-start space-x-2">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                        <div>
                            <strong>Belum Ada Rekening Bank:</strong>
                            Silakan tambahkan nomor rekening bank tujuan di panel sebelah kanan terlebih dahulu.
                        </div>
                    </div>
                @elseif ($availableBalance < 50000)
                    <div class="p-4 bg-velosurfacealt border border-veloborder text-velomuted text-xs velo-box-3">
                        Saldo yang tersedia untuk ditarik saat ini kurang dari batas minimum pencairan (Min. Rp 50.000).
                    </div>
                @else
                    <form action="{{ route('organizations.finance.payout', ['organization' => $organization->slug]) }}"
                          method="POST"
                          class="space-y-4">
                        @csrf
                        <div>
                            <label for="payout_account_id" class="block text-xs font-bold text-teal-900 mb-1">
                                Pilih Rekening Bank Tujuan <span class="text-red-500">*</span>
                            </label>
                            <select name="payout_account_id" id="payout_account_id" required class="block w-full text-xs border border-veloborderstrong p-2.5 velo-box-3 bg-white text-teal-900">
                                @foreach ($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->bank_name }} — {{ $acc->masked_account_number }} (a.n. {{ $acc->account_name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="amount" class="block text-xs font-bold text-teal-900 mb-1">
                                Nominal Penarikan (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="amount"
                                   id="amount"
                                   required
                                   min="50000"
                                   max="{{ $availableBalance }}"
                                   step="1000"
                                   value="{{ $availableBalance }}"
                                   class="block w-full text-sm font-mono font-bold border border-veloborderstrong p-2.5 velo-box-3 bg-white text-teal-900">
                            <span class="text-[11px] text-velomuted mt-1 block">
                                Maksimal penarikan saat ini: <strong>Rp {{ number_format($availableBalance, 0, ',', '.') }}</strong> (Min. Rp 50.000)
                            </span>
                        </div>

                        <button type="submit"
                                class="w-full flex items-center justify-center bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-bold py-3 text-xs velo-box-1 shadow-sm transition">
                            <i data-lucide="send" class="w-3.5 h-3.5 mr-2"></i>
                            <span>Kirim Pengajuan Pencairan Dana</span>
                        </button>
                    </form>
                @endif
            </div>

            {{-- Kolom Kanan: Rekening Bank EO (5 Cols) --}}
            <div class="lg:col-span-5 bg-white border border-veloborderstrong p-6 velo-box-1 space-y-6 shadow-sm">
                <div class="border-b border-veloborder pb-3">
                    <h2 class="text-sm font-bold text-teal-900 uppercase tracking-wider">
                        Rekening Bank Pencairan
                    </h2>
                    <p class="text-xs text-velomuted mt-0.5">
                        Nomor rekening dienkripsi secara aman.
                    </p>
                </div>

                {{-- Daftar Rekening --}}
                <div class="space-y-2">
                    @forelse ($accounts as $acc)
                    <div class="p-3 bg-velosurfacealt border border-veloborder velo-box-3 flex justify-between items-center text-xs">
                        <div>
                            <div class="font-bold text-teal-900">{{ $acc->bank_name }}</div>
                            <div class="font-mono text-velomutedlight text-[11px]">{{ $acc->masked_account_number }}</div>
                            <div class="text-[10px] text-velomuted">a.n. {{ $acc->account_name }}</div>
                        </div>
                        @if ($acc->is_primary)
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold velo-box-3 border border-emerald-300">
                            Utama
                        </span>
                        @endif
                    </div>
                    @empty
                    <div class="text-center p-4 border border-dashed border-veloborder text-xs text-velomuted velo-box-3">
                        Belum ada rekening terdaftar.
                    </div>
                    @endforelse
                </div>

                {{-- Form Tambah Rekening --}}
                <div class="pt-4 border-t border-veloborder">
                    <h3 class="text-xs font-bold text-teal-900 uppercase tracking-wider mb-3">
                        + Tambah Rekening Bank
                    </h3>
                    <form action="{{ route('organizations.finance.account.store', ['organization' => $organization->slug]) }}"
                          method="POST"
                          class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-semibold text-teal-900 mb-0.5">Nama Bank</label>
                            <input type="text" name="bank_name" placeholder="BCA / Mandiri / BRI / BNI" required class="block w-full text-xs border border-veloborderstrong p-2 velo-box-3 bg-white text-teal-900">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-teal-900 mb-0.5">Nomor Rekening</label>
                            <input type="text" name="account_number" placeholder="Contoh: 1234567890" required class="block w-full text-xs font-mono border border-veloborderstrong p-2 velo-box-3 bg-white text-teal-900">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-teal-900 mb-0.5">Nama Pemilik Rekening</label>
                            <input type="text" name="account_name" placeholder="Sesuai buku tabungan" required class="block w-full text-xs border border-veloborderstrong p-2 velo-box-3 bg-white text-teal-900">
                        </div>
                        <button type="submit" class="w-full flex items-center justify-center bg-white hover:bg-velosurfacealt text-teal-900 border border-veloborderstrong font-bold py-2.5 text-xs velo-box-3 transition">
                            <i data-lucide="plus" class="w-3.5 h-3.5 mr-1.5"></i>
                            <span>Simpan Rekening Bank</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Riwayat Pengajuan Pencairan --}}
        @if ($payoutRequests->isNotEmpty())
        <div class="bg-white border border-veloborderstrong p-6 velo-box-1 space-y-4">
            <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider border-b border-veloborder pb-3">
                Riwayat Pengajuan Pencairan Dana
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-veloborder text-[11px] font-bold text-velomuted uppercase">
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3">Nominal</th>
                            <th class="py-2.5 px-3">Rekening Tujuan</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Keterangan / Ref</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-veloborder">
                        @foreach ($payoutRequests as $req)
                        <tr>
                            <td class="py-2.5 px-3 text-velomuted font-mono text-[11px]">
                                {{ $req->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-teal-900">
                                Rp {{ number_format($req->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-velomuted">
                                {{ $req->account?->bank_name }} — {{ $req->account?->masked_account_number }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if ($req->isApproved())
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold velo-box-3 border border-emerald-300">Ditransfer</span>
                                @elseif ($req->isPending())
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold velo-box-3 border border-amber-300">Menunggu</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-bold velo-box-3 border border-red-300">Ditolak</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-[11px] text-velomuted">
                                @if ($req->reference_number)
                                    <span class="font-mono text-emerald-700">Ref: {{ $req->reference_number }}</span>
                                @elseif ($req->rejection_note)
                                    <span class="text-red-700">{{ $req->rejection_note }}</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Buku Kas Finansial (Financial Ledger) --}}
        <div class="bg-white border border-veloborderstrong p-6 velo-box-1 space-y-4">
            <div class="flex items-center justify-between border-b border-veloborder pb-3">
                <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider">
                    Buku Kas Mutasi Keuangan (Financial Ledger)
                </h3>
                <span class="text-[10px] text-velomuted">Transparansi Finansial Append-Only</span>
            </div>

            @if ($ledger->isEmpty())
            <div class="p-8 text-center text-xs text-velomuted border border-dashed border-veloborder velo-box-3">
                Belum ada transaksi finansial untuk komunitas ini. Mutasi akan muncul setelah peserta membeli tiket race.
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-veloborder text-[11px] font-bold text-velomuted uppercase">
                            <th class="py-2.5 px-3">Waktu</th>
                            <th class="py-2.5 px-3">Aktivitas</th>
                            <th class="py-2.5 px-3">Terkait Event / Catatan</th>
                            <th class="py-2.5 px-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-veloborder">
                        @foreach ($ledger as $item)
                        <tr>
                            <td class="py-2.5 px-3 text-velomuted font-mono text-[11px]">
                                {{ $item->created_at?->format('d M Y, H:i') ?? '—' }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if ($item->type === 'ticket_sale')
                                <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Penjualan Tiket
                                </span>
                                @elseif ($item->type === 'payout')
                                <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    Pencairan Dana
                                </span>
                                @elseif ($item->type === 'refund')
                                <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Refund Peserta
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[10px] font-bold bg-gray-50 text-gray-800 border border-gray-200">
                                    {{ $item->type }}
                                </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-teal-900">
                                {{ $item->note ?: ($item->event?->title ?? '—') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-xs {{ $item->direction === 'credit' ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $item->direction === 'credit' ? '+' : '-' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
