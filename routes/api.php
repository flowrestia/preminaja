<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth endpoints with rate limiting
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:10,1');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1');

        // Protected auth routes
        Route::middleware(['auth:sanctum', 'active'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });
});
