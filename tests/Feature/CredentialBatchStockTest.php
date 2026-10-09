<?php

namespace Tests\Feature;

use App\Enums\DeliveryType;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellerProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CredentialBatchStockTest extends TestCase
{
    use RefreshDatabase;

    protected User $sellerUser;
    protected SellerProfile $sellerProfile;
    protected Product $product;
    protected ProductVariant $variant;

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

        $category = Category::create([
            'name' => 'Streaming',
            'slug' => 'streaming',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $category->id,
            'name' => 'Netflix Otomatis',
            'slug' => 'netflix-otomatis',
            'description' => 'Deskripsi akun otomatis',
            'delivery_type' => DeliveryType::AUTOMATED,
            'status' => ProductStatus::PUBLISHED,
        ]);

        $this->variant = $this->product->variants()->create([
            'name' => '1 Bulan',
            'price' => 35000,
            'duration_days' => 30,
            'stock' => 0,
        ]);
    }

    public function test_bulk_textarea_import_detects_duplicates_and_increments_stock(): void
    {
        $rawText = "akun1@netflix.com|Pass123|Pin1\nakun2@netflix.com|Pass456|Pin2\nakun1@netflix.com|Pass123|Pin1";

        $response = $this->actingAs($this->sellerUser)
            ->post(route('seller.products.credentials.store-text', [$this->product, $this->variant]), [
                'raw_credentials' => $rawText,
                'instructions' => 'Login sesuai profile',
            ]);

        $response->assertRedirect(route('seller.products.credentials.index', [$this->product, $this->variant]));
        $response->assertSessionHas('status', function ($msg) {
            return str_contains($msg, 'Berhasil menambahkan 2 stok') && str_contains($msg, 'baris 3');
        });

        $this->variant->refresh();
        $this->assertEquals(2, $this->variant->stock);
        $this->assertEquals(2, $this->variant->automatedCredentials()->count());
    }

    public function test_credentials_are_never_rendered_in_html_or_json_response(): void
    {
        // Masukkan kredensial rahasia
        $secretPlain = 'rahasia_banget@domain.com|SuperSecretPass!|Pin999';

        $this->actingAs($this->sellerUser)
            ->post(route('seller.products.credentials.store-text', [$this->product, $this->variant]), [
                'raw_credentials' => $secretPlain,
            ]);

        // Cek halaman index stok kredensial: plaintext maupun ciphertext tidak boleh ada
        $viewResponse = $this->actingAs($this->sellerUser)
            ->get(route('seller.products.credentials.index', [$this->product, $this->variant]));

        $viewResponse->assertStatus(200);
        $viewResponse->assertDontSee('rahasia_banget@domain.com');
        $viewResponse->assertDontSee('SuperSecretPass!');
        $viewResponse->assertDontSee('Pin999');

        // Pastikan model $hidden menyembunyikan credential_data & credential_hash
        $credential = $this->variant->automatedCredentials()->first();
        $this->assertNotNull($credential);
        $arrayData = $credential->toArray();
        $this->assertArrayNotHasKey('credential_data', $arrayData);
        $this->assertArrayNotHasKey('credential_hash', $arrayData);
    }

    public function test_csv_streaming_upload_succeeds_and_respects_limits(): void
    {
        // Buat file CSV streaming dummy
        $csvContent = "csv_user1@test.com|pass1\ncsv_user2@test.com|pass2\ncsv_user1@test.com|pass1";
        $csvFile = UploadedFile::fake()->createWithContent('credentials.csv', $csvContent);

        $response = $this->actingAs($this->sellerUser)
            ->post(route('seller.products.credentials.store-csv', [$this->product, $this->variant]), [
                'csv_file' => $csvFile,
            ]);

        $response->assertRedirect(route('seller.products.credentials.index', [$this->product, $this->variant]));
        $response->assertSessionHas('status', function ($msg) {
            return str_contains($msg, 'Berhasil mengimpor 2 stok') && str_contains($msg, 'baris 3');
        });

        $this->variant->refresh();
        $this->assertEquals(2, $this->variant->stock);
    }

    public function test_csv_upload_over_1mb_is_rejected(): void
    {
        $largeCsvFile = UploadedFile::fake()->create('toolarge.csv', 1500, 'text/csv'); // 1.5 MB

        $response = $this->actingAs($this->sellerUser)
            ->post(route('seller.products.credentials.store-csv', [$this->product, $this->variant]), [
                'csv_file' => $largeCsvFile,
            ]);

        $response->assertSessionHasErrors(['csv_file']);
    }

    public function test_duplicate_error_message_does_not_reveal_credential_content(): void
    {
        $duplicateLine = 'very_private_account@secret.com|hidden_password';

        // Masukkan pertama kali
        $this->actingAs($this->sellerUser)
            ->post(route('seller.products.credentials.store-text', [$this->product, $this->variant]), [
                'raw_credentials' => $duplicateLine,
            ]);

        // Masukkan kedua kali (duplikat)
        $response = $this->actingAs($this->sellerUser)
            ->post(route('seller.products.credentials.store-text', [$this->product, $this->variant]), [
                'raw_credentials' => $duplicateLine,
            ]);

        $response->assertSessionHas('status', function ($msg) {
            // Laporan hanya boleh menyebut nomor baris tanpa isi
            $hasRowMention = str_contains($msg, 'baris 1');
            $hasNoCredentialContent = !str_contains($msg, 'very_private_account') && !str_contains($msg, 'hidden_password');
            return $hasRowMention && $hasNoCredentialContent;
        });
    }

    public function test_race_condition_unique_constraint_is_caught_as_duplicate(): void
    {
        $rawCredential = 'race_condition_account@secret.com|my_pass_123';
        $hash = \App\Models\AutomatedCredential::generateHash($rawCredential);

        // Simulasi race condition: baris data tiba-tiba dimasukkan oleh proses concurrent lain ke database
        // tepat sebelum batch diproses
        \App\Models\AutomatedCredential::create([
            'product_variant_id' => $this->variant->id,
            'credential_data' => $rawCredential,
            'credential_hash' => $hash,
            'status' => 'available',
        ]);

        $service = app(\App\Services\CredentialBatchService::class);
        $result = $service->importFromText($this->variant, $rawCredential);

        // Harus tertangkap sebagai duplikat (bukan melempar error 500 / unhandled exception)
        $this->assertEquals(0, $result['inserted']);
        $this->assertEquals([1], $result['duplicates']);
    }

    public function test_cross_seller_cannot_upload_stock_to_other_seller_product(): void
    {
        $otherUser = User::factory()->create([
            'role' => UserRole::SELLER,
            'status' => UserStatus::ACTIVE,
        ]);
        $otherUser->assignRole('seller');
        \App\Models\SellerProfile::create([
            'user_id' => $otherUser->id,
            'store_name' => 'Toko Hacker',
            'store_slug' => 'tokohacker',
            'verification_status' => VerificationStatus::APPROVED,
        ]);

        // Coba upload stok ke produk milik seller pertama menggunakan akun seller lain
        $response = $this->actingAs($otherUser)
            ->post(route('seller.products.credentials.store-text', [$this->product, $this->variant]), [
                'raw_credentials' => 'hacked_account@mail.com|pass',
            ]);

        $response->assertStatus(403);
    }

    public function test_temporary_csv_file_is_removed_and_no_credentials_leaked_to_logs_on_error(): void
    {
        \Illuminate\Support\Facades\Log::spy();

        $secretAccount = 'leak_test_credential_secret@confidential.com|UltraSecret123';
        $csvContent = "{$secretAccount}\n";
        $csvFile = UploadedFile::fake()->createWithContent('test_leak.csv', $csvContent);

        // Unggah CSV yang valid
        $response = $this->actingAs($this->sellerUser)
            ->post(route('seller.products.credentials.store-csv', [$this->product, $this->variant]), [
                'csv_file' => $csvFile,
            ]);

        $response->assertRedirect();

        // Pastikan temporary file dari upload dibersihkan
        $this->assertFileDoesNotExist($csvFile->getRealPath());

        // Verifikasi Log sistem tidak pernah mencatat $secretAccount
        \Illuminate\Support\Facades\Log::shouldNotHaveReceived('error', function ($message) use ($secretAccount) {
            return str_contains((string) $message, $secretAccount);
        });
    }
}
