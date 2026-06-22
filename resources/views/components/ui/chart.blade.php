@props([
    'options' => [],
    'height' => '250px',
    'id' => null,
])

@php
$chartId = $id ?? 'chrt_' . uniqid();
@endphp

<div style="height: {{ $height }}; position: relative; width: 100%; min-width: 0;"
     x-data="apexchart"
     x-init="renderChart('{{ $chartId }}', {{ Js::from($options) }}, '{{ $height }}')"
     wire:ignore
     {{ $attributes }}>
    <div id="{{ $chartId }}"></div>
</div>