@extends('layouts.app', ['title' => 'Lupa Kata Sandi — Velosha', 'nav' => false])

@section('content')
    <div class="min-h-[calc(100vh-96px)] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-velosurfacealt">
        <div class="max-w-md w-full mx-auto bg-white border border-veloborder velo-box-1 shadow-sm p-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 border border-veloborderstrong bg-white hover:bg-velosurfacealt px-3 py-1.5 text-xs font-semibold text-teal-700 velo-box-1 transition mb-5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">Lupa Kata Sandi</h2>
            <p class="text-xs text-[#556066] mt-1 mb-6">
                Masukkan email akun Anda. Kami kirimkan tautan reset kata sandi.
            </p>

            @if (session('status'))
                <div class="mb-4 px-3 py-2 text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 velo-box-1">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-teal-700 mb-1">Alamat Email</label>
                    <input id="email" type="email" name="email" required autofocus value="{{ old('email') }}"
                           placeholder="cyclist@velosha.id"
                           class="w-full px-3 py-2 text-xs border border-veloborderstrong velo-box-1 focus:outline-none focus:border-race-500 bg-white @error('email') border-red-600 @enderror">
                    @error('email')
                        <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-race-600 hover:bg-race-700 text-white font-semibold py-2.5 text-xs velo-box-1 shadow-sm transition">
                    Kirim Tautan Reset
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-veloborder text-xs text-[#556066]">
                Ingat sandi Anda?
                <a href="{{ route('login') }}" class="text-race-600 font-semibold hover:underline">Kembali masuk</a>
            </div>
        </div>
    </div>
@endsection
