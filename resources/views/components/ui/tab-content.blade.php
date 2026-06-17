@props(['value' => ''])

<div
    role="tabpanel"
    x-show="activeTab === '{{ $value }}'"
    x-transition:enter="transition ease-out duration-100"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    {{ $attributes }}
>
    {{ $slot }}
</div>
