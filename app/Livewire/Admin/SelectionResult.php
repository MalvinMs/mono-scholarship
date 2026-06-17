<?php

namespace App\Livewire\Admin;

use App\Models\ApplicationScore;
use App\Models\Scholarship;
use Livewire\Component;
use Livewire\WithPagination;

class SelectionResult extends Component
{
    use WithPagination;

    public ?int $scholarshipId = null;
    public string $filter = '';
    public ?int $detailScoreId = null;

    public function mount(?int $scholarship = null)
    {
        $this->scholarshipId = $scholarship;
    }

    public function showDetail(int $scoreId): void
    {
        $this->detailScoreId = $this->detailScoreId === $scoreId ? null : $scoreId;
    }

    public function render()
    {
        $scholarships = Scholarship::query()
            ->whereIn('status', ['selecting', 'announced'])
            ->latest()
            ->get();

        $scores = collect();
        $selectedScholarship = null;
        $detailScore = null;
        $canApprove = false;

        if ($this->scholarshipId) {
            $selectedScholarship = Scholarship::find($this->scholarshipId);

            $query = ApplicationScore::with(['application.user'])
                ->where('scholarship_id', $this->scholarshipId)
                ->whereNotNull('rank')
                ->whereNotNull('selection_result')
                ->orderBy('rank');

            if ($this->filter) {
                $query->where('selection_result', $this->filter);
            }

            $scores = $query->paginate(20);

            if ($this->detailScoreId) {
                $detailScore = ApplicationScore::with(['application.user'])
                    ->find($this->detailScoreId);
            }

            $user = auth()->user();
            $canApprove = $user
                && ($user->hasRole('approver') || $user->hasRole('super-admin'))
                && $selectedScholarship
                && $selectedScholarship->status === 'selecting';
        }

        return view('livewire.admin.selection-result', compact(
            'scholarships',
            'scores',
            'selectedScholarship',
            'detailScore',
            'canApprove',
        ))
            ->layout('components.layouts.app', ['title' => 'Hasil Seleksi']);
    }
}
