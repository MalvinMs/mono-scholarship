@props([
    'file' => null,
    'accept' => '.jpg,.jpeg,.png,.pdf',
    'maxSize' => '2 MB',
    'label' => null,
    'description' => null,
])

@php
$wireModel = $attributes->get('wire:model');
@endphp

<div wire:key="uploader-{{ $wireModel }}-{{ $file ? 'filled' : 'empty' }}">
    @if($file)
        <div class="flex items-center gap-3 p-3 rounded-sm border border-hairline bg-canvas-soft animate-scale-in">
            @if($file->getMimeType() && str_starts_with($file->getMimeType(), 'image/'))
                <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="size-12 rounded-sm object-cover border border-hairline shrink-0">
            @else
                <div class="flex size-12 shrink-0 items-center justify-center rounded-sm border border-hairline bg-canvas">
                    <x-lucide-file-text class="size-5 text-mute" />
                </div>
            @endif

            <div class="flex-1 min-w-0">
                <p class="text-body-sm font-medium text-ink truncate">{{ $file->getClientOriginalName() }}</p>
                <p class="text-caption text-mute">{{ round($file->getSize() / 1024, 1) }} KB</p>
            </div>

            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-success/10 border border-success/20 text-caption font-medium text-success shrink-0">
                <x-lucide-check class="size-3" />
                Tersimpan
            </span>

            <button type="button"
                wire:click="removeFile('{{ $wireModel }}')"
                class="flex size-7 shrink-0 items-center justify-center rounded-sm border border-hairline bg-canvas text-mute hover:bg-error/5 hover:border-error/20 hover:text-error transition-colors"
                title="Hapus file">
                <x-lucide-x class="size-3.5" />
            </button>
        </div>
    @else
        <div>
            <input type="file"
                wire:model="{{ $wireModel }}"
                accept="{{ $accept }}"
                class="block w-full text-body-sm text-ink file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-body-sm file:font-medium file:bg-canvas-soft file:text-ink hover:file:bg-canvas-soft-2 file:cursor-pointer file:transition-colors cursor-pointer" />

            <div wire:loading wire:target="{{ $wireModel }}" class="flex flex-col gap-2 mt-2 p-3 rounded-sm border border-hairline bg-canvas-soft animate-fade-in">
                <div class="flex items-center gap-2">
                    <x-lucide-loader-circle class="size-4 text-primary animate-spin" />
                    <span class="text-body-sm text-ink">Mengupload...</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-canvas border border-hairline overflow-hidden">
                    <div class="h-full rounded-full bg-primary animate-pulse"></div>
                </div>
            </div>

            <p class="text-caption text-mute mt-2">
                Maksimal {{ $maxSize }}. Format: {{ str_replace('.', '', $accept) }}.
            </p>

            @if($label)
                <p class="text-caption text-mute mt-1">{{ $label }}</p>
            @endif
        </div>
    @endif

    @error($wireModel)
        <p class="text-caption text-error mt-1">{{ $message }}</p>
    @enderror
</div>