<div>
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Dashboard Approver</h1>
        <p class="mt-2 text-body-sm text-mute">Ringkasan eksekutif program beasiswa.</p>
    </div>

    {{-- Top Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <x-ui.card class="group transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-success/10 border border-success/20">
                    <x-lucide-graduation-cap class="size-5 text-success" />
                </div>
                <div>
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Program Aktif</p>
                    <p class="text-display-sm font-semibold text-success mt-1">{{ $activeProgramsCount }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="group transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top" style="animation-delay: 0.05s">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-warning/10 border border-warning/20">
                    <x-lucide-clock class="size-5 text-warning" />
                </div>
                <div>
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Menunggu Approval</p>
                    <p class="text-display-sm font-semibold text-warning mt-1">{{ $awaitingApprovalCount }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="group transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top" style="animation-delay: 0.1s">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-primary/10 border border-primary/20">
                    <x-lucide-users class="size-5 text-primary" />
                </div>
                <div>
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Total Penerima</p>
                    <p class="text-display-sm font-semibold text-ink mt-1">{{ $totalRecipients }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="group transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top" style="animation-delay: 0.15s">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-primary/10 border border-primary/20">
                    <x-lucide-banknote class="size-5 text-primary" />
                </div>
                <div>
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Anggaran Terserap</p>
                    <p class="text-display-sm font-semibold text-ink mt-1">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Programs Needing Approval --}}
    @if($selectingScholarships->isNotEmpty())
        <x-ui.card class="mb-8 animate-scale-in">
            <h3 class="text-body-sm-strong text-ink mb-4">Program Menunggu Penetapan</h3>
            <div class="space-y-3">
                @foreach($selectingScholarships as $scholarship)
                    <div class="flex items-center justify-between rounded-sm border border-warning/20 bg-warning/5 px-4 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-body-sm-strong text-ink">{{ $scholarship->name }}</p>
                            <p class="text-caption text-mute mt-1">{{ $scholarship->academic_year }} · {{ $scholarship->applications_count }} pendaftar</p>
                        </div>
                        <a href="{{ url('/approver/penetapan?scholarship=' . $scholarship->id) }}" wire:navigate class="ml-4 inline-flex items-center gap-1.5 text-body-sm font-medium text-primary hover:text-primary-hover transition-colors">
                            <x-lucide-arrow-right class="size-4" />
                            Proses
                        </a>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    @endif

    {{-- Yearly Trend --}}
    @if(!empty($yearlyTrend['labels']))
        <x-ui.card class="mb-8 animate-scale-in">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-hairline">
                <div>
                    <h3 class="text-body-sm-strong text-ink">Tren Pendaftar per Tahun</h3>
                    <p class="text-caption text-mute mt-1">Jumlah pendaftar yang submit per tahun.</p>
                </div>
                <div class="p-2 rounded-sm bg-canvas-soft border border-hairline">
                    <x-lucide-trending-up class="size-4 text-mute" />
                </div>
            </div>
            <x-ui.chart type="line"
                :labels="$yearlyTrend['labels']"
                :datasets="$yearlyTrend['datasets']"
                :options="[
                    'scales' => [
                        'x' => ['grid' => ['display' => false]],
                        'y' => ['beginAtZero' => true, 'grid' => ['color' => '#ebebeb'], 'ticks' => ['precision' => 0]],
                    ],
                ]"
                height="220px" />
        </x-ui.card>
    @endif

    {{-- All Programs --}}
    <x-ui.card class="animate-scale-in">
        <h3 class="text-body-sm-strong text-ink mb-4">Semua Program</h3>
        @if($scholarships->isNotEmpty())
            <div class="space-y-1">
                @foreach($scholarships as $s)
                    <div class="flex items-center justify-between py-3 px-3 rounded-sm hover:bg-canvas-soft transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="text-body-sm font-medium text-ink truncate">{{ $s->name }}</p>
                            <p class="text-caption text-mute mt-1">{{ $s->academic_year }} · Kuota {{ $s->quota_primary }} · {{ $s->applications_count }} pendaftar</p>
                        </div>
                        <div class="ml-4 shrink-0">
                            <x-ui.badge :variant="match($s->status) {
                                'open' => 'success',
                                'closed' => 'secondary',
                                'selecting' => 'warning',
                                'announced' => 'success',
                                default => 'secondary'
                            }">
                                {{ match($s->status) {
                                    'draft' => 'Draft',
                                    'open' => 'Buka',
                                    'closed' => 'Tutup',
                                    'selecting' => 'Seleksi',
                                    'announced' => 'Diumumkan',
                                    default => $s->status
                                } }}
                            </x-ui.badge>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-ui.empty-state icon="graduation-cap" title="Belum ada program" description="Belum ada program beasiswa." />
        @endif
    </x-ui.card>
</div>
