<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Product::class, \App\Policies\ProductPolicy::class);

        // Validasi CREDENTIAL_HASH_KEY saat aplikasi booting di luar local/testing
        if (! app()->environment(['local', 'testing'])) {
            $key = config('app.credential_hash_key');
            if (empty($key) || str_contains($key, 'your-32-character-secret-key') || str_contains($key, 'default-credential-hash-key')) {
                throw new \RuntimeException(
                    'FATAL SECURITY CONFIGURATION: CREDENTIAL_HASH_KEY wajib diatur dengan secret key yang aman dan tidak boleh kosong atau menggunakan placeholder di luar environment local/testing.'
                );
            }
        }
    }
}
