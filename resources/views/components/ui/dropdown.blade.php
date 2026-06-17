@props(['align' => 'end', 'width' => '48'])

<div
    x-data="{ open: false }"
    x-on:click.outside="open = false"
    x-on:keydown.escape.window="open = false"
    class="relative inline-block text-left"
>
    <div x-on:click="open = !open">
        {{ $trigger ?? '' }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @if($align === 'end')
        class="absolute right-0 z-50 mt-2 w-{{ $width }} origin-top-right rounded-md bg-canvas text-ink shadow-level-5"
        @else
        class="absolute left-0 z-50 mt-2 w-{{ $width }} origin-top-left rounded-md bg-canvas text-ink shadow-level-5"
        @endif
        style="display: none;"
    >
        <div class="py-1">
            {{ $slot }}
        </div>
    </div>
</div>
