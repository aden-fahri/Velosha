@extends('layouts.app', ['title' => 'Lupa Kata Sandi — Velosha', 'nav' => false])

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[var(--paper2)] relative overflow-hidden px-4 py-12">
    {{-- Halftone dot background --}}
    <div class="dots absolute inset-0 opacity-40 pointer-events-none"></div>

    <div class="card relative w-full max-w-md bg-[var(--card)] border-[3px] border-[var(--ink)] rounded-2xl p-5 sm:p-9 shadow-[4px_4px_0_var(--ink)] sm:shadow-[8px_8px_0_var(--ink)] space-y-5 sm:space-y-6 z-10">

        {{-- Top: Back to login & Logo --}}
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
            <span class="inline-block px-3 py-1 bg-[var(--butter)] border-2 border-[var(--ink)] text-[var(--ink)] font-black text-[11px] uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)]">
                Pemulihan Akun
            </span>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-[var(--ink)] tracking-tight">
                Lupa Kata Sandi?
            </h1>
            <p class="text-xs sm:text-sm text-[var(--mute)] font-medium leading-relaxed">
                Masukkan alamat email akun Anda. Kami akan mengirimkan tautan aman untuk mengatur ulang kata sandi Anda.
            </p>
        </div>

        @if (session('status'))
            <div class="p-3.5 bg-[var(--pine-l)] border-2 border-[var(--ink)] text-[var(--ink)] font-bold text-xs rounded-xl shadow-[2px_2px_0_var(--ink)] flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block font-black text-xs uppercase tracking-wider text-[var(--ink)]">
                    Alamat Email Terdaftar <span class="text-[var(--red)]">*</span>
                </label>
                <input id="email" type="email" name="email" required autofocus value="{{ old('email') }}"
                       placeholder="nama@email.com"
                       class="w-full px-4 py-3 min-h-[46px] rounded-xl border-2 border-[var(--ink)] bg-[var(--paper)] font-bold text-base sm:text-sm text-[var(--ink)] shadow-[2px_2px_0_var(--ink)] focus:outline-none focus:border-[var(--orange)] @error('email') !border-[var(--red)] @enderror">
                @error('email')
                    <p class="text-[var(--red)] text-xs font-bold">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white font-black py-3.5 text-sm rounded-xl border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
                <span>Kirim Tautan Reset Sandi</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <div class="pt-4 border-t-2 border-[var(--line)] text-center text-xs text-[var(--mute)] font-medium">
            Ingat kata sandi Anda?
            <a href="{{ route('login') }}" wire:navigate.hover class="font-black text-[var(--ink)] hover:text-[var(--orange)] underline decoration-2 ml-1">Masuk Sekarang</a>
        </div>

    </div>
</div>
@endsection
