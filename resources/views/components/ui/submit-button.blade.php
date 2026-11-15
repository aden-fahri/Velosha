@props([
    'variant' => 'orange',
    'loadingText' => 'Memproses...',
    'target' => null,
    'type' => 'submit',
    'size' => 'md',
    'fullWidth' => false,
])

@php
    $variantClasses = match ($variant) {
        'pine', 'green' => 'bg-[#1F6F4A] hover:bg-[#18573a] text-white shadow-[3px_3px_0_#10282E]',
        'mustard', 'yellow' => 'bg-[#F4BE3F] hover:bg-[#F8E3B5] text-[#10282E] shadow-[3px_3px_0_#10282E]',
        'ink', 'black' => 'bg-[#10282E] hover:bg-black text-white shadow-[3px_3px_0_rgba(16,40,46,0.4)]',
        'ghost' => 'bg-white hover:bg-[#F8E3B5] text-[#10282E] shadow-[2px_2px_0_#10282E]',
        default => 'bg-[#DE7119] hover:bg-[#E58537] text-white shadow-[3px_3px_0_#10282E]',
    };

    $sizeClasses = match ($size) {
        'sm' => 'py-2 px-3.5 text-xs font-black',
        'lg' => 'py-4 px-6 text-sm sm:text-base font-black',
        default => 'py-3.5 px-5 text-xs sm:text-sm font-black',
    };

    $widthClass = $fullWidth ? 'w-full' : '';
@endphp

<button type="{{ $type }}"
        {{ $attributes->merge([
            'class' => "inline-flex items-center justify-center gap-2 rounded-xl border-[2.5px] border-[#10282E] uppercase tracking-wider transition-all duration-150 hover:translate-x-0.5 hover:translate-y-0.5 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none {$variantClasses} {$sizeClasses} {$widthClass}"
        ]) }}
        wire:loading.attr="disabled"
        wire:loading.class="opacity-60 cursor-not-allowed"
        @if ($target) wire:target="{{ $target }}" @endif>
    
    <span wire:loading.remove @if ($target) wire:target="{{ $target }}" @endif class="inline-flex items-center justify-center gap-2">
        {{ $slot }}
    </span>

    <span wire:loading @if ($target) wire:target="{{ $target }}" @endif class="inline-flex items-center justify-center gap-2">
        <svg class="animate-spin h-4 w-4 shrink-0 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>{{ $loadingText }}</span>
    </span>
</button>
