<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiter cache between tests
        $this->artisan('cache:clear');
    }

    /**
     * 1. Registrasi buyer berhasil, status active, Spatie role buyer
     */
    public function test_buyer_registration_succeeds_and_is_active(): void
    {
        $email = 'buyerbaru_' . uniqid() . '@test.com';
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Buyer Baru Test',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'buyer',
            'phone' => '08123456789',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => $email,
                        'role' => 'buyer',
                        'status' => 'active',
                    ],
                ],
            ]);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isActive());
        $this->assertTrue($user->hasRole('buyer'));
        $this->assertNull($user->sellerProfile);
    }

    /**
     * 2. Registrasi dengan role superadmin harus ditolak (422) via API dan Web
     */
    public function test_registration_with_superadmin_role_is_rejected(): void
    {
        // 1. Via API
        $apiResponse = $this->postJson('/api/v1/auth/register', [
            'name' => 'Hacker Admin',
            'email' => 'hacker_' . uniqid() . '@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'superadmin',
        ]);
        $apiResponse->assertStatus(422)
            ->assertJsonValidationErrors(['role']);

        // 2. Via Web
        \Livewire\Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'Hacker Admin')
            ->set('email', 'hackerweb_' . uniqid() . '@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'superadmin')
            ->call('register')
            ->assertHasErrors(['role']);
    }

    /**
     * 3. Registrasi seller berhasil, users.status active, seller_profiles.verification_status pending
     */
    public function test_seller_registration_creates_pending_seller_profile(): void
    {
        $email = 'sellerbaru_' . uniqid() . '@test.com';
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Seller Baru Test',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'seller',
            'phone' => '081299998888',
            'store_name' => 'Toko Baru Maju ' . uniqid(),
            'bio' => 'Toko digital terpercaya',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Seller Baru',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'seller_profile' => [
                        'verification_status' => 'pending',
                    ],
                ],
            ]);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isActive());
        $this->assertTrue($user->hasRole('seller'));

        $this->assertNotNull($user->sellerProfile);
        $this->assertEquals(VerificationStatus::PENDING, $user->sellerProfile->verification_status);
        $this->assertFalse($user->sellerProfile->isVerified());
        $this->assertEquals('1234567890', $user->sellerProfile->bank_account_number);
    }

    /**
     * 4. Login sukses dan mendapatkan token Sanctum
     */
    public function test_user_login_succeeds_with_sanctum_token(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@preminaja.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'token_type' => 'Bearer',
                ],
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    /**
     * 5. Pesan error login generik identik untuk 3 kasus (email tidak ada, password salah, akun blocked dgn password salah)
     */
    public function test_login_error_responses_are_completely_identical(): void
    {
        // Kasus 1: Email tidak terdaftar di sistem
        $resNonExistent = $this->postJson('/api/v1/auth/login', [
            'email' => 'tidak_ada_di_db_' . uniqid() . '@preminaja.id',
            'password' => 'apapun123',
        ]);

        // Kasus 2: Email terdaftar (buyer), password salah
        $resWrongPassword = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@preminaja.id',
            'password' => 'salahpassword123',
        ]);

        // Kasus 3: Akun diblokir, tapi password yang dicoba salah
        $resBlockedWrongPassword = $this->postJson('/api/v1/auth/login', [
            'email' => 'blocked@preminaja.id',
            'password' => 'salahpassword123',
        ]);

        $this->assertEquals(401, $resNonExistent->status());
        $this->assertEquals(401, $resWrongPassword->status());
        $this->assertEquals(401, $resBlockedWrongPassword->status());

        $this->assertEquals($resNonExistent->json('message'), $resWrongPassword->json('message'));
        $this->assertEquals($resWrongPassword->json('message'), $resBlockedWrongPassword->json('message'));
    }

    /**
     * 6. Login ditolak jika users.status = blocked (saat password benar)
     */
    public function test_blocked_user_login_with_correct_password_is_rejected(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'blocked@preminaja.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * 7. Saat status diubah menjadi blocked, semua token Sanctum dihapus dan ditolak middleware active
     */
    public function test_tokens_revoked_and_rejected_when_user_status_changed_to_blocked(): void
    {
        $user = User::factory()->create([
            'email' => 'tokenblock_' . uniqid() . '@test.com',
            'status' => UserStatus::ACTIVE,
            'role' => UserRole::BUYER,
        ]);
        $user->assignRole('buyer');

        $token = $user->createToken('test-device')->plainTextToken;

        // Verifikasi token aktif bisa akses endpoint me
        $initialResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');
        $initialResponse->assertStatus(200);

        // Ubah status menjadi blocked via model
        $user->status = UserStatus::BLOCKED;
        $user->save();

        // Verifikasi token di tabel personal_access_tokens telah terhapus
        $this->assertEquals(0, $user->tokens()->count());

        // Refresh test client auth state
        app('auth')->forgetGuards();

        // Request dengan token tadi sekarang wajib ditolak (401 Unauthenticated karena token terhapus)
        $blockedResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');
        $this->assertEquals(401, $blockedResponse->status());
    }

    /**
     * 8. Seller pending ditolak di rute seller dashboard, tapi seller approved bisa masuk
     */
    public function test_seller_route_access_pending_vs_approved(): void
    {
        $sellerPending = User::where('email', 'sellerpending@preminaja.id')->first();
        $this->assertNotNull($sellerPending);

        // Pending seller diarahkan ke /seller/pending
        $responsePending = $this->actingAs($sellerPending)->get('/seller/dashboard');
        $responsePending->assertRedirect(route('seller.pending'));

        // Seller approved (seller@preminaja.id) bisa akses /seller/dashboard (200)
        $sellerApproved = User::where('email', 'seller@preminaja.id')->first();
        $this->assertNotNull($sellerApproved);

        $responseApproved = $this->actingAs($sellerApproved)->get('/seller/dashboard');
        $responseApproved->assertStatus(200);
    }

    /**
     * 9. Buyer ditolak di panel admin Filament (/admin), Superadmin bisa masuk
     */
    public function test_panel_admin_access(): void
    {
        $buyer = User::where('email', 'buyer@preminaja.id')->first();
        $this->assertNotNull($buyer);
        $this->actingAs($buyer)->get('/admin')->assertStatus(403);

        $superadmin = User::where('email', 'admin@preminaja.id')->first();
        $this->assertNotNull($superadmin);
        $this->actingAs($superadmin)->get('/admin')->assertSuccessful();
    }

    /**
     * 10. Sinkronisasi perubahan role antara users.role dan Spatie roles
     */
    public function test_role_synchronization_between_column_and_spatie(): void
    {
        $user = User::factory()->create([
            'email' => 'rolesync_' . uniqid() . '@test.com',
            'status' => UserStatus::ACTIVE,
            'role' => UserRole::BUYER,
        ]);
        $user->assignRole('buyer');

        $this->assertTrue($user->hasRole('buyer'));
        $this->assertFalse($user->hasRole('seller'));

        // Ubah properti role di kolom user
        $user->role = UserRole::SELLER;
        $user->save();

        // Cek sinkronisasi otomatis via booted hook
        $this->assertTrue($user->fresh()->hasRole('seller'));
        $this->assertFalse($user->fresh()->hasRole('buyer'));
    }

    /**
     * 11. API logout dan API me
     */
    public function test_api_me_and_logout(): void
    {
        $user = User::where('email', 'buyer@preminaja.id')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        // Test API Me
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => 'buyer@preminaja.id',
                    'role' => 'buyer',
                ],
            ]);

        // Test API Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * 12. Lupa dan reset password via Web
     */
    public function test_web_forgot_and_reset_password_flow(): void
    {
        $email = 'pwdtest_' . uniqid() . '@test.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password_lama123'),
            'status' => UserStatus::ACTIVE,
            'role' => UserRole::BUYER,
        ]);
        $user->assignRole('buyer');

        // Request reset link
        $resForgot = $this->post('/forgot-password', [
            'email' => $email,
        ]);
        $resForgot->assertSessionHas('status');

        // Generate token for testing reset
        $token = Password::createToken($user);

        // Submit new password
        $resReset = $this->post('/reset-password', [
            'token' => $token,
            'email' => $email,
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $resReset->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('password_baru123', $user->fresh()->password));
    }

    /**
     * 13. Verifikasi email bekerja dengan signed route
     */
    public function test_email_verification_flow(): void
    {
        $email = 'unverified_' . uniqid() . '@test.com';
        $unverifiedUser = User::factory()->create([
            'email' => $email,
            'email_verified_at' => null,
            'role' => UserRole::BUYER,
            'status' => UserStatus::ACTIVE,
        ]);
        $unverifiedUser->assignRole('buyer');

        $this->assertFalse($unverifiedUser->hasVerifiedEmail());

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $unverifiedUser->id, 'hash' => sha1($unverifiedUser->email)]
        );

        $response = $this->actingAs($unverifiedUser)->get($verificationUrl);
        $response->assertRedirect('/');

        $this->assertTrue($unverifiedUser->fresh()->hasVerifiedEmail());
    }

    /**
     * 14. Rate limiting pada login
     */
    public function test_login_rate_limiting(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'buyer@preminaja.id',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt must trigger 429 Too Many Requests
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'buyer@preminaja.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }
}
