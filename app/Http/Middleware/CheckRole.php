<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Unauthorized',
                'code' => "VALIDATION_ERROR",
                'details' => 'Access denied. You do not have the necessary role to perform this action.'
            ], 401);
        }

        return $next($request);
    }
}
