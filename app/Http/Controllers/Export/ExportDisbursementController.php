<?php

namespace App\Http\Controllers\Export;

use App\Exports\DisbursementExport;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportDisbursementController extends Controller
{
    public function __invoke(int $scholarshipId)
    {
        return Excel::download(new DisbursementExport($scholarshipId), 'data-pencairan.xlsx');
    }
}
