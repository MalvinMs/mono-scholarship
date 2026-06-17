<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8 animate-fade-in">
        <div>
            <h1 class="text-display-xs text-ink">Antrian Verifikasi</h1>
            <p class="mt-2 text-body-sm text-mute">Daftar pendaftar yang menunggu verifikasi dokumen.</p>
        </div>
    </div>

    <div class="mb-6 max-w-xs">
        <x-ui.select wire:model.live="selectedScholarshipId">
            <option value="">Semua Program</option>
            @foreach($scholarships as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </x-ui.select>
    </div>

    <x-ui.card padding="none" class="animate-scale-in overflow-hidden">
        <div class="overflow-x-auto">
            <x-ui.table>
                <x-slot:header>
                    <x-ui.table.tr>
                        <x-ui.table.th class="w-[160px]">No. Registrasi</x-ui.table.th>
                        <x-ui.table.th>Nama</x-ui.table.th>
                        <x-ui.table.th class="w-[140px]">Status</x-ui.table.th>
                        <x-ui.table.th class="w-[180px]">Progress Dokumen</x-ui.table.th>
                        <x-ui.table.th class="w-[150px]">Tanggal Submit</x-ui.table.th>
                    </x-ui.table.tr>
                </x-slot>
                @forelse($applications as $application)
                    @php
                        $progress = $this->getDocumentProgress($application);
                    @endphp
                    <x-ui.table.tr class="cursor-pointer transition-colors hover:bg-canvas-soft" wire:click="goToDetail({{ $application->id }})">
                        <x-ui.table.td class="font-mono text-caption tracking-tight text-mute">{{ $application->registration_number }}</x-ui.table.td>
                        <x-ui.table.td class="font-medium text-ink text-body-sm">{{ $application->user?->name }}</x-ui.table.td>
                        <x-ui.table.td>
                            @php
                                $queueStatusVariant = [
                                    'submitted' => 'default',
                                    'under_review' => 'warning',
                                    'needs_revision' => 'destructive',
                                ][$application->status] ?? 'secondary';
                            @endphp
                            <x-ui.badge variant="{{ $queueStatusVariant }}">{{ str_replace('_', ' ', $application->status) }}</x-ui.badge>
                        </x-ui.table.td>
                        <x-ui.table.td>
                            @if($progress['total'] > 0)
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-canvas-soft overflow-hidden border border-hairline">
                                        <div class="h-full rounded-full transition-all duration-300 {{ $progress['percentage'] === 100 ? 'bg-success' : 'bg-primary' }}"
                                            style="width: {{ $progress['percentage'] }}%">
                                        </div>
                                    </div>
                                    <span class="text-caption text-mute tabular-nums">{{ $progress['approved'] }}/{{ $progress['total'] }}</span>
                                </div>
                            @else
                                <span class="text-caption text-mute">-</span>
                            @endif
                        </x-ui.table.td>
                        <x-ui.table.td class="text-mute text-caption">{{ $application->submitted_at?->format('d M Y H:i') }}</x-ui.table.td>
                    </x-ui.table.tr>
                @empty
                    <x-ui.table.tr>
                        <x-ui.table.td colspan="5">
                            <div class="py-10">
                                <x-ui.empty-state icon="clipboard-list" title="Antrian kosong" description="Tidak ada pendaftar yang menunggu verifikasi untuk program yang ditugaskan kepada Anda." />
                            </div>
                        </x-ui.table.td>
                    </x-ui.table.tr>
                @endforelse
            </x-ui.table>
        </div>
        @if($applications->hasPages())
            <div class="border-t border-hairline px-4 py-3">
                {{ $applications->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
