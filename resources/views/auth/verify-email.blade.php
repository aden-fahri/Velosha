@extends('layouts.app', ['title' => 'Verifikasi Email — Velosha', 'nav' => false])

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[var(--paper2)] relative overflow-hidden px-4 py-12">
    <div class="dots absolute inset-0 opacity-40 pointer-events-none"></div>

    <div class="card relative w-full max-w-md bg-[var(--card)] border-[3px] border-[var(--ink)] rounded-2xl p-5 sm:p-9 shadow-[4px_4px_0_var(--ink)] sm:shadow-[8px_8px_0_var(--ink)] space-y-5 sm:space-y-6 z-10 text-center">

        <div class="flex items-center justify-center mb-2">
            <a href="{{ route('home') }}" wire:navigate.hover class="flex items-center gap-2">
                <img src="/images/logo-velosha.png" alt="Velosha" class="h-8 w-auto object-contain" />
                <span class="font-heading font-black text-2xl text-[var(--ink)]">Velosha</span>
            </a>
        </div>

        <div class="w-16 h-16 mx-auto rounded-2xl bg-[var(--butter)] border-2 border-[var(--ink)] shadow-[3px_3px_0_var(--ink)] flex items-center justify-center text-[var(--orange)]">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
        </div>

        <div class="space-y-1.5">
            <span class="inline-block px-3 py-1 bg-[var(--butter)] border-2 border-[var(--ink)] text-[var(--ink)] font-black text-[11px] uppercase tracking-wider rounded-lg shadow-[2px_2px_0_var(--ink)]">
                Verifikasi Akun
            </span>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-[var(--ink)] tracking-tight">
                Cek Kotak Masuk Email
            </h1>
            <p class="text-xs sm:text-sm text-[var(--mute)] font-medium leading-relaxed">
                Sebelum memulai, mohon verifikasi alamat email Anda dengan mengeklik tautan konfirmasi yang baru saja kami kirimkan.
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="p-3.5 bg-[var(--pine-l)] border-2 border-[var(--ink)] text-[var(--ink)] font-bold text-xs rounded-xl shadow-[2px_2px_0_var(--ink)] flex items-center gap-2 text-left">
                <span class="w-5 h-5 rounded-full bg-[var(--pine)] text-white flex items-center justify-center text-[10px] font-black shrink-0">&check;</span>
                <span>Tautan verifikasi baru telah berhasil dikirim ke alamat email Anda.</span>
            </div>
        @endif

        <div class="space-y-3 pt-2">
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-[var(--orange)] hover:bg-[var(--orange-l)] text-white font-black py-3.5 text-sm rounded-xl border-2 border-[var(--ink)] shadow-[4px_4px_0_var(--ink)] hover:translate-x-0.5 hover:translate-y-0.5 transition-all cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    <span>Kirim Ulang Email Verifikasi</span>
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full py-2.5 px-4 bg-[var(--paper)] hover:bg-white text-[var(--ink)] border-2 border-[var(--ink)] rounded-xl font-bold text-xs shadow-[2px_2px_0_var(--ink)] transition cursor-pointer">
                    Keluar Akun
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
