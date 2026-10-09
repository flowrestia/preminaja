<x-seller-layout title="Edit Produk" header="Edit Produk: {{ $product->name }}">
    <div class="max-w-4xl">
        <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-semibold text-white">Informasi Utama</h3>
                    <span class="text-xs px-2.5 py-0.5 rounded bg-slate-800 text-slate-300">
                        Status saat ini: <strong class="text-indigo-400">{{ ucfirst($product->status->value) }}</strong>
                    </span>
                </div>

                @if($product->status->value === 'published')
                    <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-lg text-amber-300 text-xs">
                        Perhatian: Mengubah nama, deskripsi, gambar, atau kategori pada produk published akan mengembalikan status ke <strong>pending_review</strong> untuk dimoderasi ulang. Perubahan harga/stok tidak merubah status.
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p class="text-xs text-slate-500 mt-1">Slug permanen: <code class="text-indigo-400">{{ $product->slug }}</code> (tidak berubah).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Kategori</label>
                        <select name="category_id" required class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Tipe Pengiriman</label>
                        <select name="delivery_type" required class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="automated" {{ old('delivery_type', $product->delivery_type->value) === 'automated' ? 'selected' : '' }}>Otomatis (Credential Pool)</option>
                            <option value="manual" {{ old('delivery_type', $product->delivery_type->value) === 'manual' ? 'selected' : '' }}>Manual (Chat / Proses Toko)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Deskripsi Produk</label>
                    <textarea name="description" rows="5" required
                        class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Thumbnail Gambar (Kosongkan jika tidak diganti)</label>
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
                </div>
            </div>

            <!-- Varian Produk -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-semibold text-white">Varian Harga & Durasi</h3>
                    <button type="button" id="btn-add-variant" class="text-xs px-3 py-1.5 rounded bg-indigo-600 text-white font-medium hover:bg-indigo-500">
                        + Tambah Varian
                    </button>
                </div>

                <div id="variants-container" class="space-y-4">
                    @foreach($product->variants as $idx => $variant)
                        <div class="variant-item grid grid-cols-1 md:grid-cols-4 gap-4 p-4 rounded-lg bg-slate-950/60 border border-slate-800">
                            <input type="hidden" name="variants[{{ $idx }}][id]" value="{{ $variant->id }}">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Nama Varian</label>
                                <input type="text" name="variants[{{ $idx }}][name]" value="{{ old('variants.'.$idx.'.name', $variant->name) }}" required
                                    class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded text-sm text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Harga (Rp)</label>
                                <input type="number" name="variants[{{ $idx }}][price]" value="{{ old('variants.'.$idx.'.price', (int)$variant->price) }}" min="1000" required
                                    class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded text-sm text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Harga Coret (Opsional)</label>
                                <input type="number" name="variants[{{ $idx }}][original_price]" value="{{ old('variants.'.$idx.'.original_price', $variant->original_price ? (int)$variant->original_price : '') }}"
                                    class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded text-sm text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Durasi (Hari)</label>
                                <input type="number" name="variants[{{ $idx }}][duration_days]" value="{{ old('variants.'.$idx.'.duration_days', $variant->duration_days) }}" min="1" required
                                    class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded text-sm text-white">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <button type="submit" name="save_as_draft" value="1"
                    class="px-5 py-2.5 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800 text-sm font-semibold transition">
                    Simpan Perubahan
                </button>
                <button type="submit" name="submit_for_review" value="1"
                    class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                    Kirim untuk Review
                </button>
            </div>
        </form>
    </div>

    <script>
        let variantIndex = {{ count($product->variants) }};
        document.getElementById('btn-add-variant').addEventListener('click', function() {
            const container = document.getElementById('variants-container');
            const newRow = document.createElement('div');
            newRow.className = 'variant-item grid grid-cols-1 md:grid-cols-4 gap-4 p-4 rounded-lg bg-slate-950/60 border border-slate-800';
            newRow.innerHTML = `
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Nama Varian</label>
                    <input type="text" name="variants[${variantIndex}][name]" required placeholder="Contoh: 3 Bulan Sharing"
                        class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Harga (Rp)</label>
                    <input type="number" name="variants[${variantIndex}][price]" min="1000" required placeholder="50000"
                        class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Harga Coret (Opsional)</label>
                    <input type="number" name="variants[${variantIndex}][original_price]" placeholder="75000"
                        class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Durasi (Hari)</label>
                    <input type="number" name="variants[${variantIndex}][duration_days]" value="30" min="1" required
                        class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded text-sm text-white">
                </div>
            `;
            container.appendChild(newRow);
            variantIndex++;
        });
    </script>
</x-seller-layout>
