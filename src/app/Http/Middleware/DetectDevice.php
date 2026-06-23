<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class DetectDevice
{
    public function handle(Request $request, Closure $next)
    {
        $agent = new Agent();
        $isMobile = $agent->isMobile() || $agent->isTablet();

        session(['is_mobile_device' => $isMobile]);
        view()->share('isMobileDevice', $isMobile);

        return $next($request);
    }
}
