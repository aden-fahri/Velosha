@extends('layouts.app', ['title' => 'Tentang Velosha — Platform Registrasi Balap Sepeda Indonesia'])

@section('content')

@php $content = \App\Models\AboutContent::current(); @endphp

<!-- 1. HERO ABOUT — Amber Background -->
<section style="background: var(--butter); border-bottom: 3px solid var(--ink); padding: 52px 0">
    <div class="wrap">
        <h1 class="h2" style="font-size: 48px !important; line-height: 1.05 !important; margin-top: 0 !important; max-width: 680px">
            {{ $content->header_title ?? 'Platform Registrasi Balap Sepeda Indonesia' }}
        </h1>
        <p class="lead" style="margin-top: 14px !important; max-width: 640px">
            {{ $content->header_description ?? 'Kami membangun infrastruktur terpadu untuk endurance ride di Indonesia — mulai dari kurasi rute, timing chip RFID, hingga Race Village hari-H.' }}
        </p>
    </div>
</section>

<!-- 2. MANIFESTO + NILAI UTAMA -->
<section style="padding: 72px 0; background: var(--paper)">
    <div class="wrap">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Kiri: Manifesto -->
            <div class="lg:col-span-7">
                <h2 class="h2" style="font-size: 40px !important; margin-bottom: 24px !important">
                    {{ $content->manifesto_title ?? 'Kami menghidupkan budaya sepeda di sini' }}
                </h2>
                <div style="display: flex; flex-direction: column; gap: 16px">
                    @foreach (preg_split('/\R{2,}/', trim((string) ($content->manifesto_body ?? ''))) as $para)
                    <p style="font-size: 16px; line-height: 1.7; color: var(--mute)">{{ $para }}</p>
                    @endforeach
                </div>
            </div>

            <!-- Kanan: Nilai Utama Card -->
            <div class="lg:col-span-5">
                <div class="card" style="padding: 32px 28px; background: var(--card)">
                    <h3 style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 0.12em; color: var(--ink); margin-bottom: 24px">
                        NILAI UTAMA
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 20px">
                        @foreach($content->value_items ?? [] as $v)
                        <div style="display: flex; align-items: flex-start; gap: 14px">
                            <div class="sq" style="width: 48px; height: 48px; border-radius: 10px; background: var(--orange); flex: none">
                                <i data-lucide="{{ $v['icon'] }}" class="w-5 h-5" style="color: var(--ink)"></i>
                            </div>
                            <div>
                                <div style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 700; font-size: 16px; color: var(--ink); margin-bottom: 4px">{{ $v['title'] }}</div>
                                <p style="font-size: 14px; line-height: 1.55; color: var(--mute)">{{ $v['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. CTA BANNER -->
<section style="padding: 56px 0; background: var(--ink)">
    <div class="wrap">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 24px">
            <div>
                <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 28px; color: var(--paper); line-height: 1.15">
                    {{ $content->cta_title ?? 'Siap mulai musim 2026?' }}
                </h2>
                <p style="font-size: 15px; color: var(--mute); margin-top: 6px">
                    {{ $content->cta_desc ?? 'Daftar sekali, kelola semua race Anda lewat satu portal.' }}
                </p>
            </div>
            <a href="{{ $content->cta_url ?? '/events' }}" wire:navigate class="btn text-base">
                <span>{{ $content->cta_label ?? 'Lihat Semua Race' }}</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</section>

@endsection
