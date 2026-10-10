<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService
    ) {}

    /**
     * Tampilkan halaman katalog produk publik.
     */
    public function index(Request $request): View
    {
        return view('catalog.index');
    }

    /**
     * Tampilkan detail produk publik berdasar slug.
     * Enforce visibleInCatalog() atau hanya izinkan preview bagi pemilik produk / superadmin.
     */
    public function show(string $slug): View
    {
        $product = $this->catalogService->getProductDetail($slug, auth()->id());

        return view('catalog.show', [
            'product' => $product,
        ]);
    }
}
