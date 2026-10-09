<?php

use App\Http\Controllers\Auth\WebAuthController;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Guest Authentication Routes (With Rate Limiting)
Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', Register::class)
        ->middleware('throttle:10,1')
        ->name('register');

    // Login
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])
        ->middleware('throttle:5,1');

    // Forgot & Reset Password
    Route::get('/forgot-password', [WebAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [WebAuthController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [WebAuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [WebAuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

// Authenticated Routes
Route::middleware(['auth', 'active'])->group(function () {
    // Logout
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

    // Email Verification
    Route::get('/email/verify', [WebAuthController::class, 'showVerifyEmailNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [WebAuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [WebAuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Buyer Area
    Route::middleware(['role:buyer'])->prefix('buyer')->as('buyer.')->group(function () {
        Route::get('/dashboard', function () {
            return view('buyer.dashboard');
        })->name('dashboard');
    });

    // Seller Area (Pending notice route accessible by all sellers)
    Route::middleware(['role:seller'])->prefix('seller')->as('seller.')->group(function () {
        Route::get('/pending', function () {
            return view('seller.pending');
        })->name('pending');

        // Feature seller jualan yang wajib approved
        Route::middleware(['seller.approved'])->group(function () {
            Route::get('/dashboard', \App\Http\Controllers\Seller\DashboardController::class)->name('dashboard');
            Route::resource('products', \App\Http\Controllers\Seller\ProductController::class);
        });
    });
});
