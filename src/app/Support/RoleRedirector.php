<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class RoleRedirector
{
    /**
     * Tentukan redirect URL berdasarkan role/tipe akun user.
     * Dipakai bersama oleh LoginController & RegisterController
     * supaya logic redirect selalu konsisten di satu tempat.
     */
    public static function to(User $user): RedirectResponse
    {
        // Device mobile/tablet → semua role diarahkan ke UI mobile
        if (session('is_mobile_device', false)) {
            return redirect()->route('mobile.dashboard');
        }

        return match (true) {
            $user->hasRole('super_admin'),
            $user->hasRole('admin')   => redirect('/admin'),

            $user->hasRole('parent')  => redirect('/parent'),

            $user->is_independent     => redirect('/personal'),

            $user->hasRole('child')   => redirect('/child'),

            default                   => redirect('/'),
        };
    }
}
