@props(['href' => null, 'danger' => false])

@if($href)
    <a href="{{ $href }}"
       {{ $attributes->merge(['class' => 'block px-4 py-2 text-body-sm ' . ($danger ? 'text-error hover:bg-error/10' : 'text-mute hover:bg-canvas-soft hover:text-ink') . ' transition-colors']) }}>
        {{ $slot }}
    </a>
@else
    <button type="button"
       {{ $attributes->merge(['class' => 'flex w-full items-center px-4 py-2 text-body-sm ' . ($danger ? 'text-error hover:bg-error/10' : 'text-mute hover:bg-canvas-soft hover:text-ink') . ' transition-colors']) }}>
        {{ $slot }}
    </button>
@endif
