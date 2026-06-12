<?php

namespace App\Filament\Parent\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Form;
use Filament\Forms\Components\Component;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();
        $user = auth()->user();

        if (! $user?->hasRole('parent')) {
            auth()->logout();
            $this->addError(
                'email',
                'Anda tidak memiliki akses ke panel Parent.'
            );
            return null;
        }

        return $response;
    }

    protected function getRegisterAction(): Component
    {
        return \Filament\Actions\Action::make('register')
            ->label('Buat akun sekarang')
            ->url(route('filament.parent.auth.register'));
    }
}
