<?php

namespace App\Services;

use App\Enums\DeliveryType;
use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellerProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Generate unique slug once.
     */
    public function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = 'produk-' . Str::random(6);
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Create product with variants.
     */
    public function createProduct(SellerProfile $seller, array $data, ?UploadedFile $thumbnail = null): Product
    {
        return DB::transaction(function () use ($seller, $data, $thumbnail) {
            $slug = $this->generateUniqueSlug($data['name']);

            $featuredImagePath = null;
            if ($thumbnail) {
                $featuredImagePath = $thumbnail->store('products', 'public');
            }

            $status = !empty($data['is_draft']) ? ProductStatus::DRAFT : ProductStatus::PENDING_REVIEW;

            $product = Product::create([
                'seller_id' => $seller->id,
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? '',
                'delivery_type' => $data['delivery_type'] ?? DeliveryType::MANUAL,
                'status' => $status,
                'featured_image' => $featuredImagePath,
            ]);

            if (!empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $product->variants()->create([
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'original_price' => $variantData['original_price'] ?? null,
                        'duration_days' => $variantData['duration_days'] ?? 30,
                        'stock' => $variantData['stock'] ?? 0,
                        'is_active' => $variantData['is_active'] ?? true,
                    ]);
                }
            }

            return $product;
        });
    }

    /**
     * Update product and variants.
     * Rule:
     * - Slug is never changed.
     * - If product is published, altering title, description, category, or thumbnail reverts status to pending_review.
     * - Altering only price or stock does not revert published status.
     */
    public function updateProduct(Product $product, array $data, ?UploadedFile $newThumbnail = null): Product
    {
        return DB::transaction(function () use ($product, $data, $newThumbnail) {
            $wasPublished = ($product->status === ProductStatus::PUBLISHED);
            $sensitiveFieldChanged = false;

            // Check sensitive changes
            if (isset($data['name']) && $data['name'] !== $product->name) {
                $sensitiveFieldChanged = true;
            }
            if (isset($data['description']) && $data['description'] !== $product->description) {
                $sensitiveFieldChanged = true;
            }
            if (isset($data['category_id']) && (int) $data['category_id'] !== (int) $product->category_id) {
                $sensitiveFieldChanged = true;
            }
            if (isset($data['delivery_type']) && $data['delivery_type'] !== $product->delivery_type->value) {
                $sensitiveFieldChanged = true;
            }
            if ($newThumbnail) {
                $sensitiveFieldChanged = true;
            }

            $updatePayload = [];
            if (isset($data['name'])) {
                $updatePayload['name'] = $data['name'];
            }
            if (isset($data['description'])) {
                $updatePayload['description'] = $data['description'];
            }
            if (isset($data['category_id'])) {
                $updatePayload['category_id'] = $data['category_id'];
            }
            if (isset($data['delivery_type'])) {
                $updatePayload['delivery_type'] = $data['delivery_type'];
            }

            if ($newThumbnail) {
                if ($product->featured_image) {
                    Storage::disk('public')->delete($product->featured_image);
                }
                $updatePayload['featured_image'] = $newThumbnail->store('products', 'public');
            }

            // Determine status
            if ($wasPublished && $sensitiveFieldChanged) {
                $updatePayload['status'] = ProductStatus::PENDING_REVIEW;
                $updatePayload['rejection_reason'] = null; // reset if previously rejected
            } elseif (!empty($data['save_as_draft'])) {
                $updatePayload['status'] = ProductStatus::DRAFT;
            } elseif (!empty($data['submit_for_review'])) {
                $updatePayload['status'] = ProductStatus::PENDING_REVIEW;
                $updatePayload['rejection_reason'] = null;
            }

            $product->update($updatePayload);

            // Sync or update variants
            if (!empty($data['variants']) && is_array($data['variants'])) {
                $keptVariantIds = [];

                foreach ($data['variants'] as $variantData) {
                    if (!empty($variantData['id'])) {
                        $variant = $product->variants()->find($variantData['id']);
                        if ($variant) {
                            $oldPrice = (float) $variant->price;
                            $newPrice = (float) $variantData['price'];

                            // Catat riwayat jika harga berubah
                            if ($oldPrice !== $newPrice) {
                                \App\Models\PriceHistory::create([
                                    'product_variant_id' => $variant->id,
                                    'user_id' => auth()->id(),
                                    'old_price' => $oldPrice,
                                    'new_price' => $newPrice,
                                ]);
                            }

                            $variant->update([
                                'name' => $variantData['name'],
                                'price' => $variantData['price'],
                                'original_price' => $variantData['original_price'] ?? null,
                                'duration_days' => $variantData['duration_days'] ?? 30,
                                'stock' => $variantData['stock'] ?? $variant->stock,
                                'is_active' => $variantData['is_active'] ?? true,
                            ]);
                            $keptVariantIds[] = $variant->id;
                            continue;
                        }
                    }

                    // New variant created
                    $newVariant = $product->variants()->create([
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'original_price' => $variantData['original_price'] ?? null,
                        'duration_days' => $variantData['duration_days'] ?? 30,
                        'stock' => $variantData['stock'] ?? 0,
                        'is_active' => $variantData['is_active'] ?? true,
                    ]);
                    $keptVariantIds[] = $newVariant->id;
                }

                // Varian yang dihilangkan dari form:
                // Jika punya orderItem ATAU punya automatedCredentials -> jangan hapus, melainkan jadikan is_active = false
                $removedVariants = $product->variants()->whereNotIn('id', $keptVariantIds)->get();
                foreach ($removedVariants as $remVariant) {
                    $hasOrders = $remVariant->orderItems()->exists();
                    $hasCredentials = $remVariant->automatedCredentials()->exists();

                    if ($hasOrders || $hasCredentials) {
                        $remVariant->update(['is_active' => false]);
                    } else {
                        $remVariant->delete();
                    }
                }
            }

            return $product->fresh(['variants', 'category']);
        });
    }

    /**
     * Delete or archive product based on order existence.
     * Aturan: Produk yang sudah memiliki order tidak boleh dihapus permanen, hanya diarsipkan (archived).
     */
    public function deleteProduct(Product $product): void
    {
        $hasOrders = $product->variants()->whereHas('orderItems')->exists();

        if ($hasOrders) {
            $product->update(['status' => ProductStatus::ARCHIVED]);
        } else {
            // Hapus soft/hard
            $product->delete();
        }
    }
}
