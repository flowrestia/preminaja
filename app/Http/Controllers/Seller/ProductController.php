<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $seller = $request->user()->sellerProfile;
        $products = $seller->products()
            ->with(['category', 'variants'])
            ->withCount('variants')
            ->latest()
            ->paginate(15);

        return view('seller.products.index', compact('products'));
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('seller.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->createProduct(
            $request->user()->sellerProfile,
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()->route('seller.products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product->load(['variants']);

        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->updateProduct(
            $product,
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()->route('seller.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->productService->deleteProduct($product);

        return redirect()->route('seller.products.index')
            ->with('status', 'Produk berhasil diproses (dihapus atau diarsipkan jika memiliki riwayat pesanan).');
    }
}
