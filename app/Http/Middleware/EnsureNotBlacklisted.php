<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureNotBlacklisted
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->is_blacklisted) {
            abort(403, 'Akun Anda tidak dapat mendaftar karena status blacklist aktif.');
        }

        return $next($request);
    }
}
