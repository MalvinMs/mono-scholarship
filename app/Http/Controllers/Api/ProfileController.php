<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends BaseController
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->success(new \App\Http\Resources\Api\UserResource($request->user()));
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'village' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', Rule::in(['SMA', 'SMK', 'MA', 'PAKET_C', 'D3', 'D4', 'S1', 'S2'])],
            'school_name' => ['nullable', 'string', 'max:255'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'university_name' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'current_semester' => ['nullable', 'integer', 'min:1', 'max:14'],
        ]);

        $user->update($validated);

        return $this->success(
            new \App\Http\Resources\Api\UserResource($user->fresh()),
            'Profil berhasil diperbarui.'
        );
    }

    public function updatePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->error('Password saat ini tidak sesuai.', 422);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        // Revoke all tokens except current
        $user->tokens()
            ->where('id', '!=', $request->user()->currentAccessToken()->id)
            ->delete();

        return $this->success(null, 'Password berhasil diubah.');
    }
}
