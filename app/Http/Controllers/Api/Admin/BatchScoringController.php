<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Jobs\ProcessBatchScoring;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use App\Services\RenewalEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BatchScoringController extends BaseController
{
    public function renewalSummary(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        $engine = app(RenewalEngine::class);
        $result = $engine->calculateRenewalSlots($scholarship);

        return $this->success([
            'total_active_recipients' => $result->totalActiveRecipients,
            'total_submitted_renewal' => $result->totalSubmittedRenewal,
            'eligible_for_renewal' => $result->eligibleForRenewal,
            'remaining_for_new' => $result->remainingForNew,
        ]);
    }

    public function runBatch(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        if (!in_array($scholarship->status, ['closed', 'renewal_closed'])) {
            return $this->error('Program beasiswa belum ditutup.', 400);
        }

        ProcessBatchScoring::dispatch($scholarship);

        return $this->success([
            'scholarship_id' => $scholarship->id,
            'status' => 'processing',
        ], 'Proses scoring batch sedang berjalan.', 202);
    }

    public function progress(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        $key = "batch_scoring_progress:{$scholarship->id}";
        $progress = Cache::get($key, ['stage' => 'idle']);

        return $this->success($progress);
    }

    public function results(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $query = ApplicationScore::with('application.user')
            ->where('scholarship_id', $scholarship->id)
            ->whereNotNull('selection_result')
            ->whereNotNull('rank');

        if ($request->filled('filter.selection_result')) {
            $query->where('selection_result', $request->input('filter.selection_result'));
        }

        return $this->success(
            $query->orderBy('rank')
                ->paginate($request->input('per_page', 50))
                ->through(fn ($score) => [
                    'id' => $score->id,
                    'rank' => $score->rank,
                    'applicant' => [
                        'name' => $score->application?->user?->name,
                        'registration_number' => $score->application?->registration_number,
                    ],
                    'total_score' => $score->total_score,
                    'max_possible_score' => $score->max_possible_score,
                    'selection_result' => $score->selection_result,
                    'is_final' => $score->is_final,
                ])
        );
    }

    public function showScore(Scholarship $scholarship, ApplicationScore $score): \Illuminate\Http\JsonResponse
    {
        if ($score->application_id !== $score->application->id) {
            abort(404);
        }

        $score->load(['application.user', 'application.answers.qualification']);

        return $this->success([
            'id' => $score->id,
            'rank' => $score->rank,
            'applicant' => [
                'name' => $score->application?->user?->name,
                'registration_number' => $score->application?->registration_number,
            ],
            'total_score' => $score->total_score,
            'max_possible_score' => $score->max_possible_score,
            'selection_result' => $score->selection_result,
            'is_final' => $score->is_final,
            'breakdown' => $score->score_breakdown,
            'tiebreaker_log' => $score->tiebreaker_log,
        ]);
    }
}
