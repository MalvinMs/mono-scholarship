<?php

namespace App\Livewire\Verifier;

use App\Models\Application;
use App\Models\Scholarship;
use App\Policies\ApplicationPolicy;
use Livewire\Component;
use Livewire\WithPagination;

class VerificationQueue extends Component
{
    use WithPagination;

    public $selectedScholarshipId;

    public function mount()
    {
        $user = auth()->user();

        $hasAccess = \App\Models\ScholarshipVerifier::where('user_id', $user->id)->exists();

        if (! $hasAccess && ! $user->hasAnyRole(['super-admin', 'admin'])) {
            abort(403, 'Anda tidak memiliki akses ke antrian verifikasi.');
        }
    }

    public function render()
    {
        $user = auth()->user();

        $assignedScholarshipIds = \App\Models\ScholarshipVerifier::where('user_id', $user->id)
            ->pluck('scholarship_id');

        $scholarships = Scholarship::whereIn('id', $assignedScholarshipIds)->get();

        $query = Application::with(['user', 'documents', 'scholarship.qualifications'])
            ->whereIn('scholarship_id', $assignedScholarshipIds)
            ->whereIn('status', ['submitted', 'under_review', 'needs_revision']);

        if ($this->selectedScholarshipId) {
            $query->where('scholarship_id', $this->selectedScholarshipId);
        }

        $applications = $query->latest('submitted_at')->paginate(10);

        return view('livewire.verifier.verification-queue', compact('applications', 'scholarships'))
            ->layout('components.layouts.app', ['title' => 'Antrian Verifikasi']);
    }

    public function goToDetail(int $applicationId): void
    {
        $this->redirect(route('verification.detail', $applicationId), navigate: true);
    }

    public function getDocumentProgress(Application $application): array
    {
        $requiredDocs = $application->scholarship->qualifications()
            ->where('is_file_upload_required', true)
            ->count();

        if ($requiredDocs === 0) {
            return ['total' => 0, 'approved' => 0, 'percentage' => 100];
        }

        $approved = $application->documents()
            ->where('verification_status', 'approved')
            ->count();

        return [
            'total' => $requiredDocs,
            'approved' => $approved,
            'percentage' => $requiredDocs > 0 ? round(($approved / $requiredDocs) * 100) : 0,
        ];
    }
}
