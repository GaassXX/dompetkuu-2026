<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;

class RegistrationResponse implements RegistrationResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        $url = match(true) {
            $user->hasRole('parent') => route('filament.parent.pages.dashboard'),
            $user->hasRole('child')  => route('filament.child.pages.dashboard'),
            default                  => '/',
        };

        return redirect($url);
    }
}
