<?php

namespace App\Livewire\Verifier;

use App\Actions\Blacklist\BlacklistApplicant;
use App\Actions\Verification\ApproveAnswer;
use App\Actions\Verification\ApproveDocument;
use App\Actions\Verification\CorrectAnswer;
use App\Actions\Verification\RejectDocument;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Policies\ApplicationPolicy;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ApplicationDetail extends Component
{
    public Application $application;

    public string $activeTab = 'profile';

    #[Validate('required|string|min:10')]
    public string $rejectionReason = '';

    #[Validate('required|string|min:10')]
    public string $correctionReason = '';

    #[Validate('required|string|min:10')]
    public string $blacklistReason = '';

    public ?int $selectedDocumentId = null;
    public ?int $correctingAnswerId = null;
    public mixed $correctedValue = null;
    public bool $showRejectModal = false;
    public bool $showCorrectModal = false;
    public bool $showBlacklistModal = false;

    public function mount(Application $application)
    {
        $this->application = $application->load([
            'user',
            'scholarship.qualifications.options',
            'scholarship.qualifications.ranges',
            'documents.qualification',
            'answers.qualification',
            'answers.selectedOption',
            'score',
            'verificationLogs.verifier',
        ]);

        if (!app(ApplicationPolicy::class)->viewApplicationDetail(auth()->user(), $this->application)) {
            abort(403, 'Anda tidak memiliki akses ke pendaftar ini.');
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function approveDocument(int $documentId): void
    {
        $document = $this->application->documents->find($documentId);
        if (!$document) return;

        app(ApproveDocument::class)->execute($document, auth()->user());
        $this->refreshApplication();
        $this->dispatch('document-updated');
    }

    public function openRejectModal(int $documentId): void
    {
        $this->selectedDocumentId = $documentId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function rejectDocument(): void
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10',
        ]);

        $document = $this->application->documents->find($this->selectedDocumentId);
        if (!$document) return;

        app(RejectDocument::class)->execute($document, auth()->user(), $this->rejectionReason);
        $this->showRejectModal = false;
        $this->selectedDocumentId = null;
        $this->rejectionReason = '';
        $this->refreshApplication();
        $this->dispatch('document-updated');
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedDocumentId = null;
        $this->rejectionReason = '';
    }

    public function viewDocument(int $documentId): void
    {
        $doc = $this->application->documents->find($documentId);
        if (!$doc?->file_path) return;

        $url = $doc->temporaryViewUrl(60);
        $this->dispatch('open-document-url', url: $url);
    }

    public function openCorrectModal(int $answerId): void
    {
        $answer = $this->application->answers->find($answerId);
        if (!$answer) return;

        $this->correctingAnswerId = $answerId;
        $this->correctionReason = '';
        $this->correctedValue = $answer->selected_option_id ?? $answer->numeric_value ?? $answer->text_value ?? '';
        $this->showCorrectModal = true;
    }

    public function correctAnswer(): void
    {
        $this->validate([
            'correctionReason' => 'required|string|min:10',
        ]);

        $answer = $this->application->answers->find($this->correctingAnswerId);
        if (!$answer) return;

        app(CorrectAnswer::class)->execute($answer, auth()->user(), $this->correctedValue, $this->correctionReason);
        $this->showCorrectModal = false;
        $this->correctingAnswerId = null;
        $this->correctionReason = '';
        $this->refreshApplication();
        $this->dispatch('answer-corrected');
    }

    public function closeCorrectModal(): void
    {
        $this->showCorrectModal = false;
        $this->correctingAnswerId = null;
        $this->correctionReason = '';
    }

    public function approveAnswer(int $answerId): void
    {
        $answer = $this->application->answers->find($answerId);
        if (!$answer) return;

        app(ApproveAnswer::class)->execute($answer, auth()->user());
        $this->refreshApplication();
        $this->dispatch('answer-approved');
    }

    public function openBlacklistModal(): void
    {
        $this->blacklistReason = '';
        $this->showBlacklistModal = true;
    }

    public function blacklistApplicant(): void
    {
        $this->validate([
            'blacklistReason' => 'required|string|min:10',
        ]);

        $policy = app(\App\Policies\BlacklistPolicy::class);
        if (!$policy->create(auth()->user(), $this->application->scholarship_id)) {
            abort(403, 'Anda tidak memiliki izin untuk melakukan blacklist.');
        }

        app(BlacklistApplicant::class)->execute($this->application, auth()->user(), $this->blacklistReason);
        $this->showBlacklistModal = false;
        $this->blacklistReason = '';
        $this->refreshApplication();
        $this->dispatch('applicant-blacklisted');
    }

    public function closeBlacklistModal(): void
    {
        $this->showBlacklistModal = false;
        $this->blacklistReason = '';
    }

    #[On('document-updated')]
    #[On('answer-corrected')]
    #[On('answer-approved')]
    #[On('applicant-blacklisted')]
    public function refreshApplication(): void
    {
        $this->application->refresh();
        $this->application->load([
            'user',
            'scholarship.qualifications.options',
            'scholarship.qualifications.ranges',
            'documents.qualification',
            'answers.qualification',
            'answers.selectedOption',
            'score',
            'verificationLogs.verifier',
        ]);
    }

    public function render()
    {
        $documents = $this->application->documents;
        $answers = $this->application->answers;
        $score = $this->application->score;
        $logs = $this->application->verificationLogs()->with('verifier')->latest('created_at')->get();

        $approvedAnswerIds = $logs->where('action', 'answer_approved')->pluck('target_id')->unique()->toArray();

        return view('livewire.verifier.application-detail', compact('documents', 'answers', 'score', 'logs', 'approvedAnswerIds'))
            ->layout('components.layouts.app', ['title' => 'Detail Pendaftar — ' . $this->application->user?->name]);
    }
}
