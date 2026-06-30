<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Qualification;
use App\Models\QualificationRange;
use Illuminate\Http\Request;

class QualificationRangeController extends BaseController
{
    public function store(Qualification $qualification, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'range_min' => ['required', 'numeric'],
            'range_max' => ['required', 'numeric'],
            'value' => ['required', 'integer'],
            'label' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $range = $qualification->ranges()->create($validated);

        return $this->success($range, 'Rentang nilai berhasil dibuat.', 201);
    }

    public function update(Qualification $qualification, QualificationRange $range, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'range_min' => ['sometimes', 'numeric'],
            'range_max' => ['sometimes', 'numeric'],
            'value' => ['sometimes', 'integer'],
            'label' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $range->update($validated);

        return $this->success($range, 'Rentang nilai berhasil diperbarui.');
    }

    public function destroy(Qualification $qualification, QualificationRange $range): \Illuminate\Http\JsonResponse
    {
        $range->delete();
        return $this->success(null, 'Rentang nilai berhasil dihapus.');
    }
}
