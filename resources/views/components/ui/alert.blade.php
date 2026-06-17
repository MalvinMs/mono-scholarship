@props(['variant' => 'default', 'dismissible' => false])

@php
$variants = [
    'default' => 'bg-canvas-soft text-ink border border-hairline',
    'info' => 'bg-link-bg-soft text-link-deep border-none',
    'success' => 'bg-link-bg-soft text-link-deep border-none',
    'warning' => 'bg-warning-soft text-warning-deep border-none',
    'destructive' => 'bg-error-soft text-error-deep border-none',
];
$variantClass = $variants[$variant] ?? $variants['default'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition.opacity.duration.150ms
    {{ $attributes->merge(['class' => "relative w-full rounded-md p-4 text-body-sm $variantClass"]) }}
    role="alert"
>
    <div class="flex items-start gap-3">
        <div class="flex-1">{{ $slot }}</div>
        @if($dismissible)
            <button @click="show = false" class="shrink-0 rounded-sm p-1 opacity-70 transition-opacity hover:opacity-100 focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50">
                <x-lucide-x class="size-4" />
                <span class="sr-only">Tutup</span>
            </button>
        @endif
    </div>
</div>
