<?php

namespace App\Livewire\Admin;

use App\Models\Scholarship;
use App\Models\ScholarshipVerifier;
use App\Models\User;
use Livewire\Component;

class VerifierAssignment extends Component
{
    public Scholarship $scholarship;
    public $search = '';
    public $selectedUserId;

    public function mount(Scholarship $scholarship)
    {
        $this->scholarship = $scholarship->load('verifiers.user');
    }

    public function render()
    {
        $verifiers = User::role('verifier')
            ->when($this->search, fn($q) => $q->where('name', 'ilike', '%' . $this->search . '%'))
            ->whereDoesntHave('scholarshipVerifications', fn($q) => $q->where('scholarship_id', $this->scholarship->id))
            ->limit(10)
            ->get();

        return view('livewire.admin.verifier-assignment', [
            'availableVerifiers' => $verifiers,
        ])->layout('components.layouts.app', ['title' => 'Penugasan Verifikator']);
    }

    public function assign()
    {
        ScholarshipVerifier::create([
            'scholarship_id' => $this->scholarship->id,
            'user_id' => $this->selectedUserId,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);
        $this->scholarship->load('verifiers.user');
        $this->reset('selectedUserId', 'search');
        session()->flash('message', 'Verifikator berhasil ditugaskan.');
    }

    public function remove($verifierId)
    {
        ScholarshipVerifier::where('scholarship_id', $this->scholarship->id)
            ->where('user_id', $verifierId)
            ->delete();
        $this->scholarship->load('verifiers.user');
        session()->flash('message', 'Verifikator berhasil dihapus.');
    }
}
