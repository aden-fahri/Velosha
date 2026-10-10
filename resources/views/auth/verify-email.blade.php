@extends('layouts.app', ['title' => 'Verifikasi Email — Velosha', 'nav' => false])

@section('content')
    <div class="min-h-[calc(100vh-96px)] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-velosurfacealt">
        <div class="max-w-md w-full mx-auto bg-white border border-veloborder velo-box-1 shadow-sm p-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 border border-veloborderstrong bg-white hover:bg-velosurfacealt px-3 py-1.5 text-xs font-semibold text-teal-700 velo-box-1 transition mb-5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">Verifikasi Alamat Email</h2>
            <p class="text-xs text-[#556066] mt-1 mb-6">
                Sebelum melanjutkan, mohon verifikasi alamat email Anda dengan mengeklik tautan yang kami kirim.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="mb-4 px-3 py-2 text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 velo-box-1">
                    Tautan verifikasi baru sudah dikirim ke alamat email Anda.
                </div>
            @endif

            <form action="{{ route('verification.send') }}" method="POST" class="space-y-3">
                @csrf
                <button type="submit" class="w-full bg-race-600 hover:bg-race-700 text-white font-semibold py-2.5 text-xs velo-box-1 shadow-sm transition">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="w-full bg-white border border-veloborderstrong hover:bg-velosurfacealt text-teal-700 font-semibold py-2.5 text-xs velo-box-1 transition">
                    Keluar
                </button>
            </form>
        </div>
    </div>
@endsection
