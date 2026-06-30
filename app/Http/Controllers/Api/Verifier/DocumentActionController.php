<?php

namespace App\Http\Controllers\Api\Verifier;

use App\Actions\Verification\ApproveDocument;
use App\Actions\Verification\RejectDocument;
use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;

class DocumentActionController extends BaseController
{
    public function approve(Application $application, ApplicationDocument $document, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewApplicationDetail', $application);
        abort_if($document->application_id !== $application->id, 404);

        app(ApproveDocument::class)->execute($document, $request->user());

        return $this->success(null, 'Dokumen berhasil disetujui.');
    }

    public function reject(Application $application, ApplicationDocument $document, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewApplicationDetail', $application);
        abort_if($document->application_id !== $application->id, 404);

        $request->validate(['reason' => ['required', 'string', 'min:5']]);
        app(RejectDocument::class)->execute($document, $request->user(), $request->reason);

        return $this->success(null, 'Dokumen ditolak.');
    }
}
