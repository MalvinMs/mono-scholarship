<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Scholarship;
use App\Http\Controllers\Export\ExportApplicantsController;
use App\Http\Controllers\Export\PdfReportController;

class ExportController extends BaseController
{
    public function applicants(Scholarship $scholarship): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return app(ExportApplicantsController::class)($scholarship->id);
    }

    public function recipientsPdf(Scholarship $scholarship): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return app(PdfReportController::class)->recipients($scholarship);
    }

    public function auditLogPdf(Scholarship $scholarship): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return app(PdfReportController::class)->auditLog($scholarship);
    }
}
