<?php

namespace App\Http\Controllers\Api\Applicant;

use App\Actions\Application\SubmitApplication;
use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Qualification;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends BaseController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $applications = $request->user()->applications()
            ->with(['scholarship:id,name,slug,status', 'score'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return $this->success(
            $applications->through(fn ($app) => [
                'id' => $app->id,
                'registration_number' => $app->registration_number,
                'status' => $app->status,
                'is_renewal' => $app->is_renewal,
                'scholarship' => [
                    'id' => $app->scholarship?->id,
                    'name' => $app->scholarship?->name,
                    'slug' => $app->scholarship?->slug,
                    'status' => $app->scholarship?->status,
                ],
                'submitted_at' => $app->submitted_at?->format('Y-m-d H:i:s'),
                'selection_result' => $app->score?->selection_result,
                'rank' => $app->score?->rank,
                'total_score' => $app->score?->total_score,
            ])
        );
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if ($user->is_blacklisted) {
            return $this->error('Akun Anda tidak dapat mendaftar karena terdaftar dalam blacklist.', 403);
        }

        $request->validate([
            'scholarship_slug' => ['required', 'string', 'exists:scholarships,slug'],
            'answers' => ['sometimes', 'array'],
            'answers.*' => ['nullable'],
            'files' => ['sometimes', 'array'],
            'file' => ['sometimes', 'file'],
            'is_draft' => ['sometimes', 'boolean'],
        ]);

        $scholarship = Scholarship::where('slug', $request->scholarship_slug)->firstOrFail();

        if (!in_array($scholarship->status, ['open', 'renewal_open'])) {
            return $this->error('Program beasiswa tidak sedang dibuka.', 400);
        }

        $files = $request->hasFile('files') ? $request->file('files', []) : [];

        $application = app(SubmitApplication::class)->execute(
            $scholarship,
            $user,
            $request->input('answers', []),
            $files,
            $request->boolean('is_draft', false)
        );

        return $this->success(
            [
                'id' => $application->id,
                'registration_number' => $application->registration_number,
                'status' => $application->status,
                'scholarship_slug' => $scholarship->slug,
                'submitted_at' => $application->submitted_at?->format('Y-m-d H:i:s'),
            ],
            $request->boolean('is_draft') ? 'Draft berhasil disimpan.' : 'Pendaftaran berhasil dikirim.',
            201
        );
    }

    public function show(Application $application): \Illuminate\Http\JsonResponse
    {
        $this->authorizeOwner($application);

        $application->load([
            'scholarship',
            'answers.qualification',
            'answers.selectedOption',
            'documents',
            'score',
            'verificationLogs.verifier:id,name',
        ]);

        return $this->success([
            'id' => $application->id,
            'registration_number' => $application->registration_number,
            'status' => $application->status,
            'is_renewal' => $application->is_renewal,
            'snapshot_profile' => $application->snapshot_profile,
            'rejection_reason' => $application->rejection_reason,
            'submitted_at' => $application->submitted_at?->format('Y-m-d H:i:s'),
            'verified_at' => $application->verified_at?->format('Y-m-d H:i:s'),
            'selected_at' => $application->selected_at?->format('Y-m-d H:i:s'),
            'scholarship' => [
                'id' => $application->scholarship?->id,
                'name' => $application->scholarship?->name,
                'slug' => $application->scholarship?->slug,
                'status' => $application->scholarship?->status,
            ],
            'score' => $application->score ? [
                'total_score' => $application->score->total_score,
                'max_possible_score' => $application->score->max_possible_score,
                'rank' => $application->score->rank,
                'selection_result' => $application->score->selection_result,
                'is_final' => $application->score->is_final,
                'breakdown' => $application->score->score_breakdown,
            ] : null,
            'documents' => $application->documents->map(fn ($d) => [
                'id' => $d->id,
                'qualification_id' => $d->qualification_id,
                'doc_label' => $d->doc_label,
                'file_name' => $d->file_name,
                'file_size' => $d->file_size,
                'verification_status' => $d->verification_status,
                'rejection_reason' => $d->rejection_reason,
            ]),
            'verification_logs' => $application->verificationLogs->map(fn ($log) => [
                'action' => $log->action,
                'verifier' => $log->verifier?->name,
                'reason' => $log->reason,
                'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    public function uploadFile(Application $application, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorizeOwner($application);

        if (!in_array($application->status, ['draft', 'needs_revision'])) {
            return $this->error('Tidak dapat mengunggah file untuk aplikasi ini.', 400);
        }

        $request->validate([
            'qualification_id' => ['required', 'integer', 'exists:qualifications,id'],
            'file' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
        ]);

        $qualification = Qualification::findOrFail($request->qualification_id);
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileMime = $file->getMimeType();

        // Delete existing file for this qualification
        $existingDoc = ApplicationDocument::where('application_id', $application->id)
            ->where('qualification_id', $qualification->id)
            ->first();
        if ($existingDoc?->file_path) {
            Storage::disk('minio')->delete($existingDoc->file_path);
        }

        $path = $file->store(
            "documents/{$application->scholarship_id}/{$application->id}/{$qualification->id}",
            'minio'
        );

        $document = ApplicationDocument::updateOrCreate(
            ['application_id' => $application->id, 'qualification_id' => $qualification->id],
            [
                'doc_label' => $qualification->file_upload_label ?? '',
                'file_path' => $path,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'mime_type' => $fileMime,
                'uploaded_at' => now(),
                'verification_status' => 'pending',
            ]
        );

        return $this->success([
            'id' => $document->id,
            'file_name' => $document->file_name,
            'verification_status' => $document->verification_status,
        ], 'File berhasil diunggah.', 201);
    }

    public function deleteFile(Application $application, Qualification $qualification): \Illuminate\Http\JsonResponse
    {
        $this->authorizeOwner($application);

        $document = ApplicationDocument::where('application_id', $application->id)
            ->where('qualification_id', $qualification->id)
            ->first();

        if (!$document) {
            return $this->error('Dokumen tidak ditemukan.', 404);
        }

        if ($document->file_path) {
            Storage::disk('minio')->delete($document->file_path);
        }

        $document->delete();

        return $this->success(null, 'File berhasil dihapus.');
    }

    public function downloadDocument(Application $application, ApplicationDocument $document): \Illuminate\Http\JsonResponse
    {
        $this->authorizeOwner($application);

        if ($document->application_id !== $application->id) {
            return $this->error('Dokumen tidak ditemukan.', 404);
        }

        return $this->success([
            'url' => $document->temporaryViewUrl(60),
            'file_name' => $document->file_name,
            'expires_at' => now()->addMinutes(60)->toISOString(),
        ]);
    }

    protected function authorizeOwner(Application $application): void
    {
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke aplikasi ini.');
        }
    }
}
