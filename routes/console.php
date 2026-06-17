<?php

use App\Jobs\AutoManageScholarshipStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Auto-manage scholarship statuses based on date_start / date_end.
 *
 * Server cron entry required (add to crontab):
 *   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
 *
 * For local development, use:
 *   php artisan schedule:work
 */
Schedule::job(new AutoManageScholarshipStatus)->dailyAt('00:01');

/**
 * Cleanup Livewire temporary upload files older than 24 hours.
 * Requires php artisan livewire:configure-s3-upload-cleanup to be run first.
 */
Schedule::command('livewire:cleanup-temp-files')->daily();
