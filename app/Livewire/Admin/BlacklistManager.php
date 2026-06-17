<?php

namespace App\Livewire\Admin;

use App\Actions\Blacklist\RevokeBlacklist;
use App\Models\BlacklistLog;
use App\Policies\BlacklistPolicy;
use Livewire\Component;
use Livewire\WithPagination;

class BlacklistManager extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public ?int $revokingId = null;
    public string $revokeReason = '';
    public bool $showRevokeModal = false;

    public function openRevokeModal(int $logId): void
    {
        if (!app(BlacklistPolicy::class)->revoke(auth()->user())) {
            abort(403);
        }

        $this->revokingId = $logId;
        $this->revokeReason = '';
        $this->showRevokeModal = true;
    }

    public function revoke(): void
    {
        $this->validate(['revokeReason' => 'required|string|min:10']);

        $log = BlacklistLog::findOrFail($this->revokingId);
        app(RevokeBlacklist::class)->execute($log, auth()->user(), $this->revokeReason);

        $this->showRevokeModal = false;
        $this->revokingId = null;
        $this->dispatch('notify', type: 'success', message: 'Blacklist berhasil dicabut.');
    }

    public function closeRevokeModal(): void
    {
        $this->showRevokeModal = false;
        $this->revokingId = null;
        $this->revokeReason = '';
    }

    public function render()
    {
        $query = BlacklistLog::with(['user', 'blacklister', 'revoker']);

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'revoked') {
            $query->where('is_active', false);
        }

        $logs = $query->latest('created_at')->paginate(15);

        return view('livewire.admin.blacklist-manager', compact('logs'))
            ->layout('components.layouts.app', ['title' => 'Manajemen Blacklist']);
    }
}
