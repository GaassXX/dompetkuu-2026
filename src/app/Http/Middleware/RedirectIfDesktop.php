<?php

namespace App\Http\Middleware;

use App\Support\RoleRedirector;
use Closure;
use Illuminate\Http\Request;

class RedirectIfDesktop
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole(['super_admin', 'admin'])) {
            return $next($request);
        }

        $isMobile = session('is_mobile_device');
        if ($isMobile === null) {
            $isMobile = DetectDevice::isMobileDevice($request);
        }

        if (!$isMobile) {
            return RoleRedirector::to($user, $request);
        }

        return $next($request);
    }
}
