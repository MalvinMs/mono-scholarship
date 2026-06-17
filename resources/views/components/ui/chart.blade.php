@props([
    'type' => 'bar',
    'labels' => [],
    'datasets' => [],
    'options' => [],
    'height' => '250px',
    'id' => null,
])

@php
$chartId = $id ?? 'chrt_' . uniqid();

$mergedOptions = array_replace_recursive([
    'responsive' => true,
    'maintainAspectRatio' => false,
    'animation' => ['duration' => 600, 'easing' => 'easeOutQuart'],
    'plugins' => [
        'legend' => ['display' => false],
        'tooltip' => ['enabled' => true],
    ],
], $options);

$config = [
    'type' => $type,
    'data' => [
        'labels' => $labels,
        'datasets' => $datasets,
    ],
    'options' => $mergedOptions,
];
@endphp

<div style="height: {{ $height }}; position: relative;"
     x-data="chart"
     x-init="init('{{ $chartId }}', {{ Js::from($config) }})"
     wire:ignore>
    <canvas id="{{ $chartId }}"></canvas>
</div>