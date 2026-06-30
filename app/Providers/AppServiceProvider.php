<?php

namespace App\Providers;

use App\Events\AllDocumentsApproved;
use App\Listeners\FinalizeScoreListener;
use App\Models\ApplicationDocument;
use App\Models\BlacklistLog;
use App\Observers\ApplicationDocumentObserver;
use App\Observers\BlacklistLogObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ApplicationDocument::observe(ApplicationDocumentObserver::class);
        BlacklistLog::observe(BlacklistLogObserver::class);

        Event::listen(AllDocumentsApproved::class, FinalizeScoreListener::class);

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('exports', function (Request $request) {
            return Limit::perMinute(2)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
