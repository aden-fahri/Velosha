@extends('layouts.app', ['title' => 'Pusat Bantuan — Velosha'])

@section('content')

@php $content = \App\Models\HelpContent::current(); @endphp

@php
$contactIcons = [
    'message-circle' => ['bg' => 'var(--pine)',    'text' => '#ffffff'],
    'mail'           => ['bg' => 'var(--mustard)',  'text' => 'var(--ink)'],
    'bot'            => ['bg' => 'var(--orange)',   'text' => 'var(--ink)'],
];
@endphp

<!-- 1. HERO BANTUAN — Amber Background -->
<section style="background: var(--butter); border-bottom: 3px solid var(--ink); padding: 52px 0">
    <div class="wrap">
        <h1 class="h2" style="font-size: 48px !important; line-height: 1.05 !important; margin-top: 0 !important; max-width: 600px">
            Pusat bantuan
        </h1>
        <p class="lead" style="margin-top: 14px !important; max-width: 640px">
            Semua soal registrasi, pembayaran, ambil race pack, dan persiapan hari-H. Kalau jawabannya tidak ada, hubungi kami.
        </p>
    </div>
</section>

<!-- 2. KONTAK PINTASAN -->
<section style="padding: 40px 0; background: var(--paper); border-bottom: 2.5px solid var(--line)">
    <div class="wrap">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach($content->contacts ?? [] as $c)
            @php $ci = $contactIcons[$c['icon']] ?? ['bg' => 'var(--ink)', 'text' => '#ffffff']; @endphp
            <div class="card" style="padding: 24px 22px; background: var(--card); display: flex; align-items: flex-start; gap: 16px">
                <div class="sq" style="width: 48px; height: 48px; border-radius: 10px; background: {{ $ci['bg'] }}; flex: none">
                    <i data-lucide="{{ $c['icon'] }}" class="w-5 h-5" style="color: {{ $ci['text'] }}"></i>
                </div>
                <div style="flex: 1; min-width: 0">
                    <div style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 700; font-size: 17px; color: var(--ink)">{{ $c['name'] }}</div>
                    <div style="font-size: 13px; color: var(--mute); margin-top: 3px">{{ $c['info'] }}</div>
                    <div style="font-size: 14px; font-weight: 700; color: var(--orange-d); margin-top: 6px">{{ $c['value'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 3. FAQ SECTIONS — 2 Kolom -->
@php
$faqGroups = $content->faq_groups ?? [];
@endphp

<section style="padding: 56px 0; background: var(--paper)">
    <div class="wrap">
        <!-- Grid 2 kolom utk FAQ -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-14">
            @foreach($faqGroups as $group)
            <div>
                <!-- Section Title -->
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px">
                    <div style="width: 4px; height: 28px; background: var(--orange); border-radius: 3px; flex: none"></div>
                    <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 22px; color: var(--ink); line-height: 1.1">
                        {{ $group['title'] }}
                    </h2>
                </div>

                <!-- Accordion Items -->
                <div style="display: flex; flex-direction: column; gap: 8px">
                    @foreach($group['items'] ?? [] as $q)
                    <details class="group" style="border: 2.5px solid var(--ink); border-radius: 10px; background: var(--card); overflow: hidden">
                        <summary style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 20px; cursor: pointer; list-style: none; font-family: 'Inter', sans-serif; font-weight: 600; font-size: 15px; color: var(--ink); transition: background 0.15s"
                                 onmouseenter="this.style.background='var(--paper)'"
                                 onmouseleave="this.style.background='transparent'">
                            <span>{{ $q['q'] }}</span>
                            <svg class="flex-none transition-transform group-open:rotate-180" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--mute)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>
                        <div style="padding: 0 20px 18px 20px; font-size: 14px; line-height: 1.65; color: var(--mute)">
                            {{ $q['a'] }}
                        </div>
                    </details>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 3b. KARTU BUAT TIKET BANTUAN -->
<section style="padding: 0 0 56px 0; background: var(--paper)">
    <div class="wrap">
        <div class="card" style="padding: 32px; background: var(--butter); border: 2.5px solid var(--ink); border-radius: 16px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px; box-shadow: 4px 4px 0 var(--ink)">
            <div>
                <span class="chip font-bold text-xs bg-[var(--orange)] text-[var(--ink)] border border-[var(--ink)] px-2.5 py-0.5 rounded">Bantuan Langsung</span>
                <h3 style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 24px; color: var(--ink); margin-top: 8px">
                    Belum ketemu jawabannya? Buat tiket bantuan
                </h3>
                <p style="font-size: 14px; color: var(--ink); opacity: 0.85; margin-top: 4px; max-width: 600px">
                    Sampaikan kendala pendaftaran, pembayaran, atau nomor BIB. Tim pengelola Velosha akan menangani aduanmu langsung di Portal.
                </p>
            </div>
            <a href="/dashboard/bantuan/buat" wire:navigate class="btn" style="background: var(--orange); color: var(--ink); border: 2px solid var(--ink); font-weight: 800; padding: 12px 24px; box-shadow: 3px 3px 0 var(--ink)">
                <span>Buat Tiket Bantuan</span>
                <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
            </a>
        </div>
    </div>
</section>

<!-- 4. CTA BAWAH -->
<section style="padding: 56px 0; background: var(--ink)">
    <div class="wrap">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 24px">
            <div>
                <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 28px; color: var(--paper); line-height: 1.15">
                    {{ $content->cta_title ?? 'Masih ada yang mengganjal?' }}
                </h2>
                <p style="font-size: 15px; color: var(--paper2); margin-top: 6px">
                    {{ $content->cta_desc ?? 'Tim kami siap membantu setiap hari kerja, 09.00–18.00 WIB.' }}
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
