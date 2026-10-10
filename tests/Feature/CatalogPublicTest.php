<?php

namespace Tests\Feature;

use App\Enums\DeliveryType;
use App\Enums\ProductStatus;
use App\Enums\VerificationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\Catalog\ProductCatalog;
use App\Livewire\Catalog\WishlistButton;
use App\Livewire\Buyer\WishlistIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogPublicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    protected function createVerifiedSeller(): SellerProfile
    {
        $user = User::factory()->create([
            'role' => UserRole::SELLER->value,
            'status' => UserStatus::ACTIVE,
            'phone' => '081234567890',
        ]);
        $user->assignRole(UserRole::SELLER->value);

        return SellerProfile::create([
            'user_id' => $user->id,
            'store_name' => 'Toko Premium Aman',
            'store_slug' => 'toko-premium-aman-' . uniqid(),
            'verification_status' => VerificationStatus::APPROVED,
            'bank_account' => ['bank_name' => 'BCA', 'account_number' => '1234567890', 'account_holder' => 'Secret Seller'],
            'bio' => 'Menyediakan akun original 100% legal dan garansi resmi.',
            'rating_avg' => 4.9,
            'rating_count' => 10,
        ]);
    }

    public function test_catalog_index_only_displays_products_visible_in_catalog(): void
    {
        $seller = $this->createVerifiedSeller();
        $category = Category::create(['name' => 'Streaming', 'slug' => 'streaming']);

        // 1. Visible product
        $visibleProduct = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Netflix Premium 1 Bulan Resmi',
            'slug' => 'netflix-premium-1-bulan-resmi',
            'description' => 'Akun Netflix kualitas 4K UHD Ultra HD',
            'status' => ProductStatus::PUBLISHED,
            'delivery_type' => DeliveryType::AUTOMATED,
            'rating_avg' => 4.8,
            'sold_count' => 20,
        ]);
        ProductVariant::create([
            'product_id' => $visibleProduct->id,
            'name' => '1 Bulan',
            'price' => 35000,
            'duration_days' => 30,
            'stock' => 10,
            'is_active' => true,
        ]);

        // 2. Draft product (should not appear)
        $draftProduct = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Disney Hotstar Draft',
            'slug' => 'disney-hotstar-draft',
            'description' => 'Draft description',
            'status' => ProductStatus::DRAFT,
            'delivery_type' => DeliveryType::MANUAL,
        ]);
        ProductVariant::create([
            'product_id' => $draftProduct->id,
            'name' => '1 Bulan',
            'price' => 20000,
            'duration_days' => 30,
            'stock' => 5,
            'is_active' => true,
        ]);

        // 3. Product from blocked seller
        $blockedUser = User::factory()->create(['status' => UserStatus::BLOCKED, 'role' => UserRole::SELLER->value]);
        $blockedUser->assignRole(UserRole::SELLER->value);
        $blockedSeller = SellerProfile::create([
            'user_id' => $blockedUser->id,
            'store_name' => 'Toko Curang',
            'store_slug' => 'toko-curang',
            'verification_status' => VerificationStatus::APPROVED,
        ]);
        $blockedProduct = Product::create([
            'seller_id' => $blockedSeller->id,
            'category_id' => $category->id,
            'name' => 'Akun Ilegal Blocked',
            'slug' => 'akun-ilegal-blocked',
            'description' => 'Produk tidak boleh tampil',
            'status' => ProductStatus::PUBLISHED,
            'delivery_type' => DeliveryType::AUTOMATED,
        ]);

        // Request catalog index
        $response = $this->get(route('catalog.index'));
        $response->assertStatus(200);
        $response->assertSee('Netflix Premium 1 Bulan Resmi');
        $response->assertDontSee('Disney Hotstar Draft');
        $response->assertDontSee('Akun Ilegal Blocked');
    }

    public function test_product_detail_enforces_visibility_and_allows_preview_for_owner_and_superadmin(): void
    {
        $seller = $this->createVerifiedSeller();
        $category = Category::create(['name' => 'Produktivitas', 'slug' => 'produktivitas']);

        $pendingProduct = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Canva Pro Edu Draft Review',
            'slug' => 'canva-pro-edu-draft-review',
            'description' => 'Akun canva pro',
            'status' => ProductStatus::PENDING_REVIEW,
            'delivery_type' => DeliveryType::AUTOMATED,
        ]);
        ProductVariant::create([
            'product_id' => $pendingProduct->id,
            'name' => '1 Tahun',
            'price' => 50000,
            'duration_days' => 365,
            'stock' => 3,
            'is_active' => true,
        ]);

        // Guest visitor receives 404
        $this->get(route('catalog.show', $pendingProduct->slug))->assertStatus(404);

        // Random buyer receives 404
        $buyer = User::factory()->create(['role' => UserRole::BUYER->value, 'status' => UserStatus::ACTIVE]);
        $buyer->assignRole(UserRole::BUYER->value);
        $this->actingAs($buyer)->get(route('catalog.show', $pendingProduct->slug))->assertStatus(404);

        // Product owner (seller) can preview (200)
        $this->actingAs($seller->user)->get(route('catalog.show', $pendingProduct->slug))
            ->assertStatus(200)
            ->assertSee('Canva Pro Edu Draft Review');

        // Superadmin can preview (200)
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN->value, 'status' => UserStatus::ACTIVE]);
        $superadmin->assignRole(UserRole::SUPERADMIN->value);
        $this->actingAs($superadmin)->get(route('catalog.show', $pendingProduct->slug))
            ->assertStatus(200)
            ->assertSee('Canva Pro Edu Draft Review');
    }

    public function test_catalog_search_with_wildcards_is_safely_escaped(): void
    {
        $seller = $this->createVerifiedSeller();
        $cat = Category::create(['name' => 'Apps', 'slug' => 'apps']);

        $p1 = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $cat->id,
            'name' => '100% Legal Spotify Family',
            'slug' => '100-legal-spotify-family',
            'description' => 'Akun legal garansi',
            'status' => ProductStatus::PUBLISHED,
            'delivery_type' => DeliveryType::AUTOMATED,
        ]);
        ProductVariant::create(['product_id' => $p1->id, 'name' => '1 Bulan', 'price' => 15000, 'duration_days' => 30, 'stock' => 10, 'is_active' => true]);

        $p2 = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $cat->id,
            'name' => 'YouTube Premium Solo',
            'slug' => 'youtube-premium-solo',
            'description' => '100 persen tanpa iklan',
            'status' => ProductStatus::PUBLISHED,
            'delivery_type' => DeliveryType::AUTOMATED,
        ]);
        ProductVariant::create(['product_id' => $p2->id, 'name' => '1 Bulan', 'price' => 20000, 'duration_days' => 30, 'stock' => 5, 'is_active' => true]);

        // Searching literally for "%" should only match p1 (which contains "100%") and not all products
        Livewire::test(ProductCatalog::class, ['search' => '%'])
            ->assertSee('100% Legal Spotify Family')
            ->assertDontSee('YouTube Premium Solo');
    }

    public function test_invalid_sort_fallback_to_terbaru_safely(): void
    {
        $seller = $this->createVerifiedSeller();
        $cat = Category::create(['name' => 'Music', 'slug' => 'music']);

        $product = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $cat->id,
            'name' => 'Apple Music 1 Bulan',
            'slug' => 'apple-music-1-bulan',
            'description' => 'Akun Apple Music',
            'status' => ProductStatus::PUBLISHED,
            'delivery_type' => DeliveryType::AUTOMATED,
        ]);
        ProductVariant::create(['product_id' => $product->id, 'name' => '1 Bulan', 'price' => 25000, 'duration_days' => 30, 'stock' => 5, 'is_active' => true]);

        // Attempt SQL injection via sort parameter
        Livewire::test(ProductCatalog::class, ['sort' => 'id; DROP TABLE users; --'])
            ->assertStatus(200)
            ->assertSee('Apple Music 1 Bulan');
    }

    public function test_sensitive_seller_information_is_never_leaked_on_public_page(): void
    {
        $seller = $this->createVerifiedSeller();
        $cat = Category::create(['name' => 'Voucher', 'slug' => 'voucher']);

        $product = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $cat->id,
            'name' => 'Steam Wallet Code IDR',
            'slug' => 'steam-wallet-code-idr',
            'description' => 'Kode voucher resmi',
            'status' => ProductStatus::PUBLISHED,
            'delivery_type' => DeliveryType::AUTOMATED,
        ]);
        ProductVariant::create(['product_id' => $product->id, 'name' => '50K', 'price' => 55000, 'duration_days' => 0, 'stock' => 2, 'is_active' => true]);

        $response = $this->get(route('catalog.show', $product->slug));
        $response->assertStatus(200);

        // Store name & bio should be displayed
        $response->assertSee('Toko Premium Aman');
        $response->assertSee('Menyediakan akun original 100% legal');

        // Sensitive seller details must NEVER be displayed
        $response->assertDontSee($seller->user->email);
        $response->assertDontSee($seller->user->phone);
        $response->assertDontSee('1234567890'); // Bank account number
        $response->assertDontSee('Secret Seller'); // Bank holder
    }

    public function test_xss_payload_in_description_and_reviews_is_sanitized_properly(): void
    {
        $seller = $this->createVerifiedSeller();
        $cat = Category::create(['name' => 'Games', 'slug' => 'games']);

        $xssDescription = '<script>alert("xss-in-desc")</script>';
        $product = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $cat->id,
            'name' => 'Game Pass Ultimate',
            'slug' => 'game-pass-ultimate',
            'description' => $xssDescription,
            'status' => ProductStatus::PUBLISHED,
            'delivery_type' => DeliveryType::AUTOMATED,
        ]);
        ProductVariant::create(['product_id' => $product->id, 'name' => '1 Bulan', 'price' => 45000, 'duration_days' => 30, 'stock' => 4, 'is_active' => true]);

        // Review with XSS
        $buyer = User::factory()->create(['name' => 'Buyer <script>alert("hacker")</script>']);
        $order = \App\Models\Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-TEST-XSS-' . uniqid(),
            'total_amount' => 45000,
            'fee_amount' => 0,
            'final_amount' => 45000,
            'payment_status' => \App\Enums\PaymentStatus::PAID,
            'order_status' => \App\Enums\OrderStatus::COMPLETED,
        ]);
        $orderItem = \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'variant_id' => $product->variants->first()->id,
            'seller_id' => $seller->id,
            'quantity' => 1,
            'price' => 45000,
            'subtotal' => 45000,
        ]);

        Review::create([
            'order_item_id' => $orderItem->id,
            'product_id' => $product->id,
            'user_id' => $buyer->id,
            'rating' => 5,
            'comment' => '<img src=x onerror=alert("xss-comment")>',
            'seller_reply' => '<b onmouseover=alert("xss-reply")>Terima kasih</b>',
        ]);

        $response = $this->get(route('catalog.show', $product->slug));
        $response->assertStatus(200);

        // Raw unescaped scripts must not be in the response
        $response->assertDontSee('<script>alert("xss-in-desc")</script>', false);
        $response->assertDontSee('<img src=x onerror=alert("xss-comment")>', false);
        $response->assertDontSee('<b onmouseover=alert("xss-reply")>', false);

        // Escaped versions should exist
        $response->assertSee(e($xssDescription), false);
        $response->assertSee(e('<img src=x onerror=alert("xss-comment")>'), false);
    }

    public function test_wishlist_toggle_requires_authentication_and_is_isolated_per_user(): void
    {
        $seller = $this->createVerifiedSeller();
        $cat = Category::create(['name' => 'VOD', 'slug' => 'vod']);

        $product = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $cat->id,
            'name' => 'Vidio Platinum 1 Bulan',
            'slug' => 'vidio-platinum-1-bulan',
            'description' => 'Akun resmi Vidio',
            'status' => ProductStatus::PUBLISHED,
            'delivery_type' => DeliveryType::AUTOMATED,
        ]);
        ProductVariant::create(['product_id' => $product->id, 'name' => '1 Bulan', 'price' => 29000, 'duration_days' => 30, 'stock' => 5, 'is_active' => true]);

        // Guest attempting toggle is redirected to login
        Livewire::test(WishlistButton::class, ['productId' => $product->id])
            ->call('toggle')
            ->assertRedirect(route('login'));

        $this->assertEquals(0, Wishlist::count());

        // Authenticated buyer toggles wishlist
        $buyerA = User::factory()->create(['role' => UserRole::BUYER->value, 'status' => UserStatus::ACTIVE]);
        $buyerA->assignRole(UserRole::BUYER->value);

        Livewire::actingAs($buyerA)
            ->test(WishlistButton::class, ['productId' => $product->id])
            ->call('toggle')
            ->assertSet('isWishlisted', true)
            ->assertDispatched('wishlist-updated');

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $buyerA->id,
            'product_id' => $product->id,
        ]);

        // Buyer B checks their wishlist (must be empty, isolated from Buyer A)
        $buyerB = User::factory()->create(['role' => UserRole::BUYER->value, 'status' => UserStatus::ACTIVE]);
        $buyerB->assignRole(UserRole::BUYER->value);

        Livewire::actingAs($buyerB)
            ->test(WishlistIndex::class)
            ->assertDontSee('Vidio Platinum 1 Bulan');

        // Buyer A sees the product in their wishlist
        Livewire::actingAs($buyerA)
            ->test(WishlistIndex::class)
            ->assertSee('Vidio Platinum 1 Bulan');

        // Buyer A toggles again to remove from wishlist
        Livewire::actingAs($buyerA)
            ->test(WishlistButton::class, ['productId' => $product->id, 'isWishlisted' => true])
            ->call('toggle')
            ->assertSet('isWishlisted', false);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $buyerA->id,
            'product_id' => $product->id,
        ]);
    }
}
