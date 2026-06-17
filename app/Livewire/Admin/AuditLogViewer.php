<?php

namespace App\Livewire\Admin;

use App\Models\BlacklistLog;
use App\Models\VerificationLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogViewer extends Component
{
    use WithPagination;

    public string $logType = 'verification';
    public string $filter = '';

    public function render()
    {
        if ($this->logType === 'blacklist') {
            $logs = BlacklistLog::with(['user', 'blacklister', 'revoker'])
                ->when($this->filter === 'active', fn($q) => $q->where('is_active', true))
                ->when($this->filter === 'revoked', fn($q) => $q->where('is_active', false))
                ->latest('created_at')
                ->paginate(20);
        } else {
            $logs = VerificationLog::with(['application.scholarship', 'verifier'])
                ->when($this->filter, fn($q) => $q->where('action', $this->filter))
                ->latest('created_at')
                ->paginate(20);
        }

        return view('livewire.admin.audit-log-viewer', compact('logs'))
            ->layout('components.layouts.app', ['title' => 'Audit Log']);
    }
}
