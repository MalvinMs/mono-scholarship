@props(['open' => false, 'side' => 'left', 'title' => null, 'description' => null])

@php
$sideClasses = [
    'left' => 'inset-y-0 left-0 h-full w-72 border-r data-[state=closed]:-translate-x-full data-[state=open]:translate-x-0',
    'right' => 'inset-y-0 right-0 h-full w-72 border-l data-[state=closed]:translate-x-full data-[state=open]:translate-x-0',
    'top' => 'inset-x-0 top-0 border-b data-[state=closed]:-translate-y-full data-[state=open]:translate-y-0',
    'bottom' => 'inset-x-0 bottom-0 border-t data-[state=closed]:translate-y-full data-[state=open]:translate-y-0',
];
$sideClass = $sideClasses[$side] ?? $sideClasses['left'];
@endphp

<div
    x-data="{ open: @json($open) }"
    x-show="open"
    class="fixed inset-0 z-50"
    style="display: none;"
    x-on:keydown.escape.window="open = false"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 bg-ink/40 backdrop-blur-sm"
        x-on:click="open = false"
    ></div>

    <!-- Drawer Panel -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed {{ $sideClass }} bg-canvas shadow-level-4 border-hairline transition-transform duration-300 z-50 flex flex-col"
    >
        @if($title)
            <div class="flex items-center justify-between border-b border-hairline px-6 py-4">
                <div>
                    <h2 class="text-display-xs text-ink">{{ $title }}</h2>
                    @if($description)
                        <p class="mt-1 text-body-sm text-mute">{{ $description }}</p>
                    @endif
                </div>
                <button x-on:click="open = false" class="rounded-sm p-1 opacity-70 transition-opacity hover:opacity-100 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary text-mute hover:text-ink">
                    <x-lucide-x class="size-4" />
                    <span class="sr-only">Tutup</span>
                </button>
            </div>
        @endif
        <div class="flex-1 overflow-y-auto p-4">
            {{ $slot }}
        </div>
    </div>
</div>
