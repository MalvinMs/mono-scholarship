<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        $fallback = match (true) {
            $user->hasRole('super-admin') => $this->route('admin.dashboard'),
            $user->hasRole('admin') => $this->route('admin.dashboard'),
            $user->hasRole('verifier') => $this->route('verification.queue'),
            $user->hasRole('approver') => $this->route('approver.dashboard'),
            $user->hasRole('treasurer') => $this->route('treasurer.disbursements'),
            default => $this->route('applicant.dashboard'),
        };

        $intended = session()->pull('url.intended');

        if ($intended && $intended !== route('login')) {
            return redirect()->to($intended);
        }

        return redirect()->to($fallback);
    }

    private function route(string $name): string
    {
        return Route::has($name) ? route($name) : '/dashboard';
    }
}
