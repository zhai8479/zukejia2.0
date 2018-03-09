<?php

namespace App\Http\Middleware;

use Closure;

class CheckLog
{
    /**
     * 记录操作日志
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
