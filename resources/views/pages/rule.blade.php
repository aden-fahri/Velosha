@extends('layouts.app', ['title' => 'Aturan Balap & Kode Etik — Velosha'])

@section('content')

@php $content = \App\Models\RuleContent::current(); @endphp

<x-page-header
    eyebrow="Regulasi &amp; Keselamatan"
    title="Aturan Balap &amp; Kode Etik"
    description="Standar keselamatan, regulasi teknis perlengkapan, etika peloton, dan tata tertib resmi musim balap Velosha."
/>

@php
$groups = $content->groups ?? [];

$statusStyle = [
    'mandatory' => ['label' => 'Wajib', 'class' => 'bg-race-50 text-race-700 border-race-200'],
    'allowed' => ['label' => 'Diizinkan', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
    'forbidden' => ['label' => 'Dilarang', 'class' => 'bg-red-50 text-red-700 border-red-200'],
    'note' => ['label' => 'Informasi', 'class' => 'bg-teal-50 text-teal-800 border-teal-200'],
];

$sanksiToneClass = [
    'neutral' => 'bg-teal-50 text-teal-800 border-teal-200',
    'race' => 'bg-race-50 text-race-700 border-race-300',
    'danger' => 'bg-red-50 text-red-700 border-red-200',
];
@endphp

<!-- Main Rules Section -->
<section class="py-12 sm:py-16 bg-[#f1f4f4] border-b border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        @foreach($groups as $g)
        <div class="bg-white border border-veloborder velo-box-1 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center gap-3 border-b border-veloborder pb-4">
                <div class="w-10 h-10 bg-teal-900 text-white flex items-center justify-center velo-box-3 shrink-0 shadow-xs">
                    <i data-lucide="{{ $g['icon'] ?? 'shield-check' }}" class="w-5 h-5 text-[#EFA96A]"></i>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-teal-900 tracking-tight">{{ $g['title'] }}</h2>
                    <p class="text-xs text-velomutedlight font-medium">Ketentuan teknis dan keselamatan kategori ini.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($g['items'] as $item)
                @php($st = $statusStyle[$item['status']] ?? $statusStyle['note'])
                <div class="border border-veloborder velo-box-1 bg-[#f1f4f4] p-5 hover:border-teal-700 transition space-y-2">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm font-black text-teal-900">{{ $item['label'] }}</h3>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 border velo-box-3 shrink-0 {{ $st['class'] }}">
                            {{ $st['label'] }}
                        </span>
                    </div>
                    <p class="text-xs text-velomutedlight leading-relaxed font-medium">{{ $item['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Sanksi & Penalti Section -->
<section class="py-16 bg-white border-b border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <div class="w-10 h-1 bg-race-600 velo-box-1 mb-3"></div>
            <h2 class="text-2xl sm:text-3xl font-black text-teal-900 tracking-tight">Sanksi &amp; Diskualifikasi Pelanggaran</h2>
            <p class="text-xs text-velomutedlight mt-2 leading-relaxed">Penegakan regulasi keselamatan demi menjaga sportivitas dan keamanan seluruh rider.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($content->sanksi_items ?? [] as $s)
            <div class="p-6 border border-veloborder velo-box-1 bg-white hover:border-teal-700 transition shadow-xs space-y-3">
                <span class="inline-block text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 border velo-box-3 {{ $sanksiToneClass[$s['tone'] ?? 'neutral'] ?? $sanksiToneClass['neutral'] }}">
                    {{ $s['title'] }}
                </span>
                <p class="text-xs text-velomutedlight leading-relaxed font-medium">{{ $s['text'] }}</p>
            </div>
            @endforeach
        </div>

        @if(!empty($content->sanksi_note))
        <div class="mt-8 p-4 bg-teal-900 text-white velo-box-1 text-xs leading-relaxed font-medium border border-teal-800">
            <span class="font-extrabold text-[#EFA96A]">Catatan Keputusan Comisaris:</span> {{ $content->sanksi_note }}
        </div>
        @endif
    </div>
</section>

<!-- Bottom CTA -->
<section class="py-14 bg-[#f1f4f4]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-5">
        <div>
            <h2 class="text-xl font-black text-teal-900 tracking-tight">{{ $content->cta_title ?? 'Sudah Memahami Aturan Balap?' }}</h2>
            <p class="text-xs text-velomutedlight mt-1 font-medium">{{ $content->cta_desc ?? 'Pilih kategori jarak dan amankan slot pendaftaran Anda sekarang.' }}</p>
        </div>
        <a href="{{ $content->cta_url ?? '/events' }}" wire:navigate class="inline-flex items-center justify-center bg-race-600 hover:bg-race-700 text-white font-extrabold px-6 py-3.5 text-xs tracking-wider uppercase velo-box-1 shadow-xs transition shrink-0">
            <span>{{ $content->cta_label ?? 'Jelajahi Kalender Balap' }}</span>
            <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
        </a>
    </div>
</section>

@endsection
