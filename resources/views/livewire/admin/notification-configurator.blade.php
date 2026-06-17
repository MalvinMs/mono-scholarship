<div>
    <div class="mb-8 animate-fade-in">
        <h1 class="text-display-xs text-ink">Konfigurasi Notifikasi</h1>
        <p class="mt-2 text-body-sm text-mute">Atur channel dan template notifikasi per program beasiswa.</p>
    </div>

    <div class="mb-6 max-w-xs">
        <x-ui.select wire:model.live="scholarshipId">
            <option value="">Pilih Program</option>
            @foreach($scholarships as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </x-ui.select>
    </div>

    @if($scholarshipId)
        <x-ui.card class="mb-6 animate-scale-in">
            <h3 class="text-body-sm-strong text-ink mb-4">Channel Notifikasi</h3>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="channels.whatsapp" class="size-4 rounded-sm border-hairline accent-primary">
                    <span class="text-body-sm text-ink">WhatsApp (Fonnte)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="channels.email" class="size-4 rounded-sm border-hairline accent-primary">
                    <span class="text-body-sm text-ink">Email (SMTP)</span>
                </label>
            </div>
        </x-ui.card>

        <x-ui.card class="mb-6 animate-scale-in">
            <h3 class="text-body-sm-strong text-ink mb-2">Template Notifikasi</h3>
            <p class="text-caption text-mute mb-6">
                Gunakan placeholder: <code class="text-ink font-mono bg-canvas-soft px-1 py-0.5 rounded-sm">\{name\}</code>, <code class="text-ink font-mono bg-canvas-soft px-1 py-0.5 rounded-sm">\{registration_number\}</code>, <code class="text-ink font-mono bg-canvas-soft px-1 py-0.5 rounded-sm">\{scholarship_name\}</code>, <code class="text-ink font-mono bg-canvas-soft px-1 py-0.5 rounded-sm">\{status\}</code>, <code class="text-ink font-mono bg-canvas-soft px-1 py-0.5 rounded-sm">\{result\}</code>
            </p>

            <div class="space-y-6">
                @foreach($eventTypes as $key => $label)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-body-sm-strong text-ink">{{ $label }} ({{ $key }})</h4>
                            <button type="button" wire:click="togglePreview('{{ $key }}')" class="text-caption text-link hover:text-link-deep transition-colors">
                                {{ $previewKey === $key ? 'Sembunyikan' : 'Preview' }}
                            </button>
                        </div>
                        <textarea wire:model="templates.{{ $key }}" rows="4" class="w-full rounded-md border border-hairline bg-canvas px-3 py-2 text-body-sm font-mono focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all shadow-level-1"></textarea>
                        @if($previewKey === $key && isset($templates[$key]))
                            <div class="mt-2 p-3 rounded-sm bg-canvas-soft border border-hairline text-body-sm text-ink whitespace-pre-wrap animate-fade-in font-sans">
                                {{ $this->renderPreview($templates[$key]) }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.button variant="primary" wire:click="save">
            <x-lucide-save class="size-4" />
            Simpan Konfigurasi
        </x-ui.button>

        @if($saved)
            <p class="text-body-sm text-success mt-4 animate-fade-in font-medium">Konfigurasi berhasil disimpan.</p>
        @endif
    @else
        <x-ui.card class="animate-scale-in">
            <x-ui.empty-state icon="bell" title="Pilih Program" description="Pilih program beasiswa untuk mengkonfigurasi notifikasi." />
        </x-ui.card>
    @endif
</div>
