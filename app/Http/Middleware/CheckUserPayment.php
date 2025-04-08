<?php

namespace App\Http\Middleware;

use App\Models\Payments;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPayment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $hasPaid = Payments::where('user_id', $user->id)
                ->where('status', 'paid')
                ->exists();

            if (!$hasPaid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda belum melakukan pembayaran.',
                ], 403);
            }
        }

        return $next($request);
    }
}
