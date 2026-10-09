<?php

namespace App\Livewire\Auth;

use App\Enums\UserRole;
use App\Services\AuthService;
use App\Services\SellerRegistrationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Register extends Component
{
    public string $role = 'buyer'; // 'buyer' or 'seller'
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $phone = '';

    // Seller specific fields
    public string $store_name = '';
    public string $bio = '';
    public string $bank_name = 'BCA';
    public string $bank_account_number = '';
    public string $bank_account_holder = '';

    protected function rules(): array
    {
        return [
            'role' => ['required', Rule::in(['buyer', 'seller'])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:25'],
            'store_name' => [Rule::requiredIf(fn () => $this->role === 'seller'), 'nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'bank_name' => ['nullable', 'string', 'max:50'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function selectRole(string $role): void
    {
        $this->role = $role;
    }

    public function register(AuthService $authService, SellerRegistrationService $sellerService)
    {
        $validated = $this->validate();

        if ($this->role === 'seller') {
            $user = $sellerService->registerSeller($validated);
        } else {
            $user = $authService->registerBuyer($validated);
        }

        Auth::login($user);

        if ($this->role === 'seller') {
            return redirect()->route('seller.pending');
        }

        return redirect()->route('buyer.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.auth', ['title' => 'Daftar Akun PreminAja']);
    }
}
