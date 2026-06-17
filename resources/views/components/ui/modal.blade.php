@props(['title' => null, 'description' => null, 'maxWidth' => 'md', 'closeable' => true])

@php
$maxWidths = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
];
$maxWidthClass = $maxWidths[$maxWidth] ?? $maxWidths['md'];
@endphp

<div
    x-data="{ open: @entangle($attributes->wire('model')) }"
    x-show="open"
    x-transition.opacity.duration.200ms
    class="fixed inset-0 z-50 flex items-center justify-center"
    style="display: none;"
    x-on:keydown.escape.window="open = false"
>
    <!-- Overlay -->
    <div
        x-show="open"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 bg-black/40 backdrop-blur-sm"
        @if($closeable) x-on:click="open = false" @endif
    ></div>

    <!-- Panel -->
    <div
        x-show="open"
        x-transition:enter="animate-in fade-in-0 zoom-in-95"
        x-transition:leave="animate-out fade-out-0"
        class="relative z-50 w-full {{ $maxWidthClass }} rounded-lg bg-canvas text-ink shadow-level-5 mx-4"
        x-on:click.outside="if(open) open = false"
    >
        @if($title)
            <div class="flex items-center justify-between border-b border-hairline px-6 py-4">
                <div>
                    <h2 class="text-display-sm">{{ $title }}</h2>
                    @if($description)
                        <p class="text-body-sm text-mute">{{ $description }}</p>
                    @endif
                </div>
                @if($closeable)
                    <button @click="open = false" class="rounded-sm p-1 text-mute hover:text-ink transition-colors focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50">
                        <x-lucide-x class="size-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                @endif
            </div>
        @endif

        <div class="px-6 py-4">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="flex justify-end gap-3 border-t border-hairline px-6 py-4 bg-canvas-soft rounded-b-lg">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
