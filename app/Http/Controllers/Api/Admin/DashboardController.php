<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use App\Models\Scholarship;

class DashboardController extends BaseController
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $stats = [
            'total_scholarships' => Scholarship::count(),
            'active_scholarships' => Scholarship::whereIn('status', ['open', 'renewal_open'])->count(),
            'total_applications' => Application::count(),
            'submitted' => Application::whereIn('status', ['submitted', 'under_review', 'needs_revision'])->count(),
            'verified' => Application::where('status', 'verified')->count(),
            'selected' => Application::where('status', 'selected')->count(),
            'draft' => Application::where('status', 'draft')->count(),
        ];

        $scholarships = Scholarship::orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'name', 'status', 'date_start', 'date_end'])
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'status' => $s->status,
                'date_start' => $s->date_start?->format('Y-m-d'),
                'date_end' => $s->date_end?->format('Y-m-d'),
                'applications_count' => $s->applications()->count(),
            ]);

        return $this->success([
            'stats' => $stats,
            'recent_scholarships' => $scholarships,
        ]);
    }
}
