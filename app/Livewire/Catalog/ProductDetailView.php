<?php

namespace App\Livewire\Catalog;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ProductDetailView extends Component
{
    #[Locked]
    public Product $product;

    public ?int $selectedVariantId = null;

    public function mount(Product $product): void
    {
        $this->product = $product;

        // Default pilih varian pertama yang aktif
        $firstVariant = $product->variants->first();
        if ($firstVariant) {
            $this->selectedVariantId = $firstVariant->id;
        }
    }

    public function selectVariant(int $variantId): void
    {
        $this->selectedVariantId = $variantId;
    }

    public function getSelectedVariantProperty(): ?ProductVariant
    {
        return $this->product->variants->firstWhere('id', $this->selectedVariantId);
    }

    public function render()
    {
        $isWishlisted = auth()->check()
            ? auth()->user()->wishlists()->where('product_id', $this->product->id)->exists()
            : false;

        return view('livewire.catalog.product-detail-view', [
            'isWishlisted' => $isWishlisted,
            'selectedVariant' => $this->selectedVariant,
        ]);
    }
}
