<?php

namespace Tests\Feature;

use App\Enums\DisputeStatus;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Enums\WalletTransactionType;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellerBalance;
use App\Models\SellerProfile;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use Filament\Facades\Filament;
use Tests\TestCase;

class DomainModelTest extends TestCase
{
    public function test_seeded_settings_exist(): void
    {
        $this->assertEquals(5.0, Setting::get('platform_fee_percentage'));
        $this->assertEquals(3, Setting::get('escrow_holding_days'));
        $this->assertEquals(50000.0, Setting::get('min_withdrawal'));
    }

    public function test_user_roles_and_filament_access(): void
    {
        $adminPanel = Filament::getPanel('admin');

        $superadmin = User::where('role', UserRole::SUPERADMIN)->first();
        $this->assertNotNull($superadmin);
        $this->assertTrue($superadmin->canAccessPanel($adminPanel));

        $buyer = User::where('role', UserRole::BUYER)->first();
        $this->assertNotNull($buyer);
        $this->assertFalse($buyer->canAccessPanel($adminPanel));
    }

    public function test_seller_balance_and_order_financial_fields(): void
    {
        $balance = SellerBalance::first();
        $this->assertNotNull($balance);
        $this->assertNotNull($balance->balance);
        $this->assertNotNull($balance->pending_balance);

        $orderItem = OrderItem::first();
        $this->assertNotNull($orderItem);
        $this->assertNotNull($orderItem->platform_fee);
        $this->assertNotNull($orderItem->seller_earning);
        $this->assertNotNull($orderItem->escrow_release_at);
    }

    public function test_dispute_and_product_status(): void
    {
        $dispute = Dispute::first();
        $this->assertNotNull($dispute);
        $this->assertInstanceOf(DisputeStatus::class, $dispute->status);

        $product = Product::first();
        $this->assertNotNull($product);
        $this->assertEquals(ProductStatus::PUBLISHED, $product->status);
    }
}
