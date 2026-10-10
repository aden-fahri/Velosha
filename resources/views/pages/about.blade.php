@extends('layouts.app', ['title' => 'About Velosha — Platform Maraton & Gran Fondo Indonesia'])

@section('content')

@php $content = \App\Models\AboutContent::current(); @endphp

<x-page-header
    :eyebrow="$content->header_eyebrow"
    :title="$content->header_title"
    :description="$content->header_description"
    :meta="$content->header_meta"
/>

<!-- Manifesto -->
<section class="py-16 bg-white border-b border-veloborder">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-7">
                <div class="w-10 h-1 bg-teal-500 velo-box-1 mb-3"></div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-900 tracking-tight mb-6">{{ $content->manifesto_title }}</h2>
                <div class="space-y-4 text-sm text-[#4b555b] leading-relaxed">
                    @foreach (preg_split('/\R{2,}/', trim((string) $content->manifesto_body)) as $para)
                    <p>{{ $para }}</p>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-velosurfacealt border border-veloborder velo-box-1 p-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-teal-800 mb-5">Nilai Utama</h3>
                    <div class="space-y-4">
                        @foreach($content->value_items ?? [] as $v)
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-white text-teal-700 border border-veloborder flex items-center justify-center velo-box-1 shrink-0">
                                <i data-lucide="{{ $v['icon'] }}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-teal-900">{{ $v['title'] }}</div>
                                <p class="text-[11px] text-[#556066] leading-relaxed mt-0.5">{{ $v['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-14 bg-velosurfacealt border-t border-veloborder">
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
