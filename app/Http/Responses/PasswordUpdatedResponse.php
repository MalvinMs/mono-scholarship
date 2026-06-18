<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\PasswordUpdatedResponse as PasswordUpdatedResponseContract;

final class PasswordUpdatedResponse implements PasswordUpdatedResponseContract
{
    public function toResponse($request)
    {
        return redirect()
            ->route('profile.edit')
            ->with('status', 'password-updated');
    }
}
