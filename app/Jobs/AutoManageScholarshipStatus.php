<?php

namespace App\Jobs;

use App\Models\Scholarship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoManageScholarshipStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = now()->toDateString();

        Scholarship::where('status', 'draft')
            ->where('date_start', '<=', $today)
            ->where('date_end', '>=', $today)
            ->update(['status' => 'open', 'published_at' => now()]);

        Scholarship::where('status', 'open')
            ->where('date_end', '<', $today)
            ->update(['status' => 'closed']);
    }
}
