<x-layouts.public :title="'Pengumuman — ' . $scholarship->name">
    <div class="max-w-3xl mx-auto py-12">
        <div class="text-center mb-12">
            <x-lucide-graduation-cap class="mx-auto size-10 text-primary" />
            <h1 class="mt-6 text-display-sm text-ink">{{ $scholarship->name }}</h1>
            <p class="mt-2 text-body-lg text-mute">Pengumuman Hasil Seleksi</p>
        </div>

        {{-- Check by Registration Number --}}
        <x-ui.card class="mb-8">
            <h3 class="text-body-sm-strong text-ink mb-4">Cek Hasil Seleksi</h3>
            <form method="GET" action="{{ url('/pengumuman/' . $scholarship->slug . '/cek') }}" class="flex gap-3">
                <input type="text" name="registration_number" required placeholder="Masukkan No. Registrasi" class="flex-1 rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <x-ui.button variant="primary" type="submit">Cek</x-ui.button>
            </form>
            @if(session('error'))
                <p class="text-caption text-error mt-3">{{ session('error') }}</p>
            @endif
        </x-ui.card>

        {{-- Results Table --}}
        @if($results->isNotEmpty())
            <x-ui.card padding="none">
                <div class="px-6 py-4 border-b border-hairline bg-canvas-soft">
                    <h3 class="text-body-sm-strong text-ink">Daftar Penerima</h3>
                </div>
                <div class="overflow-x-auto">
                    <x-ui.table>
                        <x-slot:header>
                            <x-ui.table.tr>
                                <x-ui.table.th class="w-16">Rank</x-ui.table.th>
                                <x-ui.table.th>Nama</x-ui.table.th>
                                <x-ui.table.th>No. Registrasi</x-ui.table.th>
                                <x-ui.table.th>Hasil</x-ui.table.th>
                            </x-ui.table.tr>
                        </x-slot>
                        @foreach($results as $result)
                            <x-ui.table.tr class="hover:bg-canvas-soft transition-colors">
                                <x-ui.table.td class="font-mono text-caption text-mute">{{ $result['rank'] }}</x-ui.table.td>
                                <x-ui.table.td class="text-body-sm font-medium text-ink">{{ $result['name'] }}</x-ui.table.td>
                                <x-ui.table.td class="font-mono text-caption text-mute">{{ $result['registration_number'] }}</x-ui.table.td>
                                <x-ui.table.td>
                                    <x-ui.badge :variant="match($result['selection_result']) {
                                        'utama' => 'success',
                                        'cadangan' => 'warning',
                                        default => 'destructive'
                                    }">{{ str_replace('_', ' ', $result['selection_result']) }}</x-ui.badge>
                                </x-ui.table.td>
                            </x-ui.table.tr>
                        @endforeach
                    </x-ui.table>
                </div>
            </x-ui.card>
        @endif
    </div>
</x-layouts.public>
