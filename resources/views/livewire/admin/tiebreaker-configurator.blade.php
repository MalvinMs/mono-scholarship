<div>
    <a href="{{ url('/admin/beasiswa') }}" class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink transition-colors mb-4">
        <x-lucide-arrow-left class="size-4" />Kembali
    </a>
    <div class="mb-6">
        <h1 class="text-display-xs text-ink">{{ $scholarship->name }}</h1>
        <p class="mt-2 text-body-sm text-mute">Konfigurasi Tie-Breaker</p>
    </div>

    <x-ui.card>
        <h3 class="text-body-sm-strong text-ink mb-4">Daftar Kualifikasi</h3>
        <div class="space-y-3">
            @forelse($scholarship->qualifications as $q)
                <div class="flex items-center justify-between rounded-sm bg-canvas-soft border border-hairline p-3 text-body-sm">
                    <div class="flex items-center gap-3">
                        <x-lucide-grip-vertical class="size-4 text-mute cursor-grab" />
                        <span class="text-ink font-medium">{{ $q->name }}</span>
                    </div>
                    <x-ui.badge variant="secondary" class="text-caption">{{ $q->type }}</x-ui.badge>
                </div>
            @empty
                <x-ui.empty-state icon="list" title="Belum ada kualifikasi" description="Tambahkan kualifikasi melalui Qualification Builder." />
            @endforelse
        </div>
    </x-ui.card>
</div>
