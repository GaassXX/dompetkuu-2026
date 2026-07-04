<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectDevice
{
    public function handle(Request $request, Closure $next)
    {
        $isMobile = self::isMobileDevice($request);
        session(['is_mobile_device' => $isMobile]);
        view()->share('isMobileDevice', $isMobile);
        return $next($request);
    }

    public static function isMobileDevice(Request $request): bool
    {
        $ua = $request->userAgent() ?? '';
        if ($ua === '') return false;

        $patterns = [
            '/Mobile|iP(hone|od|ad)|Android|BlackBerry|IEMobile|Kindle|NetFront|Silk-Accelerated|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune|Windows\sCE/',

            '/Tablet|iPad|PlayBook|Silk|Kindle|(Android(?!.*Mobile))/',

            '/240x320|netfront|portalmmm|polaris|vodafone|bada|SonyEricsson|Symbian|Nokia|Samsung|Motorola|LG-|Alcatel|ericsson|iphone|ipod|android|pocket|palm|windows\sce/',

            '/midp|cldc|series60|series40|j2me|AVANTGO|Go.Web|WapMan|SCH-|SPH-|PRiSMS|SEC-|SGH-|AUDIOVOX|MP9/',

            '/Xiino|BlackBerry|KDDI|MOT-|SIE-|PHILIPS|PANTECH|LENOVO|BIRD|TOSHIBA|SHARP|FUJITSU|SAGEM|MITSUBISHI|NEC|PANASONIC|BENQ|BENQ-SIEMENS|SANYO|VK-|Vertu|iPAQ|PDAG|HTC_|Desire|Sensation|Wildfire|Evo|Incredible|Hero|HD2|Nexus\s(One|S|4|5|6|7|9|10)|Galaxy\s(S|Note|A|J|Tab)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $ua)) return true;
        }

        return false;
    }
}
