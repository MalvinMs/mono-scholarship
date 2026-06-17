<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8 animate-fade-in">
        <div>
            <h1 class="text-display-xs text-ink">Pencairan Dana</h1>
            <p class="mt-2 text-body-sm text-mute">Kelola pencairan dana untuk penerima beasiswa.</p>
        </div>
        @if($canExport)
            <x-ui.button variant="outline" wire:click="exportExcel">
                <x-lucide-download class="size-4" />
                Export Excel
            </x-ui.button>
        @endif
    </div>

    <div class="flex flex-wrap gap-4 mb-6">
        <div class="max-w-xs">
            <x-ui.select wire:model.live="scholarshipId">
                <option value="">Semua Program</option>
                @foreach($scholarships as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div class="max-w-[200px]">
            <x-ui.select wire:model.live="statusFilter">
                <option value="">Semua Status</option>
                <option value="waiting">Menunggu</option>
                <option value="processing">Diproses</option>
                <option value="disbursed">Sudah Cair</option>
            </x-ui.select>
        </div>
    </div>

    <x-ui.card padding="none" class="animate-scale-in overflow-hidden">
        <div class="overflow-x-auto">
            <x-ui.table>
                <x-slot:header>
                    <x-ui.table.tr>
                        <x-ui.table.th>Penerima</x-ui.table.th>
                        <x-ui.table.th>Program</x-ui.table.th>
                        <x-ui.table.th>Bank / Rekening</x-ui.table.th>
                        <x-ui.table.th>Nominal</x-ui.table.th>
                        <x-ui.table.th>Status</x-ui.table.th>
                        <x-ui.table.th class="w-[180px]">Tindakan</x-ui.table.th>
                    </x-ui.table.tr>
                </x-slot>
                @forelse($disbursements as $d)
                    <x-ui.table.tr>
                        <x-ui.table.td class="font-medium text-ink">{{ $d->application?->user?->name }}</x-ui.table.td>
                        <x-ui.table.td class="text-mute text-body-sm">{{ $d->scholarship?->name }}</x-ui.table.td>
                        <x-ui.table.td class="text-body-sm text-ink">
                            {{ $d->bank_name }}<br>
                            <span class="font-mono text-caption text-mute">{{ $d->account_number }}</span>
                            <br><span class="text-caption text-mute">{{ $d->account_holder_name }}</span>
                        </x-ui.table.td>
                        <x-ui.table.td class="text-right font-medium text-ink">Rp {{ number_format($d->amount, 0, ',', '.') }}</x-ui.table.td>
                        <x-ui.table.td>
                            <x-ui.badge :variant="match($d->status) {
                                'waiting' => 'secondary',
                                'processing' => 'warning',
                                'disbursed' => 'success',
                                default => 'secondary'
                            }">
                                {{ match($d->status) {
                                    'waiting' => 'Menunggu',
                                    'processing' => 'Diproses',
                                    'disbursed' => 'Cair',
                                    default => $d->status
                                } }}
                            </x-ui.badge>
                        </x-ui.table.td>
                        <x-ui.table.td>
                            <div class="flex gap-2">
                                @if($d->status === 'waiting')
                                    <x-ui.button size="sm" variant="outline" wire:click="updateStatus({{ $d->id }}, 'processing')">Proses</x-ui.button>
                                @elseif($d->status === 'processing')
                                    <x-ui.button size="sm" variant="primary" wire:click="updateStatus({{ $d->id }}, 'disbursed')">Cairkan</x-ui.button>
                                @endif
                            </div>
                        </x-ui.table.td>
                    </x-ui.table.tr>
                @empty
                    <x-ui.table.tr>
                        <x-ui.table.td colspan="6">
                            <div class="py-10">
                                <x-ui.empty-state icon="banknote" title="Belum ada data" description="Belum ada data pencairan dana." />
                            </div>
                        </x-ui.table.td>
                    </x-ui.table.tr>
                @endforelse
            </x-ui.table>
        </div>
        @if($disbursements->hasPages())
            <div class="border-t border-hairline px-4 py-3">
                {{ $disbursements->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
