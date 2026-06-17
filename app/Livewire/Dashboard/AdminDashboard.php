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

        $totalApplications = Application::count();
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

        // Score distribution histogram
        $scoreDistribution = $this->buildScoreDistribution($scoreQuery);

        // Geographic distribution
        $geoDistribution = $this->buildGeoDistribution($applicationQuery);

        // Daily submission monitoring
        $dailySubmissions = $this->buildDailySubmissions($applicationQuery);

        return view('livewire.dashboard.admin-dashboard', compact(
            'scholarships',
            'totalApplications', 'activeScholarships', 'totalScholarships',
            'statusCounts', 'verifiedCount', 'selectedCount', 'totalVerified', 'revisionCount',
            'scholarshipStats',
            'scoreDistribution', 'geoDistribution',
            'dailySubmissions',
        ))->layout('components.layouts.app', ['title' => 'Dashboard Admin']);
    }

    private function buildScoreDistribution($scoreQuery): array
    {
        $scores = (clone $scoreQuery)->where('is_final', true)->pluck('total_score');
        if ($scores->isEmpty()) return ['labels' => [], 'datasets' => []];

        $maxScore = $scores->max();
        $bucketCount = min(8, max(4, (int) ceil($maxScore / 10)));
        $bucketSize = max(1, (int) ceil($maxScore / $bucketCount));

        $labels = [];
        $data = [];
        for ($i = 0; $i < $bucketCount; $i++) {
            $min = $i * $bucketSize;
            $max = ($i + 1) * $bucketSize;
            $labels[] = $i === $bucketCount - 1 ? "{$min}+" : "{$min}–{$max}";
            $count = $scores->filter(fn($s) => $s >= $min && $s < $max)->count();
            if ($i === $bucketCount - 1) {
                $count += $scores->filter(fn($s) => $s >= $max)->count();
            }
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Pendaftar',
                'data' => $data,
                'backgroundColor' => '#171717',
                'borderRadius' => 4,
                'barPercentage' => 0.7,
            ]],
        ];
    }

    private function buildGeoDistribution($applicationQuery): array
    {
        $userIds = (clone $applicationQuery)->where('status', '!=', 'draft')->pluck('user_id');
        if ($userIds->isEmpty()) return ['labels' => [], 'datasets' => []];

        $rawDistribution = User::whereIn('id', $userIds)
            ->whereNotNull('district')
            ->selectRaw('district, count(*) as count')
            ->groupBy('district')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        if ($rawDistribution->isEmpty()) return ['labels' => [], 'datasets' => []];

        return [
            'labels' => $rawDistribution->pluck('district')->toArray(),
            'datasets' => [[
                'label' => 'Pendaftar',
                'data' => $rawDistribution->pluck('count')->toArray(),
                'backgroundColor' => '#0070f3',
                'borderRadius' => 4,
                'barPercentage' => 0.6,
            ]],
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

        if ($daily->isEmpty()) return ['labels' => [], 'datasets' => []];

        return [
            'labels' => $daily->map(fn($r) => Carbon::parse($r->date)->translatedFormat('d M'))->toArray(),
            'datasets' => [[
                'label' => 'Submit per Hari',
                'data' => $daily->pluck('count')->toArray(),
                'borderColor' => '#171717',
                'backgroundColor' => 'rgba(23,23,23,0.05)',
                'fill' => true,
                'tension' => 0.25,
                'pointBackgroundColor' => '#171717',
                'pointBorderColor' => '#ffffff',
                'pointBorderWidth' => 2,
                'pointRadius' => 3,
                'pointHoverRadius' => 5,
            ]],
        ];
    }
}
