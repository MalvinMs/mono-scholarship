@props([
    'variant' => 'primary',
    'size' => 'default',
    'href' => null,
    'disabled' => false,
])

@php
$variants = [
    'primary' => 'bg-primary text-primary-foreground hover:opacity-90 shadow-level-2',
    'secondary' => 'bg-canvas text-ink border border-hairline hover:bg-canvas-soft shadow-level-1',
    'destructive' => 'bg-error text-white hover:bg-error-deep shadow-level-2',
    'ghost' => 'hover:bg-canvas-soft-2 text-ink',
    'outline' => 'border border-hairline-strong bg-transparent hover:bg-canvas-soft text-ink',
    'link' => 'text-link hover:text-link-deep hover:underline',
];

$sizes = [
    'sm' => 'h-8 px-3.5 text-sm font-medium rounded-md', // 32px nav scale
    'default' => 'h-10 px-5 py-2 text-button-md rounded-md', // 40px in-app scale
    'pill' => 'h-12 px-7 text-button-lg rounded-pill', // 48px marketing
    'pill-sm' => 'h-8 px-5 text-button-md rounded-full',
    'icon' => 'size-10 rounded-md',
    'icon-circular' => 'size-8 rounded-full border border-hairline bg-canvas hover:bg-canvas-soft',
];

$variantClass = $variants[$variant] ?? $variants['primary'];
$sizeClass = $sizes[$size] ?? $sizes['default'];
@endphp

@if($href)
    <a href="{{ $href }}"
       {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap transition-all outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50 disabled:pointer-events-none disabled:opacity-50 $variantClass $sizeClass"]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button'])
        ->merge(['class' => "inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap transition-all outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50 disabled:pointer-events-none disabled:opacity-50 $variantClass $sizeClass"]) }}
        @if($disabled) disabled @endif>
        {{ $slot }}
    </button>
@endif
