@props(['size' => 'default'])

@php
$sizes = [
    'sm' => 'size-4',
    'default' => 'size-8',
    'lg' => 'size-12',
];
$sizeClass = $sizes[$size] ?? $sizes['default'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-center py-8']) }}>
    <x-lucide-loader-circle class="{{ $sizeClass }} animate-spin text-mute" />
</div>
