<?php

namespace Tests\Feature;

use App\Enums\DeliveryType;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Livewire\Seller\ProductQuickStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellerBalance;
use App\Models\SellerProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SellerProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $sellerUser;
    protected SellerProfile $sellerProfile;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->sellerUser = User::factory()->create([
            'role' => UserRole::SELLER,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->sellerUser->assignRole('seller');

        $this->sellerProfile = SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'store_name' => 'Toko Sonny Store',
            'store_slug' => 'tokosonnystore',
            'verification_status' => VerificationStatus::APPROVED,
        ]);

        SellerBalance::create([
            'seller_id' => $this->sellerProfile->id,
            'balance' => 500000,
            'pending_balance' => 100000,
        ]);

        $this->category = Category::create([
            'name' => 'Streaming',
            'slug' => 'streaming',
            'is_active' => true,
        ]);
    }

    public function test_seller_dashboard_displays_financial_and_product_metrics(): void
    {
        $response = $this->actingAs($this->sellerUser)->get(route('seller.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Toko Sonny Store');
        $response->assertSee('500.000');
        $response->assertSee('100.000');
    }

    public function test_seller_can_create_new_product_and_slug_is_generated_once(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('netflix.png');

        $payload = [
            'name' => 'Netflix Premium 4K UHD',
            'category_id' => $this->category->id,
            'description' => 'Akun Netflix 1 Bulan garansi full.',
            'delivery_type' => DeliveryType::AUTOMATED->value,
            'thumbnail' => $file,
            'is_draft' => 0,
            'variants' => [
                [
                    'name' => '1 Profil',
                    'price' => 35000,
                    'original_price' => 45000,
                    'duration_days' => 30,
                    'stock' => 10,
                ],
                [
                    'name' => '1 Akun Private',
                    'price' => 120000,
                    'original_price' => 150000,
                    'duration_days' => 30,
                    'stock' => 5,
                ],
            ],
        ];

        $response = $this->actingAs($this->sellerUser)->post(route('seller.products.store'), $payload);
        $response->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'seller_id' => $this->sellerProfile->id,
            'name' => 'Netflix Premium 4K UHD',
            'slug' => 'netflix-premium-4k-uhd',
            'status' => ProductStatus::PENDING_REVIEW->value,
        ]);

        $product = Product::where('slug', 'netflix-premium-4k-uhd')->first();
        $this->assertCount(2, $product->variants);
        $this->assertNotNull($product->featured_image);
        Storage::disk('public')->assertExists($product->featured_image);
    }

    public function test_non_image_thumbnail_upload_is_rejected(): void
    {
        Storage::fake('public');
        $fakeFile = UploadedFile::fake()->create('malicious.pdf', 500, 'application/pdf');

        $payload = [
            'name' => 'Produk Tes',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi',
            'delivery_type' => DeliveryType::MANUAL->value,
            'thumbnail' => $fakeFile,
            'variants' => [
                ['name' => 'Var 1', 'price' => 10000, 'duration_days' => 30],
            ],
        ];

        $response = $this->actingAs($this->sellerUser)->post(route('seller.products.store'), $payload);
        $response->assertSessionHasErrors(['thumbnail']);
    }

    public function test_editing_published_product_title_description_or_category_reverts_to_pending_review_and_slug_stays_unchanged(): void
    {
        $product = Product::create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Spotify Premium Asli',
            'slug' => 'spotify-premium-asli',
            'description' => 'Akun spotify resmi',
            'delivery_type' => DeliveryType::AUTOMATED,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $variant = $product->variants()->create([
            'name' => '1 Bulan',
            'price' => 25000,
            'duration_days' => 30,
            'stock' => 20,
        ]);

        // Edit title
        $response = $this->actingAs($this->sellerUser)->put(route('seller.products.update', $product), [
            'name' => 'Spotify Premium Family Plan', // Edited title
            'category_id' => $this->category->id,
            'description' => 'Akun spotify resmi',
            'delivery_type' => DeliveryType::AUTOMATED->value,
            'variants' => [
                [
                    'id' => $variant->id,
                    'name' => '1 Bulan',
                    'price' => 25000,
                    'duration_days' => 30,
                    'stock' => 20,
                ],
            ],
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $product->refresh();
        // Title changed
        $this->assertEquals('Spotify Premium Family Plan', $product->name);
        // Slug must NOT change
        $this->assertEquals('spotify-premium-asli', $product->slug);
        // Status must revert to pending_review
        $this->assertEquals(ProductStatus::PENDING_REVIEW, $product->status);
    }

    public function test_editing_only_variant_price_or_stock_on_published_product_keeps_published_status(): void
    {
        $product = Product::create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Youtube Premium',
            'slug' => 'youtube-premium',
            'description' => 'Youtube no ads',
            'delivery_type' => DeliveryType::AUTOMATED,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $variant = $product->variants()->create([
            'name' => '1 Bulan',
            'price' => 20000,
            'duration_days' => 30,
            'stock' => 15,
        ]);

        // Edit only price and stock (title, description, category unchanged)
        $response = $this->actingAs($this->sellerUser)->put(route('seller.products.update', $product), [
            'name' => 'Youtube Premium',
            'category_id' => $this->category->id,
            'description' => 'Youtube no ads',
            'delivery_type' => DeliveryType::AUTOMATED->value,
            'variants' => [
                [
                    'id' => $variant->id,
                    'name' => '1 Bulan',
                    'price' => 22000, // Price updated
                    'duration_days' => 30,
                    'stock' => 35, // Stock updated
                ],
            ],
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $product->refresh();
        // Status remains published
        $this->assertEquals(ProductStatus::PUBLISHED, $product->status);
        $this->assertEquals('youtube-premium', $product->slug);
    }

    public function test_cross_seller_product_modification_is_forbidden_via_controller_and_livewire(): void
    {
        // Other seller
        $otherUser = User::factory()->create([
            'role' => UserRole::SELLER,
            'status' => UserStatus::ACTIVE,
        ]);
        $otherUser->assignRole('seller');
        $otherSellerProfile = SellerProfile::create([
            'user_id' => $otherUser->id,
            'store_name' => 'Other Store',
            'store_slug' => 'otherstore',
            'verification_status' => VerificationStatus::APPROVED,
        ]);

        // Product belonging to other seller
        $otherProduct = Product::create([
            'seller_id' => $otherSellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produk Seller Lain',
            'slug' => 'produk-seller-lain',
            'description' => 'Rahasia',
            'delivery_type' => DeliveryType::MANUAL,
            'status' => ProductStatus::PUBLISHED,
        ]);

        // Test Controller access (forbidden 403)
        $controllerResponse = $this->actingAs($this->sellerUser)->put(route('seller.products.update', $otherProduct), [
            'name' => 'Hacked Product',
        ]);
        $controllerResponse->assertStatus(403);

        // Test Livewire component cross-seller action (forbidden 403)
        Livewire::actingAs($this->sellerUser)
            ->test(ProductQuickStatus::class, ['product' => $otherProduct])
            ->call('toggleArchive')
            ->assertForbidden();
    }
}
