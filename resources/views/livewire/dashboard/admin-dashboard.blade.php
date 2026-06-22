<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8 animate-fade-in">
        <div>
            <h1 class="text-display-xs text-ink">Dashboard Admin</h1>
            <p class="mt-2 text-body-sm text-mute">Ringkasan aktivitas platform beasiswa.</p>
        </div>
        <div class="max-w-xs">
            <x-ui.select wire:model.live="scholarshipId">
                <option value="">Semua Program</option>
                @foreach($scholarships as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </x-ui.select>
        </div>
    </div>

    {{-- Top Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <x-ui.card class="group transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-primary/10 border border-primary/20 group-hover:bg-primary/15 transition-colors">
                    <x-lucide-users class="size-5 text-primary" />
                </div>
                <div>
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Total Pendaftar</p>
                    <p class="text-display-sm font-semibold text-ink mt-1">{{ $totalApplications }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="group transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top" style="animation-delay: 0.05s">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-success/10 border border-success/20 group-hover:bg-success/15 transition-colors">
                    <x-lucide-check-circle class="size-5 text-success" />
                </div>
                <div>
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Beasiswa Aktif</p>
                    <p class="text-display-sm font-semibold text-success mt-1">{{ $activeScholarships }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="group transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top" style="animation-delay: 0.1s">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-primary/10 border border-primary/20 group-hover:bg-primary/15 transition-colors">
                    <x-lucide-layers class="size-5 text-primary" />
                </div>
                <div>
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Total Program</p>
                    <p class="text-display-sm font-semibold text-ink mt-1">{{ $totalScholarships }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="group transition-shadow duration-200 hover:shadow-level-2 animate-slide-in-from-top" style="animation-delay: 0.15s">
            <div class="flex items-center gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-warning/10 border border-warning/20 group-hover:bg-warning/15 transition-colors">
                    <x-lucide-hourglass class="size-5 text-warning" />
                </div>
                <div>
                    <p class="text-caption font-medium text-mute uppercase tracking-wider">Terverifikasi</p>
                    <p class="text-display-sm font-semibold text-warning mt-1">{{ $verifiedCount }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- The Pulse: Daily Submission Monitoring --}}
    <x-ui.card class="mb-8 animate-scale-in">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-hairline">
            <div>
                <h3 class="text-body-sm-strong text-ink">Tren Aktivitas Pendaftar</h3>
                <p class="text-caption text-mute mt-1">Dinamika jumlah pendaftar harian (The Pulse).</p>
            </div>
            <div class="p-2 rounded-sm bg-canvas-soft border border-hairline">
                <x-lucide-activity class="size-4 text-mute" />
            </div>
        </div>
        @if(!empty($dailySubmissions))
            <x-ui.chart :options="$dailySubmissions" height="260px" wire:key="daily-chart-{{ $scholarshipId ?? 'all' }}" />
        @else
            <div class="py-8">
                <x-ui.empty-state icon="activity" title="Belum ada data" description="Belum ada pendaftar yang mensubmit aplikasinya." />
            </div>
        @endif
    </x-ui.card>

    {{-- Bottom Section: Program & Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6 mb-8">
        {{-- Program Beasiswa --}}
        <x-ui.card class="lg:col-span-4 animate-scale-in flex flex-col" padding="none">
            <div class="p-6 border-b border-hairline flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-body-sm-strong text-ink">Program Terbaru</h3>
                    <p class="text-caption text-mute mt-1">Kinerja pendaftaran per program.</p>
                </div>
                <x-ui.button variant="outline" size="sm" class="shrink-0" href="{{ url('/admin/beasiswa') }}">Lihat Semua</x-ui.button>
            </div>
            <div class="flex-1">
                @if($scholarshipStats->isNotEmpty())
                    <div class="divide-y divide-hairline">
                        @foreach($scholarshipStats->take(5) as $stat)
                            <a href="{{ url('/admin/beasiswa/' . $stat['id'] . '/qualification') }}" wire:navigate class="flex items-center justify-between p-6 hover:bg-canvas-soft transition-colors group">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-sm bg-primary/5 border border-primary/10 group-hover:bg-primary/10 transition-colors">
                                        <x-lucide-graduation-cap class="size-5 text-primary" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-body-sm-strong text-ink truncate">{{ $stat['name'] }}</p>
                                        <div class="flex items-center gap-3 mt-1 text-caption text-mute">
                                            <span class="flex items-center gap-1.5 font-medium text-ink"><x-lucide-users class="size-3.5 text-mute" /> {{ $stat['applications_count'] }} Pendaftar</span>
                                            <span class="flex items-center gap-1.5"><x-lucide-target class="size-3.5 text-mute" /> Kuota {{ $stat['quota'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="shrink-0 ml-4 flex items-center gap-2">
                                    <x-ui.badge :variant="match($stat['status']) {
                                        'open' => 'success',
                                        'closed' => 'secondary',
                                        'selecting' => 'warning',
                                        'announced' => 'default',
                                        default => 'secondary'
                                    }">{{ ucfirst($stat['status']) }}</x-ui.badge>
                                    <x-lucide-chevron-right class="size-4 text-mute/40 group-hover:text-ink transition-colors" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8">
                        <x-ui.empty-state icon="graduation-cap" title="Belum ada program" description="Belum ada program beasiswa yang dibuat." />
                    </div>
                @endif
            </div>
        </x-ui.card>

        {{-- Status Breakdown --}}
        <x-ui.card class="lg:col-span-3 animate-scale-in flex flex-col">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-hairline">
                <div>
                    <h3 class="text-body-sm-strong text-ink">Distribusi Status</h3>
                    <p class="text-caption text-mute mt-1">Proporsi status aplikasi.</p>
                </div>
                <div class="p-2 rounded-sm bg-canvas-soft border border-hairline">
                    <x-lucide-pie-chart class="size-4 text-mute" />
                </div>
            </div>
            <div class="flex-1 flex flex-col justify-center min-h-[300px]">
                @if(!empty($statusDistribution))
                    <div class="flex justify-center mb-6">
                        <x-ui.chart :options="$statusDistribution" height="220px" wire:key="status-chart-{{ $scholarshipId ?? 'all' }}" />
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-3 mt-auto">
                        @php
                            $labelsMap = [
                                'draft' => ['Draft', 'bg-[#666666]'],
                                'submitted' => ['Submit', 'bg-[#171717]'],
                                'under_review' => ['Review', 'bg-[#f5a623]'],
                                'needs_revision' => ['Revisi', 'bg-[#e00]'],
                                'verified' => ['Terverifikasi', 'bg-[#0070f3]'],
                                'selected' => ['Ditetapkan', 'bg-[#0070f3]'],
                                'rejected' => ['Ditolak', 'bg-[#e00]'],
                            ];
                        @endphp
                        @foreach($statusCounts as $key => $count)
                            @if(isset($labelsMap[$key]))
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="size-2 rounded-full {{ $labelsMap[$key][1] }}"></div>
                                        <span class="text-body-sm text-ink truncate">{{ $labelsMap[$key][0] }}</span>
                                    </div>
                                    <span class="text-body-sm-strong text-ink">{{ $count }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state icon="pie-chart" title="Belum ada data" description="Belum ada pendaftar." />
                @endif
            </div>
        </x-ui.card>
    </div>
</div>
