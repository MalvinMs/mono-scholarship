<?php

namespace App\Actions\Blacklist;

use App\Models\BlacklistLog;
use App\Models\User;

final class RevokeBlacklist
{
    public function execute(BlacklistLog $log, User $revoker, string $reason): void
    {
        $log->update([
            'is_active' => false,
            'revoked_by' => $revoker->id,
            'revoked_at' => now(),
            'revoke_reason' => $reason,
        ]);
    }
}
