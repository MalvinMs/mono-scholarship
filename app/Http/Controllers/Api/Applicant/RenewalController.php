<?php

namespace App\Http\Controllers\Api\Applicant;

use App\Actions\Application\SubmitApplication;
use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use Illuminate\Http\Request;

class RenewalController extends BaseController
{
    public function store(Application $application, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($application->user_id !== $request->user()->id) {
            return $this->error('Tidak memiliki akses.', 403);
        }

        $request->validate([
            'gpa' => ['required', 'numeric', 'min:0', 'max:4.0'],
            'transcript' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
        ]);

        $scholarship = $application->scholarship;

        if (!$scholarship || $scholarship->status !== 'renewal_open') {
            return $this->error('Program perpanjangan tidak tersedia.', 400);
        }

        $successor = $scholarship->successor()->first();

        if (!$successor || !in_array($successor->status, ['renewal_open', 'open'])) {
            return $this->error('Program beasiswa untuk perpanjangan tidak ditemukan.', 404);
        }

        $renewalApp = app(SubmitApplication::class)->execute(
            $successor,
            $request->user(),
            [],
            ['transcript' => $request->file('transcript')],
            false
        );

        $renewalApp->update([
            'is_renewal' => true,
            'previous_application_id' => $application->id,
        ]);

        return $this->success([
            'id' => $renewalApp->id,
            'registration_number' => $renewalApp->registration_number,
            'status' => $renewalApp->status,
            'is_renewal' => true,
        ], 'Perpanjangan berhasil dikirim.', 201);
    }
}
