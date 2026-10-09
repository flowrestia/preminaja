<?php

namespace Database\Factories;

use App\Enums\VoucherType;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'discount_type' => VoucherType::PERCENTAGE,
            'discount_value' => 10.00,
            'min_purchase' => 50000.00,
            'max_discount' => 20000.00,
            'usage_limit' => 100,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(1),
            'is_active' => true,
        ];
    }
}
