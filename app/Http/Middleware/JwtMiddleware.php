<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Coba autentikasi token
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // Token telah kedaluwarsa
            return response()->json([
                'message' => 'Token has expired',
                'code' => 'TOKEN_EXPIRED',
                'details' => $e->getMessage()
            ], 401);
        } catch (TokenInvalidException $e) {
            // Token tidak valid
            return response()->json([
                'message' => 'Token is invalid',
                'code' => 'TOKEN_INVALID',
                'details' => $e->getMessage()
            ], 401);
        } catch (JWTException $e) {
            // Token tidak ditemukan
            return response()->json([
                'message' => 'Token is missing',
                'code' => 'TOKEN_MISSING',
                'details' => $e->getMessage()
            ], 401);
        }

        // Lanjutkan request jika token valid
        return $next($request);
    }
}
