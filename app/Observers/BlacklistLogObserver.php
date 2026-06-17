<?php

namespace App\Observers;

use App\Models\BlacklistLog;

class BlacklistLogObserver
{
    public function created(BlacklistLog $log): void
    {
        $log->user()->update(['is_blacklisted' => true]);
    }

    public function updated(BlacklistLog $log): void
    {
        if ($log->wasChanged('is_active')) {
            if ($log->is_active === false) {
                // Check if user has any other active blacklists
                $hasActive = BlacklistLog::where('user_id', $log->user_id)
                    ->where('is_active', true)
                    ->where('id', '!=', $log->id)
                    ->exists();

                if (!$hasActive) {
                    $log->user()->update(['is_blacklisted' => false]);
                }
            }
        }
    }
}
