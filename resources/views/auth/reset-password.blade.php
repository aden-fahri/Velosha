@extends('layouts.app', ['title' => 'Atur Ulang Kata Sandi — Velosha', 'nav' => false])

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[var(--paper2)] relative overflow-hidden px-4 py-12">
    <div class="dots absolute inset-0 opacity-40 pointer-events-none"></div>

    <div class="card relative w-full max-w-md bg-[var(--card)] border-[3px] border-[var(--ink)] rounded-2xl p-5 sm:p-9 shadow-[4px_4px_0_var(--ink)] sm:shadow-[8px_8px_0_var(--ink)] space-y-5 sm:space-y-6 z-10">

        <div class="flex items-center justify-between border-b-2 border-[var(--line)] pb-5">
            <a href="{{ route('login') }}" wire:navigate.hover class="inline-flex items-center gap-2 text-xs font-black text-[var(--ink)] hover:text-[var(--orange)] transition">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
                <span>Kembali Masuk</span>
            </a>

            <a href="{{ route('home') }}" wire:navigate.hover class="flex items-center gap-2">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-6 w-auto object-contain" />
                <span class="font-heading font-black text-lg text-[var(--ink)]">Velosha</span>
            </a>
        </div>

        <div class="space-y-1.5">
            <span class="inline-block px-3 py-1 bg-[var(--mustard)] border-2 border-[var(--ink)] text-[var(--ink)] font-black text-[11px] uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)]">
                Keamanan Akun
            </span>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-[var(--ink)] tracking-tight">
                Buat Sandi Baru
            </h1>
            <p class="text-xs sm:text-sm text-[var(--mute)] font-medium leading-relaxed">
                Tentukan kata sandi baru yang aman dan kuat untuk akun Velosha Anda.
            </p>
        </div>

        <form action="{{ route('password.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ old('email', $email) }}">

            <div class="space-y-1.5">
                <label for="password" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                    Kata Sandi Baru <span class="text-[var(--red)]">*</span>
                </label>
                <input id="password" type="password" name="password" required autofocus
                       placeholder="Minimal 8 karakter"
                       class="w-full px-4 py-3 min-h-[46px] rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-base sm:text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)] @error('password') !border-[var(--red)] @enderror">
                @error('password')
                    <p class="text-[var(--red)] text-xs font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                    Ulangi Kata Sandi Baru <span class="text-[var(--red)]">*</span>
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       placeholder="Konfirmasi sandi baru"
                       class="w-full px-4 py-3 min-h-[46px] rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-base sm:text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)]">
            </div>

            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white font-black py-3.5 text-sm rounded-xl border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer pt-2">
                <span>Simpan Kata Sandi Baru</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <div class="pt-4 border-t-2 border-[var(--line)] text-center text-xs text-[var(--mute)] font-medium">
            <a href="{{ route('login') }}" wire:navigate.hover class="font-black text-[var(--ink)] hover:text-[var(--orange)] underline decoration-2">Batalkan &amp; Kembali ke Halaman Masuk</a>
        </div>

    </div>
</div>
@endsection
