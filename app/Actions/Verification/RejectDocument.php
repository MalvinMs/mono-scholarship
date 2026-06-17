<?php

namespace App\Actions\Verification;

use App\Models\ApplicationDocument;
use App\Models\VerificationLog;
use App\Models\User;

final class RejectDocument
{
    public function execute(ApplicationDocument $document, User $verifier, string $reason): void
    {
        $document->update([
            'verification_status' => 'rejected',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $document->application()->update(['status' => 'needs_revision']);

        VerificationLog::create([
            'application_id' => $document->application_id,
            'verifier_id' => $verifier->id,
            'action' => 'document_rejected',
            'target_type' => 'document',
            'target_id' => $document->id,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}
