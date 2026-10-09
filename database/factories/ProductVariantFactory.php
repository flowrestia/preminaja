<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $price = fake()->randomElement([25000, 35000, 45000, 75000, 120000]);
        return [
            'product_id' => Product::factory(),
            'name' => fake()->randomElement(['1 Bulan Private', '1 Bulan Sharing', '3 Bulan Private', '1 Tahun Akun']),
            'price' => $price,
            'original_price' => $price * 1.3,
            'duration_days' => 30,
            'stock' => 10,
            'is_active' => true,
        ];
    }
}
