<div>
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Hasil Seleksi</h1>
        <p class="mt-2 text-body-sm text-mute">Review hasil ranking pendaftar per program.</p>
    </div>

    <div class="mb-6 max-w-xs">
        <x-ui.select wire:model.live="scholarshipId">
            <option value="">Pilih Program</option>
            @foreach($scholarships as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </x-ui.select>
    </div>

    @if($selectedScholarship)
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <x-ui.badge variant="outline" wire:click="$set('filter', '')" class="cursor-pointer {{ !$filter ? 'bg-canvas-soft-2 text-ink border-hairline ring-[3px] ring-primary/20' : '' }}">
                Semua
            </x-ui.badge>
            <x-ui.badge variant="success" wire:click="$set('filter', 'utama')" class="cursor-pointer {{ $filter === 'utama' ? 'ring-[3px] ring-success/20 ring-offset-1' : '' }}">
                Utama
            </x-ui.badge>
            <x-ui.badge variant="warning" wire:click="$set('filter', 'cadangan')" class="cursor-pointer {{ $filter === 'cadangan' ? 'ring-[3px] ring-warning/20 ring-offset-1' : '' }}">
                Cadangan
            </x-ui.badge>
            <x-ui.badge variant="destructive" wire:click="$set('filter', 'tidak_lolos')" class="cursor-pointer {{ $filter === 'tidak_lolos' ? 'ring-[3px] ring-destructive/20 ring-offset-1' : '' }}">
                Tidak Lolos
            </x-ui.badge>
        </div>

        <x-ui.card padding="none" class="animate-scale-in overflow-hidden">
            <div class="overflow-x-auto">
                <x-ui.table>
                    <x-slot:header>
                        <x-ui.table.tr>
                            <x-ui.table.th class="w-16">Rank</x-ui.table.th>
                            <x-ui.table.th class="w-[160px]">No. Registrasi</x-ui.table.th>
                            <x-ui.table.th>Nama</x-ui.table.th>
                            <x-ui.table.th class="w-24 text-right">Skor</x-ui.table.th>
                            <x-ui.table.th class="w-24 text-right">Maks</x-ui.table.th>
                            <x-ui.table.th class="w-[120px]">Hasil</x-ui.table.th>
                            <x-ui.table.th class="w-12"></x-ui.table.th>
                        </x-ui.table.tr>
                    </x-slot>
                    @forelse($scores as $score)
                        <x-ui.table.tr class="{{ $score->selection_result === 'utama' ? 'bg-success/5' : '' }} cursor-pointer hover:bg-canvas-soft-2 transition-colors" wire:click="showDetail({{ $score->id }})">
                            <x-ui.table.td class="font-mono text-caption text-mute">{{ $score->rank }}</x-ui.table.td>
                            <x-ui.table.td class="font-mono text-caption text-mute">{{ $score->application?->registration_number }}</x-ui.table.td>
                            <x-ui.table.td class="font-medium text-ink">{{ $score->application?->user?->name }}</x-ui.table.td>
                            <x-ui.table.td class="text-right font-medium text-ink">{{ $score->total_score }}</x-ui.table.td>
                            <x-ui.table.td class="text-right text-mute">{{ $score->max_possible_score }}</x-ui.table.td>
                            <x-ui.table.td>
                                <x-ui.badge :variant="match($score->selection_result) {
                                    'utama' => 'success',
                                    'cadangan' => 'warning',
                                    default => 'destructive'
                                }">
                                    {{ str_replace('_', ' ', $score->selection_result) }}
                                </x-ui.badge>
                            </x-ui.table.td>
                            <x-ui.table.td>
                                <x-lucide-chevron-right class="size-4 text-mute" />
                            </x-ui.table.td>
                        </x-ui.table.tr>
                        @if($detailScoreId === $score->id)
                            <x-ui.table.tr>
                                <x-ui.table.td colspan="7" class="bg-canvas-soft">
                                    <div class="py-4 px-2 space-y-5 animate-fade-in">
                                        {{-- Score Breakdown --}}
                                        <div>
                                            <h4 class="text-body-sm-strong text-ink mb-3 flex items-center gap-2">
                                                <x-lucide-bar-chart-3 class="size-4 text-mute" />
                                                Breakdown Skor
                                            </h4>
                                            @php $breakdown = $score->score_breakdown ?? []; @endphp
                                            @if(!empty($breakdown))
                                                <div class="space-y-2">
                                                    @foreach($breakdown as $qId => $item)
                                                        <div class="flex items-center justify-between gap-4 py-2 px-3 bg-canvas rounded-sm border border-hairline">
                                                            <div class="min-w-0 flex-1">
                                                                <p class="text-body-sm font-medium text-ink truncate">{{ $item['name'] ?? 'Qual #'.$qId }}</p>
                                                                <p class="text-caption text-mute truncate">{{ $item['answer_label'] ?? '-' }}</p>
                                                            </div>
                                                            <span class="text-body-sm font-mono font-medium text-ink shrink-0">{{ $item['score'] ?? 0 }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-caption text-mute">Data breakdown tidak tersedia.</p>
                                            @endif
                                        </div>

                                        {{-- Tiebreaker Log --}}
                                        @php $tiebreakerLog = $score->tiebreaker_log ?? []; @endphp
                                        @if(!empty($tiebreakerLog))
                                            <div>
                                                <h4 class="text-body-sm-strong text-ink mb-3 flex items-center gap-2">
                                                    <x-lucide-git-branch class="size-4 text-mute" />
                                                    Log Tie-breaker
                                                </h4>
                                                <div class="space-y-2">
                                                    @foreach($tiebreakerLog as $log)
                                                        <div class="py-2 px-3 bg-canvas rounded-sm border border-hairline text-body-sm space-y-1">
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-mono text-caption text-mute shrink-0">
                                                                    Langkah {{ $log['step'] ?? '?' }}
                                                                </span>
                                                                <span class="text-mute">
                                                                    QualID #{{ $log['qualification_id'] ?? '-' }}
                                                                </span>
                                                                <span class="text-caption text-mute">
                                                                    Skor maks: {{ $log['max_score'] ?? '-' }}
                                                                </span>
                                                            </div>
                                                            <div class="flex flex-wrap gap-4 text-caption">
                                                                <span class="text-success">
                                                                    Pemenang: {{ !empty($log['winners']) ? count($log['winners']) . ' pendaftar' : '-' }}
                                                                </span>
                                                                <span class="text-mute">
                                                                    Kalah: {{ !empty($log['losers']) ? count($log['losers']) . ' pendaftar' : '-' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </x-ui.table.td>
                            </x-ui.table.tr>
                        @endif
                    @empty
                        <x-ui.table.tr>
                            <x-ui.table.td colspan="7">
                                <div class="py-10">
                                    <x-ui.empty-state icon="bar-chart-3" title="Belum ada hasil" description="Jalankan batch scoring terlebih dahulu." />
                                </div>
                            </x-ui.table.td>
                        </x-ui.table.tr>
                    @endforelse
                </x-ui.table>
            </div>
            @if($scores->hasPages())
                <div class="border-t border-hairline px-4 py-3">
                    {{ $scores->links() }}
                </div>
            @endif
        </x-ui.card>

        @if($canApprove)
            <div class="mt-6 animate-fade-in">
                <x-ui.button variant="primary" href="{{ url('/approver/penetapan?scholarship=' . $scholarshipId) }}" wire:navigate>
                    <x-lucide-check-circle class="size-4" />
                    Finalisasi Penetapan Penerima
                </x-ui.button>
                <p class="text-caption text-mute mt-2">Tombol ini mengarahkan ke halaman Penetapan Penerima untuk Approver.</p>
            </div>
        @endif
    @else
        <x-ui.card class="animate-scale-in">
            <x-ui.empty-state icon="list-filter" title="Pilih Program" description="Pilih program yang sudah melalui batch scoring." />
        </x-ui.card>
    @endif
</div>
