<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }} — Beasiswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-canvas-soft text-ink antialiased">
    <header class="sticky top-0 z-40 border-b border-hairline bg-canvas">
        <div class="mx-auto flex h-16 max-w-[1400px] items-center justify-between px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold text-ink">
                <x-lucide-graduation-cap class="size-5" />
                <span class="text-button-md">Platform Beasiswa</span>
            </a>

            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ url('/') }}" class="text-body-sm text-body hover:bg-canvas-soft hover:text-ink px-3 py-1.5 rounded-full transition-colors">Home</a>
                <a href="#program" class="text-body-sm text-body hover:bg-canvas-soft hover:text-ink px-3 py-1.5 rounded-full transition-colors">Program</a>
            </nav>

            <div class="flex items-center gap-3">
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
        </div>
    </header>

    <main class="mx-auto max-w-[1400px]">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
