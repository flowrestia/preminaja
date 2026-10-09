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

    public function test_variant_price_validation_range_and_history_recording(): void
    {
        // 1. Validasi batas harga: minimal 1.000, maksimal 10.000.000
        $responseLow = $this->actingAs($this->sellerUser)->post(route('seller.products.store'), [
            'name' => 'Produk Harga Rendah',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi',
            'delivery_type' => DeliveryType::MANUAL->value,
            'variants' => [
                ['name' => 'Murah', 'price' => 500, 'duration_days' => 30],
            ],
        ]);
        $responseLow->assertSessionHasErrors(['variants.0.price']);

        $responseHigh = $this->actingAs($this->sellerUser)->post(route('seller.products.store'), [
            'name' => 'Produk Harga Terlalu Mahal',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi',
            'delivery_type' => DeliveryType::MANUAL->value,
            'variants' => [
                ['name' => 'Mahal', 'price' => 15000000, 'duration_days' => 30],
            ],
        ]);
        $responseHigh->assertSessionHasErrors(['variants.0.price']);

        // 2. Pencatatan riwayat perubahan harga
        $product = Product::create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Canva Pro Edu',
            'slug' => 'canva-pro-edu',
            'description' => 'Canva pro 1 tahun',
            'delivery_type' => DeliveryType::AUTOMATED,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $variant = $product->variants()->create([
            'name' => '1 Tahun',
            'price' => 50000,
            'duration_days' => 365,
            'stock' => 10,
        ]);

        $updateResponse = $this->actingAs($this->sellerUser)->put(route('seller.products.update', $product), [
            'name' => 'Canva Pro Edu',
            'category_id' => $this->category->id,
            'description' => 'Canva pro 1 tahun',
            'delivery_type' => DeliveryType::AUTOMATED->value,
            'variants' => [
                [
                    'id' => $variant->id,
                    'name' => '1 Tahun',
                    'price' => 65000, // Naikan harga dari 50.000 ke 65.000
                    'duration_days' => 365,
                ],
            ],
        ]);
        $updateResponse->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('price_histories', [
            'product_variant_id' => $variant->id,
            'user_id' => $this->sellerUser->id,
            'old_price' => 50000.00,
            'new_price' => 65000.00,
        ]);
    }

    public function test_product_with_orders_is_archived_instead_of_deleted_and_variant_with_orders_or_credentials_is_deactivated(): void
    {
        $product = Product::create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Disney+ Hotstar',
            'slug' => 'disney-hotstar',
            'description' => 'Disney Hotstar VIP',
            'delivery_type' => DeliveryType::AUTOMATED,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $variant1 = $product->variants()->create([
            'name' => '1 Bulan (Punya Order)',
            'price' => 25000,
            'duration_days' => 30,
            'stock' => 5,
            'is_active' => true,
        ]);

        $variant2 = $product->variants()->create([
            'name' => '3 Bulan (Punya Kredensial)',
            'price' => 70000,
            'duration_days' => 90,
            'stock' => 2,
            'is_active' => true,
        ]);

        $buyer = User::factory()->create();
        $order = \App\Models\Order::create([
            'order_number' => 'ORD-TEST-ARCHIVE-01',
            'user_id' => $buyer->id,
            'total_amount' => 25000,
            'discount_amount' => 0,
            'fee_amount' => 0,
            'final_amount' => 25000,
            'payment_method' => 'qris',
            'payment_status' => \App\Enums\PaymentStatus::PAID,
            'order_status' => \App\Enums\OrderStatus::COMPLETED,
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'variant_id' => $variant1->id,
            'seller_id' => $this->sellerProfile->id,
            'quantity' => 1,
            'price' => 25000,
            'subtotal' => 25000,
            'platform_fee' => 1250,
            'seller_earning' => 23750,
            'delivery_status' => \App\Enums\DeliveryStatus::DELIVERED,
        ]);

        // Berikan kredensial pada varian 2
        \App\Models\AutomatedCredential::create([
            'product_variant_id' => $variant2->id,
            'credential_hash' => \App\Models\AutomatedCredential::generateHash('disney_sample@test.com|secretpass'),
            'credential_data' => 'disney_sample@test.com|secretpass',
            'status' => 'available',
        ]);

        // Coba edit produk dan buang variant1 & variant2 dari input (hanya menyisakan variant baru)
        $this->actingAs($this->sellerUser)->put(route('seller.products.update', $product), [
            'name' => 'Disney+ Hotstar',
            'category_id' => $this->category->id,
            'description' => 'Disney Hotstar VIP',
            'delivery_type' => DeliveryType::AUTOMATED->value,
            'variants' => [
                ['name' => 'Varian Baru Baru', 'price' => 30000, 'duration_days' => 30],
            ],
        ]);

        // Kedua varian tidak boleh hilang dari database, melainkan is_active menjadi false
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant1->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant2->id,
            'is_active' => false,
        ]);

        // Coba hapus produk yang memiliki pesanan
        $deleteResponse = $this->actingAs($this->sellerUser)->delete(route('seller.products.destroy', $product));
        $deleteResponse->assertRedirect(route('seller.products.index'));

        // Produk tidak boleh terhapus dari tabel, melainkan berstatus archived
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatus::ARCHIVED->value,
        ]);
    }
}
