<div>
    <div class="mb-8 animate-fade-in">
        <a href="{{ url('/dashboard') }}" wire:navigate class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink mb-4 transition-colors">
            <x-lucide-arrow-left class="size-3.5" />
            Kembali ke Dashboard
        </a>
        <h1 class="text-display-xs text-ink">Renewal Beasiswa</h1>
        <p class="mt-2 text-body-sm text-mute">Ajukan perpanjangan beasiswa untuk periode selanjutnya.</p>
    </div>

    <x-ui.card class="max-w-lg animate-scale-in">
        <div class="mb-6">
            <h3 class="text-body-sm-strong text-ink">{{ $application->scholarship->name }}</h3>
            <p class="text-caption text-mute mt-1">No. Registrasi: {{ $application->registration_number }}</p>
        </div>

        <div class="bg-canvas-soft border border-hairline rounded-sm p-4 mb-6 text-body-sm">
            <p class="font-medium text-ink">Syarat Renewal:</p>
            <ul class="mt-2 space-y-1 text-mute">
                <li>- IPK minimal: <span class="font-medium text-ink">{{ $application->scholarship->min_gpa_renewal ?? 3.50 }}</span></li>
                <li>- Upload transkrip nilai terbaru</li>
                <li>- Dokumen wajib diverifikasi oleh verifikator</li>
            </ul>
        </div>

        <form wire:submit="submit" class="space-y-6">
            <x-ui.form-group label="IPK Semester Terakhir" required>
                <x-ui.input type="number" wire:model="gpa" step="0.01" min="0" max="4.00" placeholder="3.50" />
                @error('gpa') <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror
            </x-ui.form-group>

            <x-ui.form-group label="Upload Transkrip" required>
                <x-ui.input type="file" wire:model="transcriptFile" accept=".jpg,.jpeg,.png,.pdf" />
                <p class="text-caption text-mute mt-2">Maksimal 2 MB. Format: JPG, PNG, PDF.</p>
                @error('transcriptFile') <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror
            </x-ui.form-group>

            <div class="pt-2">
                <x-ui.button variant="primary" type="submit">
                    <x-lucide-send class="size-4" />
                    Submit Renewal
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
