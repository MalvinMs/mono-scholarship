<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewVerificationQueue(User $user, $scholarshipId): bool
    {
        if ($user->hasRole('super-admin')) return true;
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('verifier')) {
            return \App\Models\ScholarshipVerifier::where('scholarship_id', $scholarshipId)
                ->where('user_id', $user->id)
                ->exists();
        }

        return false;
    }

    public function viewApplicationDetail(User $user, Application $application): bool
    {
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) return true;

        if ($user->hasRole('verifier')) {
            return \App\Models\ScholarshipVerifier::where('scholarship_id', $application->scholarship_id)
                ->where('user_id', $user->id)
                ->exists();
        }

        if ($user->hasRole('applicant')) {
            return $application->user_id === $user->id;
        }

        return $user->id === $application->user_id;
    }
}
