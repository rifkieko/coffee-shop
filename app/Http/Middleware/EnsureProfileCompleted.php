<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $missingPhone = blank($user->phone);
        $emailIncomplete = blank($user->email) || is_null($user->email_verified_at);

        if ($missingPhone || $emailIncomplete) {
            $message = __('Lengkapi nomor telepon dan pastikan email Anda sudah terverifikasi sebelum melanjutkan pemesanan.');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'redirect' => route('profile.edit'),
                ], 422);
            }

            return redirect()->route('profile.edit')->withErrors($message);
        }

        return $next($request);
    }
}
