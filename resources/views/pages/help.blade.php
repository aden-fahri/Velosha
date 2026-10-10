@extends('layouts.app', ['title' => 'Help Center — Velosha'])

@section('content')

@php $content = \App\Models\HelpContent::current(); @endphp

<x-page-header
    :eyebrow="$content->header_eyebrow"
    :title="$content->header_title"
    :description="$content->header_description"
    :meta="$content->header_meta"
/>

<!-- Pintasan Kontak -->
<section class="py-10 bg-white border-b border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($content->contacts ?? [] as $c)
            <div class="border border-veloborder velo-box-1 p-5 flex items-start gap-3">
                <div class="w-9 h-9 bg-teal-50 text-teal-700 flex items-center justify-center velo-box-1 shrink-0">
                    <i data-lucide="{{ $c['icon'] }}" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-teal-900">{{ $c['name'] }}</div>
                    <div class="text-[11px] text-[#5f6a70] mt-0.5">{{ $c['info'] }}</div>
                    <div class="text-[11px] font-semibold text-race-700 mt-1">{{ $c['value'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ per kategori -->
@php
$faqGroups = $content->faq_groups ?? [];
@endphp

<section class="py-16 bg-velosurfacealt">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        @foreach($faqGroups as $group)
        <div>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-1.5 h-6 bg-race-500 velo-box-1 shrink-0"></div>
                <h2 class="text-lg sm:text-xl font-extrabold text-teal-900 tracking-tight">{{ $group['title'] }}</h2>
            </div>

            <div class="space-y-3">
                @foreach($group['items'] ?? [] as $q)
                <details class="group border border-veloborder velo-box-1 bg-white">
                    <summary class="flex items-center justify-between gap-4 px-5 py-4 cursor-pointer list-none text-sm font-bold text-teal-900 hover:bg-velosurfacealt transition-colors">
                        <span>{{ $q['q'] }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-[#5f6a70] shrink-0 transition-transform group-open:rotate-180"></i>
                    </summary>
                    <p class="px-5 pb-4 text-xs text-[#556066] leading-relaxed">{{ $q['a'] }}</p>
                </details>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- CTA -->
<section class="py-14 bg-white border-t border-veloborder">
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
