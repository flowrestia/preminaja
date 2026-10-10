<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Retrieve a setting value typed correctly, with fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'integer', 'int' => (int) $setting->value,
            'decimal', 'float' => (float) $setting->value,
            'boolean', 'bool' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set or update a setting value, validating range and recording audit history.
     */
    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null, ?int $userId = null): static
    {
        // Validasi batasan rentang
        if ($key === 'platform_fee_percentage') {
            $num = (float) $value;
            if ($num < 0 || $num > 50) {
                throw new \InvalidArgumentException('Komisi platform harus berada dalam rentang 0% hingga 50%.');
            }
        } elseif ($key === 'escrow_holding_days') {
            $num = (int) $value;
            if ($num < 0 || $num > 30) {
                throw new \InvalidArgumentException('Durasi escrow harus berada dalam rentang 0 hingga 30 hari.');
            }
        } elseif ($key === 'min_withdrawal') {
            $num = (float) $value;
            if ($num <= 0) {
                throw new \InvalidArgumentException('Batas minimal penarikan dana harus lebih besar dari 0.');
            }
        }

        $existing = static::where('key', $key)->first();
        $oldValue = $existing ? $existing->value : null;

        $stringValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
                'description' => $description,
            ]
        );

        // Catat riwayat perubahan jika nilai berubah
        if ($oldValue !== $stringValue) {
            SettingHistory::create([
                'user_id' => $userId ?? Auth::id(),
                'key' => $key,
                'old_value' => $oldValue,
                'new_value' => $stringValue,
            ]);

            \App\Models\ModerationLog::create([
                'actor_id' => $userId ?? Auth::id(),
                'action' => 'update_setting',
                'target_type' => static::class,
                'target_id' => $setting->id,
                'reason' => "Ubah {$key} dari {$oldValue} ke {$stringValue}",
                'created_at' => now(),
            ]);
        }

        return $setting;
    }
}
