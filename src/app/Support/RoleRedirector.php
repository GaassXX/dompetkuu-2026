<?php

namespace App\Support;

use App\Http\Middleware\DetectDevice;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoleRedirector
{
    public static function to(User $user, ?Request $request = null): RedirectResponse
    {
        $request ??= request();
        $isMobile = session('is_mobile_device');
        if ($isMobile === null) {
            $isMobile = DetectDevice::isMobileDevice($request);
        }
        if (!$user->hasRole(['super_admin', 'admin']) && $isMobile) {
            return redirect('/mobile/dashboard');
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
