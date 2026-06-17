<?php

namespace App\Livewire\Admin;

use App\Models\Scholarship;
use Livewire\Component;

class TiebreakerConfigurator extends Component
{
    public Scholarship $scholarship;

    public function mount(Scholarship $scholarship)
    {
        $this->scholarship = $scholarship->load('qualifications');
    }

    public function render()
    {
        return view('livewire.admin.tiebreaker-configurator')
            ->layout('components.layouts.app', ['title' => 'Konfigurasi Tie-Breaker']);
    }
}
