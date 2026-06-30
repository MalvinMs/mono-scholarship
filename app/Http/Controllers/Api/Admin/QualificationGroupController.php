<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\QualificationGroup;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class QualificationGroupController extends BaseController
{
    public function index(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        return $this->success(
            $scholarship->qualificationGroups()->orderBy('sort_order')->get()
        );
    }

    public function store(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $group = $scholarship->qualificationGroups()->create($validated);

        return $this->success($group, 'Grup kualifikasi berhasil dibuat.', 201);
    }

    public function update(Scholarship $scholarship, QualificationGroup $group, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $group->update($validated);

        return $this->success($group, 'Grup kualifikasi berhasil diperbarui.');
    }

    public function destroy(Scholarship $scholarship, QualificationGroup $group): \Illuminate\Http\JsonResponse
    {
        $group->delete();
        return $this->success(null, 'Grup kualifikasi berhasil dihapus.');
    }
}
