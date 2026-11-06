@extends('layouts.app', ['title' => 'Daftarkan Komunitas / EO — Velosha SaaS'])

@section('content')
<section class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        {{-- Card Container --}}
        <div class="card p-8 sm:p-10 border-[3px] border-[var(--ink)] bg-[var(--card)] shadow-[6px_6px_0_var(--ink)]" style="border-radius: 18px">
            {{-- Header --}}
            <div class="mb-8 border-b-2 border-[var(--line)] pb-6">
                <span class="inline-block px-3 py-1 bg-[var(--butter)] border-2 border-[var(--ink)] font-bold text-xs uppercase tracking-wider rounded-md mb-3 shadow-[2px_2px_0_var(--ink)]">
                    SaaS Penyelenggara
                </span>
                <h1 class="h3 text-3xl font-extrabold text-[var(--ink)] mb-2" style="font-family: 'Bricolage Grotesque', sans-serif">
                    Daftarkan Komunitas / Event Organizer
                </h1>
                <p class="text-[var(--mute)] text-sm">
                    Buat entitas komunitas atau klub sepedamu untuk mulai mempublikasikan race dan mengelola penjualan tiket di Velosha.
                </p>
            </div>

            {{-- Form --}}
            <form action="{{ route('organizations.store') }}" method="POST" class="flex flex-col gap-6">
                @csrf

                {{-- Nama Komunitas --}}
                <div>
                    <label for="name" class="label block font-bold text-sm text-[var(--ink)] mb-2">
                        Nama Komunitas / EO <span class="text-[var(--red)]">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           placeholder="Contoh: Bromo Cycling Club / Nusantara Gran Fondo"
                           class="input w-full px-4 py-3 border-[2.5px] border-[var(--ink)] rounded-xl font-medium text-[var(--ink)] bg-[var(--paper)] focus:outline-none focus:border-[var(--orange)] shadow-[3px_3px_0_var(--ink)] @error('name') !border-[var(--red)] @enderror">
                    @error('name')
                        <p class="text-[var(--red)] text-xs font-semibold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi Singkat --}}
                <div>
                    <label for="description" class="label block font-bold text-sm text-[var(--ink)] mb-2">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              placeholder="Ceritakan singkat tentang komunitas, kota asal, atau jenis kegiatan sepedamu..."
                              class="input w-full px-4 py-3 border-[2.5px] border-[var(--ink)] rounded-xl font-medium text-[var(--ink)] bg-[var(--paper)] focus:outline-none focus:border-[var(--orange)] shadow-[3px_3px_0_var(--ink)] @error('description') !border-[var(--red)] @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-[var(--red)] text-xs font-semibold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 border-t-2 border-[var(--line)] flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('home') }}" class="text-sm font-bold text-[var(--mute)] hover:text-[var(--ink)] transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="btn w-full sm:w-auto px-8 py-3.5 bg-[var(--orange)] text-white font-extrabold rounded-xl border-[2.5px] border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0_var(--ink)] transition-all">
                        Daftarkan Komunitas & Buat Event →
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
