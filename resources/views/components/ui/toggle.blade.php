@props(['label' => null, 'id' => null])

@php $inputId = $id ?? \Illuminate\Support\Str::random(8); @endphp

<div class="flex items-center gap-2">
    <button
        type="button"
        role="switch"
        id="{{ $inputId }}"
        x-data="{ on: @json($attributes->get('checked', false)) }"
        x-bind:aria-checked="on"
        x-on:click="on = !on"
        x-bind:class="on ? 'bg-primary' : 'bg-canvas-soft'"
        {{ $attributes->merge(['class' => 'peer inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-canvas disabled:cursor-not-allowed disabled:opacity-50'])->except(['checked']) }}
    >
        <span
            x-bind:class="on ? 'translate-x-4' : 'translate-x-0'"
            class="pointer-events-none block size-4 rounded-full bg-canvas shadow-level-1 ring-0 transition-transform"
        ></span>
    </button>
    @if($label)
        <label for="{{ $inputId }}" class="text-body-sm text-ink leading-none cursor-pointer peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
            {{ $label }}
        </label>
    @endif
</div>
