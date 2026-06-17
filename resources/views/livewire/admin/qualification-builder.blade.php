<div>
    <div class="mb-6 flex items-center justify-between animate-fade-in">
        <div>
            <a href="{{ url('/admin/beasiswa') }}" wire:navigate class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink mb-2 transition-colors">
                <x-lucide-arrow-left class="size-3.5" />Kembali ke Program
            </a>
            <h1 class="text-display-xs text-ink">{{ $scholarship->name }}</h1>
            <p class="text-body-sm text-mute mt-1">Konfigurasi kualifikasi seleksi</p>
        </div>
        @if($isLocked)
            <x-ui.badge variant="warning" class="px-3 py-1.5">Terkunci — sudah ada pendaftar</x-ui.badge>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Panel: Groups & Qualifications --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Groups --}}
            @foreach($groups as $group)
                <x-ui.card class="animate-scale-in" padding="none">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-hairline bg-canvas-soft rounded-t-md">
                        <div>
                            <h3 class="text-body-sm-strong text-ink">{{ $group->name }}</h3>
                            @if($group->description)
                                <p class="text-caption text-mute mt-0.5">{{ $group->description }}</p>
                            @endif
                        </div>
                        @unless($isLocked)
                        <div class="flex items-center gap-1">
                            <button wire:click="openQualForm(null, {{ $group->id }})" class="flex items-center gap-1 rounded-sm px-2 py-1 text-caption font-medium bg-canvas border border-hairline hover:bg-canvas-soft-2 transition-colors">
                                <x-lucide-plus class="size-3" />Tambah
                            </button>
                            <button wire:click="openGroupForm({{ $group->id }})" class="flex size-7 items-center justify-center rounded-sm text-mute hover:text-ink hover:bg-canvas-soft-2 transition-colors">
                                <x-lucide-pencil class="size-3.5" />
                            </button>
                            <button wire:click="deleteGroup({{ $group->id }})" wire:confirm="Hapus grup ini?" class="flex size-7 items-center justify-center rounded-sm text-mute hover:text-error hover:bg-error/10 transition-colors">
                                <x-lucide-trash-2 class="size-3.5" />
                            </button>
                        </div>
                        @endunless
                    </div>
                    @if($group->qualifications->isNotEmpty())
                        <div class="divide-y divide-hairline">
                            @foreach($group->qualifications as $qual)
                                <div wire:click="selectQual({{ $qual->id }})" class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-canvas-soft transition-colors {{ $selectedQualId === $qual->id ? 'bg-canvas-soft-2 border-l-[3px] border-l-primary pl-[13px]' : '' }}">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-body-sm-strong text-ink">{{ $qual->name }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <x-ui.badge variant="outline" class="text-caption">{{ $qual->type }}</x-ui.badge>
                                            @if($qual->is_required)<span class="text-caption text-error">Wajib</span>@endif
                                            @if($qual->is_file_upload_required)<span class="text-caption text-warning">+Dokumen</span>@endif
                                        </div>
                                    </div>
                                    @unless($isLocked)
                                    <div class="flex items-center gap-1 ml-2">
                                        <button wire:click.stop="openQualForm({{ $qual->id }})" class="flex size-7 items-center justify-center rounded-sm text-mute hover:text-ink hover:bg-canvas-soft-2 transition-colors">
                                            <x-lucide-pencil class="size-3.5" />
                                        </button>
                                        <button wire:click.stop="deleteQual({{ $qual->id }})" wire:confirm="Hapus pertanyaan ini?" class="flex size-7 items-center justify-center rounded-sm text-mute hover:text-error hover:bg-error/10 transition-colors">
                                            <x-lucide-trash-2 class="size-3.5" />
                                        </button>
                                    </div>
                                    @endunless
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="px-4 py-6 text-center text-body-sm text-mute">Belum ada pertanyaan di grup ini.</div>
                    @endif
                </x-ui.card>
            @endforeach

            {{-- Ungrouped Qualifications --}}
            @if($ungrouped->isNotEmpty())
                <x-ui.card class="animate-scale-in" padding="none">
                    <div class="px-4 py-3 border-b border-hairline bg-canvas-soft rounded-t-md">
                        <h3 class="text-body-sm-strong text-ink">Tanpa Grup</h3>
                    </div>
                    <div class="divide-y divide-hairline">
                        @foreach($ungrouped as $qual)
                            <div wire:click="selectQual({{ $qual->id }})" class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-canvas-soft transition-colors {{ $selectedQualId === $qual->id ? 'bg-canvas-soft-2 border-l-[3px] border-l-primary pl-[13px]' : '' }}">
                                <div class="flex-1 min-w-0">
                                    <p class="text-body-sm-strong text-ink">{{ $qual->name }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <x-ui.badge variant="outline" class="text-caption">{{ $qual->type }}</x-ui.badge>
                                    </div>
                                </div>
                                @unless($isLocked)
                                <div class="flex items-center gap-1 ml-2">
                                    <button wire:click.stop="openQualForm({{ $qual->id }})" class="flex size-7 items-center justify-center rounded-sm text-mute hover:text-ink hover:bg-canvas-soft-2 transition-colors">
                                        <x-lucide-pencil class="size-3.5" />
                                    </button>
                                    <button wire:click.stop="deleteQual({{ $qual->id }})" wire:confirm="Hapus pertanyaan ini?" class="flex size-7 items-center justify-center rounded-sm text-mute hover:text-error hover:bg-error/10 transition-colors">
                                        <x-lucide-trash-2 class="size-3.5" />
                                    </button>
                                </div>
                                @endunless
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            {{-- Add Group / Add Qualification buttons --}}
            @unless($isLocked)
                <div class="flex gap-3 mt-6">
                    <x-ui.button variant="secondary" size="sm" wire:click="openGroupForm()">
                        <x-lucide-folder-plus class="size-4" />Tambah Grup
                    </x-ui.button>
                    <x-ui.button variant="outline" size="sm" wire:click="openQualForm()">
                        <x-lucide-plus class="size-4" />Tambah Pertanyaan
                    </x-ui.button>
                </div>
            @endunless
        </div>

        {{-- Right Panel: Qualification Detail --}}
        <div class="space-y-4">
            @if($selectedQual)
                @php $qual = $selectedQual; @endphp
                <x-ui.card class="animate-scale-in" padding="none">
                    <div class="px-4 py-3 border-b border-hairline bg-canvas-soft rounded-t-md">
                        <h3 class="text-body-sm-strong text-ink">{{ $qual->name }}</h3>
                        <x-ui.badge variant="outline" class="mt-2 text-caption">{{ $qual->type }}</x-ui.badge>
                    </div>
                    <div class="p-4 space-y-4">
                        @if(in_array($qual->type, ['single_choice', 'multi_choice']))
                            <div>
                                <h4 class="text-body-sm-strong text-ink mb-3">Pilihan Jawaban</h4>
                                @if($qual->options->isNotEmpty())
                                    <div class="space-y-2 mb-4">
                                        @foreach($qual->options as $option)
                                            <div class="flex items-center justify-between rounded-sm bg-canvas-soft border border-hairline px-3 py-2 text-body-sm">
                                                <span class="text-ink">{{ $option->label }}</span>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-caption text-mute font-medium">Skor: {{ $option->value }}</span>
                                                    @unless($isLocked)
                                                    <button wire:click="editOption({{ $option->id }})" class="text-mute hover:text-ink transition-colors"><x-lucide-pencil class="size-3.5" /></button>
                                                    <button wire:click="deleteOption({{ $option->id }})" class="text-mute hover:text-error transition-colors"><x-lucide-trash-2 class="size-3.5" /></button>
                                                    @endunless
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @unless($isLocked)
                                    <form wire:submit="saveOption" class="space-y-3 border-t border-hairline pt-4">
                                        <input type="hidden" wire:model="optionQualId" value="{{ $qual->id }}">
                                        <x-ui.input wire:model="optionLabel" placeholder="Label pilihan..." class="text-body-sm" />
                                        <div class="flex gap-2">
                                            <x-ui.input type="number" wire:model="optionValue" placeholder="Skor" class="w-20 text-body-sm" />
                                            <x-ui.input wire:model="optionDescription" placeholder="Deskripsi (opsional)" class="text-body-sm flex-1" />
                                        </div>
                                        <div class="flex gap-2 pt-1">
                                            <x-ui.button type="submit" size="sm" variant="secondary">{{ $editingOptionId ? 'Update' : 'Tambah' }}</x-ui.button>
                                            @if($editingOptionId)
                                                <x-ui.button type="button" size="sm" variant="ghost" wire:click="cancelEditOption">Batal</x-ui.button>
                                            @endif
                                        </div>
                                    </form>
                                @endunless
                            </div>
                        @endif

                        @if($qual->type === 'numeric_range')
                            <div>
                                <h4 class="text-body-sm-strong text-ink mb-3">Range Nilai</h4>
                                @if($qual->ranges->isNotEmpty())
                                    <div class="space-y-2 mb-4">
                                        @foreach($qual->ranges as $range)
                                            <div class="flex items-center justify-between rounded-sm bg-canvas-soft border border-hairline px-3 py-2 text-body-sm">
                                                <span class="text-ink font-mono text-caption">{{ $range->range_min }} — {{ $range->range_max }}</span>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-caption text-mute font-medium">Skor: {{ $range->value }}</span>
                                                    @unless($isLocked)
                                                    <button wire:click="editRange({{ $range->id }})" class="text-mute hover:text-ink transition-colors"><x-lucide-pencil class="size-3.5" /></button>
                                                    <button wire:click="deleteRange({{ $range->id }})" class="text-mute hover:text-error transition-colors"><x-lucide-trash-2 class="size-3.5" /></button>
                                                    @endunless
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @unless($isLocked)
                                    <form wire:submit="saveRange" class="space-y-3 border-t border-hairline pt-4">
                                        <input type="hidden" wire:model="rangeQualId" value="{{ $qual->id }}">
                                        <div class="flex gap-2">
                                            <x-ui.input type="number" wire:model="rangeMin" step="0.01" placeholder="Min" class="w-24 text-body-sm" />
                                            <x-ui.input type="number" wire:model="rangeMax" step="0.01" placeholder="Max" class="w-24 text-body-sm" />
                                            <x-ui.input type="number" wire:model="rangeValue" placeholder="Skor" class="w-20 text-body-sm" />
                                        </div>
                                        <div class="flex gap-2">
                                            <x-ui.input wire:model="rangeLabel" placeholder="Label (opsional)" class="text-body-sm flex-1" />
                                        </div>
                                        <div class="flex gap-2 pt-1">
                                            <x-ui.button type="submit" size="sm" variant="secondary">{{ $editingRangeId ? 'Update' : 'Tambah' }}</x-ui.button>
                                            @if($editingRangeId)
                                                <x-ui.button type="button" size="sm" variant="ghost" wire:click="cancelEditRange">Batal</x-ui.button>
                                            @endif
                                        </div>
                                    </form>
                                @endunless
                            </div>
                        @endif
                    </div>
                </x-ui.card>
            @else
                <x-ui.card class="animate-scale-in">
                    <x-ui.empty-state icon="mouse-pointer-click" title="Pilih Pertanyaan" description="Klik salah satu pertanyaan di panel kiri untuk mengelola pilihan dan skornya." />
                </x-ui.card>
            @endif
        </div>
    </div>

    {{-- Group Form Modal --}}
    @if($showGroupForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" wire:click="$set('showGroupForm', false)"></div>
            <div class="relative bg-canvas border border-hairline rounded-lg shadow-level-5 w-full max-w-md mx-4 p-6 animate-scale-in">
                <h3 class="text-display-sm text-ink">{{ $editingGroupId ? 'Edit' : 'Tambah' }} Grup Kualifikasi</h3>
                <form wire:submit="saveGroup" class="mt-6 space-y-4">
                    <x-ui.form-group label="Nama Grup" required>
                        <x-ui.input wire:model="groupName" placeholder="Contoh: Data Pribadi" />
                    </x-ui.form-group>
                    <x-ui.form-group label="Deskripsi">
                        <x-ui.textarea wire:model="groupDescription" rows="2" placeholder="Penjelasan grup..." />
                    </x-ui.form-group>
                    <div class="flex justify-end gap-3 pt-6">
                        <x-ui.button variant="outline" type="button" wire:click="$set('showGroupForm', false)">Batal</x-ui.button>
                        <x-ui.button variant="primary" type="submit">Simpan</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Qualification Form Modal --}}
    @if($showQualForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" wire:click="$set('showQualForm', false)"></div>
            <div class="relative bg-canvas border border-hairline rounded-lg shadow-level-5 w-full max-w-lg mx-4 my-8 p-6 animate-scale-in">
                <h3 class="text-display-sm text-ink">{{ $editingQualId ? 'Edit' : 'Tambah' }} Pertanyaan</h3>
                <form wire:submit="saveQualification" class="mt-6 space-y-4">
                    <x-ui.form-group label="Nama Pertanyaan" required>
                        <x-ui.input wire:model="qualName" placeholder="Contoh: Nama Lengkap" />
                    </x-ui.form-group>
                    <x-ui.form-group label="Tipe">
                        <x-ui.select wire:model="qualType">
                            <option value="single_choice">Pilihan Tunggal</option>
                            <option value="multi_choice">Pilihan Ganda</option>
                            <option value="numeric_range">Rentang Numerik</option>
                            <option value="file_upload">Upload File</option>
                            <option value="text">Teks</option>
                        </x-ui.select>
                    </x-ui.form-group>
                    <x-ui.form-group label="Deskripsi">
                        <x-ui.textarea wire:model="qualDescription" rows="2" placeholder="Hint yang ditampilkan ke pendaftar..." />
                    </x-ui.form-group>
                    <x-ui.form-group label="Upload Label">
                        <x-ui.input wire:model="fileUploadLabel" placeholder="Contoh: Upload Bukti DTKS" />
                    </x-ui.form-group>
                    <div class="flex flex-wrap gap-4 pt-2 border-t border-hairline">
                        <label class="flex items-center gap-2 text-body-sm cursor-pointer">
                            <input type="checkbox" wire:model="isRequired" class="size-4 rounded-sm border-hairline accent-primary"> Wajib Diisi
                        </label>
                        <label class="flex items-center gap-2 text-body-sm cursor-pointer">
                            <input type="checkbox" wire:model="isFileUploadRequired" class="size-4 rounded-sm border-hairline accent-primary"> Perlu Upload Dokumen
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 pt-6">
                        <x-ui.button variant="outline" type="button" wire:click="$set('showQualForm', false)">Batal</x-ui.button>
                        <x-ui.button variant="primary" type="submit">Simpan</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
