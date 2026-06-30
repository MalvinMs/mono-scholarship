<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\BlacklistLog;
use App\Models\VerificationLog;
use Illuminate\Http\Request;

class AuditLogController extends BaseController
{
    public function verification(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->success(
            VerificationLog::with(['verifier:id,name', 'application:id,registration_number'])
                ->orderByDesc('created_at')
                ->paginate($request->input('per_page', 20))
                ->through(fn ($log) => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'verifier' => $log->verifier?->name,
                    'registration_number' => $log->application?->registration_number,
                    'field_changed' => $log->field_changed,
                    'old_value' => $log->old_value,
                    'new_value' => $log->new_value,
                    'reason' => $log->reason,
                    'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
                ])
        );
    }

    public function blacklist(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->success(
            BlacklistLog::with(['user:id,name,email', 'blacklister:id,name', 'revoker:id,name'])
                ->orderByDesc('created_at')
                ->paginate($request->input('per_page', 20))
                ->through(fn ($log) => [
                    'id' => $log->id,
                    'user' => ['id' => $log->user?->id, 'name' => $log->user?->name],
                    'blacklisted_by' => $log->blacklister?->name,
                    'reason' => $log->reason,
                    'is_active' => $log->is_active,
                    'revoked_by' => $log->revoker?->name,
                    'revoke_reason' => $log->revoke_reason,
                    'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
                ])
        );
    }
}
