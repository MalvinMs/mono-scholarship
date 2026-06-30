<?php

namespace App\Http\Controllers\Api\Verifier;

use App\Actions\Verification\ApproveAnswer;
use App\Actions\Verification\CorrectAnswer;
use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use App\Models\ApplicationAnswer;
use Illuminate\Http\Request;

class AnswerActionController extends BaseController
{
    public function approve(Application $application, ApplicationAnswer $answer, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewApplicationDetail', $application);
        abort_if($answer->application_id !== $application->id, 404);

        app(ApproveAnswer::class)->execute($answer, $request->user());

        return $this->success(null, 'Jawaban berhasil disetujui.');
    }

    public function correct(Application $application, ApplicationAnswer $answer, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewApplicationDetail', $application);
        abort_if($answer->application_id !== $application->id, 404);

        $request->validate([
            'value' => ['required'],
            'reason' => ['required', 'string', 'min:5'],
        ]);

        app(CorrectAnswer::class)->execute($answer, $request->user(), $request->value, $request->reason);

        return $this->success(null, 'Jawaban berhasil dikoreksi.');
    }
}
