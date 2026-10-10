@extends('layouts.app', ['title' => 'Aturan Balap & Kode Etik — Velosha'])

@section('content')

@php $content = \App\Models\RuleContent::current(); @endphp

<x-page-header
    :eyebrow="$content->header_eyebrow"
    :title="$content->header_title"
    :description="$content->header_description"
    :meta="$content->header_meta"
/>

@php
$groups = $content->groups ?? [];

$statusStyle = [
    'allowed' => ['label' => 'Diizinkan', 'class' => 'bg-teal-50 text-teal-700 border-teal-200'],
    'mandatory' => ['label' => 'Wajib', 'class' => 'bg-race-50 text-race-700 border-race-200'],
    'forbidden' => ['label' => 'Dilarang', 'class' => 'bg-red-50 text-red-700 border-red-200'],
    'note' => ['label' => 'Informasi', 'class' => 'bg-velosurfacealt text-[#4b555b] border-veloborder'],
];

$sanksiToneClass = [
    'neutral' => 'bg-velosurfacealt text-[#4b555b]',
    'race' => 'bg-race-50 text-race-700',
    'danger' => 'bg-red-50 text-red-700',
];
@endphp

<!-- Grouped Rules -->
<section class="py-16 bg-white border-b border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        @foreach($groups as $g)
        <div>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 bg-teal-50 text-teal-700 flex items-center justify-center velo-box-1 shrink-0">
                    <i data-lucide="{{ $g['icon'] }}" class="w-5 h-5"></i>
                </div>
                <h2 class="text-lg sm:text-xl font-extrabold text-teal-900 tracking-tight">{{ $g['title'] }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($g['items'] as $item)
                <div class="border border-veloborder velo-box-1 bg-white p-5">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <h3 class="text-sm font-bold text-teal-900">{{ $item['label'] }}</h3>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 border velo-box-1 shrink-0 {{ ($statusStyle[$item['status']] ?? $statusStyle['note'])['class'] }}">{{ ($statusStyle[$item['status']] ?? $statusStyle['note'])['label'] }}</span>
                    </div>
                    <p class="text-xs text-[#556066] leading-relaxed">{{ $item['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Sanksi -->
<section class="py-16 bg-velosurfacealt border-y border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="w-10 h-1 bg-race-500 velo-box-1 mb-3"></div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mb-8">Sanksi Pelanggaran</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($content->sanksi_items ?? [] as $s)
            <div class="p-6 border border-veloborder velo-box-1 bg-white">
                <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-1 border velo-box-1 mb-4 {{ $sanksiToneClass[$s['tone'] ?? 'neutral'] ?? $sanksiToneClass['neutral'] }}">{{ $s['title'] }}</span>
                <p class="text-xs text-[#556066] leading-relaxed">{{ $s['text'] }}</p>
            </div>
            @endforeach
        </div>

        <p class="text-[11px] text-[#5f6a70] mt-8 leading-relaxed">
            {{ $content->sanksi_note }}
        </p>
    </div>
</section>

<!-- CTA -->
<section class="py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-5">
        <div>
            <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">{{ $content->cta_title }}</h2>
            <p class="text-xs text-[#556066] mt-1">{{ $content->cta_desc }}</p>
        </div>
        <a href="{{ $content->cta_url }}" class="inline-flex items-center justify-center bg-race-500 hover:bg-race-600 text-white font-semibold px-6 py-3.5 text-xs tracking-wider uppercase velo-box-1 transition shrink-0">
            <span>{{ $content->cta_label }}</span>
            <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
        </a>
    </div>
</section>

@endsection
