<?php

namespace App\Http\Controllers\Export;

use App\Models\ApplicationScore;
use App\Models\Scholarship;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;

class PdfReportController extends Controller
{
    public function recipients(Scholarship $scholarship)
    {
        $results = ApplicationScore::with('application.user')
            ->where('scholarship_id', $scholarship->id)
            ->whereNotNull('selection_result')
            ->orderBy('rank')
            ->get()
            ->map(fn($score) => [
                'rank' => $score->rank,
                'registration_number' => $score->application?->registration_number ?? '-',
                'name' => $score->application?->user?->name ?? '-',
                'total_score' => $score->total_score,
                'selection_result' => $score->selection_result,
            ]);

        $pdf = Pdf::loadView('pdf.report', [
            'scholarship' => $scholarship,
            'results' => $results,
        ])->setPaper('a4', 'landscape')
          ->setOption('dpi', 150)
          ->setOption('defaultFont', 'sans-serif');

        return $pdf->download('laporan-penerima.pdf');
    }

    public function disbursement(Scholarship $scholarship)
    {
        $disbursements = \App\Models\Disbursement::with(['application.user'])
            ->where('scholarship_id', $scholarship->id)
            ->get();

        $pdf = Pdf::loadView('pdf.disbursement-report', [
            'scholarship' => $scholarship,
            'disbursements' => $disbursements,
        ])->setPaper('a4', 'landscape')
          ->setOption('dpi', 150)
          ->setOption('defaultFont', 'sans-serif');

        return $pdf->download('rekap-pencairan.pdf');
    }

    public function auditLog(Scholarship $scholarship)
    {
        $logs = \App\Models\VerificationLog::with(['application', 'verifier'])
            ->whereHas('application', fn($q) => $q->where('scholarship_id', $scholarship->id))
            ->latest('created_at')
            ->get();

        $pdf = Pdf::loadView('pdf.audit-log', [
            'scholarship' => $scholarship,
            'logs' => $logs,
        ])->setPaper('a4', 'landscape')
          ->setOption('dpi', 150)
          ->setOption('defaultFont', 'sans-serif');

        return $pdf->download('audit-log.pdf');
    }
}
