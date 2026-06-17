<div>
    <div class="mb-8 animate-fade-in">
        <a href="{{ route('application.status', $application->id) }}" wire:navigate class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink mb-4 transition-colors">
            <x-lucide-arrow-left class="size-3.5" />
            Kembali
        </a>
        <h1 class="text-display-xs text-ink">Data Rekening Bank</h1>
        <p class="mt-2 text-body-sm text-mute">Isi rekening untuk pencairan dana beasiswa.</p>
    </div>

    <x-ui.card class="max-w-lg animate-scale-in">
        @if($submitted)
            <div class="text-center py-4">
                <div class="flex size-12 mx-auto items-center justify-center rounded-full bg-success/10 mb-4">
                    <x-lucide-check-circle class="size-6 text-success" />
                </div>
                <h3 class="text-body-sm-strong text-ink">Data Rekening Tersimpan</h3>
                <div class="mt-4 space-y-2 text-body-sm">
                    <p><span class="text-mute">Bank:</span> <span class="font-medium text-ink">{{ $bankName }}</span></p>
                    <p><span class="text-mute">No. Rekening:</span> <span class="font-medium font-mono text-ink">{{ $accountNumber }}</span></p>
                    <p><span class="text-mute">Nama Pemegang:</span> <span class="font-medium text-ink">{{ $accountHolderName }}</span></p>
                </div>
                @if($existingDisbursement)
                    <x-ui.badge variant="{{ match($existingDisbursement->status) {
                        'waiting' => 'secondary',
                        'processing' => 'warning',
                        'disbursed' => 'success',
                        default => 'secondary'
                    } }}" class="mt-4">
                        Status: {{ $existingDisbursement->status === 'waiting' ? 'Menunggu' : ($existingDisbursement->status === 'processing' ? 'Diproses' : 'Sudah Dicairkan') }}
                    </x-ui.badge>
                @endif
                <x-ui.button variant="outline" wire:click="$set('submitted', false)" class="mt-4">Edit Data</x-ui.button>
            </div>
        @else
            <form wire:submit="save" class="space-y-6">
                <x-ui.form-group label="Nama Bank" required>
                    <x-ui.select wire:model="bankName">
                        <option value="">Pilih Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                        <option value="BSI">BSI</option>
                        <option value="BTN">BTN</option>
                        <option value="CIMB Niaga">CIMB Niaga</option>
                        <option value="Bank Jatim">Bank Jatim</option>
                    </x-ui.select>
                    @error('bankName') <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror
                </x-ui.form-group>

                <x-ui.form-group label="Nomor Rekening" required>
                    <x-ui.input type="text" wire:model="accountNumber" placeholder="Masukkan nomor rekening" />
                    @error('accountNumber') <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror
                </x-ui.form-group>

                <x-ui.form-group label="Nama Pemegang Rekening" required>
                    <x-ui.input type="text" wire:model="accountHolderName" placeholder="Nama sesuai buku rekening" />
                    @error('accountHolderName') <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror
                </x-ui.form-group>

                <div class="pt-2">
                    <x-ui.button variant="primary" type="submit">
                        <x-lucide-save class="size-4" />
                        Simpan Data Rekening
                    </x-ui.button>
                </div>
            </form>
        @endif
    </x-ui.card>
</div>
