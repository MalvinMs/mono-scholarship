<div>
    <a href="{{ url('/admin/beasiswa') }}" class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink transition-colors mb-4">
        <x-lucide-arrow-left class="size-4" />Kembali
    </a>
    <div class="mb-6">
        <h1 class="text-display-xs text-ink">{{ $scholarship->name }}</h1>
        <p class="mt-2 text-body-sm text-mute">Penugasan Verifikator</p>
    </div>

    @if(session()->has('message'))
        <x-ui.alert variant="success" dismissible class="mb-4">{{ session('message') }}</x-ui.alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card>
            <h3 class="text-body-sm-strong text-ink mb-4">Verifikator Ditugaskan</h3>
            @forelse($scholarship->verifiers as $assignment)
                <div class="flex items-center justify-between py-3 border-b border-hairline last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="flex size-8 items-center justify-center rounded-full bg-canvas-soft border border-hairline text-caption font-medium text-ink">{{ strtoupper(substr($assignment->user->name, 0, 2)) }}</div>
                        <span class="text-body-sm font-medium text-ink">{{ $assignment->user->name }}</span>
                    </div>
                    <x-ui.button variant="ghost" size="sm" wire:click="remove({{ $assignment->user_id }})" wire:confirm="Hapus verifikator ini?" class="text-error hover:text-error-deep">Hapus</x-ui.button>
                </div>
            @empty
                <p class="text-body-sm text-mute">Belum ada verifikator ditugaskan.</p>
            @endforelse
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-body-sm-strong text-ink mb-4">Tambah Verifikator</h3>
            <div class="mb-4">
                <x-ui.input wire:model.live="search" placeholder="Cari verifikator..." />
            </div>
            <div class="space-y-1 max-h-48 overflow-y-auto">
                @foreach($availableVerifiers as $verifier)
                    <button wire:click="$set('selectedUserId', {{ $verifier->id }})"
                        class="w-full text-left px-3 py-2 text-body-sm rounded-sm hover:bg-canvas-soft transition-colors {{ $selectedUserId == $verifier->id ? 'bg-canvas-soft-2 text-ink font-medium' : 'text-mute' }}">
                        {{ $verifier->name }}
                    </button>
                @endforeach
            </div>
            @if($selectedUserId)
                <x-ui.button variant="primary" class="w-full mt-4" wire:click="assign">Tetapkan</x-ui.button>
            @endif
        </x-ui.card>
    </div>
</div>
