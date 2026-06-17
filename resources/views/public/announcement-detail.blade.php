<x-layouts.public :title="'Hasil — ' . $application->user?->name">
    <div class="max-w-lg mx-auto py-16">
        <div class="text-center mb-8">
            <x-lucide-graduation-cap class="mx-auto size-10 text-primary" />
            <h1 class="mt-6 text-display-sm text-ink">Hasil Seleksi</h1>
            <p class="mt-2 text-body-lg text-mute">{{ $scholarship->name }}</p>
        </div>

        <x-ui.card>
            <div class="text-center mb-6">
                <p class="text-caption font-medium text-mute uppercase tracking-wider">Nama</p>
                <p class="text-body-lg-strong text-ink mt-1">{{ $application->user?->name }}</p>
            </div>
            <div class="text-center mb-6">
                <p class="text-caption font-medium text-mute uppercase tracking-wider">No. Registrasi</p>
                <p class="text-body-lg-strong font-mono text-ink mt-1">{{ $application->registration_number }}</p>
            </div>

            @if($score)
                <div class="text-center mb-6">
                    @php
                        $variant = match($score->selection_result) {
                            'utama' => 'success',
                            'cadangan' => 'warning',
                            default => 'destructive'
                        };
                        $label = match($score->selection_result) {
                            'utama' => 'Selamat! Anda Lolos (Utama)',
                            'cadangan' => 'Anda Lolos (Cadangan)',
                            default => 'Anda Tidak Lolos'
                        };
                    @endphp
                    <x-ui.badge variant="{{ $variant }}" class="text-body-sm font-medium px-4 py-2">{{ $label }}</x-ui.badge>
                </div>

                @if($score->selection_result === 'utama')
                    <div class="bg-success/5 rounded-sm p-4 text-body-sm border border-success/20">
                        <p class="font-medium text-success">Langkah Selanjutnya:</p>
                        <p class="mt-2 text-mute">Silakan login ke akun Anda dan lengkapi data rekening bank untuk pencairan dana beasiswa.</p>
                    </div>
                @endif
            @else
                <x-ui.empty-state icon="search" title="Belum ada hasil" description="Hasil seleksi belum tersedia. Silakan cek kembali nanti." />
            @endif
        </x-ui.card>
    </div>
</x-layouts.public>
