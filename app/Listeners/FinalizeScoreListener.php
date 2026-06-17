<?php

namespace App\Listeners;

use App\Actions\Verification\FinalizeApplicantScore;
use App\Events\AllDocumentsApproved;

class FinalizeScoreListener
{
    public function handle(AllDocumentsApproved $event): void
    {
        app(FinalizeApplicantScore::class)->execute($event->application);
    }
}
