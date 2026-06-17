<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-display-xs text-ink">Program Beasiswa</h1>
            <p class="mt-2 text-body-sm text-mute">Kelola semua program beasiswa di platform.</p>
        </div>
        <x-ui.button variant="primary" wire:click="create">
            <x-lucide-plus class="size-4" />Tambah Program
        </x-ui.button>
    </div>

    @if(session()->has('message'))
        <x-ui.alert variant="success" dismissible class="mb-4">{{ session('message') }}</x-ui.alert>
    @endif

    @if($showForm)
        <div class="mb-6 max-w-4xl animate-slide-in-from-top">
            <x-ui.card padding="none">
                <div class="border-b border-hairline px-6 py-4">
                    <h2 class="text-display-xs text-ink">{{ $isEditing ? 'Edit' : 'Tambah' }} Program Beasiswa</h2>
                    <p class="text-body-sm text-mute mt-1">Isi detail program beasiswa di bawah ini.</p>
                </div>
                
                <form wire:submit="save">
                    <div class="p-6 space-y-8">
                        {{-- Section 1: Informasi Dasar --}}
                        <div>
                            <h3 class="text-body-lg text-ink mb-4 font-medium">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-ui.form-group label="Nama Program" required>
                                    <x-ui.input wire:model="name" />
                                </x-ui.form-group>
                                <x-ui.form-group label="Tahun Anggaran" required>
                                    <x-ui.input wire:model="academic_year" placeholder="2025/2026" />
                                </x-ui.form-group>
                                <div class="md:col-span-2">
                                    <x-ui.form-group label="Deskripsi">
                                        <x-ui.textarea wire:model="description" rows="3" />
                                    </x-ui.form-group>
                                </div>
                            </div>
                        </div>

                        <hr class="border-hairline" />

                        {{-- Section 2: Kuota & Dana --}}
                        <div>
                            <h3 class="text-body-lg text-ink mb-4 font-medium">Kuota & Alokasi Dana</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-ui.form-group label="Kuota Utama" required>
                                    <x-ui.input type="number" wire:model="quota_primary" />
                                </x-ui.form-group>
                                <x-ui.form-group label="Kuota Cadangan">
                                    <x-ui.input type="number" wire:model="quota_reserve" />
                                </x-ui.form-group>
                                <x-ui.form-group label="Besaran Dana (Rp)">
                                    <x-ui.input type="number" wire:model="fund_amount" />
                                </x-ui.form-group>
                                <x-ui.form-group label="Program Sebelumnya (Renewal)">
                                    <x-ui.select wire:model="predecessor_scholarship_id">
                                        <option value="">— Tidak ada —</option>
                                        @foreach($existingScholarships as $s)
                                            @if(!$isEditing || $s->id != $scholarshipId)
                                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->academic_year }})</option>
                                            @endif
                                        @endforeach
                                    </x-ui.select>
                                </x-ui.form-group>
                            </div>
                        </div>

                        <hr class="border-hairline" />

                        {{-- Section 3: Timeline & Status --}}
                        <div>
                            <h3 class="text-body-lg text-ink mb-4 font-medium">Timeline & Status</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-ui.form-group label="Tanggal Buka">
                                    <x-ui.input type="date" wire:model="date_start" />
                                </x-ui.form-group>
                                <x-ui.form-group label="Tanggal Tutup">
                                    <x-ui.input type="date" wire:model="date_end" />
                                </x-ui.form-group>
                                <div class="md:col-span-2">
                                    <x-ui.form-group label="Status">
                                        <x-ui.select wire:model="status" class="max-w-xs">
                                            <option value="draft">Draft</option>
                                            <option value="open">Terbuka</option>
                                            <option value="closed">Tertutup</option>
                                            <option value="selecting">Seleksi</option>
                                            <option value="announced">Diumumkan</option>
                                        </x-ui.select>
                                    </x-ui.form-group>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-canvas-soft px-6 py-4 flex items-center gap-3 border-t border-hairline rounded-b-md">
                        <x-ui.button variant="primary" type="submit">{{ $isEditing ? 'Simpan Perubahan' : 'Buat Program' }}</x-ui.button>
                        <x-ui.button variant="outline" type="button" wire:click="$set('showForm', false)">Batal</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    @endif

    <x-ui.card padding="none">
        <x-ui.table>
            <x-slot:header>
                <x-ui.table.tr>
                    <x-ui.table.th>Nama</x-ui.table.th>
                    <x-ui.table.th>Tahun</x-ui.table.th>
                    <x-ui.table.th>Kuota</x-ui.table.th>
                    <x-ui.table.th>Status</x-ui.table.th>
                    <x-ui.table.th>Aksi</x-ui.table.th>
                    <x-ui.table.th class="w-[160px]">Konfigurasi</x-ui.table.th>
                </x-ui.table.tr>
            </x-slot>
            @foreach($scholarships as $scholarship)
                <x-ui.table.tr>
                    <x-ui.table.td>
                        <div class="font-medium text-ink">{{ $scholarship->name }}</div>
                        @if($scholarship->predecessor)
                            <div class="text-caption text-mute mt-0.5">Renewal: {{ $scholarship->predecessor->name }}</div>
                        @endif
                    </x-ui.table.td>
                    <x-ui.table.td class="text-mute">{{ $scholarship->academic_year }}</x-ui.table.td>
                    <x-ui.table.td>{{ $scholarship->quota_primary }}</x-ui.table.td>
                    <x-ui.table.td>
                        @php
                            $statusVariant = [
                                'draft' => 'secondary',
                                'open' => 'success',
                                'closed' => 'destructive',
                                'announced' => 'default',
                            ][$scholarship->status] ?? 'warning';
                        @endphp
                        <x-ui.badge variant="{{ $statusVariant }}">{{ ucfirst($scholarship->status) }}</x-ui.badge>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <div class="flex items-center gap-1">
                            <x-ui.button variant="ghost" size="sm" wire:click="edit({{ $scholarship->id }})">Edit</x-ui.button>
                            <x-ui.button variant="ghost" size="sm" wire:click="delete({{ $scholarship->id }})" wire:confirm="Hapus program ini?" class="text-error hover:text-error-deep">Hapus</x-ui.button>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <div class="flex items-center gap-1 flex-wrap">
                            <a href="{{ route('admin.qualifications', $scholarship->id) }}" wire:navigate class="inline-flex items-center gap-1 rounded-sm px-2 py-1 text-caption font-medium bg-canvas-soft border border-hairline text-ink hover:bg-canvas-soft-2 transition-colors">
                                <x-lucide-list-checks class="size-3" />
                                Kualifikasi
                            </a>
                            <a href="{{ route('admin.verifiers', $scholarship->id) }}" wire:navigate class="inline-flex items-center gap-1 rounded-sm px-2 py-1 text-caption font-medium bg-canvas-soft border border-hairline text-ink hover:bg-canvas-soft-2 transition-colors">
                                <x-lucide-user-check class="size-3" />
                                Verifikator
                            </a>
                            <a href="{{ route('admin.tiebreaker', $scholarship->id) }}" wire:navigate class="inline-flex items-center gap-1 rounded-sm px-2 py-1 text-caption font-medium bg-canvas-soft border border-hairline text-ink hover:bg-canvas-soft-2 transition-colors">
                                <x-lucide-arrow-up-down class="size-3" />
                                Tie-breaker
                            </a>
                        </div>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @endforeach
        </x-ui.table>
        <div class="p-4 border-t border-hairline">
            {{ $scholarships->links() }}
        </div>
    </x-ui.card>
</div>