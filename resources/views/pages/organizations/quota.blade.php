@extends('layouts.app', ['title' => 'Paket Kuota Event — ' . $organization->name])

@section('content')
<div class="bg-velosurfacealt min-h-[calc(100vh-140px)] py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Notifikasi Status --}}
        @if (session('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-semibold velo-box-1 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
        @endif

        {{-- Header & Saldo Kuota --}}
        <div class="bg-white border border-veloborderstrong p-6 sm:p-8 velo-box-1 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-sm">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-velomuted">Monetisasi SaaS &bull; Penyelenggara</span>
                <h1 class="text-2xl font-black text-teal-900 mt-1">{{ $organization->name }}</h1>
                <p class="text-xs text-velomuted mt-1">
                    Kuota event digunakan setiap kali organisasi Anda menayangkan (*publish*) race bersepeda di Velosha.
                </p>
            </div>
            <div class="bg-velosurfacealt border border-veloborder p-4 velo-box-3 text-center sm:text-right shrink-0 min-w-[160px]">
                <div class="text-[10px] uppercase font-bold text-velomutedlight">Saldo Kuota Aktif</div>
                <div class="text-3xl font-black font-mono {{ $balance > 0 ? 'text-emerald-700' : 'text-red-600' }} mt-0.5">
                    {{ $balance }} <span class="text-xs font-sans font-bold text-velomuted">Event</span>
                </div>
            </div>
        </div>

        {{-- Pilihan Paket Kuota --}}
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-bold text-teal-900">Pilih Paket Kuota Event</h2>
                <p class="text-xs text-velomuted">Beli sekali, berlaku selamanya tanpa batas masa aktif (*no expiry date*).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($packages as $package)
                @php($isBest = $package->events_count === 3)
                <div class="relative bg-white border {{ $isBest ? 'border-race-500 ring-2 ring-race-100 shadow-md' : 'border-veloborderstrong' }} p-6 velo-box-1 flex flex-col justify-between">
                    @if ($isBest)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[var(--orange)] text-[var(--ink)] text-[10px] font-extrabold px-3 py-0.5 velo-box-3 uppercase tracking-wider">
                        Paling Populer
                    </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-base font-extrabold text-teal-900">{{ $package->name }}</h3>
                                <div class="text-xs text-velomutedlight font-medium">{{ $package->events_count }} Kuota Event</div>
                            </div>
                            <span class="text-xs font-mono font-bold bg-velosurfacealt border border-veloborder px-2 py-0.5 velo-box-3 text-teal-800">
                                {{ $package->events_count }} Race
                            </span>
                        </div>

                        <p class="text-xs text-velomuted leading-relaxed">
                            {{ $package->description }}
                        </p>

                        <div class="pt-3 border-t border-veloborder">
                            <div class="text-[10px] text-velomuted uppercase font-bold">Harga Paket</div>
                            <div class="text-2xl font-black text-race-600">
                                Rp {{ \App\Support\EventCardPresenter::angka((float) $package->price) }}
                            </div>
                            <div class="text-[10px] text-velomutedlight mt-0.5">
                                (Rp {{ \App\Support\EventCardPresenter::angka((float) ($package->price / $package->events_count)) }} / event)
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('organizations.quota.purchase', ['organization' => $organization->slug]) }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <button type="submit"
                                class="w-full flex items-center justify-center font-bold py-3 text-xs velo-box-1 shadow-sm transition {{ $isBest ? 'bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)]' : 'bg-white hover:bg-velosurfacealt text-teal-900 border border-veloborderstrong' }}">
                            <i data-lucide="shopping-bag" class="w-3.5 h-3.5 mr-1.5"></i>
                            <span>Beli Paket Ini</span>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Riwayat Mutasi Kuota (Ledger) --}}
        <div class="bg-white border border-veloborderstrong p-6 velo-box-1 space-y-4">
            <div class="flex items-center justify-between border-b border-veloborder pb-3">
                <h3 class="text-sm font-bold text-teal-900 uppercase tracking-wider">
                    Riwayat Mutasi Kuota (Ledger)
                </h3>
                <span class="text-[10px] text-velomuted">Pencatatan Finansial Transparan</span>
            </div>

            @if ($ledger->isEmpty())
            <div class="p-8 text-center text-xs text-velomuted border border-dashed border-veloborder velo-box-3">
                Belum ada transaksi kuota untuk organisasi ini.
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-veloborder text-[11px] font-bold text-velomuted uppercase">
                            <th class="py-2.5 px-3">Waktu</th>
                            <th class="py-2.5 px-3">Aktivitas</th>
                            <th class="py-2.5 px-3">Terkait Event / Paket</th>
                            <th class="py-2.5 px-3 text-right">Perubahan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-veloborder">
                        @foreach ($ledger as $item)
                        <tr>
                            <td class="py-2.5 px-3 text-velomuted font-mono text-[11px]">
                                {{ $item->created_at?->format('d M Y, H:i') ?? '—' }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if ($item->reason === 'purchase')
                                <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Beli Kuota
                                </span>
                                @elseif ($item->reason === 'publish_event')
                                <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    Publish Event
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 velo-box-3 text-[10px] font-bold bg-gray-50 text-gray-800 border border-gray-200">
                                    {{ $item->reason }}
                                </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-teal-900">
                                @if ($item->purchase)
                                    Paket: {{ $item->purchase->package?->name ?? 'Paket Kuota' }}
                                @elseif ($item->event)
                                    Event: {{ $item->event->title }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-xs {{ $item->delta > 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $item->delta > 0 ? '+' . $item->delta : $item->delta }} Kuota
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

