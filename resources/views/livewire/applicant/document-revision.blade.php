<div>
    <div class="mb-8">
        <a href="{{ route('application.status', $application->id) }}" class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink mb-4 transition-colors"><x-lucide-arrow-left class="size-4" />Kembali</a>
        <h1 class="text-display-xs text-ink">Revisi Dokumen</h1>
        <p class="text-body-sm text-mute mt-2">{{ $application->scholarship->name }}</p>
    </div>

    @if(session()->has('message'))
        <x-ui.alert variant="success" dismissible class="mb-6">{{ session('message') }}</x-ui.alert>
    @endif

    @forelse($application->documents as $doc)
        <x-ui.card class="mb-6">
            <h3 class="text-body-sm-strong text-ink mb-2">{{ $doc->doc_label ?: $doc->file_name }}</h3>
            @if($doc->rejection_reason)
                <x-ui.alert variant="destructive" class="mb-4"><strong>Alasan ditolak:</strong> {{ $doc->rejection_reason }}</x-ui.alert>
            @endif
            <p class="text-caption text-mute mb-4">File sebelumnya: {{ $doc->file_name }}</p>
            <div class="space-y-4">
                <x-ui.input type="file" wire:model="newFiles.{{ $doc->id }}" accept=".jpg,.jpeg,.png,.pdf" />
                @error("newFiles.{$doc->id}") <p class="text-caption text-error">{{ $message }}</p> @enderror
                <x-ui.button variant="primary" wire:click="reupload({{ $doc->id }})">Upload Ulang</x-ui.button>
            </div>
        </x-ui.card>
    @empty
        <x-ui.card><x-ui.empty-state icon="check-circle" title="Tidak Ada Revisi" description="Semua dokumen sudah sesuai." /></x-ui.card>
    @endforelse
</div>
