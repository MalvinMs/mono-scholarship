<?php

namespace App\Http\Controllers\Export;

use App\Exports\ApplicantsExport;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportApplicantsController extends Controller
{
    public function __invoke(int $scholarshipId)
    {
        return Excel::download(new ApplicantsExport($scholarshipId), 'penerima-beasiswa.xlsx');
    }
}
