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
        // 1. Tambah credential_hash ke automated_credentials dengan unique index (product_variant_id, credential_hash)
        Schema::table('automated_credentials', function (Blueprint $table) {
            $table->string('credential_hash', 64)->after('credential_data');
            $table->unique(['product_variant_id', 'credential_hash'], 'variant_credential_hash_unique');
        });

        // 2. Buat tabel setting_histories untuk mencatat audit log perubahan settings (siapa, kapan, nilai lama, nilai baru)
        Schema::create('setting_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key', 100)->index();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_histories');

        Schema::table('automated_credentials', function (Blueprint $table) {
            $table->dropUnique('variant_credential_hash_unique');
            $table->dropColumn('credential_hash');
        });
    }
};
