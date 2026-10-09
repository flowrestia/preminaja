<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CredentialBatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CredentialStockController extends Controller
{
    public function __construct(
        protected CredentialBatchService $batchService
    ) {}

    public function index(Product $product, ProductVariant $variant): View
    {
        $this->authorize('update', $product);

        abort_unless($variant->product_id === $product->id, 404);

        $stockStats = [
            'available' => $variant->automatedCredentials()->where('status', 'available')->count(),
            'assigned' => $variant->automatedCredentials()->where('status', 'assigned')->count(),
            'used' => $variant->automatedCredentials()->where('status', 'used')->count(),
            'total' => $variant->automatedCredentials()->count(),
        ];

        return view('seller.products.credentials', compact('product', 'variant', 'stockStats'));
    }

    public function storeText(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->authorize('update', $product);
        abort_unless($variant->product_id === $product->id, 404);

        $validated = $request->validate([
            'raw_credentials' => ['required', 'string'],
            'instructions' => ['nullable', 'string', 'max:1000'],
        ], [
            'raw_credentials.required' => 'Daftar kredensial wajib diisi.',
        ]);

        try {
            $result = $this->batchService->importFromText(
                $variant,
                $validated['raw_credentials'],
                $validated['instructions'] ?? null
            );

            $message = "Berhasil menambahkan {$result['inserted']} stok kredensial.";
            if (!empty($result['duplicates'])) {
                $duplicateNotice = implode(', ', array_map(fn($line) => "baris {$line}", $result['duplicates']));
                $message .= " Ditemukan {$duplicateNotice} duplikat (dilewati).";
            }

            return redirect()->route('seller.products.credentials.index', [$product, $variant])
                ->with('status', $message);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['raw_credentials' => $e->getMessage()])->withInput();
        }
    }

    public function storeCsv(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->authorize('update', $product);
        abort_unless($variant->product_id === $product->id, 404);

        $request->validate([
            'csv_file' => ['required', 'file', 'max:1024'], // 1 MB
            'instructions' => ['nullable', 'string', 'max:1000'],
        ], [
            'csv_file.required' => 'File CSV wajib diunggah.',
            'csv_file.max' => 'Ukuran file CSV maksimal 1 MB.',
        ]);

        try {
            $result = $this->batchService->importFromCsv(
                $variant,
                $request->file('csv_file'),
                $request->input('instructions')
            );

            $message = "Berhasil mengimpor {$result['inserted']} stok kredensial dari CSV.";
            if (!empty($result['duplicates'])) {
                $duplicateNotice = implode(', ', array_map(fn($line) => "baris {$line}", $result['duplicates']));
                $message .= " Ditemukan {$duplicateNotice} duplikat (dilewati).";
            }

            return redirect()->route('seller.products.credentials.index', [$product, $variant])
                ->with('status', $message);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['csv_file' => $e->getMessage()]);
        }
    }
}
