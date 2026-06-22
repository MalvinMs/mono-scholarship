<div>
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Jalankan Seleksi Batch</h1>
        <p class="mt-2 text-body-sm text-mute">Pilih program untuk menjalankan proses scoring dan ranking otomatis.</p>
    </div>

    <div class="mb-6 max-w-xs">
        <x-ui.select wire:model.live="scholarshipId" wire:change="loadSummary">
            <option value="">Pilih Program</option>
            @foreach($scholarships as $s)
                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->status }})</option>
            @endforeach
        </x-ui.select>
    </div>

    @if(!empty($renewalSummary))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 animate-scale-in">
            <x-ui.card>
                <div class="text-center">
                    <p class="text-caption text-mute uppercase tracking-wider">Kuota Utama</p>
                    <p class="text-display-sm text-ink mt-1">{{ $renewalSummary['quota_primary'] }}</p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-center">
                    <p class="text-caption text-mute uppercase tracking-wider">Slot Renewal</p>
                    <p class="text-display-sm text-warning mt-1">{{ $renewalSummary['eligible_for_renewal'] }}</p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-center">
                    <p class="text-caption text-mute uppercase tracking-wider">Sisa Kuota Baru</p>
                    <p class="text-display-sm text-success mt-1">{{ $renewalSummary['remaining_for_new'] }}</p>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-center">
                    <p class="text-caption text-mute uppercase tracking-wider">Pendaftar Terverifikasi</p>
                    <p class="text-display-sm text-ink mt-1">{{ $renewalSummary['verified_count'] }}</p>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card class="mb-6 animate-scale-in">
            <h3 class="text-body-sm-strong text-ink mb-4">Ringkasan Renewal</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-body-sm">
                <div>
                    <span class="text-mute">Penerima Aktif Predecessor:</span>
                    <span class="font-medium text-ink ml-2">{{ $renewalSummary['total_active_recipients'] }}</span>
                </div>
                <div>
                    <span class="text-mute">Diajukan Renewal:</span>
                    <span class="font-medium text-ink ml-2">{{ $renewalSummary['total_submitted_renewal'] }}</span>
                </div>
            </div>
        </x-ui.card>

        <div class="flex items-center gap-3">
            @if(in_array($renewalSummary['status'], ['closed', 'renewal_closed']))
                <x-ui.button variant="primary" wire:click="confirmRun" :disabled="$jobDispatched">
                    <x-lucide-play class="size-4" />
                    {{ $jobDispatched ? 'Job Telah Dijalankan...' : 'Jalankan Batch Scoring' }}
                </x-ui.button>
            @elseif($batchCompleted)
                <x-ui.badge variant="success">Seleksi selesai — ranking telah dihasilkan</x-ui.badge>
            @else
                <x-ui.badge variant="secondary">Program berstatus {{ $renewalSummary['status'] }} — belum bisa dijalankan</x-ui.badge>
            @endif

            @if($batchCompleted)
                <a href="{{ url('/admin/seleksi?scholarship=' . $scholarshipId) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-body-sm font-medium text-primary hover:text-primary-hover transition-colors">
                    <x-lucide-arrow-right class="size-4" />
                    Lihat Hasil Seleksi
                </a>
            @endif
        </div>

        @if($polling)
            <div class="mt-6 animate-scale-in" wire:poll.2s="checkProgress">
                <x-ui.card>
                    @if(!empty($progress))
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-primary/10 border border-primary/20">
                            <x-lucide-loader-circle class="size-5 text-primary animate-spin" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-body-sm font-medium text-ink">{{ $progress['label'] }}</p>
                            <div class="mt-2 h-1.5 w-full rounded-full bg-canvas-soft-2 overflow-hidden">
                                <div class="h-full rounded-full bg-primary animate-pulse transition-all duration-700" style="width: {{ match($progress['stage']) { 'preparing' => '10%', 'processing_renewal' => '30%', 'ranking' => '50%', 'applying_tiebreaker' => '70%', 'persisting' => '90%', 'completed' => '100%', default => '5%' } }}"></div>
                            </div>
                            <p class="text-caption text-mute mt-1">Tahap: {{ str_replace('_', ' ', $progress['stage']) }}</p>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center size-10 shrink-0 rounded-full bg-primary/10 border border-primary/20">
                            <x-lucide-loader-circle class="size-5 text-primary animate-spin" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-body-sm font-medium text-ink">Menunggu job antrian dimulai...</p>
                            <div class="mt-2 h-1.5 w-full rounded-full bg-canvas-soft-2 overflow-hidden">
                                <div class="h-full rounded-full bg-primary animate-pulse transition-all duration-700" style="width: 5%"></div>
                            </div>
                            <p class="text-caption text-mute mt-1">Job telah dikirim ke queue. Pastikan queue worker berjalan.</p>
                        </div>
                    </div>
                    @endif
                </x-ui.card>
            </div>
        @endif
    @else
        <x-ui.card class="animate-scale-in">
            <x-ui.empty-state icon="layers" title="Pilih Program" description="Pilih program yang sudah ditutup untuk menjalankan seleksi." />
        </x-ui.card>
    @endif

    @if($showConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" wire:click="cancelRun"></div>
            <div class="relative bg-canvas border border-hairline rounded-lg shadow-level-5 w-full max-w-md mx-4 p-6 animate-scale-in">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10">
                        <x-lucide-zap class="size-5 text-primary" />
                    </div>
                    <div>
                        <h3 class="text-display-sm text-ink">Konfirmasi Batch Scoring</h3>
                        <p class="text-body-sm text-mute mt-1">Proses ini akan menghitung ranking dan menentukan penerima.</p>
                    </div>
                </div>
                <div class="bg-canvas-soft border border-hairline rounded-sm p-4 text-body-sm mb-6 space-y-2">
                    <p class="text-mute">Program: <span class="font-medium text-ink">{{ $renewalSummary['name'] }}</span></p>
                    <p class="text-mute">Kuota Utama: <span class="font-medium text-ink">{{ $renewalSummary['quota_primary'] }}</span></p>
                    <p class="text-mute">Slot Renewal: <span class="font-medium text-ink">{{ $renewalSummary['eligible_for_renewal'] }}</span></p>
                    <p class="text-mute">Pendaftar Final: <span class="font-medium text-ink">{{ $renewalSummary['verified_count'] }}</span></p>
                </div>
                <div class="flex justify-end gap-3">
                    <x-ui.button variant="outline" wire:click="cancelRun">Batal</x-ui.button>
                    <x-ui.button variant="primary" wire:click="runBatch">Jalankan Seleksi</x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
