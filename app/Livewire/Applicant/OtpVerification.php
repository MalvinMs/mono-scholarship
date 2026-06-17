<?php

namespace App\Livewire\Applicant;

use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
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
        }
        if ($channel === 'whatsapp' && $user->phone_verified_at) {
            $this->verified = true;
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

        $service->generate($user, $this->channel);
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
}
