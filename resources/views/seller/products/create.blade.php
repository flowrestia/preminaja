<x-seller-layout title="Tambah Produk" header="Tambah Produk Baru">
    <div class="max-w-4xl">
        <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Utama Produk</h3>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Akun Netflix Premium 1 Bulan Private"
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition shadow-sm">
                    <p class="text-xs text-slate-500 mt-1">Slug akan dibuat otomatis sekali dan tidak akan berubah meski judul diedit.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Kategori</label>
                        <select name="category_id" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition shadow-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Tipe Pengiriman</label>
                        <select name="delivery_type" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition shadow-sm">
                            <option value="automated" {{ old('delivery_type') === 'automated' ? 'selected' : '' }}>Otomatis (Credential Pool)</option>
                            <option value="manual" {{ old('delivery_type') === 'manual' ? 'selected' : '' }}>Manual (Chat / Proses Toko)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Produk</label>
                    <textarea name="description" rows="5" required placeholder="Jelajahi fitur lengkap, ketentuan garansi, dan panduan penggunaan..."
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition shadow-sm">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Thumbnail Gambar (Opsional)</label>
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <!-- Varian Produk -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Varian Harga & Durasi</h3>
                        <p class="text-xs text-slate-500">Tentukan harga dan durasi aktif akun untuk setiap varian.</p>
                    </div>
                    <button type="button" id="btn-add-variant" class="text-xs px-3.5 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition shadow-sm">
                        + Tambah Varian
                    </button>
                </div>

                <div id="variants-container" class="space-y-4">
                    <!-- Default initial variant row -->
                    <div class="variant-row p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3 relative">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Varian</label>
                                <input type="text" name="variants[0][name]" required placeholder="Cth: 1 Bulan (1 User)"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Harga (Rp)</label>
                                <input type="number" name="variants[0][price]" required min="1000" placeholder="35000"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Durasi Garansi (Hari)</label>
                                <input type="number" name="variants[0][duration_days]" required min="1" placeholder="30"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-md shadow-indigo-600/20 transition">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let variantIndex = 1;
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
                                <input type="text" name="variants[${variantIndex}][name]" required placeholder="Cth: 3 Bulan"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Harga (Rp)</label>
                                <input type="number" name="variants[${variantIndex}][price]" required min="1000" placeholder="95000"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Durasi Garansi (Hari)</label>
                                <input type="number" name="variants[${variantIndex}][duration_days]" required min="1" placeholder="90"
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
