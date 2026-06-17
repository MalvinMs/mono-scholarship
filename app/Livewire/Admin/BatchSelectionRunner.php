<?php

namespace App\Livewire\Admin;

use App\Jobs\ProcessBatchScoring;
use App\Models\Scholarship;
use App\Services\RenewalEngine;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class BatchSelectionRunner extends Component
{
    public ?int $scholarshipId = null;
    public array $renewalSummary = [];
    public bool $jobDispatched = false;
    public bool $showConfirm = false;
    public array $progress = [];
    public bool $polling = false;

    public function mount(?int $scholarship = null)
    {
        $this->scholarshipId = $scholarship;
        $this->loadSummary();
        
        if ($this->scholarshipId) {
            $this->checkProgress();
        }
    }

    public function loadSummary(): void
    {
        if (!$this->scholarshipId) return;

        $scholarship = Scholarship::with('predecessor')->find($this->scholarshipId);
        if (!$scholarship) return;

        $engine = app(RenewalEngine::class);
        $result = $engine->calculateRenewalSlots($scholarship);

        $this->renewalSummary = [
            'name' => $scholarship->name,
            'slug' => $scholarship->slug,
            'quota_primary' => $scholarship->quota_primary,
            'quota_reserve' => $scholarship->quota_reserve ?? 0,
            'total_active_recipients' => $result->totalActiveRecipients,
            'total_submitted_renewal' => $result->totalSubmittedRenewal,
            'eligible_for_renewal' => $result->eligibleForRenewal,
            'remaining_for_new' => $result->remainingForNew,
            'status' => $scholarship->status,
            'verified_count' => \App\Models\ApplicationScore::query()
                ->where('scholarship_id', $scholarship->id)
                ->where('is_final', true)
                ->count(),
        ];

        // Auto-refresh status for polling
        if ($this->polling && $scholarship->status === 'selecting') {
            $this->polling = false;
            $this->jobDispatched = true;
        }
    }

    public function checkProgress(): void
    {
        if (!$this->scholarshipId) return;

        $cacheKey = "batch_scoring_progress:{$this->scholarshipId}";
        $data = Cache::get($cacheKey);

        if ($data) {
            $this->progress = $data;
            $this->polling = $data['status'] === 'running';
        }
    }

    public function confirmRun(): void
    {
        $this->showConfirm = true;
    }

    public function cancelRun(): void
    {
        $this->showConfirm = false;
    }

    public function runBatch()
    {
        if (!$this->scholarshipId) return;

        $scholarship = Scholarship::findOrFail($this->scholarshipId);

        if (!in_array($scholarship->status, ['closed', 'renewal_closed'])) {
            $this->dispatch('notify', type: 'error', message: 'Program harus dalam status closed untuk menjalankan seleksi.');
            return;
        }

        ProcessBatchScoring::dispatch($this->scholarshipId);

        $this->jobDispatched = true;
        $this->showConfirm = false;
        $this->polling = true;

        $this->dispatch('notify', type: 'success', message: 'Batch scoring berhasil dijalankan. Monitoring progress...');
    }

    public function render()
    {
        $scholarships = Scholarship::query()
            ->whereIn('status', ['closed', 'renewal_closed', 'selecting', 'announced'])
            ->latest()
            ->get();

        // Check if current scholarship is in selecting/announced status (batch done)
        $batchCompleted = false;
        if ($this->scholarshipId) {
            $scholarship = Scholarship::find($this->scholarshipId);
            $batchCompleted = $scholarship && in_array($scholarship->status, ['selecting', 'announced']);
        }

        return view('livewire.admin.batch-selection-runner', compact('scholarships', 'batchCompleted'))
            ->layout('components.layouts.app', ['title' => 'Jalankan Seleksi']);
    }
}
