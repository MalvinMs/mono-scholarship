<?php

namespace App\Livewire\Approver;

use App\Jobs\SendNotification;
use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use Livewire\Component;

class RecipientApproval extends Component
{
    public ?int $scholarshipId = null;
    public bool $showConfirm = false;

    public function mount(?int $scholarship = null)
    {
        $this->scholarshipId = $scholarship;
    }

    public function confirmApproval(): void
    {
        $this->showConfirm = true;
    }

    public function cancelApproval(): void
    {
        $this->showConfirm = false;
    }

    public function approveRecipients()
    {
        if (!$this->scholarshipId) return;

        $scholarship = Scholarship::findOrFail($this->scholarshipId);

        if ($scholarship->status !== 'selecting') {
            $this->dispatch('notify', type: 'error', message: 'Program harus dalam status selecting untuk penetapan.');
            return;
        }

        // BV-07: Lock scores as immutable
        ApplicationScore::query()
            ->where('scholarship_id', $this->scholarshipId)
            ->where('selection_result', 'utama')
            ->whereNotNull('rank')
            ->update(['finalized_at' => now()]);

        // Update applications to selected
        Application::query()
            ->where('scholarship_id', $this->scholarshipId)
            ->whereHas('score', fn($q) => $q->where('selection_result', 'utama'))
            ->update([
                'status' => 'selected',
                'selected_at' => now(),
            ]);

        // Announce scholarship
        $scholarship->update(['status' => 'announced']);

        // Dispatch notifications to all applicants
        $this->dispatchNotifications($scholarship);

        $this->showConfirm = false;

        $this->dispatch('notify', type: 'success', message: 'Penetapan penerima berhasil. Beasiswa telah diumumkan dan notifikasi sedang dikirim.');
    }

    private function dispatchNotifications(Scholarship $scholarship): void
    {
        $channels = $scholarship->notification_channels ?? [];
        $useWhatsApp = $channels['whatsapp'] ?? true;
        $useEmail = $channels['email'] ?? false;

        $applications = Application::with('score', 'user')
            ->where('scholarship_id', $scholarship->id)
            ->whereIn('status', ['selected', 'verified'])
            ->get();

        foreach ($applications as $application) {
            $result = $application->score?->selection_result ?? 'tidak_lolos';
            $resultLabel = match ($result) {
                'utama' => 'Lolos Utama',
                'cadangan' => 'Lolos Cadangan',
                default => 'Tidak Lolos',
            };

            $templateData = [
                'name' => $application->user->name,
                'registration_number' => $application->registration_number,
                'scholarship_name' => $scholarship->name,
                'result' => $resultLabel,
            ];

            if ($useWhatsApp && $application->user->phone) {
                SendNotification::dispatch(
                    $application->user_id,
                    $application->id,
                    'whatsapp',
                    'result_announced',
                    $templateData
                );
            }

            if ($useEmail && $application->user->email) {
                SendNotification::dispatch(
                    $application->user_id,
                    $application->id,
                    'email',
                    'result_announced',
                    $templateData
                );
            }
        }
    }

    public function render()
    {
        $scholarships = Scholarship::query()
            ->whereIn('status', ['selecting', 'announced'])
            ->latest()
            ->get();

        $summary = [];
        if ($this->scholarshipId) {
            $totalUtama = ApplicationScore::query()
                ->where('scholarship_id', $this->scholarshipId)
                ->where('selection_result', 'utama')
                ->count();

            $totalCadangan = ApplicationScore::query()
                ->where('scholarship_id', $this->scholarshipId)
                ->where('selection_result', 'cadangan')
                ->count();

            $totalTidakLolos = ApplicationScore::query()
                ->where('scholarship_id', $this->scholarshipId)
                ->where('selection_result', 'tidak_lolos')
                ->count();

            $scholarship = Scholarship::find($this->scholarshipId);

            $summary = [
                'name' => $scholarship?->name,
                'quota_primary' => $scholarship?->quota_primary ?? 0,
                'total_utama' => $totalUtama,
                'total_cadangan' => $totalCadangan,
                'total_tidak_lolos' => $totalTidakLolos,
                'status' => $scholarship?->status,
                'is_announced' => $scholarship?->status === 'announced',
            ];
        }

        return view('livewire.approver.recipient-approval', compact('scholarships', 'summary'))
            ->layout('components.layouts.app', ['title' => 'Penetapan Penerima']);
    }
}
