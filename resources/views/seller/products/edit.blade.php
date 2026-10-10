<x-seller-layout title="Edit Produk" header="Edit Produk: {{ $product->name }}">
    <div class="max-w-4xl">
        <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Informasi Utama</h3>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold">
                        Status saat ini: <strong class="text-indigo-600">{{ ucfirst($product->status->value) }}</strong>
                    </span>
                </div>

                @if($product->status->value === 'published')
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        Perhatian: Mengubah nama, deskripsi, gambar, atau kategori pada produk published akan mengembalikan status ke <strong>pending_review</strong> untuk dimoderasi ulang. Perubahan harga/stok tidak merubah status.
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition shadow-sm">
                    <p class="text-xs text-slate-500 mt-1">Slug permanen: <code class="text-indigo-600 font-bold">{{ $product->slug }}</code> (tidak berubah).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Kategori</label>
                        <select name="category_id" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition shadow-sm">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Tipe Pengiriman</label>
                        <select name="delivery_type" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition shadow-sm">
                            <option value="automated" {{ old('delivery_type', $product->delivery_type->value) === 'automated' ? 'selected' : '' }}>Otomatis (Credential Pool)</option>
                            <option value="manual" {{ old('delivery_type', $product->delivery_type->value) === 'manual' ? 'selected' : '' }}>Manual (Chat / Proses Toko)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Produk</label>
                    <textarea name="description" rows="5" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition shadow-sm">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Thumbnail Gambar (Kosongkan jika tidak diganti)</label>
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <!-- Varian Produk -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Varian Harga & Durasi</h3>
                        <p class="text-xs text-slate-500">Edit harga atau tambah varian baru.</p>
                    </div>
                    <button type="button" id="btn-add-variant" class="text-xs px-3.5 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition shadow-sm">
                        + Tambah Varian
                    </button>
                </div>

                <div id="variants-container" class="space-y-4">
                    @foreach($product->variants as $index => $variant)
                        <div class="variant-row p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3 relative">
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Varian</label>
                                    <input type="text" name="variants[{{ $index }}][name]" value="{{ old("variants.$index.name", $variant->name) }}" required
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Harga (Rp)</label>
                                    <input type="number" name="variants[{{ $index }}][price]" value="{{ old("variants.$index.price", $variant->price) }}" required min="1000"
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Durasi Garansi (Hari)</label>
                                    <input type="number" name="variants[{{ $index }}][duration_days]" value="{{ old("variants.$index.duration_days", $variant->duration_days) }}" required min="1"
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-md shadow-indigo-600/20 transition">
                    Perbarui Produk
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let variantIndex = {{ count($product->variants) }};
            const container = document.getElementById('variants-container');
            const addBtn = document.getElementById('btn-add-variant');

            if (addBtn && container) {
                addBtn.addEventListener('click', function () {
                    const row = document.createElement('div');
                    row.className = 'variant-row p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3 relative';
                    row.innerHTML = `
                        <button type="button" class="btn-remove-variant absolute top-2 right-2 text-rose-500 hover:text-rose-700 text-xs font-bold p-1">
                            &times; Hapus
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Varian</label>
                                <input type="text" name="variants[${variantIndex}][name]" required placeholder="Cth: 6 Bulan"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Harga (Rp)</label>
                                <input type="number" name="variants[${variantIndex}][price]" required min="1000" placeholder="180000"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Durasi Garansi (Hari)</label>
                                <input type="number" name="variants[${variantIndex}][duration_days]" required min="1" placeholder="180"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                        </div>
                    `;
                    container.appendChild(row);
                    variantIndex++;

                    row.querySelector('.btn-remove-variant').addEventListener('click', function () {
                        row.remove();
                    });
                });
            }
        });
    </script>
</x-seller-layout>
