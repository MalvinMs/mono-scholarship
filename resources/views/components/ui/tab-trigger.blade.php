@props(['value' => '', 'activeTab' => ''])

<button
    type="button"
    role="tab"
    x-on:click="$dispatch('tab-change', { tab: '{{ $value }}' })"
    x-bind:aria-selected="activeTab === '{{ $value }}'"
    x-bind:class="activeTab === '{{ $value }}'
        ? 'bg-canvas text-ink shadow-level-1'
        : 'text-body hover:bg-canvas-soft hover:text-ink'"
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center whitespace-nowrap rounded-pill-sm px-4 h-8 text-body-sm transition-all outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50']) }}
>
    {{ $slot }}
</button>
