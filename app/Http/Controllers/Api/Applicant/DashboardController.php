<?php

namespace App\Http\Controllers\Api\Applicant;

use App\Http\Controllers\Api\BaseController;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $totalApplications = $user->applications()->count();
        $submittedApplications = $user->applications()->where('status', '!=', 'draft')->count();

        $scholarships = Scholarship::query()
            ->whereIn('status', ['open', 'closed', 'announced'])
            ->orderByDesc('date_start')
            ->limit(5)
            ->withCount('applications')
            ->get(['id', 'name', 'slug', 'date_start', 'date_end', 'status']);

        return $this->success([
            'total_applications' => $totalApplications,
            'submitted_applications' => $submittedApplications,
            'available_scholarships' => $scholarships,
        ]);
    }
}
