<x-seller-layout title="Kelola Stok Kredensial" header="Stok Otomatis: {{ $product->name }} ({{ $variant->name }})">
    <div class="max-w-4xl space-y-6">
        <!-- Stock Overview -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <span class="text-xs text-emerald-700 font-bold uppercase tracking-wider">Tersedia</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stockStats['available'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Siap dikirim otomatis</p>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <span class="text-xs text-amber-700 font-bold uppercase tracking-wider">Dalam Proses</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stockStats['assigned'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Sedang dialokasikan</p>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <span class="text-xs text-indigo-700 font-bold uppercase tracking-wider">Terkirim</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stockStats['used'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Sudah diterima pembeli</p>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Pool</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stockStats['total'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Total riwayat akun</p>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl text-xs text-indigo-900 space-y-1">
            <p class="font-bold text-indigo-950">Keamanan Data Kredensial:</p>
            <p>Kredensial disimpan dengan enkripsi end-to-end. Demi keamanan akun Anda dan pembeli, sistem tidak akan menampilkan isi kredensial setelah disimpan. Sistem mendeteksi duplikasi secara otomatis menggunakan hash kriptografis.</p>
        </div>

        <!-- Method 1: Bulk Textarea -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-2">1. Input Massal via Textarea</h3>
            <p class="text-xs text-slate-500 mb-4">Masukkan satu kredensial per baris (maksimal 1.000 baris sekaligus). Format contoh: <code>email|password|pin</code></p>

            <form action="{{ route('seller.products.credentials.store-text', [$product, $variant]) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Daftar Kredensial (Satu per baris)</label>
                    <textarea name="raw_credentials" rows="6" required placeholder="user1@email.com|Pass123|Pin1&#10;user2@email.com|Pass456|Pin2"
                        class="w-full px-4 py-2 font-mono text-xs bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Instruksi Penggunaan untuk Pembeli (Opsional)</label>
                    <input type="text" name="instructions" placeholder="Contoh: Login via aplikasi resmi, pilih profil 1"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        Tambahkan Kredensial
                    </button>
                </div>
            </form>
        </div>

        <!-- Method 2: CSV Upload -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-2">2. Unggah File CSV</h3>
            <p class="text-xs text-slate-500 mb-4">File CSV harus memiliki kolom header: <code>credential</code> dan opsi <code>instructions</code>.</p>

            <form action="{{ route('seller.products.credentials.store-csv', [$product, $variant]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <input type="file" name="file" accept=".csv,.txt" required
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        Unggah CSV
                    </button>
                </div>
            </form>
        </div>

        <div class="pt-2">
            <a href="{{ route('seller.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600">
                &larr; Kembali ke Daftar Produk
            </a>
        </div>
    </div>
</x-seller-layout>
