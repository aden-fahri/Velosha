@extends('layouts.app', ['title' => 'Konfirmasi Kata Sandi — Velosha', 'nav' => false])

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[var(--paper2)] relative overflow-hidden px-4 py-12">
    <div class="dots absolute inset-0 opacity-40 pointer-events-none"></div>

    <div class="card relative w-full max-w-md bg-[var(--card)] border-[3px] border-[var(--ink)] rounded-2xl p-7 sm:p-9 shadow-[8px_8px_0_var(--ink)] space-y-6 z-10">

        <div class="flex items-center justify-between border-b-2 border-[var(--line)] pb-5">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-black text-[var(--ink)] hover:text-[var(--orange)] transition">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
                <span>Beranda</span>
            </a>

            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-6 w-auto object-contain" />
                <span class="font-heading font-black text-lg text-[var(--ink)]">Velosha</span>
            </a>
        </div>

        <div class="space-y-1.5">
            <span class="inline-block px-3 py-1 bg-[var(--butter)] border-2 border-[var(--ink)] text-[var(--ink)] font-black text-[11px] uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)]">
                Konfirmasi Keamanan
            </span>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-[var(--ink)] tracking-tight">
                Konfirmasi Sandi
            </h1>
            <p class="text-xs sm:text-sm text-[var(--mute)] font-medium leading-relaxed">
                Ini adalah area sensitif. Mohon masukkan kata sandi Anda kembali sebelum melanjutkan.
            </p>
        </div>

        <form action="{{ route('password.confirm') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="password" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                    Kata Sandi Anda <span class="text-[var(--red)]">*</span>
                </label>
                <input id="password" type="password" name="password" required autofocus
                       placeholder="Masukkan sandi akun Anda"
                       class="w-full px-4 py-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)] @error('password') !border-[var(--red)] @enderror">
                @error('password')
                    <p class="text-[var(--red)] text-xs font-bold">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white font-black py-3.5 text-sm rounded-xl border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
                <span>Konfirmasi Sandi</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
        </form>

    </div>
</div>
@endsection
