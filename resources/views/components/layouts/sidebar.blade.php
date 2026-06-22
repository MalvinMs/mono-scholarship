@props(['mobile' => false])

@php
$isActive = fn (string $pattern) => request()->is($pattern);
@endphp

<nav class="flex h-full flex-col bg-canvas text-ink" data-sidebar="sidebar">
    {{-- Header --}}
    <div class="flex h-14 shrink-0 items-center gap-2 border-b border-hairline px-6" data-sidebar="header">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-semibold">
            <img src="{{ asset('favicon.png') }}" alt="Logo" class="size-8 rounded-sm object-cover shadow-sm" />
            <span class="text-button-md tracking-tight">Beasiswa</span>
        </a>
    </div>

    {{-- Content --}}
    <div class="flex min-h-0 flex-1 flex-col gap-2 overflow-y-auto py-4" data-sidebar="content">
        {{-- Admin & Super Admin --}}
        @hasrole('super-admin|admin')
        <x-ui.sidebar-group label="Administrasi">
            <x-ui.sidebar-item href="{{ url('/admin/dashboard') }}" icon="layout-dashboard" :active="$isActive('admin/dashboard')">Dashboard</x-ui.sidebar-item>
            <x-ui.sidebar-item href="{{ url('/admin/beasiswa') }}" icon="graduation-cap" :active="$isActive('admin/beasiswa*')">Program Beasiswa</x-ui.sidebar-item>
            <x-ui.sidebar-item href="{{ url('/admin/pengguna') }}" icon="users" :active="$isActive('admin/pengguna*')">Pengguna</x-ui.sidebar-item>
            <x-ui.sidebar-item href="{{ url('/admin/blacklist') }}" icon="user-x" :active="$isActive('admin/blacklist*')">Blacklist</x-ui.sidebar-item>
        </x-ui.sidebar-group>
        <x-ui.sidebar-group label="Seleksi">
            <x-ui.sidebar-item href="{{ url('/admin/seleksi') }}" icon="list-checks" :active="$isActive('admin/seleksi*') && !$isActive('admin/seleksi/batch*')">Hasil Seleksi</x-ui.sidebar-item>
            <x-ui.sidebar-item href="{{ url('/admin/seleksi/batch') }}" icon="zap" :active="$isActive('admin/seleksi/batch*')">Jalankan Batch</x-ui.sidebar-item>
            <x-ui.sidebar-item href="{{ url('/admin/notifikasi') }}" icon="bell" :active="$isActive('admin/notifikasi*')">Notifikasi</x-ui.sidebar-item>
            <x-ui.sidebar-item href="{{ url('/admin/audit-log') }}" icon="history" :active="$isActive('admin/audit-log*')">Audit Log</x-ui.sidebar-item>
        </x-ui.sidebar-group>
        @endhasrole

        {{-- Verifier --}}
        @hasrole('verifier')
        <x-ui.sidebar-group label="Verifikasi">
            <x-ui.sidebar-item href="{{ url('/verifikasi') }}" icon="clipboard-check" :active="$isActive('verifikasi*')">Antrian Verifikasi</x-ui.sidebar-item>
        </x-ui.sidebar-group>
        @endhasrole

        {{-- Approver --}}
        @hasrole('approver')
        <x-ui.sidebar-group label="Penetapan">
            <x-ui.sidebar-item href="{{ url('/approver/dashboard') }}" icon="pie-chart" :active="$isActive('approver/dashboard')">Dashboard</x-ui.sidebar-item>
            <x-ui.sidebar-item href="{{ url('/approver/penetapan') }}" icon="badge-check" :active="$isActive('approver/penetapan*')">Penetapan Penerima</x-ui.sidebar-item>
        </x-ui.sidebar-group>
        @endhasrole

        {{-- Treasurer --}}
        @hasrole('treasurer')
        <x-ui.sidebar-group label="Keuangan">
            <x-ui.sidebar-item href="{{ url('/keuangan/pencairan') }}" icon="wallet" :active="$isActive('keuangan/*')">Pencairan Dana</x-ui.sidebar-item>
        </x-ui.sidebar-group>
        @endhasrole

        {{-- Applicant --}}
        @hasrole('applicant')
        <x-ui.sidebar-group label="Pendaftaran">
            <x-ui.sidebar-item href="{{ url('/dashboard') }}" icon="layout-dashboard" :active="$isActive('dashboard')">Dashboard Saya</x-ui.sidebar-item>
            <x-ui.sidebar-item href="{{ url('/beasiswa') }}" icon="compass" :active="$isActive('beasiswa*')">Daftar Beasiswa</x-ui.sidebar-item>
        </x-ui.sidebar-group>
        @endhasrole
    </div>

    {{-- Footer --}}
    <div class="mt-auto border-t border-hairline" data-sidebar="footer">
        <div class="flex items-center gap-3 px-5 py-3">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 flex-1 min-w-0 transition-colors hover:opacity-75">
                <div class="flex size-8 shrink-0 items-center justify-center rounded-sm bg-primary text-on-primary text-xs font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-body-sm-strong text-ink">{{ auth()->user()->name }}</p>
                    <p class="truncate text-caption text-mute">{{ auth()->user()->email }}</p>
                </div>
            </a>
            <div class="flex items-center gap-1 shrink-0">
                <x-ui.theme-toggle />
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="flex size-8 items-center justify-center rounded-sm text-mute transition-colors hover:bg-canvas-soft hover:text-ink">
                        <x-lucide-log-out class="size-4" />
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
