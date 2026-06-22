<?php

namespace App\Livewire\Dashboard;

use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class AdminDashboard extends Component
{
    public ?int $scholarshipId = null;

    public function render()
    {
        $scholarships = Scholarship::latest()->get();

        $applicationQuery = Application::query();
        $scoreQuery = ApplicationScore::query();

        if ($this->scholarshipId) {
            $applicationQuery->where('scholarship_id', $this->scholarshipId);
            $scoreQuery->where('scholarship_id', $this->scholarshipId);
        }

        $totalApplications = (clone $applicationQuery)->count();
        $activeScholarships = Scholarship::where('status', 'open')->count();
        $totalScholarships = Scholarship::count();

        $statusCounts = (clone $applicationQuery)->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $verifiedCount = $scoreQuery->where('is_final', true)->count();
        $selectedCount = (clone $applicationQuery)->where('status', 'selected')->count();
        $totalVerified = (clone $applicationQuery)->where('status', 'verified')->count();
        $revisionCount = (clone $applicationQuery)->where('status', 'needs_revision')->count();

        $scholarshipStats = Scholarship::withCount(['applications', 'verifiers'])
            ->latest()
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'status' => $s->status,
                'applications_count' => $s->applications_count,
                'verifiers_count' => $s->verifiers_count,
                'quota' => $s->quota_primary,
            ]);

        // Status distribution (Donut chart)
        $statusDistribution = $this->buildStatusDistribution($statusCounts);

        // Daily submission monitoring
        $dailySubmissions = $this->buildDailySubmissions($applicationQuery);

        return view('livewire.dashboard.admin-dashboard', compact(
            'scholarships',
            'totalApplications', 'activeScholarships', 'totalScholarships',
            'statusCounts', 'verifiedCount', 'selectedCount', 'totalVerified', 'revisionCount',
            'scholarshipStats',
            'statusDistribution',
            'dailySubmissions',
        ))->layout('components.layouts.app', ['title' => 'Dashboard Admin']);
    }

    private function buildStatusDistribution(array $statusCounts): array
    {
        if (empty($statusCounts)) return [];

        $labelsMap = [
            'draft' => 'Draft',
            'submitted' => 'Submit',
            'under_review' => 'Review',
            'needs_revision' => 'Revisi',
            'verified' => 'Terverifikasi',
            'selected' => 'Ditetapkan',
            'rejected' => 'Ditolak',
        ];

        // Design colors matching the Tailwind classes
        $colorsMap = [
            'draft' => '#666666',
            'submitted' => '#171717',
            'under_review' => '#f5a623',
            'needs_revision' => '#e00',
            'verified' => '#0070f3',
            'selected' => '#0070f3',
            'rejected' => '#e00',
        ];

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($statusCounts as $status => $count) {
            $labels[] = $labelsMap[$status] ?? ucfirst($status);
            $data[] = $count;
            $colors[] = $colorsMap[$status] ?? '#171717';
        }

        return [
            'chart' => ['type' => 'donut'],
            'series' => $data,
            'labels' => $labels,
            'colors' => $colors,
            'plotOptions' => [
                'pie' => [
                    'donut' => ['size' => '70%'],
                    'expandOnClick' => false
                ]
            ],
            'stroke' => ['width' => 0],
            'legend' => ['show' => false],
            'dataLabels' => ['enabled' => false],
        ];
    }

    private function buildDailySubmissions($applicationQuery): array
    {
        $daily = (clone $applicationQuery)
            ->whereNotNull('submitted_at')
            ->selectRaw("submitted_at::date as date, count(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($daily->isEmpty()) return [];

        return [
            'chart' => ['type' => 'area'],
            'series' => [[
                'name' => 'Submit per Hari',
                'data' => $daily->pluck('count')->toArray(),
            ]],
            'xaxis' => [
                'categories' => $daily->map(fn($r) => Carbon::parse($r->date)->translatedFormat('d M'))->toArray()
            ],
            'colors' => ['#171717'],
            'stroke' => ['curve' => 'smooth', 'width' => 2],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shadeIntensity' => 1,
                    'opacityFrom' => 0.1,
                    'opacityTo' => 0,
                    'stops' => [0, 100]
                ]
            ]
        ];
    }
}
