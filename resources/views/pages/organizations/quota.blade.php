@extends('layouts.app', ['title' => 'Paket Kuota Event — ' . $organization->name, 'nav' => false])

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

        {{-- Header Kartu Komunitas Penyelenggara (Screen-Print Neobrutalist) --}}
        <div class="p-6 sm:p-8 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[6px_6px_0_var(--ink)] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="chip font-black text-xs uppercase tracking-wider">
                    Penyelenggara
                </span>

                <h1 class="text-3xl sm:text-4xl font-black text-[var(--ink)] tracking-tight" style="font-family: 'Bricolage Grotesque', sans-serif">
                    {{ $organization->name }}
                </h1>
                <p class="text-xs sm:text-sm text-[var(--mute)] max-w-2xl font-medium">
                    Kuota event digunakan setiap kali organisasi Anda menayangkan (*publish*) race bersepeda di Velosha.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="px-4 py-2.5 bg-[var(--paper)] border-2 border-[var(--ink)] rounded-xl shadow-[3px_3px_0_var(--ink)] text-center">
                    <div class="text-[10px] uppercase font-black text-[var(--mute)]">Saldo Kuota Aktif</div>
                    <div class="text-2xl font-black {{ $balance > 0 ? 'text-[var(--pine)]' : 'text-[var(--red)]' }}" style="font-family: 'Bricolage Grotesque', sans-serif">
                        {{ $balance }} <span class="text-xs font-bold text-[var(--ink)]">Race</span>
                    </div>
                </div>

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
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--mustard)] text-[var(--ink)] shadow-[3px_3px_0_var(--ink)] -translate-y-0.5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path><path d="M12 18V6"></path></svg>
                <span>Paket &amp; Kuota ({{ $balance }})</span>
            </a>

            <a href="{{ route('organizations.finance', $organization) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                <span>Keuangan &amp; Payout</span>
            </a>

            <a href="{{ route('organizations.verification', $organization) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-[var(--ink)] font-black text-sm transition-all flex items-center gap-2 bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Verifikasi Identitas</span>
            </a>
        </div>

        {{-- Pilihan 3 Paket Kuota --}}
        <div class="space-y-4">
            <div>
                <h2 class="text-2xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Pilih Paket Kuota Event
                </h2>
                <p class="text-xs sm:text-sm text-[var(--mute)] font-medium">Beli sekali, berlaku selamanya tanpa batas masa aktif (*no expiry date*).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch pt-2">
                @foreach ($packages as $pkg)
                    @php($isPopular = $pkg->events_count === 3)
                    <div class="p-6 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] {{ $isPopular ? 'shadow-[7px_7px_0_var(--ink)] ring-2 ring-[var(--orange)]' : 'shadow-[4px_4px_0_var(--ink)]' }} flex flex-col justify-between space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                                    {{ $pkg->name }}
                                </h3>
                                <span class="px-2.5 py-0.5 text-xs font-black border-2 border-[var(--ink)] rounded-md bg-[var(--paper)] shadow-[1.5px_1.5px_0_var(--ink)]">
                                    {{ $pkg->events_count }} Race
                                </span>
                            </div>

                            <p class="text-xs text-[var(--mute)] leading-relaxed min-h-[36px] font-medium">
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
                                    (Rp {{ number_format($pkg->price / $pkg->events_count, 0, ',', '.') }} / race)
                                </div>
                            </div>

                            <ul class="space-y-2 text-xs font-semibold text-[var(--ink)]">
                                <li class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black">&check;</span>
                                    <span><strong>{{ $pkg->events_count }} Event</strong> aktif di kalender</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black">&check;</span>
                                    <span>Penerbitan nomor BIB &amp; QR otomatis</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black">&check;</span>
                                    <span>Tanpa batas masa aktif</span>
                                </li>
                            </ul>
                        </div>

                        <form action="{{ route('organizations.quota.purchase', $organization) }}" method="POST" class="pt-2">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                            <button type="submit"
                                    class="w-full py-3 px-4 rounded-xl border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider {{ $isPopular ? 'bg-[var(--orange)] text-white hover:bg-[var(--orange-d)]' : 'bg-[var(--card)] text-[var(--ink)] hover:bg-[var(--paper2)]' }} shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all flex items-center justify-center gap-2">
                                <span>Beli Kuota (Rp {{ number_format($pkg->price, 0, ',', '.') }})</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Riwayat Mutasi Kuota --}}
        <div class="p-6 sm:p-8 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[5px_5px_0_var(--ink)] space-y-4">
            <h3 class="text-xl font-black text-[var(--ink)]" style="font-family: 'Bricolage Grotesque', sans-serif">
                Riwayat Mutasi Kuota Event
            </h3>

            @if ($ledger->isEmpty())
                <p class="text-xs text-[var(--mute)] font-medium">Belum ada riwayat mutasi kuota.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b-2 border-[var(--ink)] font-black text-[var(--ink)]">
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">Aktivitas</th>
                                <th class="py-2.5 px-3">Perubahan</th>
                                <th class="py-2.5 px-3">Referensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--line)] font-medium">
                            @foreach ($ledger as $entry)
                                <tr class="hover:bg-[var(--paper)]">
                                    <td class="py-3 px-3 text-[var(--mute)]">
                                        {{ $entry->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-3 font-bold text-[var(--ink)]">
                                        {{ $entry->reasonLabel() }}
                                    </td>
                                    <td class="py-3 px-3 font-black {{ $entry->delta > 0 ? 'text-[var(--pine)]' : 'text-[var(--red)]' }}">
                                        {{ $entry->delta > 0 ? "+{$entry->delta}" : $entry->delta }} Event
                                    </td>
                                    <td class="py-3 px-3 text-[var(--mute)]">
                                        {{ $entry->reference() }}
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
