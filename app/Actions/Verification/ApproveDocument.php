<?php

namespace App\Actions\Verification;

use App\Models\ApplicationDocument;
use App\Models\VerificationLog;
use App\Models\User;

final class ApproveDocument
{
    public function execute(ApplicationDocument $document, User $verifier): void
    {
        $document->update([
            'verification_status' => 'approved',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);

        VerificationLog::create([
            'application_id' => $document->application_id,
            'verifier_id' => $verifier->id,
            'action' => 'document_approved',
            'target_type' => 'document',
            'target_id' => $document->id,
            'reason' => 'Dokumen disetujui.',
            'created_at' => now(),
        ]);
    }
}
