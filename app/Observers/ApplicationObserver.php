<?php

namespace App\Observers;

use App\Models\Application;
use Illuminate\Support\Facades\Storage;

class ApplicationObserver
{
    public function deleting(Application $application): void
    {
        foreach ($application->documents as $doc) {
            if ($doc->file_path) {
                Storage::disk('minio')->delete($doc->file_path);
            }
        }

        $application->documents()->delete();
    }
}