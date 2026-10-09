<?php

namespace Database\Seeders;

use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use App\Enums\DisputeStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Enums\VoucherType;
use App\Enums\WalletTransactionType;
use App\Models\AutomatedCredential;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\SellerBalance;
use App\Models\SellerProfile;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\Voucher;
use App\Models\WalletTransaction;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions setup (Runs in ALL environments)
        $this->call(RolePermissionSeeder::class);

        // 2. Superadmin Seeder (Runs in ALL environments)
        $this->call(SuperadminSeeder::class);

        // 3. Default Platform Settings (Runs in ALL environments)
        Setting::updateOrCreate(
            ['key' => 'platform_fee_percentage'],
            [
                'value' => '5.0',
                'type' => 'decimal',
                'description' => 'Persentase potongan komisi platform per item terjual (%)',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'escrow_holding_days'],
            [
                'value' => '3',
                'type' => 'integer',
                'description' => 'Jumlah hari penahanan dana di escrow sebelum otomatis cair ke seller balance (hari)',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'min_withdrawal'],
            [
                'value' => '50000.00',
                'type' => 'decimal',
                'description' => 'Batas minimum nominal penarikan saldo seller (Rp)',
            ]
        );

        // 4. Dummy Data - HANYA jika environment = local atau testing
        if (! App::environment(['local', 'testing'])) {
            $this->command->info('DatabaseSeeder: Melewati dummy data karena environment bukan local atau testing.');
            return;
        }

        $defaultPassword = env('SEED_DEFAULT_PASSWORD', 'password123');

        $roleSeller = Role::firstOrCreate(['name' => UserRole::SELLER->value, 'guard_name' => 'web']);
        $roleBuyer = Role::firstOrCreate(['name' => UserRole::BUYER->value, 'guard_name' => 'web']);

        // Dummy Users (Sellers, Buyers, Blocked User)
        $sellerUser1 = User::firstOrCreate(
            ['email' => 'seller@preminaja.id'],
            [
                'name' => 'Budi Santoso (PremiumStore)',
                'password' => Hash::make($defaultPassword),
                'email_verified_at' => now(),
                'status' => UserStatus::ACTIVE,
                'phone' => '081298765432',
                'last_login_at' => now(),
            ]
        );
        $sellerUser1->role = UserRole::SELLER;
        $sellerUser1->save();
        $sellerUser1->assignRole($roleSeller);

        $sellerUser2 = User::firstOrCreate(
            ['email' => 'digitalzone@preminaja.id'],
            [
                'name' => 'Rina Wijaya (DigitalZone)',
                'password' => Hash::make($defaultPassword),
                'email_verified_at' => now(),
                'status' => UserStatus::ACTIVE,
                'phone' => '081377889900',
                'last_login_at' => now(),
            ]
        );
        $sellerUser2->role = UserRole::SELLER;
        $sellerUser2->save();
        $sellerUser2->assignRole($roleSeller);

        $sellerPendingUser = User::firstOrCreate(
            ['email' => 'sellerpending@preminaja.id'],
            [
                'name' => 'Toko Menunggu Verifikasi',
                'password' => Hash::make($defaultPassword),
                'email_verified_at' => now(),
                'status' => UserStatus::ACTIVE,
                'phone' => '081399887766',
                'last_login_at' => now(),
            ]
        );
        $sellerPendingUser->role = UserRole::SELLER;
        $sellerPendingUser->save();
        $sellerPendingUser->assignRole($roleSeller);

        $buyerUser = User::firstOrCreate(
            ['email' => 'buyer@preminaja.id'],
            [
                'name' => 'Ahmad Pembeli',
                'password' => Hash::make($defaultPassword),
                'email_verified_at' => now(),
                'status' => UserStatus::ACTIVE,
                'phone' => '085611223344',
                'last_login_at' => now(),
            ]
        );
        $buyerUser->role = UserRole::BUYER;
        $buyerUser->save();
        $buyerUser->assignRole($roleBuyer);

        $blockedUser = User::firstOrCreate(
            ['email' => 'blocked@preminaja.id'],
            [
                'name' => 'Pengguna Diblokir',
                'password' => Hash::make($defaultPassword),
                'email_verified_at' => now(),
                'status' => UserStatus::BLOCKED,
                'phone' => '089900112233',
                'last_login_at' => null,
            ]
        );
        $blockedUser->role = UserRole::BUYER;
        $blockedUser->save();
        $blockedUser->assignRole($roleBuyer);

        // Dummy Seller Profiles & Balances
        $sellerProfile1 = SellerProfile::updateOrCreate(
            ['user_id' => $sellerUser1->id],
            [
                'store_name' => 'PremiumStore ID',
                'store_slug' => 'premiumstore-id',
                'bio' => 'Pusat akun streaming & produktivitas resmi terpercaya, garansi 100% aman dan bergaransi penuh.',
                'bank_name' => 'BCA',
                'bank_account_number' => '8801234567',
                'bank_account_holder' => 'Budi Santoso',
                'verification_status' => VerificationStatus::APPROVED,
                'verification_notes' => 'Dokumen identitas dan riwayat penjualan terverifikasi resmi.',
                'rating_avg' => 4.95,
                'rating_count' => 128,
            ]
        );

        $sellerProfile2 = SellerProfile::updateOrCreate(
            ['user_id' => $sellerUser2->id],
            [
                'store_name' => 'DigitalZone Official',
                'store_slug' => 'digitalzone-official',
                'bio' => 'Spesialis software desain, AI tools, dan VPN premium dengan pengiriman otomatis detik itu juga.',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1370012345678',
                'bank_account_holder' => 'Rina Wijaya',
                'verification_status' => VerificationStatus::APPROVED,
                'verification_notes' => 'Toko resmi mitra terverifikasi.',
                'rating_avg' => 4.88,
                'rating_count' => 84,
            ]
        );

        // Pending Seller Profile (Verification Status: PENDING)
        SellerProfile::updateOrCreate(
            ['user_id' => $sellerPendingUser->id],
            [
                'store_name' => 'Toko Baru Pending',
                'store_slug' => 'toko-baru-pending',
                'bio' => 'Menunggu review dokumen oleh tim kepatuhan.',
                'bank_name' => 'BRI',
                'bank_account_number' => '0012345678901',
                'bank_account_holder' => 'Calon Seller',
                'verification_status' => VerificationStatus::PENDING,
                'verification_notes' => 'Dokumen KTP dalam antrean moderasi.',
                'rating_avg' => 0.00,
                'rating_count' => 0,
            ]
        );

        SellerBalance::updateOrCreate(
            ['seller_id' => $sellerProfile1->id],
            [
                'balance' => 1750000.00,
                'pending_balance' => 250000.00,
                'total_withdrawn' => 5000000.00,
            ]
        );

        SellerBalance::updateOrCreate(
            ['seller_id' => $sellerProfile2->id],
            [
                'balance' => 820000.00,
                'pending_balance' => 90000.00,
                'total_withdrawn' => 2300000.00,
            ]
        );

        // Categories
        $catStreaming = Category::updateOrCreate(
            ['slug' => 'streaming-video-musik'],
            [
                'name' => 'Streaming & Musik',
                'icon' => 'heroicon-o-play-circle',
                'description' => 'Akun langganan Netflix, Spotify, YouTube Premium, Disney+, Prime Video dll.',
                'is_active' => true,
            ]
        );

        $catProductivity = Category::updateOrCreate(
            ['slug' => 'produktivitas-kantor'],
            [
                'name' => 'Produktivitas & Desain',
                'icon' => 'heroicon-o-sparkles',
                'description' => 'Canva Pro, ChatGPT Plus, Claude Pro, Notion, Microsoft 365, Adobe Creative Cloud.',
                'is_active' => true,
            ]
        );

        // Products & Variants & Credentials
        $p1 = Product::updateOrCreate(
            ['slug' => 'netflix-premium-uhd-4k'],
            [
                'seller_id' => $sellerProfile1->id,
                'category_id' => $catStreaming->id,
                'name' => 'Netflix Premium UHD 4K (Garansi Penuh)',
                'description' => "Nikmati ribuan film dan series Netflix favorit dengan kualitas streaming Ultra HD 4K.\n- Akun resmi anti hold\n- Profil privat dengan PIN pengaman\n- Pengiriman instan otomatis ke email & dashboard setelah bayar\n- Full garansi selama durasi berlangganan",
                'delivery_type' => DeliveryType::AUTOMATED,
                'status' => ProductStatus::PUBLISHED,
                'rejection_reason' => null,
                'featured_image' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=600&auto=format&fit=crop&q=80',
                'rating_avg' => 4.96,
                'rating_count' => 54,
                'sold_count' => 180,
            ]
        );

        $v1_1 = ProductVariant::updateOrCreate(
            ['product_id' => $p1->id, 'name' => '1 Bulan (1 Profil Private PIN)'],
            [
                'price' => 38000.00,
                'original_price' => 54000.00,
                'duration_days' => 30,
                'stock' => 5,
                'is_active' => true,
            ]
        );

        $credData = 'netflix_cust01@preminaja.id|NetflixPass123!|Profile: Budi (PIN: 1982)';
        AutomatedCredential::firstOrCreate(
            [
                'product_variant_id' => $v1_1->id,
                'credential_hash' => AutomatedCredential::generateHash($credData),
            ],
            [
                'credential_data' => $credData,
                'instructions' => 'Login via web atau aplikasi Netflix. Pilih profile sesuai nomor PIN yang diberikan.',
                'status' => 'available',
            ]
        );

        // Vouchers
        Voucher::updateOrCreate(
            ['code' => 'PREMINHEMAT'],
            [
                'discount_type' => VoucherType::PERCENTAGE,
                'discount_value' => 15.00,
                'min_purchase' => 50000.00,
                'max_discount' => 20000.00,
                'usage_limit' => 500,
                'used_count' => 23,
                'starts_at' => now()->subDays(5),
                'expires_at' => now()->addDays(25),
                'is_active' => true,
            ]
        );

        // Dummy Completed Order
        $order = Order::updateOrCreate(
            ['order_number' => 'ORD-202610-00109'],
            [
                'user_id' => $buyerUser->id,
                'total_amount' => 38000.00,
                'discount_amount' => 0.00,
                'fee_amount' => 750.00,
                'final_amount' => 38750.00,
                'payment_method' => 'tripay_qris',
                'payment_status' => PaymentStatus::PAID,
                'order_status' => OrderStatus::COMPLETED,
                'voucher_id' => null,
                'notes' => 'Tolong kirimkan segera ya min.',
                'paid_at' => now()->subDays(2),
                'completed_at' => now()->subDays(2),
                'expires_at' => now()->addHours(24),
            ]
        );

        $orderItem = OrderItem::updateOrCreate(
            ['order_id' => $order->id, 'variant_id' => $v1_1->id],
            [
                'seller_id' => $sellerProfile1->id,
                'quantity' => 1,
                'price' => 38000.00,
                'subtotal' => 38000.00,
                'platform_fee' => 1900.00,
                'seller_earning' => 36100.00,
                'escrow_release_at' => now()->addDay(),
                'delivery_status' => DeliveryStatus::DELIVERED,
                'credentials_payload' => 'Email: netflix_sample@preminaja.id | Pass: SuperSecret2026! | Profil 1 PIN: 8899',
                'delivered_at' => now()->subDays(2),
                'product_name_snapshot' => $p1->name,
                'variant_name_snapshot' => $v1_1->name,
            ]
        );

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'tripay_reference' => 'DEV-T261009133742-PREMIN',
                'merchant_ref' => 'ORD-202610-00109',
                'payment_method' => 'QRIS',
                'amount' => 38000.00,
                'fee' => 750.00,
                'total_amount' => 38750.00,
                'status' => 'PAID',
                'checkout_url' => 'https://tripay.co.id/checkout/sample',
                'qr_url' => 'https://tripay.co.id/qr/sample',
                'pay_code' => 'QRIS982348234',
                'paid_at' => now()->subDays(2),
                'expires_at' => now()->subDays(1),
            ]
        );

        Review::updateOrCreate(
            ['order_item_id' => $orderItem->id],
            [
                'user_id' => $buyerUser->id,
                'product_id' => $p1->id,
                'rating' => 5,
                'comment' => 'Mantap sekali! Pembayaran QRIS langsung sukses dan akun otomatis langsung muncul di layar.',
                'seller_reply' => 'Terima kasih banyak atas pesanannya kak!',
                'seller_replied_at' => now()->subDay(),
            ]
        );

        WalletTransaction::firstOrCreate(
            [
                'seller_id' => $sellerProfile1->id,
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'type' => WalletTransactionType::ESCROW_HOLD,
            ],
            [
                'amount' => 36100.00,
                'balance_before' => 1713900.00,
                'balance_after' => 1750000.00,
                'description' => 'Penahanan dana escrow pesanan ORD-202610-00109 (bersih setelah fee 5%)',
            ]
        );

        Dispute::firstOrCreate(
            [
                'order_item_id' => $orderItem->id,
                'buyer_id' => $buyerUser->id,
            ],
            [
                'reason' => 'Pertanyaan garansi akun.',
                'status' => DisputeStatus::CLOSED,
                'resolution_note' => 'Diselesaikan dengan damai antara pembeli dan penjual.',
            ]
        );

        $superadminUser = User::where('email', env('SUPERADMIN_EMAIL', 'admin@preminaja.id'))->first();

        $ticket = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-202610-0001'],
            [
                'user_id' => $buyerUser->id,
                'order_id' => $order->id,
                'subject' => 'Cara ganti subtitle bahasa Indonesia di smart TV',
                'category' => 'technical',
                'priority' => 'low',
                'status' => TicketStatus::REPLIED,
            ]
        );

        TicketMessage::firstOrCreate(
            ['ticket_id' => $ticket->id, 'user_id' => $buyerUser->id],
            ['message' => 'Halo tim PreminAja, bagaimana cara memilih subtitle bahasa Indonesia?']
        );

        if ($superadminUser) {
            TicketMessage::firstOrCreate(
                ['ticket_id' => $ticket->id, 'user_id' => $superadminUser->id],
                ['message' => 'Halo kak Ahmad! Masuk ke menu Audio & Subtitle saat memutar video di TV ya.']
            );
        }
    }
}
