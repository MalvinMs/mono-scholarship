<?php

namespace App\Services;

final readonly class ScoreResult
{
    public function __construct(
        public int $total,
        public int $max,
        public array $breakdown,
    ) {}
}
