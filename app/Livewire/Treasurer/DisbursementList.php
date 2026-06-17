<?php

namespace App\Livewire\Treasurer;

use App\Models\Disbursement;
use App\Models\Scholarship;
use Livewire\Component;
use Livewire\WithPagination;

class DisbursementList extends Component
{
    use WithPagination;

    public ?int $scholarshipId = null;
    public string $statusFilter = '';
    public array $selectedIds = [];

    public function render()
    {
        $scholarships = Scholarship::latest()->get();

        $query = Disbursement::with(['application.user', 'scholarship'])
            ->when($this->scholarshipId, fn($q) => $q->where('scholarship_id', $this->scholarshipId))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest();

        $disbursements = $query->paginate(15);

        $canExport = (bool) $this->scholarshipId;

        return view('livewire.treasurer.disbursement-list', compact('scholarships', 'disbursements', 'canExport'))
            ->layout('components.layouts.app', ['title' => 'Pencairan Dana']);
    }

    public function exportExcel(): void
    {
        if (!$this->scholarshipId) return;

        $this->redirect(url("/keuangan/export/{$this->scholarshipId}"));
    }

    public function updateStatus(int $id, string $status): void
    {
        $disbursement = Disbursement::findOrFail($id);

        $allowed = match ($disbursement->status) {
            'waiting' => 'processing',
            'processing' => 'disbursed',
            default => null,
        };

        if ($status !== $allowed) return;

        $disbursement->update([
            'status' => $status,
            'processed_by' => auth()->id(),
            ...($status === 'disbursed' ? ['disbursed_at' => now()] : []),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Status pencairan diperbarui.');
    }

    public function bulkUpdateStatus(string $status): void
    {
        foreach ($this->selectedIds as $id) {
            $this->updateStatus((int) $id, $status);
        }
        $this->selectedIds = [];
    }
}
