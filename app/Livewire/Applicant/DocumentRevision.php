<?php

namespace App\Livewire\Applicant;

use App\Models\Application;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentRevision extends Component
{
    use WithFileUploads;

    public Application $application;
    public array $newFiles = [];

    public function mount(Application $application)
    {
        $this->application = $application->load(['documents' => fn($q) => $q->where('verification_status', 'rejected')]);
    }

    public function render()
    {
        return view('livewire.applicant.document-revision')
            ->layout('components.layouts.app', ['title' => 'Revisi Dokumen']);
    }

    public function reupload(int $documentId)
    {
        $this->validate([
            "newFiles.{$documentId}" => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
        ]);

        $doc = $this->application->documents->find($documentId);
        $file = $this->newFiles[$documentId];

        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileMime = $file->getMimeType();

        $path = $file->store(
            "documents/{$this->application->scholarship_id}/{$this->application->id}/{$doc->qualification_id}",
            'minio'
        );

        $doc->update([
            'file_path' => $path,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'mime_type' => $fileMime,
            'verification_status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $this->application->update(['status' => 'under_review']);
        $this->application->load(['documents' => fn($q) => $q->where('verification_status', 'rejected')]);
        $this->reset('newFiles');

        session()->flash('message', 'Dokumen berhasil di-upload ulang.');
    }
}
