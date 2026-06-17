<?php

namespace App\Actions\Blacklist;

use App\Models\Application;
use App\Models\BlacklistLog;
use App\Models\User;

final class BlacklistApplicant
{
    public function execute(Application $application, User $verifier, string $reason): BlacklistLog
    {
        $log = BlacklistLog::create([
            'user_id' => $application->user_id,
            'application_id' => $application->id,
            'blacklisted_by' => $verifier->id,
            'reason' => $reason,
            'is_active' => true,
            'created_at' => now(),
        ]);

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => "Blacklist: {$reason}",
        ]);

        return $log;
    }
}
