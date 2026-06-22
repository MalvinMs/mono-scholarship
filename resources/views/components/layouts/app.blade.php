<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($title ?? '') . ' — ' . config('app.name') }} — Beasiswa</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-layouts.theme-script />
</head>
<body class="h-full bg-canvas-soft text-ink antialiased">
    <div class="flex h-full" x-data="{ mobileOpen: false }">
        {{-- Mobile drawer --}}
        <x-ui.drawer side="left" x-model="mobileOpen" title="Navigasi">
            <x-layouts.sidebar :mobile="true" />
        </x-ui.drawer>

        {{-- Desktop sidebar --}}
        <aside class="hidden lg:flex lg:shrink-0">
            <div class="flex w-[var(--sidebar-width)] flex-col border-r border-hairline bg-canvas">
                <x-layouts.sidebar />
            </div>
        </aside>

        {{-- Main content area --}}
        <div class="flex flex-1 flex-col overflow-hidden bg-canvas-soft">
            {{-- Mobile header --}}
            <header class="flex h-14 shrink-0 items-center gap-4 border-b border-hairline bg-canvas px-4 lg:hidden">
                <x-ui.button variant="ghost" size="icon" x-on:click="mobileOpen = true">
                    <x-lucide-menu class="size-5" />
                    <span class="sr-only">Buka menu</span>
                </x-ui.button>
                <span class="text-button-md tracking-tight">Platform Beasiswa</span>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto">
                <div class="mx-auto max-w-[1400px] p-6 lg:p-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
