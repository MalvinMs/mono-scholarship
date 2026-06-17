@props(['label' => null, 'error' => null, 'id' => null])

@php
    $inputId = $id ?? \Illuminate\Support\Str::random(8);
@endphp

<div>
    @if($label)
        <label for="{{ $inputId }}" class="block text-body-sm-strong mb-2">
            {{ $label }}
        </label>
    @endif
    <select
        id="{{ $inputId }}"
        {{ $attributes->merge([
            'class' => 'flex h-10 w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm text-ink transition-colors focus-visible:outline-none focus-visible:border-hairline-strong focus-visible:ring-[3px] focus-visible:ring-primary/20 disabled:cursor-not-allowed disabled:bg-canvas-soft disabled:text-mute'
            . ($error ? ' border-error focus-visible:ring-error/20' : '')
        ]) }}
    >
        {{ $slot }}
    </select>
    @if($error)
        <p class="mt-1.5 text-caption text-error">{{ $error }}</p>
    @endif
</div>
