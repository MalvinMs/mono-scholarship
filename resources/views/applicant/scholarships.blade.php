<x-layouts.app :title="'Daftar Beasiswa'">
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Daftar Beasiswa</h1>
        <p class="mt-2 text-body-sm text-mute">Program beasiswa yang sedang terbuka.</p>
    </div>

    @php $scholarships = \App\Models\Scholarship::where('status', 'open')->latest()->get(); @endphp

    @if($scholarships->isEmpty())
        <x-ui.card class="animate-scale-in">
            <x-ui.empty-state icon="search" title="Belum Tersedia" description="Belum ada program beasiswa yang dibuka." />
        </x-ui.card>
    @else
        <div class="space-y-4">
            @foreach($scholarships as $scholarship)
                <x-ui.card class="group border-l-2 border-l-primary/50 transition-shadow duration-200 hover:shadow-level-2 hover:border-l-primary animate-slide-in-from-top" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 space-y-2">
                            <h3 class="text-body-sm-strong text-ink">{{ $scholarship->name }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-caption text-mute">
                                <span class="inline-flex items-center gap-1.5 font-medium">
                                    <x-lucide-calendar class="size-3.5 text-mute" />
                                    {{ $scholarship->academic_year }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 font-medium">
                                    <x-lucide-users class="size-3.5 text-mute" />
                                    Kuota: {{ $scholarship->quota_primary }}
                                </span>
                            </div>
                            <p class="text-body-sm text-mute line-clamp-2 mt-2">{{ Str::limit($scholarship->description, 160) }}</p>
                        </div>
                        <div class="shrink-0 mt-1">
                            <x-ui.button variant="primary" size="sm" href="{{ route('application.form', $scholarship->slug) }}">
                                <x-lucide-file-plus class="size-3.5" />
                                Daftar
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    @endif
</x-layouts.app>
