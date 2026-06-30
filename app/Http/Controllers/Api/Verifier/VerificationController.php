<?php

namespace App\Http\Controllers\Api\Verifier;

use App\Actions\Verification\FinalizeApplicantScore;
use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use App\Models\ScholarshipVerifier;
use Illuminate\Http\Request;

class VerificationController extends BaseController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $assignedScholarshipIds = ScholarshipVerifier::where('user_id', $user->id)
            ->pluck('scholarship_id');

        $applications = Application::with(['scholarship:id,name,slug', 'user:id,name,email', 'score'])
            ->whereIn('scholarship_id', $assignedScholarshipIds)
            ->whereIn('status', ['submitted', 'under_review', 'needs_revision']);

        if ($request->filled('filter.scholarship_id')) {
            $applications->where('scholarship_id', $request->input('filter.scholarship_id'));
        }

        if ($request->filled('filter.status')) {
            $applications->where('status', $request->input('filter.status'));
        }

        return $this->success(
            $applications->orderByDesc('submitted_at')
                ->paginate($request->input('per_page', 15))
                ->through(fn ($app) => [
                    'id' => $app->id,
                    'registration_number' => $app->registration_number,
                    'status' => $app->status,
                    'applicant' => [
                        'id' => $app->user?->id,
                        'name' => $app->user?->name,
                    ],
                    'scholarship' => [
                        'id' => $app->scholarship?->id,
                        'name' => $app->scholarship?->name,
                        'slug' => $app->scholarship?->slug,
                    ],
                    'submitted_at' => $app->submitted_at?->format('Y-m-d H:i:s'),
                    'needs_revision' => $app->status === 'needs_revision',
                ])
        );
    }

    public function show(Application $application): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewApplicationDetail', $application);

        $application->load([
            'user',
            'scholarship.qualifications.options',
            'answers.qualification',
            'answers.selectedOption',
            'documents.verifier:id,name',
            'score',
            'verificationLogs.verifier:id,name',
        ]);

        return $this->success([
            'id' => $application->id,
            'registration_number' => $application->registration_number,
            'status' => $application->status,
            'applicant' => $application->user,
            'scholarship' => [
                'id' => $application->scholarship?->id,
                'name' => $application->scholarship?->name,
                'is_verification_enabled' => $application->scholarship?->is_verification_enabled,
            ],
            'answers' => $application->answers->map(fn ($a) => [
                'id' => $a->id,
                'qualification_id' => $a->qualification_id,
                'qualification_name' => $a->qualification?->name,
                'qualification_type' => $a->qualification?->type,
                'selected_option_id' => $a->selected_option_id,
                'numeric_value' => $a->numeric_value,
                'text_value' => $a->text_value,
                'computed_score' => $a->computed_score,
                'is_corrected' => $a->is_corrected_by_verifier,
            ]),
            'documents' => $application->documents->map(fn ($d) => [
                'id' => $d->id,
                'qualification_id' => $d->qualification_id,
                'doc_label' => $d->doc_label,
                'file_name' => $d->file_name,
                'verification_status' => $d->verification_status,
                'rejection_reason' => $d->rejection_reason,
                'verified_by' => $d->verifier?->name,
                'verified_at' => $d->verified_at?->format('Y-m-d H:i:s'),
            ]),
            'score' => $application->score ? [
                'total_score' => $application->score->total_score,
                'max_possible_score' => $application->score->max_possible_score,
                'is_final' => $application->score->is_final,
            ] : null,
            'verification_logs' => $application->verificationLogs,
        ]);
    }

    public function finalize(Application $application): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewApplicationDetail', $application);

        app(FinalizeApplicantScore::class)->execute($application);

        return $this->success(null, 'Skor berhasil difinalisasi.');
    }
}
