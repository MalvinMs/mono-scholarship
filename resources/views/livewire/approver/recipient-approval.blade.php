<div>
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Penetapan Penerima</h1>
        <p class="mt-2 text-body-sm text-mute">Finalisasi hasil seleksi dan pengumuman penerima beasiswa.</p>
    </div>

    <div class="mb-6 max-w-xs">
        <x-ui.select wire:model.live="scholarshipId">
            <option value="">Pilih Program</option>
            @foreach($scholarships as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </x-ui.select>
    </div>

    @if(!empty($summary))
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 animate-scale-in">
            <x-ui.card>
                <div class="text-center">
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Lolos Utama</p>
                    <p class="text-display-md font-bold mt-2 text-success">{{ $summary['total_utama'] }}</p>
                    <p class="text-caption text-mute mt-1">dari {{ $summary['quota_primary'] }} kuota</p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-center">
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Cadangan</p>
                    <p class="text-display-md font-bold mt-2 text-warning">{{ $summary['total_cadangan'] }}</p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-center">
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Tidak Lolos</p>
                    <p class="text-display-md font-bold mt-2 text-mute">{{ $summary['total_tidak_lolos'] }}</p>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card class="mb-6 animate-scale-in">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-body-sm-strong text-ink">{{ $summary['name'] }}</h3>
                    <p class="text-body-sm text-mute mt-1">
                        Kuota utama: {{ $summary['quota_primary'] }}
                    </p>
                </div>
                <div>
                    @if($summary['is_announced'])
                        <x-ui.badge variant="success">Sudah Diumumkan</x-ui.badge>
                    @else
                        <x-ui.badge variant="warning">Menunggu Penetapan</x-ui.badge>
                    @endif
                </div>
            </div>
        </x-ui.card>

        @if(!$summary['is_announced'] && $summary['status'] === 'selecting')
            <x-ui.button variant="primary" wire:click="confirmApproval" class="px-6 py-2.5">
                <x-lucide-check-circle class="size-4" />
                Setujui Penetapan Penerima
            </x-ui.button>
        @endif
    @else
        <x-ui.card class="animate-scale-in">
            <x-ui.empty-state icon="clipboard-check" title="Pilih Program" description="Pilih program yang sudah melalui batch scoring untuk penetapan." />
        </x-ui.card>
    @endif

    @if($showConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-ink/40 backdrop-blur-sm" wire:click="cancelApproval"></div>
            <div class="relative bg-canvas border border-hairline rounded-md shadow-level-4 w-full max-w-md mx-4 p-6 animate-scale-in">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-success/10 border border-success/20">
                        <x-lucide-check-circle class="size-6 text-success" />
                    </div>
                    <div>
                        <h3 class="text-display-xs font-semibold text-ink">Konfirmasi Penetapan</h3>
                        <p class="text-body-sm text-mute mt-1">Setelah disetujui, hasil seleksi akan dikunci dan tidak dapat diubah.</p>
                    </div>
                </div>
                <div class="bg-canvas-soft rounded-sm p-4 text-body-sm mb-6 border border-hairline">
                    <p class="text-ink">Program: <span class="font-medium">{{ $summary['name'] }}</span></p>
                    <p class="text-ink mt-2">Penerima Utama: <span class="font-medium text-success">{{ $summary['total_utama'] }} orang</span></p>
                    <p class="text-caption text-error mt-4">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <x-ui.button variant="outline" wire:click="cancelApproval">Batal</x-ui.button>
                    <x-ui.button variant="primary" wire:click="approveRecipients">Setujui & Umumkan</x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
