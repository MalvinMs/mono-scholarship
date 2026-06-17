<?php

namespace App\Observers;

use App\Models\ApplicationDocument;

class ApplicationDocumentObserver
{
    public function updated(ApplicationDocument $document): void
    {
        if ($document->wasChanged('verification_status') && $document->verification_status === 'approved') {
            $application = $document->application()->with('scholarship.qualifications')->first();
            $scholarship = $application->scholarship;
            $requiredQualificationIds = $scholarship->qualifications()
                ->where('is_file_upload_required', true)
                ->pluck('id');

            $allApproved = $application->documents()
                ->whereIn('qualification_id', $requiredQualificationIds)
                ->where('verification_status', '!=', 'approved')
                ->doesntExist();

            if ($allApproved) {
                // FinalizeApplicantScore will be triggered
                event(new \App\Events\AllDocumentsApproved($application));
            }
        }
    }
}
