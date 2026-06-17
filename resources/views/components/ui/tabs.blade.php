@props(['defaultValue' => ''])

<div
    x-data="{ activeTab: '{{ $defaultValue }}' }"
    x-on:tab-change.window="if ($event.detail.tab) activeTab = $event.detail.tab"
    {{ $attributes->merge(['class' => 'w-full']) }}
>
    <div role="tablist" class="inline-flex items-center gap-2">
        {{ $triggers ?? '' }}
    </div>
    <div class="mt-6">
        {{ $slot }}
    </div>
</div>
