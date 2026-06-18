<?php

declare(strict_types=1);

namespace App\Livewire\Applicant;

use App\Mail\OtpMail;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class OtpVerification extends Component
{
    public string $channel = 'email';
    public string $code = '';
    public bool $codeSent = false;
    public bool $verified = false;
    public string $error = '';
    public bool $canResend = true;
    public int $countdown = 0;

    public function mount(string $channel = 'email')
    {
        $this->channel = $channel;
        $user = Auth::user();

        if ($channel === 'email' && $user->email_verified_at) {
            $this->verified = true;
            return;
        }
        if ($channel === 'whatsapp' && $user->phone_verified_at) {
            $this->verified = true;
            return;
        }

        // If an OTP was already sent (e.g. after registration), show the input field
        $latestOtp = \App\Models\OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if ($latestOtp) {
            $this->codeSent = true;
            $this->canResend = false;
            $this->countdown = $this->calculateRemainingCooldown($latestOtp);
        }
    }

    public function render()
    {
        return view('livewire.applicant.otp-verification');
    }

    public function sendOtp()
    {
        $user = Auth::user();
        $service = app(OtpService::class);

        if (!$service->canResend($user, $this->channel)) {
            $this->error = 'Silakan tunggu 60 detik sebelum mengirim ulang.';
            return;
        }

        $result = $service->generate($user, $this->channel);

        try {
            Mail::to($user->email)->send(new OtpMail($result['plain']));
        } catch (\Throwable $e) {
            Log::error('OTP email failed', [
                'user_id' => $user->id,
                'channel' => $this->channel,
                'error' => $e->getMessage(),
            ]);
            $this->error = 'Gagal mengirim email. Silakan coba lagi.';
            return;
        }

        $this->codeSent = true;
        $this->error = '';
        $this->canResend = false;
        $this->countdown = 60;
    }

    public function verify()
    {
        $this->validate(['code' => 'required|digits:6']);

        $user = Auth::user();
        $service = app(OtpService::class);

        if ($service->verify($user, $this->channel, $this->code)) {
            $this->verified = true;
            $this->error = '';
            $this->dispatch('otp-verified');
            $this->redirect(route('applicant.dashboard'));
        } else {
            $this->error = 'Kode OTP salah atau sudah kadaluarsa.';
        }
    }

    public function decrementCountdown()
    {
        if ($this->countdown > 0) {
            $this->countdown--;
        }
        if ($this->countdown <= 0) {
            $this->canResend = true;
        }
    }

    private function calculateRemainingCooldown(\App\Models\OtpVerification $otp): int
    {
        $secondsSinceCreated = (int) $otp->created_at->diffInSeconds(now());
        $remaining = 60 - $secondsSinceCreated;

        if ($remaining <= 0) {
            $this->canResend = true;
            return 0;
        }

        return $remaining;
    }
}
