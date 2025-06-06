<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // TODO: Upgrade JWT implementation to newest version of tymon/jwt-auth. 
        $user = JWTAuth::parseToken()->authenticate(); // @phpstan-ignore-line

        return $next($request);
    }
}
