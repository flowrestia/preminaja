<x-layouts.app :title="$product->name . ' - PreminAja'" :description="mb_substr(strip_tags($product->description), 0, 160)">
    @livewire('catalog.product-detail-view', ['product' => $product])
</x-layouts.app>
