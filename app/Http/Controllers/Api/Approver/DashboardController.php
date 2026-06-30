<?php

namespace App\Http\Controllers\Api\Approver;

use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use App\Models\Scholarship;

class DashboardController extends BaseController
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $selectingScholarships = Scholarship::where('status', 'selecting')->count();
        $activePrograms = Scholarship::whereIn('status', ['open', 'renewal_open'])->count();
        $totalRecipients = Application::where('status', 'selected')->count();
        $totalBudget = Scholarship::whereIn('status', ['selecting', 'announced'])->sum('fund_amount');

        return $this->success([
            'selecting_scholarships' => $selectingScholarships,
            'active_programs' => $activePrograms,
            'total_recipients' => $totalRecipients,
            'total_budget' => $totalBudget,
        ]);
    }
}
