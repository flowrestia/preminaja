<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. seller_profiles
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name', 100);
            $table->string('store_slug', 120)->unique();
            $table->text('bio')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('bank_name', 50)->nullable();
            $table->text('bank_account_number')->nullable(); // encrypted
            $table->text('bank_account_holder')->nullable(); // encrypted
            $table->string('verification_status', 20)->default('pending')->index(); // pending, approved, rejected
            $table->text('verification_notes')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0.00)->index();
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamps();
        });

        // 2. categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('icon', 100)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 3. products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('seller_profiles')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->text('description');
            $table->string('delivery_type', 20)->default('manual'); // automated, manual
            $table->string('status', 30)->default('draft')->index(); // draft, pending_review, published, rejected, inactive, archived
            $table->text('rejection_reason')->nullable();
            $table->string('featured_image')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0.00);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('sold_count')->default(0)->index();
            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index(['seller_id', 'status']);
        });

        // 4. product_variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100); // e.g. "1 Bulan Private", "3 Bulan Sharing"
            $table->decimal('price', 15, 2);
            $table->decimal('original_price', 15, 2)->nullable();
            $table->unsignedInteger('duration_days')->default(30);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 5. automated_credentials
        Schema::create('automated_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->text('credential_data'); // encrypted
            $table->text('instructions')->nullable();
            $table->string('status', 20)->default('available')->index(); // available, reserved, delivered
            $table->foreignId('order_item_id')->nullable();
            $table->timestamps();

            $table->index(['product_variant_id', 'status']);
        });

        // 6. vouchers
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('discount_type', 20)->default('fixed'); // percentage, fixed
            $table->decimal('discount_value', 15, 2);
            $table->decimal('min_purchase', 15, 2)->default(0.00);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 7. orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('fee_amount', 15, 2)->default(0.00);
            $table->decimal('final_amount', 15, 2);
            $table->string('payment_method', 30)->nullable();
            $table->string('payment_status', 20)->default('pending')->index(); // pending, paid, failed, expired
            $table->string('order_status', 20)->default('pending')->index(); // pending, processing, completed, cancelled, disputed
            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'order_status']);
            $table->index(['payment_status', 'created_at']);
        });

        // 8. order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('product_variants')->restrictOnDelete();
            $table->foreignId('seller_id')->constrained('seller_profiles')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('platform_fee', 15, 2)->default(0.00);
            $table->decimal('seller_earning', 15, 2)->default(0.00);
            $table->timestamp('escrow_release_at')->nullable()->index();
            $table->string('delivery_status', 20)->default('pending')->index(); // pending, delivered, failed
            $table->text('credentials_payload')->nullable(); // encrypted snapshot delivered to buyer
            $table->timestamp('delivered_at')->nullable();

            // Snapshot product & variant details when purchased
            $table->string('product_name_snapshot', 200)->nullable();
            $table->string('variant_name_snapshot', 100)->nullable();

            $table->timestamps();

            $table->index(['seller_id', 'delivery_status']);
        });

        // 9. payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('tripay_reference', 100)->nullable()->unique();
            $table->string('merchant_ref', 100)->unique();
            $table->string('payment_method', 30);
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2);
            $table->string('status', 20)->default('UNPAID')->index(); // UNPAID, PAID, FAILED, EXPIRED
            $table->text('checkout_url')->nullable();
            $table->text('qr_url')->nullable();
            $table->string('pay_code', 100)->nullable();
            $table->json('tripay_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 10. reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1 to 5
            $table->text('comment')->nullable();
            $table->text('seller_reply')->nullable();
            $table->timestamp('seller_replied_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'rating']);
        });

        // 11. tickets (Customer Support & General Inquiries)
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject', 200);
            $table->string('category', 50)->default('order'); // order, technical, billing, general
            $table->string('priority', 20)->default('medium'); // low, medium, high
            $table->string('status', 20)->default('open')->index(); // open, replied, closed
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // 12. ticket_messages
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->string('attachment_url')->nullable();
            $table->timestamps();
        });

        // 13. disputes (Transaction Mediation & Escrow Freezing)
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason');
            $table->string('status', 30)->default('open')->index(); // open, investigating, resolved_buyer, resolved_seller, closed
            $table->text('resolution_note')->nullable();
            $table->timestamps();

            $table->index(['buyer_id', 'status']);
        });

        // 14. seller_balances
        Schema::create('seller_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->unique()->constrained('seller_profiles')->cascadeOnDelete();
            $table->decimal('balance', 15, 2)->default(0.00); // available balance for withdrawal
            $table->decimal('pending_balance', 15, 2)->default(0.00); // escrow holding balance
            $table->decimal('total_withdrawn', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 15. wallet_transactions
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('seller_profiles')->cascadeOnDelete();
            $table->string('type', 30)->index(); // order_settlement, escrow_hold, escrow_release, platform_fee, withdrawal, refund, refund_deduction, adjustment
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['seller_id', 'type', 'created_at']);
        });

        // 16. withdrawals
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('withdrawal_number', 50)->unique();
            $table->foreignId('seller_id')->constrained('seller_profiles')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0.00);
            $table->decimal('net_amount', 15, 2);
            $table->string('bank_name', 50);
            $table->text('bank_account_number'); // encrypted
            $table->text('bank_account_holder'); // encrypted
            $table->string('status', 20)->default('pending')->index(); // pending, approved, rejected, processed
            $table->text('proof_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['seller_id', 'status']);
        });

        // 17. cart_items
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'variant_id']);
        });

        // 18. wishlists
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });

        // 19. settings (Platform-wide configuration)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string'); // string, integer, decimal, boolean, json
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Add foreign key constraint to automated_credentials.order_item_id
        Schema::table('automated_credentials', function (Blueprint $table) {
            $table->foreign('order_item_id')->references('id')->on('order_items')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automated_credentials', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
        });

        Schema::dropIfExists('settings');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('seller_balances');
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('automated_credentials');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('seller_profiles');
    }
};
