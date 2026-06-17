<div>
    <div class="mb-8">
        <a href="{{ url('/beasiswa') }}" class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink mb-4 transition-colors">
            <x-lucide-arrow-left class="size-4" />Kembali
        </a>
        <h1 class="text-display-xs text-ink">{{ $scholarship->name }}</h1>
        <p class="text-body-sm text-mute mt-2">{{ $scholarship->description }}</p>
    </div>

    <!-- Stepper -->
    <div class="mb-8">
        <div class="flex items-center gap-2">
            @foreach($formConfig as $index => $group)
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center size-8 rounded-full text-caption font-medium {{ $index <= $currentStep ? 'bg-canvas-soft border border-hairline text-ink ring-[3px] ring-primary/20' : 'bg-canvas-soft border border-hairline text-mute' }}">
                        {{ $index + 1 }}
                    </div>
                    <span class="text-caption {{ $index <= $currentStep ? 'text-ink font-medium' : 'text-mute' }} hidden sm:inline">{{ $group['name'] }}</span>
                </div>
                @if($index < count($formConfig) - 1)
                    <div class="h-px flex-1 {{ $index < $currentStep ? 'bg-primary' : 'bg-hairline' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    @if(!empty($formConfig))
        @php $currentGroup = $formConfig[$currentStep]; @endphp

        <x-ui.card>
            <h3 class="text-display-sm text-ink mb-1">{{ $currentGroup['name'] }}</h3>
            @if($currentGroup['description'])
                <p class="text-body-sm text-mute mb-6">{{ $currentGroup['description'] }}</p>
            @endif

            <form wire:submit="{{ $currentStep < count($formConfig) - 1 ? 'nextStep' : 'submit' }}" class="space-y-6">
                @foreach($currentGroup['qualifications'] as $q)
                    <x-ui.form-group :label="$q['name']" :required="$q['is_required']" :description="$q['description']">
                        @switch($q['type'])
                            @case('single_choice')
                                @foreach($q['options'] as $opt)
                                    <div class="flex items-center gap-3 mb-2 p-2 rounded-sm hover:bg-canvas-soft transition-colors">
                                        <input type="radio" wire:model="answers.{{ $q['id'] }}" value="{{ $opt['id'] }}" id="q_{{ $q['id'] }}_{{ $opt['id'] }}" class="size-4 border-hairline text-primary focus:ring-1 focus:ring-primary">
                                        <label for="q_{{ $q['id'] }}_{{ $opt['id'] }}" class="text-body-sm text-ink cursor-pointer flex-1">{{ $opt['label'] }}</label>
                                    </div>
                                @endforeach
                                @break

                            @case('multi_choice')
                                @foreach($q['options'] as $opt)
                                    <div class="flex items-center gap-3 mb-2 p-2 rounded-sm hover:bg-canvas-soft transition-colors">
                                        <x-ui.checkbox :label="$opt['label']" wire:model="answers.{{ $q['id'] }}" value="{{ $opt['id'] }}" />
                                    </div>
                                @endforeach
                                @break

                            @case('numeric_range')
                                <x-ui.input type="number" wire:model="answers.{{ $q['id'] }}" step="0.01" placeholder="0.00" />
                                @break

                            @case('file_upload')
                                <x-ui.document-uploader wire:model="files.{{ $q['id'] }}" :file="$files[$q['id']] ?? null" />
                                @if($q['file_upload_label'])
                                    <p class="text-caption text-mute mt-1">{{ $q['file_upload_label'] }}</p>
                                @endif
                                @break

                            @case('text')
                                <x-ui.textarea wire:model="answers.{{ $q['id'] }}" rows="3" placeholder="Tulis jawaban Anda..." />
                                @break
                        @endswitch
                        @error("answers.{$q['id']}") <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror

                        @if($q['is_file_upload_required'] && $q['type'] !== 'file_upload')
                            <div class="mt-4 pt-4 border-t border-hairline">
                                <label class="text-body-sm-strong text-ink">
                                    {{ $q['file_upload_label'] ?? 'Upload Dokumen Pendukung' }}
                                    <span class="text-error">*</span>
                                </label>
                                <x-ui.document-uploader wire:model="files.{{ $q['id'] }}" :file="$files[$q['id']] ?? null" class="mt-2" />
                            </div>
                        @endif
                    </x-ui.form-group>
                @endforeach

                <div class="flex items-center justify-between pt-6 border-t border-hairline mt-8">
                    <div>
                        @if($currentStep > 0)
                            <x-ui.button variant="outline" type="button" wire:click="prevStep">Sebelumnya</x-ui.button>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <x-ui.button variant="ghost" type="button" wire:click="saveDraft">Simpan Draft</x-ui.button>
                        @if($currentStep < count($formConfig) - 1)
                            <x-ui.button variant="primary" type="submit">Selanjutnya</x-ui.button>
                        @else
                            <x-ui.button variant="primary" type="submit">Submit Pendaftaran</x-ui.button>
                        @endif
                    </div>
                </div>

                @if($currentStep === count($formConfig) - 1)
                    @php $attachedFiles = collect($files)->filter(); @endphp
                    @if($attachedFiles->isNotEmpty())
                        <div class="mt-6 p-4 rounded-sm border border-hairline bg-canvas-soft animate-scale-in">
                            <h4 class="text-body-sm-strong text-ink mb-3 flex items-center gap-2">
                                <x-lucide-paperclip class="size-4 text-mute" />
                                Dokumen Terlampir
                            </h4>
                            <div class="space-y-2">
                                @foreach($attachedFiles as $qId => $file)
                                    <div class="flex items-center gap-3 py-2 px-3 rounded-sm bg-canvas border border-hairline">
                                        @if($file->getMimeType() && str_starts_with($file->getMimeType(), 'image/'))
                                            <img src="{{ $file->temporaryUrl() }}" class="size-8 rounded-sm object-cover border border-hairline shrink-0">
                                        @else
                                            <x-lucide-file-text class="size-4 text-mute shrink-0" />
                                        @endif
                                        <span class="text-body-sm text-ink truncate flex-1">{{ $file->getClientOriginalName() }}</span>
                                        <span class="text-caption text-mute shrink-0">{{ round($file->getSize() / 1024, 1) }} KB</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </form>
        </x-ui.card>
    @else
        <x-ui.card>
            <x-ui.empty-state icon="clipboard-list" title="Belum Ada Kualifikasi" description="Admin belum mengkonfigurasi kualifikasi untuk program ini." />
        </x-ui.card>
    @endif
</div>
