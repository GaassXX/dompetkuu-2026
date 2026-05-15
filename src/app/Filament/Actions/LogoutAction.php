<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class LogoutAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'logout';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function () {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/login');
        });
    }
}
