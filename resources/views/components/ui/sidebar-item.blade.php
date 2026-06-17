@props([
    'href' => '#',
    'icon' => null,
    'active' => false,
    'badge' => null,
])

<li class="relative">
    <a
        href="{{ $href }}"
        title="{{ $slot }}"
        {{ $attributes->merge(['class' => 'group/menu-item flex w-full items-center gap-3 rounded-sm px-3 py-2 text-body-sm transition-all duration-200 outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50 '
            . ($active
                ? 'bg-canvas-soft text-ink font-medium before:absolute before:inset-y-1 before:left-0 before:w-1 before:rounded-r-sm before:bg-primary'
                : 'text-body hover:bg-canvas-soft hover:text-ink')]) }}
    >
        @if($icon)
            <x-dynamic-component :component="'lucide-' . $icon" class="size-4 shrink-0" />
        @endif
        <span class="truncate">{{ $slot }}</span>

        @if($badge !== null)
            <span class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-sm bg-canvas px-1.5 text-[10px] font-semibold text-ink border border-hairline tabular-nums group-hover/menu-item:text-ink">
                {{ $badge }}
            </span>
        @endif
    </a>
</li>
