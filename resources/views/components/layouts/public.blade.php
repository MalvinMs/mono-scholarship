<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }} — Beasiswa</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-layouts.theme-script />
</head>
<body class="min-h-screen bg-canvas-soft text-ink antialiased" x-data="{ navOpen: false }">
    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-hairline bg-canvas">
        <div class="mx-auto flex h-16 max-w-[1400px] items-center justify-between px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold text-ink">
                <img src="{{ asset('favicon.png') }}" alt="Logo" class="size-6 rounded-sm object-cover" />
                <span class="text-button-md">Platform Beasiswa</span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ url('/') }}" class="text-body-sm text-body hover:bg-canvas-soft hover:text-ink px-3 py-1.5 rounded-full transition-colors">Home</a>
                <a href="{{ route('announcement.list') }}" class="text-body-sm text-body hover:bg-canvas-soft hover:text-ink px-3 py-1.5 rounded-full transition-colors">Pengumuman</a>
                <a href="{{ route('program.list') }}" class="text-body-sm text-body hover:bg-canvas-soft hover:text-ink px-3 py-1.5 rounded-full transition-colors">Program</a>
            </nav>

            {{-- Desktop Auth Buttons --}}
            <div class="hidden md:flex items-center gap-3">
                <x-ui.theme-toggle />
                @guest
                    <x-ui.button variant="secondary" size="sm" href="{{ url('/login') }}">Log In</x-ui.button>
                    <x-ui.button variant="primary" size="sm" href="{{ url('/daftar') }}">Sign Up</x-ui.button>
                @else
                    @php
                        $user = auth()->user();
                        $navDashboardUrl = url('/dashboard');
                        if ($user->hasAnyRole(['super-admin', 'admin'])) {
                            $navDashboardUrl = url('/admin/dashboard');
                        } elseif ($user->hasRole('verifier')) {
                            $navDashboardUrl = url('/verifikasi');
                        } elseif ($user->hasRole('approver')) {
                            $navDashboardUrl = url('/approver/dashboard');
                        } elseif ($user->hasRole('treasurer')) {
                            $navDashboardUrl = url('/keuangan/pencairan');
                        }
                    @endphp
                    <x-ui.button variant="secondary" size="sm" href="{{ $navDashboardUrl }}">Dashboard</x-ui.button>
                    <form method="POST" action="{{ url('/logout') }}" class="inline-block">
                        @csrf
                        <x-ui.button variant="ghost" size="sm" type="submit">Logout</x-ui.button>
                    </form>
                @endguest
            </div>

            {{-- Mobile Actions --}}
            <div class="md:hidden flex items-center gap-2">
                <x-ui.theme-toggle />
                <button @click="navOpen = !navOpen" class="flex items-center justify-center size-10 text-body hover:bg-canvas-soft rounded-lg">
                    <x-lucide-menu class="size-5" />
                </button>
            </div>
        </div>

        {{-- Mobile Nav Drawer --}}
        <div x-show="navOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.away="navOpen = false"
             class="md:hidden border-t border-hairline bg-canvas px-6 py-4 space-y-3">
            <a href="{{ url('/') }}" @click="navOpen = false" class="block text-body-sm text-body hover:text-ink py-2">Home</a>
            <a href="{{ route('announcement.list') }}" @click="navOpen = false" class="block text-body-sm text-body hover:text-ink py-2">Pengumuman</a>
            <a href="{{ route('program.list') }}" @click="navOpen = false" class="block text-body-sm text-body hover:text-ink py-2">Program</a>
            <div class="pt-3 border-t border-hairline flex flex-col gap-2">
                @guest
                    <x-ui.button variant="secondary" size="sm" href="{{ url('/login') }}" class="w-full justify-center">Log In</x-ui.button>
                    <x-ui.button variant="primary" size="sm" href="{{ url('/daftar') }}" class="w-full justify-center">Sign Up</x-ui.button>
                @else
                    <x-ui.button variant="secondary" size="sm" href="{{ $navDashboardUrl }}" class="w-full justify-center">Dashboard</x-ui.button>
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <x-ui.button variant="ghost" size="sm" type="submit" class="w-full justify-center">Logout</x-ui.button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-[1400px]">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-canvas border-t border-hairline py-12 mt-20">
        <div class="max-w-[1400px] mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Brand --}}
                <div>
                    <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold text-ink mb-3">
                        <img src="{{ asset('favicon.png') }}" alt="Logo" class="size-6 rounded-sm object-cover" />
                        <span class="text-button-md">Platform Beasiswa</span>
                    </a>
                    <p class="text-body-sm text-body mb-4">
                        Sistem manajemen beasiswa multi-program untuk institusi pendidikan dan pemerintah.
                    </p>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-caption-mono text-mute mb-3">Navigasi</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/') }}" class="text-body-sm text-body hover:text-ink transition-colors">Home</a></li>
                        <li><a href="{{ route('announcement.list') }}" class="text-body-sm text-body hover:text-ink transition-colors">Pengumuman</a></li>
                        <li><a href="{{ route('program.list') }}" class="text-body-sm text-body hover:text-ink transition-colors">Program</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="text-caption-mono text-mute mb-3">Kontak</h4>
                    <ul class="space-y-2">
                        <li class="text-body-sm text-body">Email: info@beasiswa.go.id</li>
                        <li class="text-body-sm text-body">Telepon: (0351) 123456</li>
                    </ul>
                </div>
            </div>

            {{-- Copyright --}}
            <div class="mt-8 pt-8 border-t border-hairline text-center">
                <p class="text-caption text-mute">
                    © {{ date('Y') }} Platform Beasiswa. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
