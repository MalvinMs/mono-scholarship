<?php

namespace App\Livewire\Approver;

use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use Livewire\Component;

class ApproverDashboard extends Component
{
    public function render()
    {
        $scholarships = Scholarship::withCount('applications')
            ->latest()
            ->get();

        $selectingScholarships = Scholarship::where('status', 'selecting')
            ->withCount(['applications'])
            ->latest()
            ->get();

        $announcedScholarships = Scholarship::where('status', 'announced')
            ->latest()
            ->get();

        $activeProgramsCount = Scholarship::where('status', 'open')->count();
        $awaitingApprovalCount = $selectingScholarships->count();
        $totalRecipients = Application::where('status', 'selected')->count();
        $totalVerified = ApplicationScore::where('is_final', true)->count();

        $totalAnggaran = Scholarship::where('status', 'announced')
            ->sum('fund_amount');

        // Yearly trend
        $yearlyTrend = $this->buildYearlyTrend();

        return view('livewire.approver.approver-dashboard', compact(
            'scholarships', 'selectingScholarships', 'announcedScholarships',
            'activeProgramsCount', 'awaitingApprovalCount', 'totalRecipients', 'totalVerified',
            'totalAnggaran',
            'yearlyTrend',
        ))->layout('components.layouts.app', ['title' => 'Dashboard Approver']);
    }

    private function buildYearlyTrend(): array
    {
        $trends = Application::where('status', '!=', 'draft')
            ->whereNotNull('submitted_at')
            ->selectRaw("EXTRACT(YEAR FROM submitted_at)::int as year, count(*) as count")
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        if ($trends->isEmpty()) return ['labels' => [], 'datasets' => []];

        return [
            'labels' => $trends->pluck('year')->toArray(),
            'datasets' => [[
                'label' => 'Pendaftar',
                'data' => $trends->pluck('count')->toArray(),
                'borderColor' => '#171717',
                'backgroundColor' => 'rgba(23,23,23,0.06)',
                'fill' => true,
                'tension' => 0.3,
                'pointBackgroundColor' => '#171717',
                'pointBorderColor' => '#ffffff',
                'pointBorderWidth' => 2,
                'pointRadius' => 4,
                'pointHoverRadius' => 6,
            ]],
        ];
    }
}
