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
    <div class="flex h-full" x-data="{ mobileOpen: false, desktopCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }" x-init="$watch('desktopCollapsed', val => localStorage.setItem('sidebarCollapsed', val))">
        {{-- Mobile drawer --}}
        <x-ui.drawer side="left" x-model="mobileOpen" title="Navigasi">
            <x-layouts.sidebar :mobile="true" />
        </x-ui.drawer>

        {{-- Desktop sidebar --}}
        <aside class="hidden lg:flex lg:shrink-0 transition-all duration-300 ease-in-out relative z-20 group/sidebar" :class="desktopCollapsed ? 'w-[72px]' : 'w-[260px]'">
            <div class="flex w-full h-full flex-col border-r border-hairline bg-canvas overflow-hidden">
                <x-layouts.sidebar />
            </div>
            {{-- Floating Toggle Button --}}
            <button type="button" @click="desktopCollapsed = !desktopCollapsed" 
                    class="absolute -right-3.5 top-5 z-50 flex size-7 items-center justify-center rounded-full border border-hairline bg-canvas text-mute hover:text-ink shadow-sm transition-all duration-300 opacity-0 group-hover/sidebar:opacity-100 focus:opacity-100" 
                    :class="desktopCollapsed ? 'rotate-180 opacity-100' : ''" title="Toggle Sidebar">
                <x-lucide-chevron-left class="size-4" />
            </button>
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
