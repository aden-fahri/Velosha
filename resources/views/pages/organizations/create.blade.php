@extends('layouts.app', ['title' => 'Daftarkan Komunitas / Penyelenggara — Velosha', 'nav' => true])

@section('content')

@php
    $targetPkg = !empty($packageId) ? \App\Models\QuotaPackage::find($packageId) : null;
@endphp

<section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 bg-[var(--paper)] min-h-[calc(100vh-64px)] flex items-center justify-center">
    <div class="max-w-xl w-full">
        {{-- Card Container Neobrutalist --}}
        <div class="p-8 sm:p-10 rounded-2xl border-[3px] border-[var(--ink)] bg-white shadow-[6px_6px_0_var(--ink)] space-y-7">
            
            {{-- Header --}}
            <div class="space-y-3 border-b-2 border-[var(--ink)] pb-6">
                <div class="flex items-center gap-2 flex-wrap">
                    @if ($targetPkg)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[var(--mustard)] border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)] text-[var(--ink)]">
                            ★ Paket Terpilih: {{ $targetPkg->name }} ({{ $targetPkg->events_count }} Race)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[var(--butter)] border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)] text-[var(--ink)]">
                            Penyelenggara Event
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl sm:text-4xl font-heading font-black text-[var(--ink)] tracking-tight leading-tight">
                    Daftarkan Komunitas / EO
                </h1>

                <p class="text-xs sm:text-sm text-[var(--ink)]/80 leading-relaxed font-medium">
                    @if ($targetPkg)
                        Masukkan nama komunitas atau klub sepeda Anda untuk langsung melanjutkan ke pembayaran paket <strong>{{ $targetPkg->name }}</strong>.
                    @else
                        Daftarkan entitas komunitas atau klub sepedamu untuk mulai membeli kuota, mempublikasikan race, dan mengelola pendaftaran peserta di Velosha.
                    @endif
                </p>
            </div>

            {{-- Form --}}
            <form action="{{ route('organizations.store') }}" method="POST" class="space-y-6">
                @csrf

                @if(!empty($packageId))
                    <input type="hidden" name="package_id" value="{{ $packageId }}">
                @endif

                {{-- Nama Komunitas --}}
                <div class="space-y-1.5">
                    <label for="name" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                        Nama Komunitas / Klub / EO <span class="text-[var(--red)]">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           placeholder="Contoh: Bromo Cycling Club / Bandung Road Riders"
                           class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)] @error('name') !border-[var(--red)] @enderror">
                    @error('name')
                        <p class="text-[var(--red)] text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi Singkat --}}
                <div class="space-y-1.5">
                    <label for="description" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="Ceritakan singkat tentang kegiatan komunitas, kota asal, atau jenis event sepedamu..."
                              class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)]">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-[var(--red)] text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="pt-5 border-t-2 border-[var(--ink)] flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ $targetPkg ? route('packages.index') : route('home') }}" class="text-xs font-black text-[var(--mute)] hover:text-[var(--ink)] transition">
                        &larr; Kembali
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto px-7 py-3.5 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white font-black text-xs uppercase tracking-wider rounded-xl border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        @if ($targetPkg)
                            <span>Lanjut ke Pembayaran Kuota &rarr;</span>
                        @else
                            <span>Daftarkan &amp; Lanjutkan &rarr;</span>
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
