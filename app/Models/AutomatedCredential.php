<?php

namespace App\Models;

use App\Enums\CredentialStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomatedCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'credential_data',
        'credential_hash',
        'instructions',
        'status',
        'order_item_id',
    ];

    /**
     * credential_data wajib disembunyikan dari serialisasi (JSON, API, Livewire)
     */
    protected $hidden = [
        'credential_data',
        'credential_hash',
    ];

    protected function casts(): array
    {
        return [
            'credential_data' => 'encrypted',
            'status' => CredentialStatus::class,
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Generate standard HMAC-SHA256 hash dari data kredensial ternormalisasi.
     */
    public static function generateHash(string $plainCredential): string
    {
        $normalized = trim(strtolower($plainCredential));
        $key = config('app.credential_hash_key', 'preminaja-fallback-credential-key-32b');

        return hash_hmac('sha256', $normalized, $key);
    }
}
