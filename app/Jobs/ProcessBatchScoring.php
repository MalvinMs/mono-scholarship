<?php

namespace App\Jobs;

use App\Models\ApplicationScore;
use App\Models\Scholarship;
use App\Services\ApplyTieBreaker;
use App\Services\RenewalEngine;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessBatchScoring implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $scholarshipId,
    ) {
        $this->onQueue('scoring');
    }

    public function handle(): void
    {
        $cacheKey = "batch_scoring_progress:{$this->scholarshipId}";

        $this->progress($cacheKey, 'preparing', 'Memvalidasi status program...');

        $scholarship = Scholarship::with(['predecessor', 'qualifications.options', 'qualifications.ranges'])
            ->findOrFail($this->scholarshipId);

        if (!in_array($scholarship->status, ['closed', 'renewal_closed'])) {
            $this->progress($cacheKey, 'error', 'Program tidak dalam status yang valid.');
            return;
        }

        // BV-09: Process renewal first
        $this->progress($cacheKey, 'processing_renewal', 'Memproses slot renewal...');

        $renewalEngine = app(RenewalEngine::class);
        $renewalResult = $renewalEngine->calculateRenewalSlots($scholarship);

        $quotaRenewalLocked = $renewalResult->eligibleForRenewal;

        if ($quotaRenewalLocked > $scholarship->quota_primary) {
            $quotaRenewalLocked = $scholarship->quota_primary;
        }

        // Lock renewal slots
        $scholarship->update(['quota_renewal_locked' => $quotaRenewalLocked]);

        // Mark renewal applications as selected (utama)
        if ($scholarship->predecessor) {
            ApplicationScore::query()
                ->where('scholarship_id', $scholarship->id)
                ->where('is_final', true)
                ->whereHas('application', fn($q) => $q->where('is_renewal', true))
                ->update([
                    'selection_result' => 'utama',
                    'rank' => 0,
                ]);

            \App\Models\Application::query()
                ->where('scholarship_id', $scholarship->id)
                ->where('is_renewal', true)
                ->whereHas('score', fn($q) => $q->where('is_final', true))
                ->update([
                    'status' => 'selected',
                    'selected_at' => now(),
                ]);
        }

        // BV-08: Only process is_final = true
        $availableQuota = $scholarship->quota_primary - $quotaRenewalLocked;

        $this->progress($cacheKey, 'ranking', 'Mengambil data pendaftar final...');

        $scores = ApplicationScore::query()
            ->with('application.user')
            ->where('scholarship_id', $scholarship->id)
            ->where('is_final', true)
            ->whereHas('application', fn($q) => $q->where('is_renewal', false))
            ->orderByDesc('total_score')
            ->get();

        $this->progress($cacheKey, 'applying_tiebreaker', "Menerapkan tie-breaker untuk {$scores->count()} pendaftar...");

        // Apply tie-breaker
        $tieBreaker = app(ApplyTieBreaker::class);
        $tieBreakerConfig = $scholarship->tiebreaker_config;

        $tieBreaker->resolve(
            $scores,
            $tieBreakerConfig,
            $scholarship->id,
            max(0, $availableQuota),
            $scholarship->quota_reserve ?? 0,
        );

        $this->progress($cacheKey, 'persisting', 'Menyimpan hasil ranking...');

        // Persist ranks and selection results
        foreach ($scores as $score) {
            $score->save();

            if ($score->selection_result === 'utama') {
                $score->application()->update([
                    'status' => 'selected',
                    'selected_at' => now(),
                ]);
            }
        }

        // Set scholarship to selecting phase
        $scholarship->update(['status' => 'selecting']);

        $this->progress($cacheKey, 'completed', 'Seleksi selesai. Program beralih ke status selecting.', 'completed');
    }

    private function progress(string $cacheKey, string $stage, string $label, string $status = 'running'): void
    {
        Cache::put($cacheKey, [
            'stage' => $stage,
            'label' => $label,
            'status' => $status,
            'updated_at' => now()->toIso8601String(),
        ], now()->addHour());
    }
}
