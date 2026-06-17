@props([])

<nav {{ $attributes->merge(['class' => 'flex flex-col gap-1']) }}>
    {{ $slot }}
</nav>
