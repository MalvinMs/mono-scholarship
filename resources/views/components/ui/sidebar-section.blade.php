@props(['title' => null])

<div class="mb-4">
    @if($title)
        <h3 class="mb-2 px-3 text-caption font-medium uppercase tracking-wider text-mute">{{ $title }}</h3>
    @endif
    <x-ui.sidebar>
        {{ $slot }}
    </x-ui.sidebar>
</div>
