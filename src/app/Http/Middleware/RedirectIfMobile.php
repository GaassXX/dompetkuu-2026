<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfMobile
{
    protected array $allowedPaths = [
        'parent/laporan',
        'parent/savings-overview',
        'parent/anggaran',
        'parent/family-members',
        'parent/approval-queue',
        'parent/transaction-view',
        'parent/incomes',
        'parent/expenses',
        'parent/saving-resource',
        'child/laporan',
        'child/savings-overview',
        'child/budget-view',
        'child/transaction-view',
        'child/incomes',
        'child/expenses',
        'child/saving-resource',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole(['super_admin', 'admin'])) {
            return $next($request);
        }

        $path = $request->path();

        foreach ($this->allowedPaths as $allowed) {
            if (str_starts_with($path, $allowed)) {
                return $next($request);
            }
        }

        $isMobile = session('is_mobile_device');
        if ($isMobile === null) {
            $isMobile = DetectDevice::isMobileDevice($request);
        }

        if ($isMobile) {
            return redirect('/mobile/dashboard');
        }

        return $next($request);
    }
}
