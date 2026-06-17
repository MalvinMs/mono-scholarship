<?php

namespace App\Providers;

use App\Events\AllDocumentsApproved;
use App\Listeners\FinalizeScoreListener;
use App\Models\ApplicationDocument;
use App\Models\BlacklistLog;
use App\Observers\ApplicationDocumentObserver;
use App\Observers\BlacklistLogObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ApplicationDocument::observe(ApplicationDocumentObserver::class);
        BlacklistLog::observe(BlacklistLogObserver::class);

        Event::listen(AllDocumentsApproved::class, FinalizeScoreListener::class);
    }
}
