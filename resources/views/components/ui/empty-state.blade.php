@props(['icon' => 'inbox', 'title' => 'Tidak ada data', 'description' => null])

<div class="flex flex-col items-center justify-center py-12 text-center">
    <div class="flex size-12 items-center justify-center rounded-full bg-canvas-soft border border-hairline mb-4">
        <x-lucide-{{ $icon }} class="size-6 text-mute" />
    </div>
    <h3 class="text-body-sm-strong text-ink">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-body-sm text-mute max-w-sm">{{ $description }}</p>
    @endif
    @if(isset($slot) && $slot->isNotEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
