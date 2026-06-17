<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public ?int $applicationId,
        public string $channel,
        public string $eventType,
        public ?array $templateData = [],
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) return;

        $application = $this->applicationId
            ? \App\Models\Application::with('scholarship')->find($this->applicationId)
            : null;

        $scholarship = $application?->scholarship;

        $message = $this->buildMessage($user, $application, $scholarship);
        $recipient = $this->channel === 'email' ? $user->email : $user->phone;

        $status = 'sent';
        $errorMessage = null;

        try {
            if ($this->channel === 'email') {
                Mail::raw($message, function ($mail) use ($user) {
                    $mail->to($user->email)
                        ->subject('Platform Beasiswa — ' . $this->eventLabel());
                });
            } elseif ($this->channel === 'whatsapp') {
                $this->sendWhatsApp($user->phone, $message);
            }
        } catch (\Throwable $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error('Notification failed', [
                'user_id' => $this->userId,
                'channel' => $this->channel,
                'error' => $e->getMessage(),
            ]);
        }

        NotificationLog::create([
            'user_id' => $this->userId,
            'application_id' => $this->applicationId,
            'channel' => $this->channel,
            'event_type' => $this->eventType,
            'recipient' => $recipient,
            'message_body' => $message,
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => now(),
            'created_at' => now(),
        ]);
    }

    private function buildMessage(User $user, $application, $scholarship): string
    {
        $template = $this->getTemplate($scholarship);
        $data = $this->templateData;

        if (empty($data)) {
            $data = [
                'name' => $user->name,
                'registration_number' => $application?->registration_number ?? '-',
                'scholarship_name' => $scholarship?->name ?? '-',
                'status' => $application?->status ?? '-',
            ];
        }

        $replacements = [
            '{name}' => $data['name'] ?? $user->name,
            '{registration_number}' => $data['registration_number'] ?? ($application?->registration_number ?? '-'),
            '{scholarship_name}' => $data['scholarship_name'] ?? ($scholarship?->name ?? '-'),
            '{status}' => $data['status'] ?? ($application?->status ?? '-'),
            '{result}' => $data['result'] ?? '-',
        ];

        $message = $template ?: match ($this->eventType) {
            'registered' => "Halo {name},\n\nPendaftaran Anda di {scholarship_name} berhasil. No. Registrasi: {registration_number}.",
            'needs_revision' => "Halo {name},\n\nDokumen Anda perlu direvisi. Silakan cek dashboard untuk detail.",
            'result_announced' => "Halo {name},\n\nHasil seleksi {scholarship_name} telah diumumkan. Status: {result}.",
            'disbursed' => "Halo {name},\n\nDana beasiswa telah dicairkan. Silakan cek rekening Anda.",
            'blacklisted' => "Halo {name},\n\nAkun Anda telah diblacklist dari platform beasiswa.",
            'renewal_reminder' => "Halo {name},\n\nPeriode renewal {scholarship_name} telah dibuka. Segera submit dokumen Anda.",
            default => "Halo {name},\n\nAda update pada pendaftaran Anda di Platform Beasiswa.",
        };

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    private function getTemplate($scholarship): ?string
    {
        if (!$scholarship || !$scholarship->notification_templates) return null;

        $templates = $scholarship->notification_templates;
        return $templates[$this->eventType] ?? null;
    }

    private function sendWhatsApp(string $phone, string $message): void
    {
        $token = config('services.fonnte.token');
        $target = $this->sanitizePhone($phone);

        if (!$token || !$target) return;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => str_replace(
                    array_keys(config('services.fonnte.replacements', [])),
                    array_values(config('services.fonnte.replacements', [])),
                    $message
                ),
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $token,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            throw new \RuntimeException("Fonnte API error: HTTP {$httpCode} — {$response}");
        }
    }

    private function sanitizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    private function eventLabel(): string
    {
        return match ($this->eventType) {
            'registered' => 'Pendaftaran Berhasil',
            'needs_revision' => 'Revisi Dokumen',
            'result_announced' => 'Pengumuman Hasil',
            'disbursed' => 'Pencairan Dana',
            'blacklisted' => 'Pemberitahuan Blacklist',
            'renewal_reminder' => 'Reminder Renewal',
            'status_changed' => 'Update Status',
            default => 'Notifikasi',
        };
    }
}
