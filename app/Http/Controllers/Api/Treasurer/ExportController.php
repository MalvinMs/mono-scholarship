<?php

namespace App\Http\Controllers\Api\Treasurer;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Export\ExportDisbursementController;
use App\Http\Controllers\Export\PdfReportController;
use App\Models\Scholarship;

class ExportController extends BaseController
{
    public function excel(Scholarship $scholarship): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return app(ExportDisbursementController::class)($scholarship->id);
    }

    public function pdf(Scholarship $scholarship): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return app(PdfReportController::class)->disbursement($scholarship);
    }
}
