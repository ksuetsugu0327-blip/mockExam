<?php

namespace App\Http\Middleware;

use App\Utils\RouteUtil;
use Closure;

class CheckIpLimitation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $schCode = $request->route()->parameter('schCode');

        // 外部からアクセスの場合、許可されている IP かチェック
        if( !RouteUtil::isPermitIp( $schCode ) ){
            abort(403);
        }
        return $next($request);
    }
}
