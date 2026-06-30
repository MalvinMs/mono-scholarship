<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Qualification;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class QualificationController extends BaseController
{
    public function index(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        return $this->success(
            $scholarship->qualifications()
                ->with(['group:id,name', 'options', 'ranges'])
                ->orderBy('sort_order')
                ->get()
        );
    }

    public function store(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'qualification_group_id' => ['nullable', 'exists:qualification_groups,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:single_choice,multi_choice,numeric_range,text,file_upload'],
            'is_required' => ['sometimes', 'boolean'],
            'is_file_upload_required' => ['sometimes', 'boolean'],
            'file_upload_label' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $qual = $scholarship->qualifications()->create($validated);

        return $this->success($qual, 'Kualifikasi berhasil dibuat.', 201);
    }

    public function update(Qualification $qualification, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'qualification_group_id' => ['nullable', 'exists:qualification_groups,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'in:single_choice,multi_choice,numeric_range,text,file_upload'],
            'is_required' => ['sometimes', 'boolean'],
            'is_file_upload_required' => ['sometimes', 'boolean'],
            'file_upload_label' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $qualification->update($validated);

        return $this->success($qualification, 'Kualifikasi berhasil diperbarui.');
    }

    public function destroy(Qualification $qualification): \Illuminate\Http\JsonResponse
    {
        $qualification->delete();
        return $this->success(null, 'Kualifikasi berhasil dihapus.');
    }
}
