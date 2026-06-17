<?php

namespace App\Livewire\Applicant;

use App\Models\Application;
use App\Services\ScoringEngine;
use Livewire\Component;
use Livewire\WithFileUploads;

class SemesterRenewal extends Component
{
    use WithFileUploads;

    public Application $application;
    public $transcriptFile;
    public string $gpa = '';
    public ?Application $predecessorApp = null;

    public function mount(Application $application)
    {
        abort_if($application->user_id !== auth()->id(), 403);
        abort_unless(in_array($application->status, ['selected', 'verified']), 403, 'Anda tidak eligible untuk renewal.');

        $this->application = $application->load('scholarship');

        $this->predecessorApp = Application::where('scholarship_id', $application->scholarship->predecessor_scholarship_id)
            ->where('user_id', auth()->id())
            ->where('status', 'selected')
            ->first();
    }

    public function submit()
    {
        $this->validate([
            'transcriptFile' => 'required|file|max:2048|mimes:jpg,jpeg,png,pdf',
            'gpa' => 'required|numeric|min:0|max:4.00',
        ]);

        if ((float) $this->gpa < (float) $this->application->scholarship->min_gpa_renewal) {
            $this->addError('gpa', 'IPK minimal ' . $this->application->scholarship->min_gpa_renewal . ' untuk renewal.');
            return;
        }

        $fileName = $this->transcriptFile->getClientOriginalName();
        $fileSize = $this->transcriptFile->getSize();
        $fileMime = $this->transcriptFile->getMimeType();

        $path = $this->transcriptFile->store(
            "documents/{$this->application->scholarship_id}/{$this->application->id}/renewal",
            'minio'
        );

        $this->application->documents()->create([
            'qualification_id' => null,
            'doc_label' => 'Transkrip Renewal',
            'file_path' => $path,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'mime_type' => $fileMime,
            'uploaded_at' => now(),
            'verification_status' => 'pending',
        ]);

        $profile = $this->application->snapshot_profile ?? [];
        $profile['gpa'] = (float) $this->gpa;

        $this->application->update([
            'is_renewal' => true,
            'previous_application_id' => $this->predecessorApp?->id ?? $this->application->id,
            'snapshot_profile' => $profile,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Recalculate score
        $this->application->load(['scholarship.qualifications.options', 'scholarship.qualifications.ranges', 'answers.selectedOption']);
        $scoreResult = app(ScoringEngine::class)->calculate($this->application);

        $this->application->score()->updateOrCreate(
            ['application_id' => $this->application->id],
            [
                'scholarship_id' => $this->application->scholarship_id,
                'score_breakdown' => $scoreResult->breakdown,
                'total_score' => $scoreResult->total,
                'max_possible_score' => $scoreResult->max,
                'is_final' => false,
                'calculated_at' => now(),
            ]
        );

        $this->dispatch('notify', type: 'success', message: 'Renewal berhasil disubmit.');
        $this->redirect(route('application.status', $this->application->id));
    }

    public function render()
    {
        return view('livewire.applicant.semester-renewal')
            ->layout('components.layouts.app', ['title' => 'Renewal Beasiswa']);
    }
}
