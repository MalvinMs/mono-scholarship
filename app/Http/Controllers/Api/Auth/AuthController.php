<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Mail\OtpMail;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class AuthController extends BaseController
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:20', 'unique:users,nik'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('applicant');

        // Generate and send OTP email verification
        $service = app(OtpService::class);
        $result = $service->generate($user, 'email');
        Mail::to($user->email)->send(new OtpMail($result['plain']));

        $token = $user->createToken($validated['device_name'] ?? 'default');

        return $this->success([
            'user' => new \App\Http\Resources\Api\UserResource($user),
            'token' => $token->plainTextToken,
        ], 'Registrasi berhasil. OTP telah dikirim ke email Anda.', 201);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->error('Email atau password salah.', 401);
        }

        if (!$user->is_active) {
            return $this->error('Akun Anda tidak aktif.', 403);
        }

        if ($user->is_blacklisted) {
            return $this->error('Akun Anda terdaftar dalam blacklist.', 403);
        }

        $token = $user->createToken($validated['device_name'] ?? 'default');

        return $this->success([
            'user' => new \App\Http\Resources\Api\UserResource($user),
            'token' => $token->plainTextToken,
        ], 'Login berhasil.');
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout berhasil.');
    }

    public function forgotPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => ['required', 'string', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, __($status));
        }

        return $this->error(__($status), 400);
    }

    public function resetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(null, __($status));
        }

        return $this->error(__($status), 400);
    }

    public function user(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->success(new \App\Http\Resources\Api\UserResource($request->user()));
    }

    public function tokens(Request $request): \Illuminate\Http\JsonResponse
    {
        $tokens = $request->user()->tokens()->get()->map(fn ($token) => [
            'id' => $token->id,
            'name' => $token->name,
            'abilities' => $token->abilities,
            'last_used_at' => $token->last_used_at,
            'created_at' => $token->created_at,
        ]);

        return $this->success($tokens);
    }

    public function revokeToken(Request $request, string $tokenId): \Illuminate\Http\JsonResponse
    {
        $token = $request->user()->tokens()->findOrFail($tokenId);
        $token->delete();

        return $this->success(null, 'Token berhasil dihapus.');
    }
}
