<?php

namespace App\Policies;

use App\Models\User;

class BlacklistPolicy
{
    public function create(User $user, int $scholarshipId): bool
    {
        if ($user->hasRole('super-admin')) return true;

        if ($user->hasRole('verifier')) {
            return \App\Models\ScholarshipVerifier::where('scholarship_id', $scholarshipId)
                ->where('user_id', $user->id)
                ->exists();
        }

        return false;
    }

    public function revoke(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super-admin');
    }
}
