<?php

namespace App\Actions\Application;

use App\Models\Application;
use App\Models\ApplicationAnswer;
use App\Models\ApplicationDocument;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use App\Models\User;
use App\Services\ScoringEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class SubmitApplication
{
    public function execute(
        Scholarship $scholarship,
        User $user,
        array $answers,
        array $files = [],
        bool $isDraft = false
    ): Application {
        return DB::transaction(function () use ($scholarship, $user, $answers, $files, $isDraft) {
            // Check existing draft
            $application = Application::where('scholarship_id', $scholarship->id)
                ->where('user_id', $user->id)
                ->where('status', 'draft')
                ->first();

            if (!$application) {
                $application = new Application([
                    'scholarship_id' => $scholarship->id,
                    'user_id' => $user->id,
                    'status' => 'draft',
                ]);
                
                // Generate registration number early to satisfy database NOT NULL constraint
                $application->registration_number = $this->generateRegistrationNumber($scholarship);
            }

            $isSubmit = !$isDraft;

            if ($isSubmit) {
                // Snapshot profile
                $snapshot = app(SnapshotApplicantProfile::class)->execute($user);
                $application->snapshot_profile = $snapshot;

                // Set status
                $application->status = 'submitted';
                $application->submitted_at = now();
            }

            $application->save();

            // Delete old answers for re-save (both draft and submit)
            ApplicationAnswer::where('application_id', $application->id)->delete();

            $scoringEngine = app(ScoringEngine::class);

            // Save answers
            foreach ($answers as $qualificationId => $value) {
                $answer = new ApplicationAnswer([
                    'application_id' => $application->id,
                    'qualification_id' => $qualificationId,
                ]);

                $qualification = $scholarship->qualifications->find($qualificationId);

                if ($qualification) {
                    match ($qualification->type) {
                        'single_choice' => $answer->selected_option_id = $value,
                        'multi_choice' => $answer->selected_option_ids = is_array($value) ? $value : [$value],
                        'numeric_range' => $answer->numeric_value = $value,
                        'text' => $answer->text_value = $value,
                        default => null,
                    };
                }

                $answer->save();

                // Compute per-answer score
                $answer->computed_score = $scoringEngine->resolveAnswerScore($answer);
                $answer->save();
            }

            // Save uploaded files (both draft and submit)
            if (!empty($files)) {
                foreach ($files as $qualificationId => $file) {
                    if (!$file) continue;

                    $qualification = $scholarship->qualifications->find($qualificationId);

                    // Capture metadata before store() — store() moves the file,
                    // making subsequent getSize()/getMimeType() calls fail on S3
                    $fileName = $file->getClientOriginalName();
                    $fileSize = $file->getSize();
                    $fileMime = $file->getMimeType();

                    $existingDoc = ApplicationDocument::where('application_id', $application->id)
                        ->where('qualification_id', $qualificationId)
                        ->first();

                    if ($existingDoc?->file_path) {
                        Storage::disk('minio')->delete($existingDoc->file_path);
                    }

                    $path = $file->store(
                        "documents/{$scholarship->id}/{$application->id}/{$qualificationId}",
                        'minio'
                    );

                    ApplicationDocument::updateOrCreate(
                        ['application_id' => $application->id, 'qualification_id' => $qualificationId],
                        [
                            'doc_label' => $qualification?->file_upload_label ?? '',
                            'file_path' => $path,
                            'file_name' => $fileName,
                            'file_size' => $fileSize,
                            'mime_type' => $fileMime,
                            'uploaded_at' => now(),
                            'verification_status' => 'pending',
                        ]
                    );
                }
            }

            // Calculate temporary score
            if ($isSubmit) {
                $application->load(['scholarship.qualifications.options', 'scholarship.qualifications.ranges', 'answers.selectedOption']);
                $scoreResult = $scoringEngine->calculate($application);

                ApplicationScore::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'scholarship_id' => $scholarship->id,
                        'score_breakdown' => $scoreResult->breakdown,
                        'total_score' => $scoreResult->total,
                        'max_possible_score' => $scoreResult->max,
                        'is_final' => false,
                        'calculated_at' => now(),
                    ]
                );
            }

            return $application->fresh();
        });
    }

    private function generateRegistrationNumber(Scholarship $scholarship): string
    {
        $prefix = strtoupper(Str::substr($scholarship->slug, 0, 5));
        $year = now()->format('Y');

        // BV-01: Random 8-char alphanumeric suffix — no sequential collision, no lock needed.
        $suffix = strtoupper(Str::random(8));

        return sprintf('%s%s-%s', $prefix, $year, $suffix);
    }
}
