<?php

namespace App\Livewire\Admin;

use App\Models\Scholarship;
use Livewire\Component;

class NotificationConfigurator extends Component
{
    public ?int $scholarshipId = null;
    public array $channels = ['whatsapp' => true, 'email' => false];
    public array $templates = [];
    public bool $saved = false;
    public ?string $previewKey = null;

    public array $eventTypes = [
        'registered' => 'Setelah Pendaftaran',
        'status_changed' => 'Perubahan Status',
        'needs_revision' => 'Revisi Dokumen',
        'result_announced' => 'Pengumuman Hasil',
        'renewal_reminder' => 'Reminder Renewal',
        'disbursed' => 'Pencairan Dana',
        'blacklisted' => 'Blacklist',
    ];

    public array $sampleData = [
        'name' => 'Ahmad Fauzi',
        'registration_number' => 'BBK2025-00123',
        'scholarship_name' => 'BBK Kabupaten Madiun 2025',
        'status' => 'Terverifikasi',
        'result' => 'Lolos Utama',
    ];

    public function togglePreview(string $key): void
    {
        $this->previewKey = $this->previewKey === $key ? null : $key;
    }

    public function renderPreview(string $template): string
    {
        $replacements = [
            '{name}' => $this->sampleData['name'],
            '{registration_number}' => $this->sampleData['registration_number'],
            '{scholarship_name}' => $this->sampleData['scholarship_name'],
            '{status}' => $this->sampleData['status'],
            '{result}' => $this->sampleData['result'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    public function mount(?int $scholarship = null)
    {
        $this->scholarshipId = $scholarship;
        if ($scholarship) {
            $this->loadScholarship();
        }
    }

    public function updatedScholarshipId(): void
    {
        $this->loadScholarship();
    }

    public function loadScholarship(): void
    {
        $this->saved = false;
        if (!$this->scholarshipId) {
            $this->channels = ['whatsapp' => true, 'email' => false];
            $this->templates = [];
            return;
        }

        $scholarship = Scholarship::find($this->scholarshipId);
        if (!$scholarship) return;

        $this->channels = $scholarship->notification_channels ?: ['whatsapp' => true, 'email' => false];
        $this->templates = $scholarship->notification_templates ?: [];

        foreach ($this->eventTypes as $key => $label) {
            if (!isset($this->templates[$key])) {
                $this->templates[$key] = $this->defaultTemplate($key);
            }
        }
    }

    public function save(): void
    {
        if (!$this->scholarshipId) return;

        Scholarship::find($this->scholarshipId)->update([
            'notification_channels' => $this->channels,
            'notification_templates' => $this->templates,
        ]);

        $this->saved = true;
        $this->dispatch('notify', type: 'success', message: 'Konfigurasi notifikasi disimpan.');
    }

    private function defaultTemplate(string $type): string
    {
        return match ($type) {
            'registered' => "Halo {name},\n\nPendaftaran Anda di {scholarship_name} berhasil. No. Registrasi: {registration_number}. Silakan pantau status melalui dashboard.",
            'needs_revision' => "Halo {name},\n\nDokumen Anda pada pendaftaran {scholarship_name} ({registration_number}) perlu direvisi. Silakan login dan upload ulang dokumen.",
            'result_announced' => "Halo {name},\n\nHasil seleksi {scholarship_name} telah diumumkan. Status Anda: {result}. Silakan cek dashboard untuk detail.",
            'disbursed' => "Halo {name},\n\nDana beasiswa {scholarship_name} telah dicairkan ke rekening Anda.",
            'blacklisted' => "Halo {name},\n\nAkun Anda telah diblacklist dari platform beasiswa terkait pelanggaran pada pendaftaran {scholarship_name}.",
            'renewal_reminder' => "Halo {name},\n\nPeriode renewal {scholarship_name} telah dibuka. Segera submit dokumen renewal Anda.",
            'status_changed' => "Halo {name},\n\nStatus pendaftaran Anda di {scholarship_name} ({registration_number}) telah berubah menjadi {status}.",
            default => "Halo {name},\n\nAda pembaruan pada pendaftaran Anda di Platform Beasiswa.",
        };
    }

    public function render()
    {
        $scholarships = Scholarship::latest()->get();

        return view('livewire.admin.notification-configurator', compact('scholarships'))
            ->layout('components.layouts.app', ['title' => 'Konfigurasi Notifikasi']);
    }
}
