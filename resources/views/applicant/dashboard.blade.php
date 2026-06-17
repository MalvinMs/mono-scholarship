<x-layouts.app :title="'Dashboard'">
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Dashboard Pendaftar</h1>
        <p class="mt-2 text-body-sm text-mute">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    @php $applications = \App\Models\Application::where('user_id', auth()->id())->with('scholarship','score')->latest()->get(); @endphp

    @if($applications->isEmpty())
        <x-ui.card class="animate-scale-in">
            <x-ui.empty-state icon="file-text" title="Belum Ada Pendaftaran" description="Anda belum mendaftar beasiswa. Lihat program yang tersedia.">
                <x-ui.button variant="primary" href="{{ url('/beasiswa') }}">Lihat Beasiswa</x-ui.button>
            </x-ui.empty-state>
        </x-ui.card>
    @else
        <div class="space-y-4">
            @foreach($applications as $app)
                @php
                    $statusColor = match($app->status) {
                        'verified' => 'border-l-success',
                        'submitted' => 'border-l-primary',
                        'rejected' => 'border-l-destructive',
                        default => 'border-l-mute/30',
                    };
                @endphp
                <x-ui.card class="border-l-2 {{ $statusColor }} transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="truncate text-body-sm-strong text-ink">{{ $app->scholarship->name }}</h3>
                            <p class="mt-1 text-caption text-mute">No. Registrasi: {{ $app->registration_number ?: '(draft)' }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <x-ui.badge :variant="match($app->status) {
                                'draft' => 'secondary',
                                'submitted' => 'default',
                                'verified' => 'success',
                                'rejected' => 'destructive',
                                default => 'warning'
                            }">{{ ucfirst($app->status) }}</x-ui.badge>
                            @if($app->status !== 'draft')
                                <x-ui.button size="sm" variant="outline" href="{{ route('application.status', $app->id) }}">Detail</x-ui.button>
                            @endif
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    @endif
</x-layouts.app>
