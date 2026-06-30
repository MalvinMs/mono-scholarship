<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Blacklist\RevokeBlacklist;
use App\Http\Controllers\Api\BaseController;
use App\Models\BlacklistLog;
use Illuminate\Http\Request;

class BlacklistController extends BaseController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = BlacklistLog::with(['user:id,name,email', 'blacklister:id,name']);

        if ($request->filled('filter.is_active')) {
            if ($request->input('filter.is_active') === 'true') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        return $this->success(
            $query->orderByDesc('created_at')
                ->paginate($request->input('per_page', 15))
                ->through(fn ($log) => [
                    'id' => $log->id,
                    'user' => ['id' => $log->user?->id, 'name' => $log->user?->name, 'email' => $log->user?->email],
                    'blacklisted_by' => $log->blacklister?->name,
                    'reason' => $log->reason,
                    'is_active' => $log->is_active,
                    'revoked_by' => $log->revoker?->name,
                    'revoke_reason' => $log->revoke_reason,
                    'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
                ])
        );
    }

    public function revoke(BlacklistLog $blacklist_log, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['reason' => ['required', 'string', 'min:10']]);

        app(RevokeBlacklist::class)->execute($blacklist_log, $request->user(), $request->reason);

        return $this->success(null, 'Blacklist berhasil dicabut.');
    }
}
