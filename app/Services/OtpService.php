<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class OtpService
{
    /**
     * Generate and persist an OTP code.
     *
     * @return array{otp: OtpVerification, plain: string}
     */
    public function generate(User $user, string $channel): array
    {
        // Invalidate old unused codes
        OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->where('is_used', false)
            ->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $otp = OtpVerification::create([
            'user_id' => $user->id,
            'channel' => $channel,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
        ]);

        return ['otp' => $otp, 'plain' => $code];
    }

    public function verify(User $user, string $channel, string $inputCode): bool
    {
        $otp = OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (!$otp || !Hash::check($inputCode, $otp->code)) {
            return false;
        }

        $otp->update(['is_used' => true]);

        // Mark channel as verified
        if ($channel === 'email') {
            $user->update(['email_verified_at' => now()]);
        } elseif ($channel === 'whatsapp') {
            $user->update(['phone_verified_at' => now()]);
        }

        return true;
    }

    public function canResend(User $user, string $channel): bool
    {
        $lastOtp = OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->latest('created_at')
            ->first();

        if (!$lastOtp) {
            return true;
        }

        // Rate limit: 1 OTP per 60 seconds
        return $lastOtp->created_at->diffInSeconds(now()) > 60;
    }

    public function getPlainCode(OtpVerification $otp): string
    {
        // We store hashed codes, so the plain code must be returned at generation time
        // This method is a placeholder — actual plain code is returned by generate()
        return '';
    }
}
