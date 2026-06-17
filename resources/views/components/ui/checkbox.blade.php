@props(['label' => null, 'error' => null, 'id' => null])

@php $inputId = $id ?? \Illuminate\Support\Str::random(8); @endphp

<div class="flex items-start gap-2">
    <div class="flex h-5 items-center">
        <input
            type="checkbox"
            id="{{ $inputId }}"
            {{ $attributes->merge([
                'class' => 'peer size-4 shrink-0 rounded-sm border border-primary shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 accent-primary'
            ]) }}
        >
    </div>
    @if($label)
        <label for="{{ $inputId }}" class="text-sm leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer">
            {{ $label }}
        </label>
    @endif
</div>
