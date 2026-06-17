<div>
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Manajemen Blacklist</h1>
        <p class="mt-2 text-body-sm text-mute">Riwayat blacklist pendaftar yang dilakukan oleh verifikator.</p>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <x-ui.badge variant="outline" wire:click="$set('statusFilter', '')" class="cursor-pointer {{ !$statusFilter ? 'bg-canvas-soft text-ink' : '' }}">Semua</x-ui.badge>
        <x-ui.badge variant="destructive" wire:click="$set('statusFilter', 'active')" class="cursor-pointer {{ $statusFilter === 'active' ? 'ring-[3px] ring-error/50' : '' }}">Aktif</x-ui.badge>
        <x-ui.badge variant="success" wire:click="$set('statusFilter', 'revoked')" class="cursor-pointer {{ $statusFilter === 'revoked' ? 'ring-[3px] ring-link/50' : '' }}">Dicabut</x-ui.badge>
    </div>

    <x-ui.card padding="none" class="animate-scale-in">
        <div class="overflow-x-auto">
            <x-ui.table>
                <x-slot:header>
                    <x-ui.table.tr>
                        <x-ui.table.th>Pendaftar</x-ui.table.th>
                        <x-ui.table.th>Diblacklist Oleh</x-ui.table.th>
                        <x-ui.table.th>Alasan</x-ui.table.th>
                        <x-ui.table.th>Tanggal</x-ui.table.th>
                        <x-ui.table.th>Status</x-ui.table.th>
                        <x-ui.table.th class="w-[100px]">Tindakan</x-ui.table.th>
                    </x-ui.table.tr>
                </x-slot>
                @forelse($logs as $log)
                    <x-ui.table.tr>
                        <x-ui.table.td class="font-medium text-ink">{{ $log->user?->name }}</x-ui.table.td>
                        <x-ui.table.td>{{ $log->blacklister?->name }}</x-ui.table.td>
                        <x-ui.table.td class="text-mute max-w-xs">
                            <p class="truncate" title="{{ $log->reason }}">{{ \Illuminate\Support\Str::limit($log->reason, 60) }}</p>
                            @if($log->revoke_reason)
                                <p class="text-caption text-link-deep mt-0.5">Dicabut: {{ \Illuminate\Support\Str::limit($log->revoke_reason, 60) }}</p>
                            @endif
                        </x-ui.table.td>
                        <x-ui.table.td class="text-mute text-caption">{{ $log->created_at?->format('d M Y') }}</x-ui.table.td>
                        <x-ui.table.td>
                            <x-ui.badge :variant="$log->is_active ? 'destructive' : 'success'">
                                {{ $log->is_active ? 'Aktif' : 'Dicabut' }}
                            </x-ui.badge>
                        </x-ui.table.td>
                        <x-ui.table.td>
                            @if($log->is_active)
                                <x-ui.button size="sm" variant="outline" wire:click="openRevokeModal({{ $log->id }})">Cabut</x-ui.button>
                            @endif
                        </x-ui.table.td>
                    </x-ui.table.tr>
                @empty
                    <x-ui.table.tr>
                        <x-ui.table.td colspan="6">
                            <div class="py-10">
                                <x-ui.empty-state icon="shield-check" title="Tidak ada data" description="Belum ada pendaftar yang diblacklist." />
                            </div>
                        </x-ui.table.td>
                    </x-ui.table.tr>
                @endforelse
            </x-ui.table>
        </div>
        @if($logs->hasPages())
            <div class="border-t border-hairline px-4 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </x-ui.card>

    @if($showRevokeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeRevokeModal"></div>
            <div class="relative bg-canvas border border-hairline rounded-lg shadow-level-5 w-full max-w-md mx-4 p-6 animate-scale-in">
                <h3 class="text-display-sm text-ink">Cabut Blacklist</h3>
                <p class="text-body-sm text-mute mt-2">Jelaskan alasan pencabutan blacklist.</p>
                <div class="mt-6">
                    <label class="block text-body-sm-strong mb-2">Alasan Pencabutan</label>
                    <textarea wire:model="revokeReason" rows="3" class="flex min-h-[80px] w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm text-ink transition-colors placeholder:text-mute focus-visible:outline-none focus-visible:border-hairline-strong focus-visible:ring-[3px] focus-visible:ring-primary/20" placeholder="Minimal 10 karakter..."></textarea>
                    @error('revokeReason') <p class="text-caption text-error mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <x-ui.button variant="outline" wire:click="closeRevokeModal">Batal</x-ui.button>
                    <x-ui.button variant="destructive" wire:click="revoke">Cabut Blacklist</x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
