<?php

namespace App\Livewire\Catalog;

use App\Services\CatalogService;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'kategori')]
    public ?int $categoryId = null;

    #[Url(as: 'min')]
    public ?string $minPrice = null;

    #[Url(as: 'max')]
    public ?string $maxPrice = null;

    #[Url(as: 'urutan')]
    public string $sort = 'terbaru';

    #[Locked]
    public int $perPage = 12;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->categoryId = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->sort = 'terbaru';
        $this->resetPage();
    }

    public function render(CatalogService $catalogService)
    {
        $filters = [
            'search' => $this->search,
            'category_id' => $this->categoryId,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'sort' => $this->sort,
            'per_page' => $this->perPage,
        ];

        $products = $catalogService->getCatalogProducts($filters);
        $categories = $catalogService->getActiveCategories();

        $userWishlistProductIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')->all()
            : [];

        return view('livewire.catalog.product-catalog', [
            'products' => $products,
            'categories' => $categories,
            'userWishlistProductIds' => $userWishlistProductIds,
        ]);
    }
}
