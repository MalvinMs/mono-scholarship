<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class TiebreakerController extends BaseController
{
    public function show(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        $scholarship->load('qualifications:id,name,scholarship_id');

        return $this->success([
            'tiebreaker_config' => $scholarship->tiebreaker_config,
            'qualifications' => $scholarship->qualifications->map(fn ($q) => [
                'id' => $q->id,
                'name' => $q->name,
            ]),
        ]);
    }

    public function update(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'tiebreaker_config' => ['required', 'array'],
            'tiebreaker_config.*.priority' => ['required', 'integer', 'min:1'],
            'tiebreaker_config.*.qualification_id' => ['required', 'integer', 'exists:qualifications,id'],
        ]);

        $scholarship->update(['tiebreaker_config' => $request->tiebreaker_config]);

        return $this->success($scholarship->tiebreaker_config, 'Konfigurasi tiebreaker berhasil disimpan.');
    }
}
