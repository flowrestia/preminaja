# Implementation Plan - Fase 2: Seller + Katalog Produk PreminAja

Dokumen ini memuat rencana kerja terperinci untuk pelaksanaan **Fase 2 (Seller Center & Katalog Produk)** pada PreminAja Marketplace. Seluruh implementasi dirancang tanpa melakukan pembaruan/penambahan dependency pada `composer.json` (mempertahankan Filament 3.3.x dan Livewire 3.8.x).

---

## 1. Analisis Kebutuhan & Ruang Lingkup

### A. Dashboard Seller (`/seller/dashboard`)
- **Akses**: Hanya seller dengan `seller_profiles.verification_status === 'approved'` dan `users.status === 'active'` (dilindungi middleware `auth`, `active`, `role:seller`, `seller.approved`).
- **Data Metrik**:
  - Saldo Toko: `balance` (tersedia untuk penarikan) dan `pending_balance` (tertahan escrow).
  - Ringkasan Penjualan: Total omset bersih, total pesanan berhasil, dan total produk terjual.
  - Statistik Status Produk: Jumlah produk berdasarkan 6 status (`draft`, `pending_review`, `published`, `rejected`, `inactive`, `archived`).
  - Tabel 5 pesanan/item pesanan terbaru yang masuk ke toko seller terkait.

### B. CRUD Produk & Varian Seller
- **Rute Web**:
  - `GET /seller/products`: Daftar produk milik seller sendiri dengan filter status dan pencarian.
  - `GET /seller/products/create`: Form tambah produk & varian dinamis.
  - `POST /seller/products`: Simpan produk baru.
  - `GET /seller/products/{product}/edit`: Form edit produk & varian miliknya.
  - `PUT /seller/products/{product}`: Update produk.
  - `DELETE /seller/products/{product}`: Hapus produk (soft archive atau delete jika belum ada transaksi).
- **Atribut Produk**:
  - Judul / nama produk, slug unik otomatis (menggunakan `Str::slug` + deteksi duplikat).
  - Kategori (relasi ke tabel `categories` aktif).
  - Deskripsi lengkap (Markdown / format teks).
  - Thumbnail / Featured Image: Upload aman (validasi `image|mimes:jpg,jpeg,png,webp|max:2048`), hash filename unik, tersimpan di `storage/app/public/products`.
  - Varian Produk (1 atau lebih): nama paket (cth: "1 Bulan Private PIN"), harga (Rp), harga coret (opsional), durasi (hari), dan tipe pengiriman (`automated` vs `manual`).
- **Alur Status Moderasi**:
  - Tombol **"Simpan sebagai Draft"**: status = `draft`.
  - Tombol **"Kirim untuk Ditinjau"**: status = `pending_review` (baik saat baru dibuat maupun setelah diedit oleh seller).
  - Seller tidak dapat mengubah status menjadi `published` secara mandiri tanpa persetujuan Superadmin.

### C. Manajemen Stok Kredensial Otomatis (Instant Delivery Stock)
- **Rute Web**:
  - `GET /seller/products/{product}/variants/{variant}/credentials`: Tampilan stok varian.
  - `POST /seller/products/{product}/variants/{variant}/credentials`: Tambah stok massal.
- **Mekanisme Input Massal**:
  - Pilihan input melalui:
    1. **Textarea multi-line**: Satu kredensial per baris (format: `email|password|instruksi` atau serial key).
    2. **File CSV Upload**: Parsing baris demi baris.
- **Penyimpanan Terenkripsi & Anti-Duplikasi**:
  - Setiap kredensial disimpan ke tabel `automated_credentials` dengan status `available`.
  - Atribut `credential_data` disimpan terenkripsi menggunakan Laravel encryption (`'credential_data' => 'encrypted'`).
  - Sistem memeriksa duplikasi terhadap stok yang sudah ada pada varian tersebut (baik yang available maupun reserved/delivered).
  - **Prinsip Keamanan**: Setelah data disimpan, antarmuka **hanya menampilkan jumlah stok tersedia (count)**, tanggal penambahan, dan status. Isi data kredensial tidak pernah ditampilkan kembali kepada seller demi menjaga privasi dan keamanan akun.

### D. Panel Superadmin (Filament 3.3 Resource)
Panel Filament terpusat di `/admin` dengan akses eksklusif untuk role `superadmin`:
1. **ProductResource (`ProductReview`)**:
   - Filter tab: `pending_review`, `published`, `rejected`, `all`.
   - Tindakan:
     - **Approve**: Mengubah status produk menjadi `published`.
     - **Reject**: Modal pop-up dengan input alasan wajib (`rejection_reason`), mengubah status produk menjadi `rejected`.
2. **CategoryResource**:
   - CRUD Kategori: Nama, slug, icon, deskripsi, sub-kategori (`parent_id`), toggle aktif/nonaktif.
3. **UserResource & SellerResource**:
   - Daftar Buyer & Seller.
   - Moderasi Seller: Approve toko (`verification_status = approved`), Reject toko dengan catatan.
   - Tindakan Pemblokiran: Blokir akun user (`status = blocked`).
   - **Sembunyikan Produk Otomatis**: Saat akun seller atau buyer diblokir, seluruh produk miliknya secara otomatis disembunyikan dari katalog publik (`Product::withoutGlobalScopes()` / query scope `published` yang mengecek status seller).
4. **SettingPage / SettingResource**:
   - Antarmuka pengelolaan pengaturan platform:
     - `platform_fee_percentage` (persentase komisi platform),
     - `escrow_holding_days` (durasi penahanan dana escrow),
     - `min_withdrawal` (minimal nominal penarikan dana).

### E. Katalog Publik & Fitur Buyer
- **Rute Web**:
  - `GET /catalog`: Halaman eksplorasi produk.
  - `GET /catalog/{product:slug}`: Halaman detail produk publik.
  - `POST /buyer/wishlist/{product}`: Toggle simpan produk ke wishlist.
  - `GET /buyer/wishlist`: Daftar produk favorit buyer.
- **Logika Penampilan Produk**:
  - Produk hanya tampil jika:
    1. `products.status === 'published'`.
    2. `seller_profiles.verification_status === 'approved'`.
    3. `users.status === 'active'`.
- **Fitur Pencarian & Filter**:
  - Pencarian kata kunci (nama & deskripsi).
  - Filter kategori & rentang harga (min & max).
  - Sorting: Terbaru, Terpopuler / Paling Banyak Terjual (`sold_count`), Rating Tertinggi (`rating_avg`), Harga Terendah / Tertinggi.
  - Pagination (12 item per halaman) dengan UI Tailwind responsif.
- **Halaman Detail Produk**:
  - Galeri gambar, deskripsi, rating & ulasan pembeli terverifikasi.
  - Profil toko penjual (nama toko, badge terverifikasi, rating toko).
  - Pilihan varian interaktif: Nama paket, harga, estimasi pengiriman (Instan Otomatis vs Pengiriman Manual Seller), dan status ketersediaan stok (`Tersedia (x akun)` atau `Stok Habis`).

### F. Authorization Policy & Validasi Keamanan
1. **ProductPolicy**:
   - `view`: Seller hanya bisa melihat produk miliknya; superadmin bisa melihat semua.
   - `update`: Seller hanya bisa mengedit produk miliknya; superadmin dapat memoderasi.
   - `delete`: Seller hanya bisa menghapus produk miliknya.
   - Uji akses lintas seller: Jika Seller B mencoba membuka/mengedit produk Seller A, sistem wajib mengembalikan **403 Forbidden** atau **404 Not Found**.
2. **CredentialPolicy**:
   - Input stok massal hanya diizinkan untuk pemilik produk yang bersangkutan.
3. **Upload File Validation**:
   - Validasi ketat: `image`, `mimes:jpeg,jpg,png,webp`, `max:2048` (2MB).
   - Sanitasi nama berkas: Nama file di-generate ulang menggunakan hash acak (`Str::random(40) . '.' . $extension`).

---

## 2. Struktur Arsitektur & Daftar File yang Akan Dibuat/Diubah

```
app/
├── Filament/
│   ├── Pages/
│   │   └── ManageSettings.php
│   └── Resources/
│       ├── CategoryResource.php
│       ├── ProductResource.php
│       ├── SellerProfileResource.php
│       └── UserResource.php
├── Http/
│   ├── Controllers/
│   │   ├── CatalogController.php
│   │   ├── Seller/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   └── CredentialController.php
│   │   └── WishlistController.php
│   └── Requests/
│       ├── Product/
│       │   ├── StoreProductRequest.php
│       │   └── UpdateProductRequest.php
│       └── Credential/
│           └── StoreCredentialBatchRequest.php
├── Livewire/
│   ├── Catalog/
│   │   ├── ProductList.php
│   │   └── ProductDetail.php
│   └── Seller/
│       ├── ProductForm.php
│       └── CredentialStockManager.php
├── Models/
│   └── Product.php (tambah Scope published & relasi)
├── Policies/
│   ├── ProductPolicy.php
│   └── AutomatedCredentialPolicy.php
└── Services/
    ├── ProductService.php
    ├── CredentialBatchService.php
    └── CatalogService.php

resources/views/
├── catalog/
│   ├── index.blade.php
│   └── show.blade.php
├── seller/
│   ├── dashboard.blade.php
│   ├── products/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── credentials.blade.php
│   └── layouts/
│       └── seller.blade.php
└── buyer/
    └── wishlist.blade.php

tests/Feature/
├── SellerDashboardTest.php
├── SellerProductCrudTest.php
├── CredentialBatchStockTest.php
├── FilamentAdminModerationTest.php
└── CatalogPublicTest.php
```

---

## 3. Tahapan Eksekusi

### Langkah 1: Service Classes & Authorization Policies
- Buat `ProductService`: Menangani slug unik, upload sanitasi gambar, manipulasi varian, dan transisi status (`draft` vs `pending_review`).
- Buat `CredentialBatchService`: Parsing multi-line text & CSV, deteksi duplikasi hash kredensial, enkripsi, dan kalkulasi penambahan stok varian.
- Buat `ProductPolicy` & daftarkan di `AppServiceProvider`.

### Langkah 2: Antarmuka Seller Center (Dashboard, CRUD Produk, & Batch Stock)
- Buat layout Seller Center (`seller.blade.php`) dengan navigasi lengkap (Dashboard, Kelola Produk, Stok Kredensial, Pesanan, Keuangan).
- Buat `DashboardController` untuk kalkulasi saldo, status produk, dan pesanan terbaru.
- Buat `ProductController` & Livewire form untuk varian dinamis.
- Buat komponen input massal kredensial dengan umpan balik jumlah stok yang berhasil ditambahkan.

### Langkah 3: Filament Admin Resources (Superadmin Panel)
- Konfigurasi `ProductResource`: Tabel produk dengan badge status, preview detail produk, aksi Approve & Reject (dialog alasan penolakan).
- Konfigurasi `CategoryResource`: Form dan tabel kategori hierarki.
- Konfigurasi `SellerProfileResource` & `UserResource`: Moderasi seller dan aksi blokir user.
- Konfigurasi Halaman Pengaturan Platform (`ManageSettings`): Pengaturan komisi %, hari escrow, dan batas penarikan.

### Langkah 4: Storefront & Katalog Buyer (Blade + Livewire)
- Buat `CatalogController` dan komponen Livewire `ProductList`: Pencarian instan, filter kategori, slider harga, pengurutan, pagination.
- Buat halaman detail produk `ProductDetail`: Pemilihan varian interaktif, informasi stok real-time, tab ulasan & profil toko.
- Buat controller & view Wishlist untuk pembeli terdaftar.

### Langkah 5: Pengujian Menyeluruh (Feature Tests)
- `SellerDashboardTest`: Akses terbatas seller approved, verifikasi metrik saldo dan ringkasan produk.
- `SellerProductCrudTest`: Pembuatan produk draft/pending_review, uji slug otomatis, validasi upload gambar.
- `ProductCrossAccessTest`: Percobaan akses Seller A terhadap produk milik Seller B wajib menghasilkan 403.
- `CredentialBatchStockTest`: Batch input multi-line dan CSV, deteksi duplikat, verifikasi kredensial tersimpan terenkripsi dan tidak bocor ke antarmuka.
- `FilamentAdminModerationTest`: Approve produk menjadi published, reject dengan alasan, pemblokiran seller otomatis menyembunyikan seluruh produknya dari katalog publik.
- `CatalogPublicTest`: Filter pencarian, hanya produk dari seller approved dan aktif yang tampil.

---

## 4. Kriteria Keberhasilan (Definition of Done)
1. Seluruh 8 poin cakupan Fase 2 terpenuhi tanpa penambahan/perubahan paket composer.
2. Semua form validasi dan antarmuka pengguna tersaji dalam bahasa Indonesia yang konsisten.
3. Seluruh suite pengujian (`php artisan test`) lulus 100% tanpa kegagalan.
