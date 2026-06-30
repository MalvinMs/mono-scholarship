<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Mail\OtpMail;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class OtpController extends BaseController
{
    public function send(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'channel' => ['required', 'string', Rule::in(['email', 'whatsapp'])],
        ]);

        $user = $request->user();
        $service = app(OtpService::class);

        if (!$service->canResend($user, $validated['channel'])) {
            return $this->error('Silakan tunggu 60 detik sebelum mengirim OTP baru.', 429);
        }

        $result = $service->generate($user, $validated['channel']);

        if ($validated['channel'] === 'email') {
            Mail::to($user->email)->send(new OtpMail($result['plain']));
        }
        // WhatsApp OTP dikirim oleh SendNotification job (field)

        return $this->success([
            'channel' => $validated['channel'],
            'expires_at' => $result['otp']->expires_at,
        ], 'OTP berhasil dikirim.');
    }

    public function verify(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'channel' => ['required', 'string', Rule::in(['email', 'whatsapp'])],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $service = app(OtpService::class);

        if ($service->verify($request->user(), $validated['channel'], $validated['code'])) {
            return $this->success(null, 'Verifikasi berhasil.');
        }

        return $this->error('Kode OTP tidak valid atau sudah kedaluwarsa.', 422);
    }
}
