<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Utils\RouteUtil;
use Closure;

class CheckIpLimitation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $schCode = $request->route()->parameter('schCode');

        // 外部からアクセスの場合、許可されている IP かチェック
        if (! RouteUtil::isPermitIp($schCode)) {
            abort(403);
        }

        return $next($request);
    }
}
