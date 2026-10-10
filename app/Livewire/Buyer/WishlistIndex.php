<?php

namespace App\Livewire\Buyer;

use App\Models\Wishlist;
use App\Services\CatalogService;
use Livewire\Component;
use Livewire\WithPagination;

class WishlistIndex extends Component
{
    use WithPagination;

    public function removeFromWishlist(int $productId, CatalogService $catalogService): void
    {
        $catalogService->toggleWishlist(auth()->id(), $productId);
        $this->dispatch('wishlist-updated', productId: $productId, isWishlisted: false);
    }

    public function render()
    {
        // Hanya tampilkan wishlist produk yang visible in catalog
        $wishlists = Wishlist::where('user_id', auth()->id())
            ->whereHas('product', fn ($q) => $q->visibleInCatalog())
            ->with([
                'product' => fn ($q) => $q->with([
                    'seller:id,store_name,store_slug,rating_avg',
                    'category:id,name,slug',
                    'variants' => fn ($vq) => $vq->where('is_active', true)->select('id', 'product_id', 'price', 'original_price', 'stock'),
                ]),
            ])
            ->latest()
            ->paginate(12);

        return view('livewire.buyer.wishlist-index', [
            'wishlists' => $wishlists,
        ])->layout('components.layouts.app', [
            'title' => 'Wishlist Saya - PreminAja',
        ]);
    }
}
