<?php

namespace App\Livewire\Applicant;

use App\Actions\Application\SubmitApplication as SubmitApplicationAction;
use App\Models\Scholarship;
use App\Services\DynamicFormRenderer;
use Livewire\Component;
use Livewire\WithFileUploads;

class ApplicationForm extends Component
{
    use WithFileUploads;

    public Scholarship $scholarship;
    public array $answers = [];
    public array $files = [];
    public int $currentStep = 0;
    public array $formConfig = [];

    public function mount(Scholarship $scholarship)
    {
        abort_if(auth()->user()->is_blacklisted, 403, 'Akun Anda tidak dapat mendaftar.');

        $this->scholarship = $scholarship;
        $renderer = app(DynamicFormRenderer::class);
        $this->formConfig = $renderer->getFormConfig($scholarship);

        foreach ($this->formConfig as $group) {
            foreach ($group['qualifications'] as $q) {
                if ($q['type'] === 'multi_choice') {
                    $this->answers[$q['id']] = [];
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.applicant.application-form')
            ->layout('components.layouts.app', ['title' => 'Pendaftaran - ' . $this->scholarship->name]);
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        if ($this->currentStep < count($this->formConfig) - 1) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
        }
    }

    public function saveDraft()
    {
        $action = app(SubmitApplicationAction::class);
        $action->execute($this->scholarship, auth()->user(), $this->answers, $this->files, true);
        $this->dispatch('notify', type: 'success', message: 'Draft berhasil disimpan.');
    }

    public function submit()
    {
        $this->validateAll();

        $action = app(SubmitApplicationAction::class);
        $application = $action->execute($this->scholarship, auth()->user(), $this->answers, $this->files);

        session()->flash('registration_number', $application->registration_number);
        $this->redirect(route('application.status', $application->id));
    }

    public function removeFile(string $key): void
    {
        $parts = explode('.', $key);
        $qualificationId = end($parts);

        if (isset($this->files[$qualificationId])) {
            $this->files[$qualificationId] = null;
            unset($this->files[$qualificationId]);
        }
    }

    private function validateCurrentStep(): void
    {
        $currentQualifications = collect($this->formConfig[$this->currentStep]['qualifications'] ?? []);
        $rules = [];

        foreach ($currentQualifications as $q) {
            $field = "answers.{$q['id']}";
            if ($q['is_required']) {
                $rules[$field] = ['required'];
            } else {
                $rules[$field] = ['nullable'];
            }

            switch ($q['type']) {
                case 'single_choice':
                    $rules[$field][] = 'exists:qualification_options,id';
                    break;
                case 'multi_choice':
                    $rules[$field][] = 'array';
                    break;
                case 'numeric_range':
                    $rules[$field][] = 'numeric';
                    break;
                case 'text':
                    $rules[$field][] = 'string';
                    break;
                case 'file_upload':
                    unset($rules[$field]);
                    if ($q['is_required']) {
                        $rules["files.{$q['id']}"] = ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'];
                    }
                    break;
            }

            if ($q['is_file_upload_required'] && $q['type'] !== 'file_upload') {
                if ($q['is_required']) {
                    $rules["files.{$q['id']}"] = ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'];
                } else {
                    $rules["files.{$q['id']}"] = ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'];
                }
            }
        }

        $this->validate($rules);
    }

    private function validateAll(): void
    {
        $renderer = app(DynamicFormRenderer::class);
        $rules = $renderer->getValidationRules($this->scholarship);
        $this->validate($rules);
    }
}
