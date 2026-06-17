<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Qualification;
use App\Models\Scholarship;

final class ScoringEngine
{
    public function calculate(Application $application): ScoreResult
    {
        $scholarship = $application->scholarship;
        $scholarship->load(['qualifications.options', 'qualifications.ranges']);
        $application->load('answers');

        $breakdown = [];
        $total = 0;

        foreach ($scholarship->qualifications as $qualification) {
            $answer = $application->answers->firstWhere('qualification_id', $qualification->id);
            $score = $this->resolveScore($qualification, $answer);

            $breakdown[$qualification->id] = [
                'name' => $qualification->name,
                'answer_label' => $this->resolveLabel($qualification, $answer),
                'score' => $score,
            ];

            $total += $score;
        }

        return new ScoreResult(
            total: $total,
            max: $this->calculateMax($scholarship),
            breakdown: $breakdown,
        );
    }

    public function resolveAnswerScore(\App\Models\ApplicationAnswer $answer): int
    {
        return $this->resolveScore($answer->qualification, $answer);
    }

    private function resolveScore(Qualification $q, ?\App\Models\ApplicationAnswer $answer): int
    {
        return match ($q->type) {
            'single_choice' => $answer?->selectedOption?->value ?? 0,
            'multi_choice' => $this->resolveMultiChoiceScore($answer),
            'numeric_range' => $this->resolveRangeScore($q, $answer?->numeric_value),
            'file_upload', 'text' => 0,
        };
    }

    private function resolveMultiChoiceScore(?\App\Models\ApplicationAnswer $answer): int
    {
        if (!$answer || empty($answer->selected_option_ids)) {
            return 0;
        }

        return \App\Models\QualificationOption::whereIn('id', $answer->selected_option_ids)
            ->max('value') ?? 0;
    }

    private function resolveRangeScore(Qualification $q, mixed $value): int
    {
        if ($value === null) {
            return 0;
        }

        $numericValue = (float) $value;

        return $q->ranges
            ->first(fn($r) => $numericValue >= $r->range_min && $numericValue <= $r->range_max)
            ?->value ?? 0;
    }

    private function resolveLabel(Qualification $q, ?\App\Models\ApplicationAnswer $answer): string
    {
        return match ($q->type) {
            'single_choice' => $answer?->selectedOption?->label ?? '-',
            'multi_choice' => $answer
                ? \App\Models\QualificationOption::whereIn('id', $answer->selected_option_ids ?? [])
                    ->pluck('label')->join(', ')
                : '-',
            'numeric_range' => $answer?->numeric_value !== null ? (string) $answer->numeric_value : '-',
            'file_upload' => '-',
            'text' => $answer?->text_value ?? '-',
        };
    }

    private function calculateMax(Scholarship $scholarship): int
    {
        return $scholarship->qualifications->sum(function ($q) {
            return match ($q->type) {
                'single_choice' => $q->options->max('value') ?? 0,
                'multi_choice' => $q->options->max('value') ?? 0,
                'numeric_range' => $q->ranges->max('value') ?? 0,
                default => 0,
            };
        });
    }

    public function validateRanges(Qualification $qualification): bool
    {
        $ranges = $qualification->ranges()->orderBy('range_min')->get();

        for ($i = 0; $i < $ranges->count() - 1; $i++) {
            if ((float) $ranges[$i]->range_max >= (float) $ranges[$i + 1]->range_min) {
                return false;
            }
        }

        return true;
    }
}
