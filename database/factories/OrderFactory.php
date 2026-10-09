<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $amount = fake()->randomElement([35000, 50000, 100000]);
        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => User::factory()->buyer(),
            'total_amount' => $amount,
            'discount_amount' => 0.00,
            'fee_amount' => 1500.00,
            'final_amount' => $amount + 1500.00,
            'payment_method' => 'tripay_qris',
            'payment_status' => PaymentStatus::PAID,
            'order_status' => OrderStatus::COMPLETED,
            'voucher_id' => null,
            'notes' => null,
            'paid_at' => now(),
            'completed_at' => now(),
            'expires_at' => now()->addHours(24),
        ];
    }
}
