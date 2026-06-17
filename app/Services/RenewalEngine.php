<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Scholarship;

final class RenewalEngine
{
    public function calculateRenewalSlots(Scholarship $newScholarship): RenewalSlotResult
    {
        $predecessor = $newScholarship->predecessor;

        if (!$predecessor) {
            return RenewalSlotResult::empty();
        }

        $activeRecipients = Application::query()
            ->where('scholarship_id', $predecessor->id)
            ->where('status', 'selected')
            ->whereHas('score', fn($q) => $q->where('selection_result', 'utama'))
            ->get();

        $renewalApplications = Application::query()
            ->where('scholarship_id', $newScholarship->id)
            ->where('is_renewal', true)
            ->whereIn('previous_application_id', $activeRecipients->pluck('id'))
            ->get();

        $eligibleRenewals = $renewalApplications->filter(
            fn($app) => $app->score?->is_final
                && ($app->snapshot_profile['gpa'] ?? 0) >= $newScholarship->min_gpa_renewal
        );

        return new RenewalSlotResult(
            totalActiveRecipients: $activeRecipients->count(),
            totalSubmittedRenewal: $renewalApplications->count(),
            eligibleForRenewal: $eligibleRenewals->count(),
            remainingForNew: $newScholarship->quota_primary - $eligibleRenewals->count(),
        );
    }
}
