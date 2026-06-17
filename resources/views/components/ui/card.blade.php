@props([
    'variant' => 'default',
    'padding' => 'default',
    'interactive' => false
])

@php
$variants = [
    'default' => 'bg-canvas text-ink shadow-level-2 rounded-md',
    'large' => 'bg-canvas text-ink shadow-level-4 rounded-lg',
    'soft' => 'bg-canvas-soft text-ink shadow-level-2 rounded-md',
    'empty' => 'bg-canvas-soft text-ink border-2 border-dashed border-hairline rounded-md shadow-none',
    'pricing' => 'bg-canvas text-ink shadow-level-4 rounded-lg',
    'pricing-featured' => 'bg-primary text-primary-foreground shadow-level-4 rounded-lg',
];

$paddings = [
    'none' => '',
    'sm' => 'p-4', // 16px
    'default' => 'p-6', // 24px
    'lg' => 'p-8', // 32px
    'xl' => 'p-10', // 40px
];

$variantClass = $variants[$variant] ?? $variants['default'];
$padClass = $paddings[$padding] ?? $paddings['default'];

$baseClasses = "transition-all duration-200 $variantClass $padClass";
if ($interactive) {
    $baseClasses .= " hover:shadow-level-3 cursor-pointer";
}
@endphp

<div {{ $attributes->merge(['class' => $baseClasses]) }}>
    {{ $slot }}
</div>
