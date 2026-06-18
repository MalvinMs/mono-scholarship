<?php

use App\Mail\OtpMail;
use App\Models\OtpVerification;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

// ── OtpService ──

it('generates a 6-digit OTP and stores it hashed', function () {
    $user = User::factory()->unverified()->applicant()->create();
    $service = app(OtpService::class);

    $result = $service->generate($user, 'email');

    expect($result)->toHaveKeys(['otp', 'plain']);
    expect($result['plain'])->toMatch('/^\d{6}$/');
    expect($result['otp'])->toBeInstanceOf(OtpVerification::class);
    expect(Hash::check($result['plain'], $result['otp']->code))->toBeTrue();
});

it('invalidates previous unused OTPs when generating a new one', function () {
    $user = User::factory()->unverified()->applicant()->create();
    $service = app(OtpService::class);

    $first = $service->generate($user, 'email');
    $second = $service->generate($user, 'email');

    expect(OtpVerification::where('user_id', $user->id)->where('is_used', false)->count())->toBe(1);
    expect($second['otp']->id)->not->toBe($first['otp']->id);
});

it('verifies a correct OTP', function () {
    $user = User::factory()->unverified()->applicant()->create();
    $service = app(OtpService::class);

    $result = $service->generate($user, 'email');
    $verified = $service->verify($user, 'email', $result['plain']);

    expect($verified)->toBeTrue();
    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

it('rejects an incorrect OTP', function () {
    $user = User::factory()->unverified()->applicant()->create();
    $service = app(OtpService::class);

    $service->generate($user, 'email');
    $verified = $service->verify($user, 'email', '000000');

    expect($verified)->toBeFalse();
    expect($user->fresh()->email_verified_at)->toBeNull();
});

it('rejects an expired OTP', function () {
    $user = User::factory()->unverified()->applicant()->create();
    $otp = OtpVerification::create([
        'user_id' => $user->id,
        'channel' => 'email',
        'code' => Hash::make('123456'),
        'expires_at' => now()->subMinute(),
        'created_at' => now()->subMinutes(6),
    ]);

    $service = app(OtpService::class);
    $verified = $service->verify($user, 'email', '123456');

    expect($verified)->toBeFalse();
});

it('enforces 60-second cooldown between sends', function () {
    $user = User::factory()->unverified()->applicant()->create();
    $service = app(OtpService::class);

    $service->generate($user, 'email');
    $can = $service->canResend($user, 'email');

    expect($can)->toBeFalse();
});

// ── OtpMail Mailable ──

it('builds an OTP email with correct subject and code', function () {
    $mail = new OtpMail('123456');

    expect($mail->code)->toBe('123456');
    expect($mail->envelope()->subject)->toBe('Kode Verifikasi Email — Platform Beasiswa');
});

// ── Registration Flow ──

it('sends an OTP email when user registers', function () {
    $data = [
        'name' => 'Test User',
        'nik' => '3201010101010001',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->post('/register', $data);

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('applicant'))->toBeTrue();
    expect($user->email_verified_at)->toBeNull(); // not verified yet

    // OTP should be in the database
    $otp = OtpVerification::where('user_id', $user->id)->where('channel', 'email')->first();
    expect($otp)->not->toBeNull();
    expect($otp->is_used)->toBeFalse();

    // Email should have been sent
    Mail::assertSent(OtpMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('prevents OTP reuse', function () {
    $user = User::factory()->unverified()->applicant()->create();
    $service = app(OtpService::class);

    $result = $service->generate($user, 'email');
    $firstTry = $service->verify($user, 'email', $result['plain']);
    $secondTry = $service->verify($user, 'email', $result['plain']);

    expect($firstTry)->toBeTrue();
    expect($secondTry)->toBeFalse(); // code already used
});
