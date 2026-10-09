<?php

namespace App\Livewire\Seller;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ProductQuickStatus extends Component
{
    use AuthorizesRequests;

    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function toggleArchive()
    {
        $this->authorize('update', $this->product);

        if ($this->product->status->value === 'archived') {
            $this->product->update(['status' => 'draft']);
        } else {
            $this->product->update(['status' => 'archived']);
        }

        $this->dispatch('product-updated');
    }

    public function render()
    {
        return view('livewire.seller.product-quick-status');
    }
}
