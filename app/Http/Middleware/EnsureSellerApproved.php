<?php

namespace App\Http\Middleware;

use App\Enums\VerificationStatus;
use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerApproved
{
    /**
     * Handle an incoming request.
     * Ensures seller user has a verified seller profile (seller_profiles.verification_status === approved).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error('Tidak terautentikasi.', Response::HTTP_UNAUTHORIZED);
            }
            return redirect()->route('login');
        }

        $profile = $user->sellerProfile;

        if (! $profile || $profile->verification_status !== VerificationStatus::APPROVED) {
            $status = $profile ? $profile->verification_status->value : 'not_registered';
            $message = match ($status) {
                'pending' => 'Toko Anda sedang menunggu proses verifikasi oleh tim PreminAja. Fitur jualan akan aktif setelah disetujui.',
                'rejected' => 'Permohonan verifikasi toko Anda ditolak: ' . ($profile->verification_notes ?? 'Silakan lengkapi data profil.'),
                default => 'Anda belum terdaftar sebagai penjual di PreminAja.',
            };

            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error($message, Response::HTTP_FORBIDDEN, [
                    'verification_status' => $status,
                ]);
            }

            return redirect()->route('seller.pending')->with('warning', $message);
        }

        return $next($request);
    }
}
