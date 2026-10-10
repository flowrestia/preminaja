<?php

namespace App\Livewire\Catalog;

use App\Services\CatalogService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class WishlistButton extends Component
{
    #[Locked]
    public int $productId;

    public bool $isWishlisted = false;

    public function mount(int $productId, bool $isWishlisted = false): void
    {
        $this->productId = $productId;
        $this->isWishlisted = $isWishlisted;
    }

    public function toggle(CatalogService $catalogService): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        $this->isWishlisted = $catalogService->toggleWishlist(auth()->id(), $this->productId);
        $this->dispatch('wishlist-updated', productId: $this->productId, isWishlisted: $this->isWishlisted);
    }

    public function render()
    {
        return view('livewire.catalog.wishlist-button');
    }
}
