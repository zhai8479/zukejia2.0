<?php

namespace App\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

/**
 * 刷新token中间件
 * Class RefreshToken
 * @package App\Middleware
 */
class RefreshToken
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
        $response = $next($request);
        $user = $request->user();
        if ($user) {
            $newToken = \JWTAuth::fromUser($user);
            $response->headers->set('Authorization', 'Bearer '.$newToken);
        }
        return $response;
    }
}
