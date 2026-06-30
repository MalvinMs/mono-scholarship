<?php

namespace App\Http\Controllers\Api\Verifier;

use App\Actions\Blacklist\BlacklistApplicant;
use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use Illuminate\Http\Request;

class BlacklistActionController extends BaseController
{
    public function store(Application $application, Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewApplicationDetail', $application);

        $request->validate(['reason' => ['required', 'string', 'min:10']]);
        app(BlacklistApplicant::class)->execute($application, $request->user(), $request->reason);

        return $this->success(null, 'Pelamar berhasil dimasukkan ke blacklist.');
    }
}
