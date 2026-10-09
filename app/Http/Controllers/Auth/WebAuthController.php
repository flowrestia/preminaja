<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $this->authService->authenticate($validated['email'], $validated['password']);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->isSuperAdmin()) {
            return redirect()->intended('/admin');
        }

        if ($user->isSeller()) {
            return redirect()->intended(route('seller.dashboard'));
        }

        return redirect()->intended(route('buyer.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Tautan pemulihan kata sandi telah dikirim ke email Anda.')
            : back()->withErrors(['email' => 'Gagal mengirim tautan reset kata sandi ke email tersebut.']);
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Kata sandi Anda berhasil diperbarui! Silakan masuk.')
            : back()->withErrors(['email' => 'Token reset kata sandi tidak valid atau telah kedaluwarsa.']);
    }

    public function showVerifyEmailNotice(Request $request): View|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended('/')
            : view('auth.verify-email');
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->intended('/')->with('verified', true);
    }

    public function resendVerificationEmail(Request $request): RedirectResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Tautan verifikasi baru telah dikirimkan ke alamat email Anda.');
    }
}
