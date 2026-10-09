<x-seller-layout title="Kelola Stok Kredensial" header="Stok Otomatis: {{ $product->name }} ({{ $variant->name }})">
    <div class="max-w-4xl space-y-6">
        <!-- Stock Overview -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-xs text-emerald-400 font-semibold uppercase">Tersedia</span>
                <p class="text-2xl font-bold text-white mt-1">{{ $stockStats['available'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Siap dikirim otomatis</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-xs text-amber-400 font-semibold uppercase">Dalam Proses</span>
                <p class="text-2xl font-bold text-white mt-1">{{ $stockStats['assigned'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Sedang dialokasikan</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-xs text-indigo-400 font-semibold uppercase">Terkirim</span>
                <p class="text-2xl font-bold text-white mt-1">{{ $stockStats['used'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Sudah diterima pembeli</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <span class="text-xs text-slate-400 font-semibold uppercase">Total Pool</span>
                <p class="text-2xl font-bold text-white mt-1">{{ $stockStats['total'] }}</p>
                <p class="text-xs text-slate-500 mt-1">Total riwayat akun</p>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="p-4 bg-indigo-950/40 border border-indigo-800/40 rounded-xl text-xs text-indigo-300 space-y-1">
            <p class="font-semibold text-indigo-200">Keamanan Data Kredensial:</p>
            <p>Kredensial disimpan dengan enkripsi end-to-end. Demi keamanan akun Anda dan pembeli, sistem tidak akan menampilkan isi kredensial setelah disimpan. Sistem mendeteksi duplikasi secara otomatis menggunakan hash kriptografis.</p>
        </div>

        <!-- Method 1: Bulk Textarea -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-2">1. Input Massal via Textarea</h3>
            <p class="text-xs text-slate-400 mb-4">Masukkan satu kredensial per baris (maksimal 1.000 baris sekaligus). Contoh: <code>email|password|pin</code></p>

            <form action="{{ route('seller.products.credentials.store-text', [$product, $variant]) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Daftar Kredensial (Satu per baris)</label>
                    <textarea name="raw_credentials" rows="6" required placeholder="user1@email.com|Pass123|Pin1&#10;user2@email.com|Pass456|Pin2"
                        class="w-full px-4 py-2 font-mono text-xs bg-slate-950 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Instruksi Penggunaan untuk Pembeli (Opsional)</label>
                    <input type="text" name="instructions" placeholder="Contoh: Login via aplikasi resmi, pilih profil 1"
                        class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-lg text-xs text-white">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold transition">
                        Tambahkan Kredensial
                    </button>
                </div>
            </form>
        </div>

        <!-- Method 2: CSV Upload -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-2">2. Unggah File CSV</h3>
            <p class="text-xs text-slate-400 mb-4">Unggah file format .CSV (maksimal 1 MB dan 1.000 baris). Baris pertama akan langsung dibaca sebagai data akun.</p>

            <form action="{{ route('seller.products.credentials.store-csv', [$product, $variant]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Pilih File CSV</label>
                    <input type="file" name="csv_file" accept=".csv,text/csv" required
                        class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Instruksi Penggunaan untuk Pembeli (Opsional)</label>
                    <input type="text" name="instructions" placeholder="Contoh: Login via browser web"
                        class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-lg text-xs text-white">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold transition">
                        Unggah & Impor CSV
                    </button>
                </div>
            </form>
        </div>

        <div class="pt-4">
            <a href="{{ route('seller.products.index') }}" class="text-xs text-slate-400 hover:text-white transition">
                &larr; Kembali ke Daftar Produk
            </a>
        </div>
    </div>
</x-seller-layout>
