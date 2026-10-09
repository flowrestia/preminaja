<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status === UserStatus::BLOCKED) {
            if ($request->expectsJson() || $request->is('api/*')) {
                // Revoke current token if using Sanctum
                if ($user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }
                return ApiResponse::error('Akun Anda telah dinonaktifkan atau diblokir oleh sistem. Silakan hubungi dukungan pelanggan.', Response::HTTP_FORBIDDEN);
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan atau diblokir oleh sistem.',
            ]);
        }

        return $next($request);
    }
}
