<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $nik = '';
    public string $phone = '';
    public array $selectedRoles = [];
    public string $roleFilter = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->editingId ? ",{$this->editingId}" : ''),
            'password' => $this->editingId ? 'nullable|string|min:8' : 'required|string|min:8',
            'nik' => 'required|string|min:16|max:16' . ($this->editingId ? "|unique:users,nik,{$this->editingId}" : '|unique:users,nik'),
            'phone' => 'nullable|string|max:20',
            'selectedRoles' => 'array',
        ];
    }

    public function render()
    {
        $roles = Role::all();
        $users = User::with('roles')
            ->when($this->search, fn($q) => $q->where('name', 'ilike', '%' . $this->search . '%')
                ->orWhere('email', 'ilike', '%' . $this->search . '%'))
            ->when($this->roleFilter, fn($q) => $q->role($this->roleFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.user-manager', compact('users', 'roles'))
            ->layout('components.layouts.app', ['title' => 'Manajemen Pengguna']);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::with('roles')->findOrFail($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->nik = $user->nik;
        $this->phone = $user->phone ?? '';
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'nik' => $data['nik'],
                'phone' => $data['phone'],
            ]);
            if ($data['password']) {
                $user->update(['password' => bcrypt($data['password'])]);
            }
        } else {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'nik' => $data['nik'],
                'phone' => $data['phone'],
            ]);
        }

        $user->syncRoles($data['selectedRoles']);

        $this->resetForm();
        $this->dispatch('notify', type: 'success', message: 'Pengguna ' . ($this->editingId ? 'diperbarui' : 'dibuat') . '.');
    }

    public function deleteUser(int $id): void
    {
        User::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Pengguna dihapus.');
    }

    public function closeForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->nik = '';
        $this->phone = '';
        $this->selectedRoles = [];
    }
}
