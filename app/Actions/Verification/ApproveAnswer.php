<?php

namespace App\Actions\Verification;

use App\Models\ApplicationAnswer;
use App\Models\VerificationLog;
use App\Models\User;
use App\Services\ScoringEngine;

final class ApproveAnswer
{
    public function execute(ApplicationAnswer $answer, User $verifier): void
    {
        $qualification = $answer->qualification;

        // Recalculate score to ensure computed_score is correct
        $scoringEngine = app(ScoringEngine::class);
        $answer->computed_score = $scoringEngine->resolveAnswerScore($answer);
        $answer->save();

        VerificationLog::create([
            'application_id' => $answer->application_id,
            'verifier_id' => $verifier->id,
            'action' => 'answer_approved',
            'target_type' => 'answer',
            'target_id' => $answer->id,
            'field_changed' => $qualification?->name ?? 'Answer',
            'old_value' => 0,
            'new_value' => (string) $answer->computed_score,
            'reason' => 'Jawaban telah diverifikasi dan disetujui. Skor: ' . $answer->computed_score,
            'created_at' => now(),
        ]);
    }
}