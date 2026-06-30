<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Qualification;
use App\Models\QualificationOption;
use Illuminate\Http\Request;

class QualificationOptionController extends BaseController
{
    public function store(Qualification $qualification, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'value' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $option = $qualification->options()->create($validated);

        return $this->success($option, 'Opsi berhasil dibuat.', 201);
    }

    public function update(Qualification $qualification, QualificationOption $option, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'label' => ['sometimes', 'string', 'max:255'],
            'value' => ['sometimes', 'integer'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $option->update($validated);

        return $this->success($option, 'Opsi berhasil diperbarui.');
    }

    public function destroy(Qualification $qualification, QualificationOption $option): \Illuminate\Http\JsonResponse
    {
        $option->delete();
        return $this->success(null, 'Opsi berhasil dihapus.');
    }
}
