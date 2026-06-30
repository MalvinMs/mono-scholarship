@props(['label' => null, 'collapsible' => true, 'expanded' => true])

<div class="relative flex w-full min-w-0 flex-col px-2 py-1" data-sidebar="group" x-data="{ expanded: {{ $expanded ? 'true' : 'false' }} }">
    @if($label)
        <button
            type="button"
            x-show="!desktopCollapsed"
            @if($collapsible) x-on:click="expanded = !expanded" @endif
            class="mb-1 flex w-full h-8 shrink-0 items-center justify-between rounded-sm px-2 text-caption-mono uppercase text-mute transition-all duration-300 outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50 @if($collapsible) cursor-pointer hover:text-ink hover:bg-canvas-soft @endif"
            data-sidebar="group-label"
        >
            <span class="truncate">{{ $label }}</span>
            @if($collapsible)
                <x-lucide-chevron-down class="size-3.5 shrink-0 transition-transform duration-200" x-bind:class="expanded ? '' : '-rotate-90'" />
            @endif
        </button>
    @endif
    <ul x-show="expanded" class="flex w-full min-w-0 flex-col gap-0.5 mt-0.5" data-sidebar="menu" x-transition>
        {{ $slot }}
    </ul>
</div>
