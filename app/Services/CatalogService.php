<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CatalogService
{
    /**
     * Whitelist urutan sorting yang diperbolehkan.
     */
    public const SORT_WHITELIST = [
        'terbaru' => ['column' => 'created_at', 'direction' => 'desc'],
        'harga_terendah' => ['column' => 'min_price', 'direction' => 'asc'],
        'harga_tertinggi' => ['column' => 'min_price', 'direction' => 'desc'],
        'rating' => ['column' => 'rating_avg', 'direction' => 'desc'],
        'terlaris' => ['column' => 'sold_count', 'direction' => 'desc'],
    ];

    /**
     * Ambil produk untuk etalase katalog dengan filter dan pagination.
     *
     * @param array{
     *   search?: ?string,
     *   category_id?: ?int,
     *   min_price?: ?numeric,
     *   max_price?: ?numeric,
     *   sort?: ?string,
     *   per_page?: ?int
     * } $filters
     */
    public function getCatalogProducts(array $filters = []): LengthAwarePaginator
    {
        $query = Product::visibleInCatalog()
            ->with([
                'seller:id,store_name,store_slug,rating_avg,rating_count',
                'category:id,name,slug',
                'variants' => fn ($q) => $q->where('is_active', true)->select('id', 'product_id', 'name', 'price', 'original_price', 'stock'),
            ])
            ->select('products.*');

        // 1. Pencarian teks dengan escaping SQL wildcard (% dan _) serta batasan 100 karakter
        if (!empty($filters['search'])) {
            $rawSearch = mb_substr(trim($filters['search']), 0, 100);
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $rawSearch);

            $query->where(function (Builder $sub) use ($escapedSearch) {
                $sub->where('name', 'like', "%{$escapedSearch}%")
                    ->orWhere('description', 'like', "%{$escapedSearch}%");
            });
        }

        // 2. Filter Kategori (mencakup kategori terpilih dan seluruh sub-kategori anaknya)
        if (!empty($filters['category_id'])) {
            $categoryId = (int) $filters['category_id'];
            $childIds = Category::where('parent_id', $categoryId)->pluck('id')->all();
            $categoryIds = array_merge([$categoryId], $childIds);

            $query->whereIn('category_id', $categoryIds);
        }

        // 3. Filter Rentang Harga (divalidasi min <= max jika keduanya ada)
        $minPrice = isset($filters['min_price']) && is_numeric($filters['min_price']) ? (float) $filters['min_price'] : null;
        $maxPrice = isset($filters['max_price']) && is_numeric($filters['max_price']) ? (float) $filters['max_price'] : null;

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            // Jika min > max, swap agar rentang valid
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        if ($minPrice !== null && $minPrice >= 0) {
            $query->whereHas('variants', function (Builder $vQ) use ($minPrice) {
                $vQ->where('is_active', true)->where('price', '>=', $minPrice);
            });
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $query->whereHas('variants', function (Builder $vQ) use ($maxPrice) {
                $vQ->where('is_active', true)->where('price', '<=', $maxPrice);
            });
        }

        // 4. Sorting Berdasarkan Whitelist (Jangan pernah memasukkan input langsung ke orderBy)
        $sortKey = $filters['sort'] ?? 'terbaru';
        $sortConfig = self::SORT_WHITELIST[$sortKey] ?? self::SORT_WHITELIST['terbaru'];

        if ($sortKey === 'harga_terendah' || $sortKey === 'harga_tertinggi') {
            // Join subquery atau min variants price
            $query->withMin(['variants' => fn ($q) => $q->where('is_active', true)], 'price')
                ->orderBy('variants_min_price', $sortConfig['direction']);
        } else {
            $query->orderBy($sortConfig['column'], $sortConfig['direction']);
        }

        // 5. Pagination dibatasi maksimal 24
        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 12;
        $perPage = max(1, min(24, $perPage));

        return $query->paginate($perPage);
    }

    /**
     * Dapatkan detail produk publik dengan proteksi visibleInCatalog.
     * Pemilik produk dan superadmin diizinkan melakukan pratinjau (preview).
     */
    public function getProductDetail(string $slug, ?int $authUserId = null): Product
    {
        $product = Product::where('slug', $slug)
            ->with([
                'seller:id,user_id,store_name,store_slug,bio,banner_url,rating_avg,rating_count,verification_status',
                'category:id,name,slug',
                'variants' => fn ($q) => $q->where('is_active', true)->select('id', 'product_id', 'name', 'price', 'original_price', 'duration_days', 'stock', 'is_active'),
                'reviews' => fn ($q) => $q->with('user:id,name')->latest()->limit(20),
            ])
            ->firstOrFail();

        // Cek hak akses jika produk tidak visibleInCatalog
        $isPublished = ($product->status->value === 'published');
        $isSellerApproved = ($product->seller && $product->seller->verification_status->value === 'approved');
        $isUserActive = ($product->seller && $product->seller->user && $product->seller->user->status->value === 'active');
        $isVisible = $isPublished && $isSellerApproved && $isUserActive;

        if (! $isVisible) {
            $isOwner = $authUserId && ($product->seller->user_id === $authUserId);
            $isSuperadmin = $authUserId && \App\Models\User::find($authUserId)?->hasRole('superadmin');

            if (! $isOwner && ! $isSuperadmin) {
                abort(404, 'Produk tidak ditemukan atau tidak tersedia.');
            }
        }

        return $product;
    }

    /**
     * Toggle wishlist item untuk user yang terotentikasi.
     */
    public function toggleWishlist(int $userId, int $productId): bool
    {
        // Pastikan produk berstatus visible in catalog
        $product = Product::visibleInCatalog()->where('id', $productId)->firstOrFail();

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return false; // Wishlist removed
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $product->id,
        ]);

        return true; // Wishlist added
    }

    /**
     * Dapatkan daftar kategori aktif untuk filter beserta sub-kategorinya.
     */
    public function getActiveCategories(): Collection
    {
        return Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();
    }
}
