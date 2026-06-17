@props(['variant' => 'default'])

@php
$variants = [
    'default' => 'bg-canvas-soft text-body',
    'secondary' => 'bg-canvas text-ink border border-hairline',
    'outline' => 'bg-transparent text-body border border-hairline',
    'destructive' => 'bg-error-soft text-error-deep',
    'success' => 'bg-link-bg-soft text-link-deep',
    'warning' => 'bg-warning-soft text-warning-deep',
];

$variantClass = $variants[$variant] ?? $variants['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2 py-0.5 text-caption transition-colors $variantClass"]) }}>
    {{ $slot }}
</span>
