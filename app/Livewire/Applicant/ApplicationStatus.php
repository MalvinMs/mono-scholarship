<?php

namespace App\Livewire\Applicant;

use App\Models\Application;
use Livewire\Component;

class ApplicationStatus extends Component
{
    public Application $application;

    public function mount(Application $application)
    {
        $this->application = $application->load([
            'scholarship', 'documents',
        ]);
    }

    public function render()
    {
        return view('livewire.applicant.application-status')
            ->layout('components.layouts.app', ['title' => 'Status Pendaftaran']);
    }
}
