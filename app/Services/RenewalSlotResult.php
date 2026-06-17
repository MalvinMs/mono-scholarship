<?php

namespace App\Services;

final readonly class RenewalSlotResult
{
    public function __construct(
        public int $totalActiveRecipients,
        public int $totalSubmittedRenewal,
        public int $eligibleForRenewal,
        public int $remainingForNew,
    ) {}

    public static function empty(): self
    {
        return new self(0, 0, 0, 0);
    }
}
