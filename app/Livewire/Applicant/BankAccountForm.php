<?php

namespace App\Livewire\Applicant;

use App\Models\Application;
use App\Models\Disbursement;
use Livewire\Component;

class BankAccountForm extends Component
{
    public Application $application;
    public string $bankName = '';
    public string $accountNumber = '';
    public string $accountHolderName = '';
    public bool $submitted = false;
    public ?Disbursement $existingDisbursement = null;

    public function mount(Application $application)
    {
        abort_if($application->user_id !== auth()->id(), 403);
        abort_unless(in_array($application->status, ['selected', 'verified']), 403, 'Anda belum dapat mengisi data rekening.');

        $this->application = $application;

        $this->existingDisbursement = Disbursement::where('application_id', $application->id)->first();

        if ($this->existingDisbursement) {
            $this->bankName = $this->existingDisbursement->bank_name;
            $this->accountNumber = $this->existingDisbursement->account_number;
            $this->accountHolderName = $this->existingDisbursement->account_holder_name;
            $this->submitted = true;
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'bankName' => 'required|string|max:100',
            'accountNumber' => 'required|string|max:50|regex:/^[0-9]+$/',
            'accountHolderName' => 'required|string|max:255',
        ]);

        if ($this->existingDisbursement) {
            $this->existingDisbursement->update([
                'bank_name' => $validated['bankName'],
                'account_number' => $validated['accountNumber'],
                'account_holder_name' => $validated['accountHolderName'],
            ]);
        } else {
            Disbursement::create([
                'application_id' => $this->application->id,
                'scholarship_id' => $this->application->scholarship_id,
                'bank_name' => $validated['bankName'],
                'account_number' => $validated['accountNumber'],
                'account_holder_name' => $validated['accountHolderName'],
                'amount' => $this->application->scholarship->fund_amount ?? 0,
                'status' => 'waiting',
            ]);
        }

        $this->submitted = true;
        $this->dispatch('notify', type: 'success', message: 'Data rekening berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.applicant.bank-account-form')
            ->layout('components.layouts.app', ['title' => 'Data Rekening']);
    }
}
