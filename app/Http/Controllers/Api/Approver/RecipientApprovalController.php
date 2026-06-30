<?php

namespace App\Http\Controllers\Api\Approver;

use App\Http\Controllers\Api\BaseController;
use App\Jobs\SendNotification;
use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;

class RecipientApprovalController extends BaseController
{
    public function candidates(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        if ($scholarship->status !== 'selecting') {
            return $this->error('Program belum siap untuk penetapan.', 400);
        }

        $scores = ApplicationScore::with('application.user')
            ->where('scholarship_id', $scholarship->id)
            ->whereNotNull('rank')
            ->orderBy('rank')
            ->get()
            ->map(fn ($score) => [
                'id' => $score->id,
                'rank' => $score->rank,
                'registration_number' => $score->application?->registration_number,
                'applicant' => $score->application?->user?->name,
                'total_score' => $score->total_score,
                'max_possible_score' => $score->max_possible_score,
                'selection_result' => $score->selection_result,
                'is_final' => $score->is_final,
            ]);

        return $this->success([
            'scholarship' => [
                'id' => $scholarship->id,
                'name' => $scholarship->name,
                'quota_primary' => $scholarship->quota_primary,
                'quota_reserve' => $scholarship->quota_reserve,
            ],
            'candidates' => $scores,
        ]);
    }

    public function approve(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($scholarship->status !== 'selecting') {
            return $this->error('Program belum siap untuk penetapan.', 400);
        }

        $request->validate([
            'selected_ids' => ['required', 'array'],
            'selected_ids.*' => ['integer', 'exists:application_scores,id'],
        ]);

        $scores = ApplicationScore::whereIn('id', $request->selected_ids)
            ->where('scholarship_id', $scholarship->id)
            ->get();

        foreach ($scores as $score) {
            $score->update(['is_final' => true, 'finalized_at' => now()]);

            if ($score->selection_result === 'utama' && $score->application) {
                $score->application->update(['status' => 'selected', 'selected_at' => now()]);

                $user = $score->application->user;
                if ($user) {
                    $channels = $scholarship->notification_channels ?? ['email'];
                    foreach ($channels as $channel) {
                        SendNotification::dispatch($user, $score->application, $scholarship, $channel, 'result_announced');
                    }
                }
            }
        }

        $scholarship->update(['status' => 'announced']);

        return $this->success(null, 'Penerima berhasil ditetapkan.');
    }
}
