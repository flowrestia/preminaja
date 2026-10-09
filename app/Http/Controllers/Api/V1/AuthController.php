<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use App\Services\SellerRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected SellerRegistrationService $sellerRegistrationService
    ) {}

    /**
     * Register a new user (buyer or seller) via API.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($validated['role'] === UserRole::SELLER->value) {
            $user = $this->sellerRegistrationService->registerSeller($validated);
        } else {
            $user = $this->authService->registerBuyer($validated);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'status' => $user->status->value,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
            'seller_profile' => $user->sellerProfile ? [
                'store_name' => $user->sellerProfile->store_name,
                'store_slug' => $user->sellerProfile->store_slug,
                'verification_status' => $user->sellerProfile->verification_status->value,
            ] : null,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Pendaftaran akun berhasil.', Response::HTTP_CREATED);
    }

    /**
     * Authenticate user credentials and return Sanctum token.
     * Uses generic error messaging to avoid email enumeration.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $user = $this->authService->authenticate($validated['email'], $validated['password']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'status' => $user->status->value,
                'email_verified' => $user->hasVerifiedEmail(),
            ],
            'seller_profile' => $user->sellerProfile ? [
                'store_name' => $user->sellerProfile->store_name,
                'store_slug' => $user->sellerProfile->store_slug,
                'verification_status' => $user->sellerProfile->verification_status->value,
            ] : null,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login berhasil.');
    }

    /**
     * Logout current session and revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return ApiResponse::success(null, 'Berhasil keluar (logout).');
    }

    /**
     * Return authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return ApiResponse::success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role->value,
            'status' => $user->status->value,
            'email_verified' => $user->hasVerifiedEmail(),
            'avatar_url' => $user->avatar_url,
            'seller_profile' => $user->sellerProfile ? [
                'id' => $user->sellerProfile->id,
                'store_name' => $user->sellerProfile->store_name,
                'store_slug' => $user->sellerProfile->store_slug,
                'verification_status' => $user->sellerProfile->verification_status->value,
                'rating_avg' => $user->sellerProfile->rating_avg,
                'rating_count' => $user->sellerProfile->rating_count,
            ] : null,
        ], 'Data profil berhasil diambil.');
    }
}
