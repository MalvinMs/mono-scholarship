<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;

class VerifierAssignmentController extends BaseController
{
    public function index(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        return $this->success(
            $scholarship->verifiers()->with('user:id,name,email')->get()
        );
    }

    public function store(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $verifier = $scholarship->verifiers()->firstOrCreate(
            ['user_id' => $request->user_id],
            [
                'assigned_by' => $request->user()->id,
                'assigned_at' => now(),
            ]
        );

        return $this->success($verifier, 'Verifikator berhasil ditugaskan.', 201);
    }

    public function destroy(Scholarship $scholarship, User $user): \Illuminate\Http\JsonResponse
    {
        $scholarship->verifiers()->where('user_id', $user->id)->delete();
        return $this->success(null, 'Verifikator berhasil dihapus.');
    }
}
