<?php

namespace App\Actions\Verification;

use App\Models\Application;
use App\Services\ScoringEngine;

final class FinalizeApplicantScore
{
    public function execute(Application $application): void
    {
        $application->load([
            'scholarship.qualifications.options',
            'scholarship.qualifications.ranges',
            'answers.selectedOption',
        ]);

        $scoreResult = app(ScoringEngine::class)->calculate($application);

        $score = $application->score;

        if ($score) {
            $score->update([
                'score_breakdown' => $scoreResult->breakdown,
                'total_score' => $scoreResult->total,
                'max_possible_score' => $scoreResult->max,
                'is_final' => true,
                'calculated_at' => now(),
            ]);
        } else {
            $application->score()->create([
                'scholarship_id' => $application->scholarship_id,
                'score_breakdown' => $scoreResult->breakdown,
                'total_score' => $scoreResult->total,
                'max_possible_score' => $scoreResult->max,
                'is_final' => true,
                'calculated_at' => now(),
            ]);
        }

        $application->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
    }
}
