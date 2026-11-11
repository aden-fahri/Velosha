@extends('layouts.app', ['title' => 'Daftarkan Komunitas / EO — Velosha SaaS', 'nav' => true])

@section('content')

<section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 bg-[var(--paper)] min-h-[calc(100vh-64px)] flex items-center justify-center">
    <div class="max-w-xl w-full">
        {{-- Card Container Neobrutalist --}}
        <div class="p-8 sm:p-10 rounded-2xl border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[6px_6px_0_var(--ink)] space-y-8">
            {{-- Header --}}
            <div class="space-y-2 border-b-2 border-[var(--line)] pb-6">
                <span class="inline-block px-3 py-1 bg-[var(--butter)] border-2 border-[var(--ink)] font-black text-xs uppercase tracking-wider rounded-md shadow-[2px_2px_0_var(--ink)]">
                    SaaS Penyelenggara Balap
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-[var(--ink)] tracking-tight" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Daftarkan Komunitas / Event Organizer
                </h1>
                <p class="text-xs sm:text-sm text-[var(--mute)] leading-relaxed font-medium">
                    Buat entitas komunitas atau klub sepedamu untuk mulai membeli kuota, mempublikasikan race, dan mengelola pendaftaran peserta di Velosha.
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
                           placeholder="Contoh: Bromo Cycling Club / Nusantara Gran Fondo"
                           class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)] @error('name') !border-[var(--red)] @enderror">
                    @error('name')
                        <p class="text-[var(--red)] text-xs font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi Singkat --}}
                <div class="space-y-1.5">
                    <label for="description" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                        Deskripsi Komunitas (Opsional)
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="Ceritakan singkat tentang komunitas, kota asal, atau jenis kegiatan sepedamu..."
                              class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-medium text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)]">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-[var(--red)] text-xs font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 border-t-2 border-[var(--line)] flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('home') }}" class="text-xs font-black text-[var(--mute)] hover:text-[var(--ink)] transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto px-8 py-3.5 bg-[var(--orange)] text-white font-extrabold text-sm rounded-xl border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all flex items-center justify-center gap-2">
                        <span>Lanjutkan ke Dasbor EO →</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
