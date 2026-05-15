<?php

namespace App\Filament\Admin\Auth;

use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?\Filament\Http\Responses\Auth\Contracts\LoginResponse
    {
        $response = parent::authenticate();

        $user = Filament::auth()->user();

        if (! $user?->hasRole('admin')) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'Anda tidak memiliki akses ke panel Admin.',
            ]);
        }

        return $response;
    }

    public function form(Form $form): Form
    {
        return parent::form($form);
    }
}
