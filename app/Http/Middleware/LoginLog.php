<?php

namespace App\Http\Middleware;

use App\Models;
use Closure;

class LoginLog extends Model
{
    /**
     * Handle an incoming request.
     * 记录登陆日志
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        //执行动作
    $log = LoginLog::create(['ip' =>$request->ip()]);
    $log->save();

        return $response;
    }
}
