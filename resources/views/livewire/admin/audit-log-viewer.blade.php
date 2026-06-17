<div>
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Audit Log</h1>
        <p class="mt-2 text-body-sm text-mute">Riwayat aktivitas verifikasi dan blacklist.</p>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <button wire:click="$set('logType', 'verification')" class="rounded-sm px-3 py-1.5 text-body-sm-strong transition-colors {{ $logType === 'verification' ? 'bg-canvas-soft border border-hairline text-ink ring-[3px] ring-primary/20' : 'bg-transparent text-mute hover:bg-canvas-soft' }}">
            Log Verifikasi
        </button>
        <button wire:click="$set('logType', 'blacklist')" class="rounded-sm px-3 py-1.5 text-body-sm-strong transition-colors {{ $logType === 'blacklist' ? 'bg-canvas-soft border border-hairline text-ink ring-[3px] ring-primary/20' : 'bg-transparent text-mute hover:bg-canvas-soft' }}">
            Log Blacklist
        </button>

        @if($logType === 'verification')
            <x-ui.select wire:model.live="filter" class="ml-auto w-auto max-w-[200px]">
                <option value="">Semua Aksi</option>
                <option value="document_approved">Document Approved</option>
                <option value="document_rejected">Document Rejected</option>
                <option value="answer_corrected">Answer Corrected</option>
                <option value="applicant_blacklisted">Applicant Blacklisted</option>
            </x-ui.select>
        @else
            <x-ui.select wire:model.live="filter" class="ml-auto w-auto max-w-[200px]">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="revoked">Dicabut</option>
            </x-ui.select>
        @endif
    </div>

    <x-ui.card padding="none" class="animate-scale-in overflow-hidden">
        <div class="overflow-x-auto">
            @if($logType === 'verification')
                <x-ui.table>
                    <x-slot:header>
                        <x-ui.table.tr>
                            <x-ui.table.th>Verifikator</x-ui.table.th>
                            <x-ui.table.th>Aksi</x-ui.table.th>
                            <x-ui.table.th>Target</x-ui.table.th>
                            <x-ui.table.th>Detail</x-ui.table.th>
                            <x-ui.table.th>Alasan</x-ui.table.th>
                            <x-ui.table.th class="w-[140px]">Tanggal</x-ui.table.th>
                        </x-ui.table.tr>
                    </x-slot>
                    @forelse($logs as $log)
                        <x-ui.table.tr>
                            <x-ui.table.td class="font-medium text-ink">{{ $log->verifier?->name }}</x-ui.table.td>
                            <x-ui.table.td>
                                <x-ui.badge :variant="match($log->action) {
                                    'document_approved' => 'success',
                                    'document_rejected' => 'destructive',
                                    'answer_corrected' => 'warning',
                                    'applicant_blacklisted' => 'destructive',
                                    default => 'secondary'
                                }" class="text-caption">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </x-ui.badge>
                            </x-ui.table.td>
                            <x-ui.table.td class="text-caption font-mono text-mute">{{ $log->application?->registration_number }}</x-ui.table.td>
                            <x-ui.table.td class="text-caption text-mute max-w-[200px] truncate">
                                @if($log->field_changed){{ $log->field_changed }}: {{ $log->old_value }} → {{ $log->new_value }}@else-@endif
                            </x-ui.table.td>
                            <x-ui.table.td class="text-caption text-mute max-w-[200px] truncate">{{ $log->reason }}</x-ui.table.td>
                            <x-ui.table.td class="text-caption text-mute">{{ $log->created_at?->format('d M Y H:i') }}</x-ui.table.td>
                        </x-ui.table.tr>
                    @empty
                        <x-ui.table.tr>
                            <x-ui.table.td colspan="6">
                                <div class="py-10">
                                    <x-ui.empty-state icon="scroll-text" title="Tidak ada log" description="Belum ada aktivitas verifikasi." />
                                </div>
                            </x-ui.table.td>
                        </x-ui.table.tr>
                    @endforelse
                </x-ui.table>
            @else
                <x-ui.table>
                    <x-slot:header>
                        <x-ui.table.tr>
                            <x-ui.table.th>Pendaftar</x-ui.table.th>
                            <x-ui.table.th>Verifikator</x-ui.table.th>
                            <x-ui.table.th>Alasan</x-ui.table.th>
                            <x-ui.table.th>Status</x-ui.table.th>
                            <x-ui.table.th>Pencabutan</x-ui.table.th>
                            <x-ui.table.th class="w-[140px]">Tanggal</x-ui.table.th>
                        </x-ui.table.tr>
                    </x-slot>
                    @forelse($logs as $log)
                        <x-ui.table.tr>
                            <x-ui.table.td class="font-medium text-ink">{{ $log->user?->name }}</x-ui.table.td>
                            <x-ui.table.td>{{ $log->blacklister?->name }}</x-ui.table.td>
                            <x-ui.table.td class="text-caption text-mute max-w-[250px] truncate">{{ $log->reason }}</x-ui.table.td>
                            <x-ui.table.td>
                                <x-ui.badge :variant="$log->is_active ? 'destructive' : 'success'">{{ $log->is_active ? 'Aktif' : 'Dicabut' }}</x-ui.badge>
                            </x-ui.table.td>
                            <x-ui.table.td class="text-caption text-mute">
                                @if($log->revoke_reason)
                                    <span>Oleh {{ $log->revoker?->name }}: {{ \Illuminate\Support\Str::limit($log->revoke_reason, 40) }}</span>
                                @else-@endif
                            </x-ui.table.td>
                            <x-ui.table.td class="text-caption text-mute">{{ $log->created_at?->format('d M Y H:i') }}</x-ui.table.td>
                        </x-ui.table.tr>
                    @empty
                        <x-ui.table.tr>
                            <x-ui.table.td colspan="6">
                                <div class="py-10">
                                    <x-ui.empty-state icon="shield-check" title="Tidak ada log" description="Belum ada aktivitas blacklist." />
                                </div>
                            </x-ui.table.td>
                        </x-ui.table.tr>
                    @endforelse
                </x-ui.table>
            @endif
        </div>
        @if($logs->hasPages())
            <div class="border-t border-hairline px-4 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
