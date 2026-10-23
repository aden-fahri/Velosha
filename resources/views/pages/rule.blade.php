@extends('layouts.app', ['title' => 'Aturan Balap & Kode Etik — Velosha'])

@section('content')

@php $content = \App\Models\RuleContent::current(); @endphp

@php
$groups = $content->groups ?? [];

$statusStyle = [
    'mandatory' => ['label' => 'WAJIB',       'bg' => 'var(--mustard)', 'color' => 'var(--ink)',   'border' => 'var(--ink)'],
    'allowed'   => ['label' => 'DIIZINKAN',    'bg' => 'var(--pine-l)',  'color' => 'var(--pine)',  'border' => 'var(--ink)'],
    'forbidden' => ['label' => 'DILARANG',     'bg' => 'var(--red-l)',   'color' => 'var(--red)',   'border' => 'var(--ink)'],
    'note'      => ['label' => 'INFORMASI',    'bg' => 'var(--butter)',  'color' => 'var(--ink)',   'border' => 'var(--ink)'],
];

$sanksiColors = [
    'neutral' => ['bg' => 'var(--pine)',    'text' => '#ffffff'],
    'race'    => ['bg' => 'var(--mustard)', 'text' => 'var(--ink)'],
    'danger'  => ['bg' => 'var(--red)',     'text' => '#ffffff'],
];
@endphp

<!-- 1. HERO ATURAN — Amber Background -->
<section style="background: var(--butter); border-bottom: 3px solid var(--ink); padding: 52px 0">
    <div class="wrap">
        <h1 class="h2" style="font-size: 48px !important; line-height: 1.05 !important; margin-top: 0 !important; max-width: 680px">
            Aturan balap & kode etik
        </h1>
        <p class="lead" style="margin-top: 14px !important; max-width: 640px">
            Standar keselamatan, perlengkapan, etika peloton, dan tata tertib resmi musim balap Velosha.
        </p>
    </div>
</section>

<!-- 2. KELOMPOK ATURAN -->
<section style="padding: 56px 0; background: var(--paper)">
    <div class="wrap" style="display: flex; flex-direction: column; gap: 48px">
        @foreach($groups as $g)
        <div class="card" style="padding: 32px 28px; background: var(--card)">
            <!-- Group Header -->
            <div style="display: flex; align-items: center; gap: 14px; padding-bottom: 20px; border-bottom: 2.5px solid var(--line); margin-bottom: 24px">
                <div class="sq" style="width: 48px; height: 48px; border-radius: 10px; background: var(--ink)">
                    <i data-lucide="{{ $g['icon'] ?? 'shield-check' }}" class="w-5 h-5" style="color: var(--orange)"></i>
                </div>
                <div>
                    <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 22px; color: var(--ink); line-height: 1.1">{{ $g['title'] }}</h2>
                    <p style="font-size: 13px; color: var(--mute); margin-top: 2px">Ketentuan teknis dan keselamatan kategori ini.</p>
                </div>
            </div>

            <!-- Rule Items Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($g['items'] as $item)
                @php $st = $statusStyle[$item['status']] ?? $statusStyle['note']; @endphp
                <div style="border: 2.5px solid var(--ink); border-radius: 10px; background: var(--paper); padding: 20px; transition: transform 0.15s ease, box-shadow 0.15s ease"
                     onmouseenter="this.style.transform='translate(-2px,-2px)'; this.style.boxShadow='4px 4px 0 var(--ink)'"
                     onmouseleave="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 10px">
                        <h3 style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 700; font-size: 16px; color: var(--ink)">{{ $item['label'] }}</h3>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; padding: 4px 10px; border: 2px solid {{ $st['border'] }}; border-radius: 6px; background: {{ $st['bg'] }}; color: {{ $st['color'] }}; white-space: nowrap; flex: none">
                            {{ $st['label'] }}
                        </span>
                    </div>
                    <p style="font-size: 14px; line-height: 1.6; color: var(--mute)">{{ $item['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- 3. SANKSI & DISKUALIFIKASI -->
<section style="padding: 56px 0; background: var(--paper2)">
    <div class="wrap">
        <div style="margin-bottom: 32px">
            <h2 class="h2" style="font-size: 36px !important">Sanksi & diskualifikasi</h2>
            <p class="lead" style="margin-top: 10px !important; max-width: 640px">
                Penegakan aturan demi sportivitas dan keselamatan seluruh rider.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($content->sanksi_items ?? [] as $s)
            @php($tone = $sanksiColors[$s['tone'] ?? 'neutral'] ?? $sanksiColors['neutral'])
            <div class="card" style="padding: 28px 24px; background: var(--card); display: flex; flex-direction: column; gap: 14px">
                <span style="display: inline-flex; align-self: flex-start; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; padding: 6px 14px; border: 2.5px solid var(--ink); border-radius: 6px; background: {{ $tone['bg'] }}; color: {{ $tone['text'] }}">
                    {{ $s['title'] }}
                </span>
                <p style="font-size: 14px; line-height: 1.6; color: var(--mute)">{{ $s['text'] }}</p>
            </div>
            @endforeach
        </div>

        @if(!empty($content->sanksi_note))
        <div style="margin-top: 28px; padding: 18px 24px; background: var(--mustard); border: 2.5px solid var(--ink); border-radius: 10px; display: flex; align-items: flex-start; gap: 12px">
            <i data-lucide="alert-triangle" class="w-5 h-5 flex-none" style="color: var(--ink); margin-top: 2px"></i>
            <p style="font-size: 14px; line-height: 1.55; color: var(--ink)">
                <strong>Catatan Keputusan Commissaire:</strong> {{ $content->sanksi_note }}
            </p>
        </div>
        @endif
    </div>
</section>

<!-- 4. CTA BAWAH -->
<section style="padding: 56px 0; background: var(--ink)">
    <div class="wrap">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 24px">
            <div>
                <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 28px; color: var(--paper); line-height: 1.15">
                    {{ $content->cta_title ?? 'Sudah paham aturannya?' }}
                </h2>
                <p style="font-size: 15px; color: var(--mute); margin-top: 6px">
                    {{ $content->cta_desc ?? 'Lihat event yang tersedia dan pilih kategorimu.' }}
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
