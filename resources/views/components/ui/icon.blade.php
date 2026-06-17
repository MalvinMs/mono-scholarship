@props(['name', 'size' => '4'])

@php
$sizes = [
    '3' => 'size-3',
    '4' => 'size-4',
    '5' => 'size-5',
    '6' => 'size-6',
    '8' => 'size-8',
];

$sizeClass = $sizes[$size] ?? 'size-4';
@endphp

<x-dynamic-component component="lucide-{{ $name }}" {{ $attributes->merge(['class' => "$sizeClass shrink-0"]) }} />
