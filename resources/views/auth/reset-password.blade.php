@extends('layouts.app', ['title' => 'Atur Ulang Kata Sandi — Velosha', 'nav' => false])

@section('content')
    <div class="min-h-[calc(100vh-96px)] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-velosurfacealt">
        <div class="max-w-md w-full mx-auto bg-white border border-veloborder velo-box-1 shadow-sm p-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 border border-veloborderstrong bg-white hover:bg-velosurfacealt px-3 py-1.5 text-xs font-semibold text-teal-700 velo-box-1 transition mb-5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">Atur Ulang Kata Sandi</h2>
            <p class="text-xs text-velomutedlight mt-1 mb-6">
                Buat kata sandi baru untuk akun Velosha Anda.
            </p>

            <form action="{{ route('password.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ old('email', $email) }}">

                <div>
                    <label for="password" class="block text-xs font-semibold text-teal-700 mb-1">Kata Sandi Baru</label>
                    <input id="password" type="password" name="password" required autofocus
                           placeholder="Minimal 8 karakter"
                           class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white @error('password') border-red-600 @enderror">
                    @error('password')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-teal-700 mb-1">Ulangi Kata Sandi Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           placeholder="Konfirmasi sandi"
                           class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white">
                </div>

                <button type="submit" class="w-full bg-[var(--orange)] hover:bg-[var(--orange-l)] text-[var(--ink)] font-semibold py-2.5 text-xs velo-box-1 shadow-sm transition">
                    Simpan Kata Sandi Baru
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-veloborder text-xs text-velomutedlight">
                <a href="{{ route('login') }}" class="text-race-600 font-semibold hover:underline">Kembali masuk</a>
            </div>
        </div>
    </div>
@endsection
