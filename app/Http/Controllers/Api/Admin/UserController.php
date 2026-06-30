<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends BaseController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = User::query();

        if ($request->filled('filter.role')) {
            $query->role($request->input('filter.role'));
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('nik', 'like', "%{$q}%");
            });
        }

        return $this->success(
            $query->orderBy('name')
                ->paginate($request->input('per_page', 15))
                ->through(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'phone' => $u->phone,
                    'roles' => $u->getRoleNames(),
                    'is_active' => $u->is_active,
                    'is_blacklisted' => $u->is_blacklisted,
                    'created_at' => $u->created_at?->format('Y-m-d H:i:s'),
                ])
        );
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nik' => ['nullable', 'string', 'unique:users,nik'],
            'role' => ['required', 'string', Rule::in(['applicant', 'verifier', 'admin', 'approver', 'treasurer'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'nik' => $validated['nik'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return $this->success(new \App\Http\Resources\Api\UserResource($user), 'Pengguna berhasil dibuat.', 201);
    }

    public function show(User $user): \Illuminate\Http\JsonResponse
    {
        return $this->success(new \App\Http\Resources\Api\UserResource($user));
    }

    public function update(User $user, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'nik' => ['nullable', 'string', Rule::unique('users')->ignore($user->id)],
            'role' => ['sometimes', 'string', Rule::in(['applicant', 'verifier', 'admin', 'approver', 'treasurer', 'super-admin'])],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return $this->success(new \App\Http\Resources\Api\UserResource($user->fresh()), 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): \Illuminate\Http\JsonResponse
    {
        $user->delete();
        return $this->success(null, 'Pengguna berhasil dihapus.');
    }
}
