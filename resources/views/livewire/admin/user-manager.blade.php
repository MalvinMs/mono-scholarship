<div>
    <div class="flex items-center justify-between mb-8 animate-fade-in">
        <div>
            <h1 class="text-display-xs text-ink">Manajemen Pengguna</h1>
            <p class="mt-2 text-body-sm text-mute">Kelola semua akun pengguna platform.</p>
        </div>
        <x-ui.button variant="primary" wire:click="openCreate">
            <x-lucide-user-plus class="size-4" />
            Tambah Pengguna
        </x-ui.button>
    </div>

    <div class="flex flex-wrap gap-4 mb-6">
        <div class="max-w-xs flex-1">
            <x-ui.input wire:model.live="search" placeholder="Cari pengguna..." />
        </div>
        <div class="max-w-[200px]">
            <x-ui.select wire:model.live="roleFilter">
                <option value="">Semua Role</option>
                <option value="super-admin">Super Admin</option>
                <option value="admin">Admin</option>
                <option value="verifier">Verifikator</option>
                <option value="approver">Approver</option>
                <option value="treasurer">Bendahara</option>
                <option value="applicant">Pendaftar</option>
            </x-ui.select>
        </div>
    </div>

    <x-ui.card padding="none" class="animate-scale-in">
        <div class="overflow-x-auto">
            <x-ui.table>
                <x-slot:header>
                    <x-ui.table.tr>
                        <x-ui.table.th>Nama</x-ui.table.th>
                        <x-ui.table.th>Email</x-ui.table.th>
                        <x-ui.table.th>Role</x-ui.table.th>
                        <x-ui.table.th>Status</x-ui.table.th>
                        <x-ui.table.th class="w-[120px]">Tindakan</x-ui.table.th>
                    </x-ui.table.tr>
                </x-slot>
                @forelse($users as $user)
                    <x-ui.table.tr>
                        <x-ui.table.td class="font-medium text-ink">{{ $user->name }}</x-ui.table.td>
                        <x-ui.table.td class="text-mute">{{ $user->email }}</x-ui.table.td>
                        <x-ui.table.td>
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->getRoleNames() as $role)
                                    <x-ui.badge variant="secondary" class="text-caption">{{ $role }}</x-ui.badge>
                                @endforeach
                            </div>
                        </x-ui.table.td>
                        <x-ui.table.td>
                            @if($user->is_blacklisted)
                                <x-ui.badge variant="destructive">Blacklist</x-ui.badge>
                            @else
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @endif
                        </x-ui.table.td>
                        <x-ui.table.td>
                            <div class="flex gap-1">
                                <x-ui.button size="sm" variant="outline" wire:click="openEdit({{ $user->id }})">Edit</x-ui.button>
                                <x-ui.button size="sm" variant="ghost" class="text-error hover:text-error-deep" wire:click="deleteUser({{ $user->id }})" wire:confirm="Hapus pengguna ini?">Hapus</x-ui.button>
                            </div>
                        </x-ui.table.td>
                    </x-ui.table.tr>
                @empty
                    <x-ui.table.tr>
                        <x-ui.table.td colspan="5">
                            <div class="py-10">
                                <x-ui.empty-state icon="users" title="Tidak ada pengguna" description="Belum ada pengguna yang terdaftar." />
                            </div>
                        </x-ui.table.td>
                    </x-ui.table.tr>
                @endforelse
            </x-ui.table>
        </div>
        <div class="border-t border-hairline px-4 py-3">
            {{ $users->links() }}
        </div>
    </x-ui.card>

    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeForm"></div>
            <div class="relative bg-canvas border border-hairline rounded-lg shadow-level-5 w-full max-w-lg mx-4 my-8 p-6 animate-scale-in">
                <h3 class="text-display-sm text-ink">{{ $editingId ? 'Edit' : 'Tambah' }} Pengguna</h3>

                <form wire:submit="save" class="mt-6 space-y-4">
                    <x-ui.form-group label="Nama" required>
                        <x-ui.input wire:model="name" placeholder="Nama lengkap" />
                    </x-ui.form-group>

                    <x-ui.form-group label="NIK" required>
                        <x-ui.input wire:model="nik" placeholder="16 digit NIK" />
                    </x-ui.form-group>

                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.form-group label="Email" required>
                            <x-ui.input type="email" wire:model="email" placeholder="email@example.com" />
                        </x-ui.form-group>
                        <x-ui.form-group label="Telepon">
                            <x-ui.input wire:model="phone" placeholder="081234567890" />
                        </x-ui.form-group>
                    </div>

                    <x-ui.form-group label="Password" :required="!$editingId">
                        <x-ui.input type="password" wire:model="password" :placeholder="$editingId ? 'Biarkan kosong jika tidak diubah' : 'Minimal 8 karakter'" />
                    </x-ui.form-group>

                    <x-ui.form-group label="Role">
                        <div class="flex flex-wrap gap-2">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2 text-body-sm cursor-pointer">
                                    <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" class="size-4 rounded-sm border-hairline accent-primary">
                                    {{ $role->name }}
                                </label>
                            @endforeach
                        </div>
                    </x-ui.form-group>

                    <div class="flex justify-end gap-3 pt-6">
                        <x-ui.button variant="outline" type="button" wire:click="closeForm">Batal</x-ui.button>
                        <x-ui.button variant="primary" type="submit">
                            <x-lucide-save class="size-4" />
                            {{ $editingId ? 'Simpan' : 'Tambah' }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
