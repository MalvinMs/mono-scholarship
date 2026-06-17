<?php

namespace App\Actions\Verification;

use App\Models\ApplicationAnswer;
use App\Models\VerificationLog;
use App\Models\User;
use App\Services\ScoringEngine;

final class CorrectAnswer
{
    public function execute(ApplicationAnswer $answer, User $verifier, mixed $newValue, string $reason): void
    {
        $qualification = $answer->qualification;

        $oldValue = match ($qualification->type) {
            'single_choice' => (string) $answer->selected_option_id,
            'multi_choice' => json_encode($answer->selected_option_ids),
            'numeric_range' => (string) $answer->numeric_value,
            'text' => $answer->text_value,
            default => '',
        };

        if ($qualification->type === 'single_choice') {
            $answer->original_selected_option_id = $answer->selected_option_id;
            $answer->selected_option_id = $newValue;
        } elseif ($qualification->type === 'multi_choice') {
            $answer->selected_option_ids = is_array($newValue) ? $newValue : [$newValue];
        } elseif ($qualification->type === 'numeric_range') {
            $answer->original_numeric_value = $answer->numeric_value;
            $answer->numeric_value = $newValue;
        } elseif ($qualification->type === 'text') {
            $answer->text_value = $newValue;
        }

        $answer->is_corrected_by_verifier = true;
        $answer->corrected_by = $verifier->id;
        $answer->corrected_at = now();

        $scoringEngine = app(ScoringEngine::class);
        $answer->computed_score = $scoringEngine->resolveAnswerScore($answer);

        $answer->save();

        VerificationLog::create([
            'application_id' => $answer->application_id,
            'verifier_id' => $verifier->id,
            'action' => 'answer_corrected',
            'target_type' => 'answer',
            'target_id' => $answer->id,
            'field_changed' => $qualification->name,
            'old_value' => $oldValue,
            'new_value' => is_array($newValue) ? json_encode($newValue) : (string) $newValue,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}
