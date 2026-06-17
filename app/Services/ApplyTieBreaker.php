<?php

namespace App\Services;

use App\Models\ApplicationScore;
use Illuminate\Support\Collection;

final class ApplyTieBreaker
{
    /**
     * Resolve ties in a collection of scores based on tie-breaker config.
     * Returns the collection sorted with rank assigned.
     */
    public function resolve(Collection $scores, ?array $tiebreakerConfig, int $scholarshipId, int $availableQuota, int $reserveQuota): Collection
    {
        if ($scores->isEmpty()) {
            return $scores;
        }

        $scores = $scores->sortByDesc('total_score');

        if (empty($tiebreakerConfig)) {
            return $this->assignRanks($scores, $availableQuota, $reserveQuota, []);
        }

        $groups = $scores->groupBy('total_score');
        $ranked = collect();
        $allTiebreakerLogs = [];
        $currentRank = 0;

        foreach ($groups as $scoreValue => $group) {
            $currentRank++;

            if ($group->count() === 1) {
                $score = $group->first();
                $ranked->push($score);
                continue;
            }

            $tieLog = [];
            $contestants = $group->values();

            foreach ($tiebreakerConfig as $config) {
                if ($contestants->count() <= 1) break;

                $qualificationId = $config['qualification_id'];
                $winners = [];
                $losers = [];

                $maxTieScore = $contestants->max(function ($score) use ($qualificationId) {
                    return $this->getQualificationScore($score, $qualificationId);
                });

                foreach ($contestants as $contestant) {
                    $qScore = $this->getQualificationScore($contestant, (int) $qualificationId);
                    if ($qScore >= $maxTieScore) {
                        $winners[] = $contestant;
                    } else {
                        $losers[] = $contestant;
                    }
                }

                $tieLog[] = [
                    'step' => $config['priority'] ?? count($tieLog) + 1,
                    'qualification_id' => $qualificationId,
                    'max_score' => $maxTieScore,
                    'winners' => collect($winners)->pluck('application_id')->toArray(),
                    'losers' => collect($losers)->pluck('application_id')->toArray(),
                ];

                $contestants = collect($winners);
            }

            foreach ($group as $score) {
                $ranked->push($score);
            }
        }

        return $this->assignRanks($ranked, $availableQuota, $reserveQuota, $allTiebreakerLogs);
    }

    private function getQualificationScore(ApplicationScore $score, int $qualificationId): int
    {
        $breakdown = $score->score_breakdown ?? [];
        return $breakdown[$qualificationId]['score'] ?? 0;
    }

    private function assignRanks(Collection $scores, int $availableQuota, int $reserveQuota, array $tiebreakerLogs): Collection
    {
        $rank = 0;
        $lastScore = null;
        $sameRankCount = 0;

        foreach ($scores as $score) {
            if ($score->total_score !== $lastScore) {
                $rank += 1 + $sameRankCount;
                $sameRankCount = 0;
            } else {
                $sameRankCount++;
            }

            $lastScore = $score->total_score;

            $selectionResult = match (true) {
                $rank <= $availableQuota => 'utama',
                $rank <= $availableQuota + $reserveQuota => 'cadangan',
                default => 'tidak_lolos',
            };

            $score->rank = $rank;
            $score->selection_result = $selectionResult;
            $score->tiebreaker_log = $tiebreakerLogs;
        }

        return $scores;
    }
}
