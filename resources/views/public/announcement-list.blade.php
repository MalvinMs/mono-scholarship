<x-layouts.public :title="'Pengumuman'">
    <div class="max-w-4xl mx-auto py-12 px-6">
        <div class="text-center mb-12">
            <x-lucide-bell class="mx-auto size-10 text-primary" />
            <h1 class="mt-6 text-display-sm text-ink">Pengumuman Hasil Seleksi</h1>
            <p class="mt-2 text-body-lg text-mute">Daftar beasiswa yang telah mengumumkan hasil seleksi</p>
        </div>

        @if($scholarships->isEmpty())
            <x-ui.card class="text-center py-12">
                <x-lucide-inbox class="mx-auto size-10 text-mute" />
                <p class="mt-4 text-body-sm text-mute">Belum ada pengumuman yang tersedia.</p>
            </x-ui.card>
        @else
            <div class="grid gap-4">
                @foreach($scholarships as $scholarship)
                    <a href="{{ route('announcement', $scholarship->slug) }}"
                       class="group flex items-center justify-between rounded-lg border border-hairline bg-canvas p-5 hover:bg-canvas-soft hover:shadow-sm transition-all">
                        <div>
                            <h3 class="text-body-base-strong text-ink group-hover:text-primary transition-colors">
                                {{ $scholarship->name }}
                            </h3>
                            @if($scholarship->description)
                                <p class="text-body-sm text-mute mt-1">{{ $scholarship->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <time class="text-caption text-mute" datetime="{{ $scholarship->updated_at->toIso8601String() }}">
                                {{ $scholarship->updated_at->isoFormat('D MMMM Y') }}
                            </time>
                            <x-lucide-chevron-right class="size-5 text-mute group-hover:text-primary transition-colors" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.public>
