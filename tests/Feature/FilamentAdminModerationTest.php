<?php

namespace Tests\Feature;

use App\Enums\DeliveryType;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\SellerProfileResource;
use App\Filament\Resources\UserResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Setting;
use App\Models\SettingHistory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SuperadminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentAdminModerationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $buyerUser;
    protected User $sellerUser;
    protected SellerProfile $sellerProfile;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(SuperadminSeeder::class);

        $this->superadmin = User::where('email', env('SUPERADMIN_EMAIL', 'admin@preminaja.id'))->first();

        $this->buyerUser = User::factory()->create([
            'role' => UserRole::BUYER,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->buyerUser->assignRole('buyer');

        $this->sellerUser = User::factory()->create([
            'role' => UserRole::SELLER,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->sellerUser->assignRole('seller');

        $this->sellerProfile = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'store_name' => 'Toko Moderasi Test',
            'store_slug' => 'tokomoderasi',
            'verification_status' => VerificationStatus::APPROVED,
        ]);

        $category = Category::create([
            'name' => 'Produktivitas',
            'slug' => 'produktivitas',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $category->id,
            'name' => 'ChatGPT Team Subscription',
            'slug' => 'chatgpt-team-subscription',
            'description' => 'Akun ChatGPT berbayar resmi',
            'delivery_type' => DeliveryType::AUTOMATED,
            'status' => ProductStatus::PENDING_REVIEW,
        ]);
    }

    public function test_buyer_and_seller_cannot_access_filament_admin_panel(): void
    {
        // Buyer akses /admin -> 403 Forbidden
        $buyerResponse = $this->actingAs($this->buyerUser)->get('/admin');
        $buyerResponse->assertStatus(403);

        // Seller akses /admin -> 403 Forbidden
        $sellerResponse = $this->actingAs($this->sellerUser)->get('/admin');
        $sellerResponse->assertStatus(403);

        // Superadmin akses /admin -> 200 OK
        $adminResponse = $this->actingAs($this->superadmin)->get('/admin');
        $adminResponse->assertStatus(200);
    }

    public function test_product_approval_and_rejection_requiring_mandatory_reason(): void
    {
        // 1. Approve produk ke published
        Livewire::actingAs($this->superadmin)
            ->test(ProductResource\Pages\ListProducts::class)
            ->callTableAction('approve', $this->product);

        $this->product->refresh();
        $this->assertEquals(ProductStatus::PUBLISHED, $this->product->status);

        // 2. Reject produk tanpa alasan ditolak (validasi error / form invalid)
        Livewire::actingAs($this->superadmin)
            ->test(ProductResource\Pages\ListProducts::class)
            ->callTableAction('reject', $this->product, data: [
                'rejection_reason' => '', // Kosong -> harus ditolak
            ])
            ->assertHasTableActionErrors(['rejection_reason']);

        // 3. Reject dengan alasan yang sah
        Livewire::actingAs($this->superadmin)
            ->test(ProductResource\Pages\ListProducts::class)
            ->callTableAction('reject', $this->product, data: [
                'rejection_reason' => 'Deskripsi produk melanggar syarat ketentuan toko.',
            ])
            ->assertHasNoTableActionErrors();

        $this->product->refresh();
        $this->assertEquals(ProductStatus::REJECTED, $this->product->status);
        $this->assertEquals('Deskripsi produk melanggar syarat ketentuan toko.', $this->product->rejection_reason);
    }

    public function test_blocked_seller_products_disappear_from_visible_in_catalog_scope(): void
    {
        // Setujui produk ke published
        $this->product->update(['status' => ProductStatus::PUBLISHED]);

        // Cek scope awal visibleInCatalog: produk harus muncul
        $this->assertTrue(Product::visibleInCatalog()->where('id', $this->product->id)->exists());

        // Blokir seller via Filament SellerProfileResource action
        Livewire::actingAs($this->superadmin)
            ->test(SellerProfileResource\Pages\ListSellerProfiles::class)
            ->callTableAction('blockSeller', $this->sellerProfile);

        $this->sellerUser->refresh();
        $this->assertEquals(UserStatus::BLOCKED, $this->sellerUser->status);

        // Scope visibleInCatalog: Produk otomatis TIDAK BOLEH tampil
        $this->assertFalse(Product::visibleInCatalog()->where('id', $this->product->id)->exists());
    }

    public function test_superadmin_cannot_block_themselves_or_other_superadmins_in_user_resource(): void
    {
        $otherSuperadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'status' => UserStatus::ACTIVE,
        ]);
        $otherSuperadmin->assignRole('superadmin');

        // 1. Cek tombol aksi blokir pada diri sendiri tidak muncul
        Livewire::actingAs($this->superadmin)
            ->test(UserResource\Pages\ListUsers::class)
            ->assertTableActionHidden('blockUser', $this->superadmin);

        // 2. Cek tombol aksi blokir pada sesama superadmin tidak muncul
        Livewire::actingAs($this->superadmin)
            ->test(UserResource\Pages\ListUsers::class)
            ->assertTableActionHidden('blockUser', $otherSuperadmin);

        // 3. Buyer biasa bisa diblokir
        Livewire::actingAs($this->superadmin)
            ->test(UserResource\Pages\ListUsers::class)
            ->assertTableActionVisible('blockUser', $this->buyerUser)
            ->callTableAction('blockUser', $this->buyerUser);

        $this->buyerUser->refresh();
        $this->assertEquals(UserStatus::BLOCKED, $this->buyerUser->status);
    }

    public function test_settings_range_validation_and_audit_history_logging(): void
    {
        // Validasi rentang: komisi di atas 50% atau hari di atas 30 harus error
        Livewire::actingAs($this->superadmin)
            ->test(ManageSettings::class)
            ->fillForm([
                'platform_fee_percentage' => 75, // > 50 -> error
                'escrow_holding_days' => 45, // > 30 -> error
                'min_withdrawal' => 0, // <= 0 -> error
            ])
            ->call('save')
            ->assertHasFormErrors([
                'platform_fee_percentage',
                'escrow_holding_days',
                'min_withdrawal',
            ]);

        // Simpan nilai yang sah
        Livewire::actingAs($this->superadmin)
            ->test(ManageSettings::class)
            ->fillForm([
                'platform_fee_percentage' => 7.5,
                'escrow_holding_days' => 4,
                'min_withdrawal' => 100000,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // Verifikasi Setting terupdate
        $this->assertEquals(7.5, Setting::get('platform_fee_percentage'));
        $this->assertEquals(4, Setting::get('escrow_holding_days'));
        $this->assertEquals(100000.0, Setting::get('min_withdrawal'));

        // Verifikasi audit log tercatat di setting_histories (siapa dan kapan)
        $this->assertDatabaseHas('setting_histories', [
            'key' => 'platform_fee_percentage',
            'user_id' => $this->superadmin->id,
            'new_value' => '7.5',
        ]);
        $this->assertDatabaseHas('setting_histories', [
            'key' => 'escrow_holding_days',
            'user_id' => $this->superadmin->id,
            'new_value' => '4',
        ]);
    }
}
