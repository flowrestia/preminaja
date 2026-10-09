<?php

namespace Database\Factories;

use App\Enums\DeliveryType;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'seller_id' => SellerProfile::factory(),
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->paragraphs(3, true),
            'delivery_type' => DeliveryType::AUTOMATED,
            'status' => ProductStatus::PUBLISHED,
            'rejection_reason' => null,
            'featured_image' => null,
            'rating_avg' => 4.85,
            'rating_count' => 15,
            'sold_count' => 42,
        ];
    }
}
