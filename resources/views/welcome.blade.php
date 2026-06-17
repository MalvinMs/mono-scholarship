<x-layouts.public :title="'Home'">
    <div class="text-center py-20 lg:py-32 px-6">
        <x-lucide-graduation-cap class="mx-auto size-16 text-primary" />
        <h1 class="mt-8 text-display-lg text-ink">Platform Beasiswa</h1>
        <p class="mt-6 text-body-lg text-mute max-w-[800px] mx-auto">Sistem Manajemen Beasiswa Multi-Program — satu platform, banyak program, tanpa coding.</p>
        @guest
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <x-ui.button variant="secondary" size="pill" href="{{ url('/login') }}">Log In</x-ui.button>
                <x-ui.button variant="primary" size="pill" href="{{ url('/daftar') }}">Daftar Sekarang</x-ui.button>
            </div>
        @else
            @php
                $user = auth()->user();
                if ($user->hasAnyRole(['super-admin', 'admin'])) {
                    $dashboardUrl = url('/admin/dashboard');
                } elseif ($user->hasRole('verifier')) {
                    $dashboardUrl = url('/verifikasi');
                } elseif ($user->hasRole('approver')) {
                    $dashboardUrl = url('/approver/dashboard');
                } elseif ($user->hasRole('treasurer')) {
                    $dashboardUrl = url('/keuangan/pencairan');
                } else {
                    $dashboardUrl = url('/dashboard');
                }
            @endphp
            <div class="mt-10">
                <x-ui.button variant="primary" size="pill" href="{{ $dashboardUrl }}">Buka Dashboard</x-ui.button>
            </div>
        @endguest
    </div>

    <div id="program" class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-[1200px] mx-auto px-6 pb-32">
        <x-ui.card variant="large" padding="lg">
            <x-lucide-layers class="size-8 text-primary mb-6" />
            <h3 class="text-display-xs text-ink">Multi-Program</h3>
            <p class="mt-3 text-body-sm text-mute">Kelola banyak program beasiswa dalam satu platform yang terpusat.</p>
        </x-ui.card>
        <x-ui.card variant="large" padding="lg">
            <x-lucide-zap class="size-8 text-primary mb-6" />
            <h3 class="text-display-xs text-ink">Dynamic Scoring</h3>
            <p class="mt-3 text-body-sm text-mute">Formula skor otomatis tanpa coding, mendukung pembobotan dinamis.</p>
        </x-ui.card>
        <x-ui.card variant="large" padding="lg">
            <x-lucide-shield-check class="size-8 text-primary mb-6" />
            <h3 class="text-display-xs text-ink">Akuntabilitas Penuh</h3>
            <p class="mt-3 text-body-sm text-mute">Audit trail immutable untuk setiap tindakan dan proses persetujuan.</p>
        </x-ui.card>
    </div>
</x-layouts.public>
