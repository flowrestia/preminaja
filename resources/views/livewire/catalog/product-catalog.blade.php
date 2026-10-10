<div class="space-y-12" x-data="{ filterDrawerOpen: false }">
    @if(!$search && !$categoryId && !$minPrice && !$maxPrice && $sort === 'terbaru')
        <!-- Hero Section Beranda PreminAja -->
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-700 via-indigo-600 to-purple-700 text-white p-8 sm:p-12 lg:p-16 shadow-xl shadow-indigo-600/15">
            <div class="relative z-10 max-w-3xl space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Marketplace Akun Premium & Produk Digital #1 di Indonesia
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                    Beli Akun Digital & Tools Resmi, <span class="text-indigo-200">Garansi Escrow 100%.</span>
                </h1>

                <p class="text-sm sm:text-base text-indigo-100/90 leading-relaxed max-w-2xl">
                    Nikmati kemudahan transaksi instan akun streaming, tools AI, voucher game, dan lisensi perangkat lunak. Dana pembeli aman tertahan sampai pesanan berhasil digunakan.
                </p>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="#katalog-produk" class="px-6 py-3 rounded-xl bg-white text-indigo-700 font-bold text-sm shadow-md hover:bg-indigo-50 transition active:scale-95">
                        Jelajahi Katalog &darr;
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-indigo-900/60 hover:bg-indigo-900/80 text-white font-bold text-sm border border-white/20 transition active:scale-95">
                            Buka Toko Seller Gratis
                        </a>
                    @endguest
                </div>
            </div>

            <!-- Decorative Pattern -->
            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-10 top-1/2 -translate-y-1/2 hidden lg:flex items-center justify-center opacity-90">
                <div class="w-64 h-64 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 p-6 shadow-2xl space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white text-indigo-600 flex items-center justify-center font-black">P</div>
                        <div>
                            <div class="text-xs font-bold">Escrow Guarantee</div>
                            <div class="text-[10px] text-indigo-200">100% Proteksi Dana</div>
                        </div>
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="p-2.5 rounded-lg bg-white/10 flex items-center justify-between">
                            <span>Pengiriman Akun</span>
                            <span class="font-bold text-emerald-300">Instan Otomatis</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-white/10 flex items-center justify-between">
                            <span>Masa Garansi</span>
                            <span class="font-bold text-indigo-200">Penuh s/d Selesai</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Penjelasan Escrow & Keamanan Transaksi -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Escrow System Otomatis</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Dana Anda disimpan aman di sistem bersama dan baru diteruskan ke seller setelah akun terverifikasi aktif.</p>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Pengiriman Akun Instan</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Untuk produk otomatis, kredensial akun langsung dikirim detik itu juga setelah pembayaran berhasil.</p>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Seller Terverifikasi</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Seluruh toko dan seller melalui proses kurasi identitas ketat oleh tim kepatuhan demi integritas transaksi.</p>
                </div>
            </div>
        </section>

        <!-- Kategori Populer Quick Grid -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Kategori Populer</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih kategori produk digital favorit Anda</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($categories->take(4) as $cat)
                    <button
                        type="button"
                        wire:click="$set('categoryId', {{ $cat->id }})"
                        class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-indigo-300 hover:shadow-md transition text-left group shadow-sm"
                    >
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 transition">{{ $cat->name }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $cat->children->count() }} Sub-kategori</p>
                    </button>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Main Catalog Anchor -->
    <div id="katalog-produk" class="space-y-6 pt-4">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Katalog Produk & Akun Digital</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Jelajahi akun streaming resmi, voucher game, dan tools produktivitas bergaransi penuh.</p>
            </div>

            <!-- Mobile Filter Button Trigger -->
            <div class="flex items-center gap-2.5 lg:hidden">
                <button
                    type="button"
                    x-on:click="filterDrawerOpen = true"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition shadow-sm"
                >
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filter Katalog</span>
                    @if($categoryId || $minPrice || $maxPrice)
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    @endif
                </button>
            </div>
        </div>

        <!-- Search & Sort Row -->
        <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
            <div class="w-full sm:max-w-md relative">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Cari akun Netflix, Spotify, Canva..."
                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition shadow-sm"
                >
                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex items-center gap-3 justify-end shrink-0">
                <label for="sort-select" class="text-xs font-semibold text-slate-600 whitespace-nowrap hidden sm:inline">Urutan:</label>
                <select
                    id="sort-select"
                    wire:model.live="sort"
                    class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition shadow-sm"
                >
                    <option value="terbaru">Terbaru Ditambahkan</option>
                    <option value="harga_terendah">Harga Terendah</option>
                    <option value="harga_tertinggi">Harga Tertinggi</option>
                    <option value="rating">Rating Tertinggi</option>
                    <option value="terlaris">Paling Laris</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            <!-- Desktop Sidebar Filter -->
            <aside class="hidden lg:block space-y-6 sticky top-28">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 space-y-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                            Filter Katalog
                        </h3>
                        @if($search || $categoryId || $minPrice || $maxPrice || $sort !== 'terbaru')
                            <button wire:click="resetFilters" type="button" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold transition">
                                Reset
                            </button>
                        @endif
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2.5">Kategori Produk</h4>
                        <div class="space-y-1 max-h-64 overflow-y-auto pr-1">
                            <button
                                wire:click="$set('categoryId', null)"
                                type="button"
                                class="w-full text-left px-3 py-2 rounded-xl text-xs transition {{ is_null($categoryId) ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                            >
                                Semua Kategori
                            </button>
                            @foreach($categories as $cat)
                                <button
                                    wire:click="$set('categoryId', {{ $cat->id }})"
                                    type="button"
                                    class="w-full text-left px-3 py-2 rounded-xl text-xs transition {{ $categoryId === $cat->id ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                                >
                                    {{ $cat->name }}
                                </button>
                                @if($cat->children->isNotEmpty())
                                    <div class="pl-4 space-y-1 border-l border-slate-200 my-1 ml-2">
                                        @foreach($cat->children as $child)
                                            <button
                                                wire:click="$set('categoryId', {{ $child->id }})"
                                                type="button"
                                                class="w-full text-left px-2.5 py-1.5 rounded-lg text-xs transition {{ $categoryId === $child->id ? 'bg-indigo-100 text-indigo-800 font-semibold' : 'text-slate-500 hover:text-slate-800' }}"
                                            >
                                                &bull; {{ $child->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="border-t border-slate-100 pt-5 space-y-3">
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Rentang Harga (Rp)</h4>
                        <div class="space-y-2">
                            <div>
                                <input
                                    type="number"
                                    wire:model.live.debounce.500ms="minPrice"
                                    placeholder="Harga Min (cth: 10000)"
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-600"
                                >
                            </div>
                            <div>
                                <input
                                    type="number"
                                    wire:model.live.debounce.500ms="maxPrice"
                                    placeholder="Harga Max (cth: 200000)"
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-600"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Product Grid Area -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Active Filters Chips -->
                @if($search || $categoryId || $minPrice || $maxPrice)
                    <div class="flex flex-wrap items-center gap-2 pb-2">
                        <span class="text-xs text-slate-500 font-medium">Filter aktif:</span>
                        @if($search)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 text-slate-700 rounded-full text-xs shadow-sm">
                                Pencarian: "{{ $search }}"
                                <button wire:click="$set('search', '')" class="hover:text-rose-600">&times;</button>
                            </span>
                        @endif
                        @if($categoryId)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-full text-xs">
                                Kategori dipilih
                                <button wire:click="$set('categoryId', null)" class="hover:text-rose-600">&times;</button>
                            </span>
                        @endif
                        @if($minPrice || $maxPrice)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 text-slate-700 rounded-full text-xs shadow-sm">
                                Harga: {{ $minPrice ? 'Rp ' . number_format($minPrice, 0, ',', '.') : '0' }} - {{ $maxPrice ? 'Rp ' . number_format($maxPrice, 0, ',', '.') : 'Maks' }}
                                <button wire:click="resetFilters" class="hover:text-rose-600">&times;</button>
                            </span>
                        @endif
                    </div>
                @endif

                <!-- Loading Skeleton Indicator -->
                <div wire:loading.delay wire:target="search, categoryId, minPrice, maxPrice, sort" class="w-full">
                    <x-ui.skeleton type="card" :count="6" />
                </div>

                <!-- Products Listing -->
                <div wire:loading.remove wire:target="search, categoryId, minPrice, maxPrice, sort">
                    @if($products->isEmpty())
                        <x-ui.empty-state
                            title="Tidak Ada Produk Ditemukan"
                            description="Maaf, kami tidak dapat menemukan produk yang sesuai dengan kata kunci atau filter Anda. Silakan coba filter yang lain."
                        >
                            <x-slot:icon>
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </x-slot:icon>
                            <x-slot:action>
                                <x-ui.button wire:click="resetFilters" variant="secondary" size="sm">
                                    Reset Semua Filter
                                </x-ui.button>
                            </x-slot:action>
                        </x-ui.empty-state>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <div class="group relative bg-white border border-slate-200/90 rounded-2xl overflow-hidden hover:border-indigo-300 hover:shadow-md transition-all duration-300 flex flex-col shadow-sm">
                                    <!-- Thumbnail Container -->
                                    <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                        @if($product->thumbnail_url)
                                            <img
                                                src="{{ $product->thumbnail_url }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 p-4">
                                                <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-[10px] font-medium uppercase tracking-wider">Tanpa Pratinjau</span>
                                            </div>
                                        @endif

                                        <!-- Delivery Type Badge -->
                                        <div class="absolute top-2.5 left-2.5">
                                            @if($product->delivery_type->value === 'automated')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-indigo-600 text-white shadow-sm">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                                    Instan Otomatis
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-800 text-white shadow-sm">
                                                    Proses Manual
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Wishlist Button -->
                                        <button
                                            type="button"
                                            wire:click="toggleWishlist({{ $product->id }})"
                                            class="absolute top-2.5 right-2.5 p-2 rounded-xl bg-white/90 backdrop-blur-md text-slate-400 hover:text-pink-500 hover:scale-110 active:scale-95 transition-all shadow-sm"
                                            aria-label="Tambah ke Wishlist"
                                        >
                                            <svg class="w-4 h-4 {{ in_array($product->id, $userWishlistProductIds ?? []) ? 'text-pink-500 fill-pink-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Card Content Body -->
                                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                        <div>
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md uppercase tracking-wider">
                                                    {{ $product->category->name ?? 'Digital' }}
                                                </span>
                                                <!-- Rating Snippet -->
                                                <div class="flex items-center gap-1 text-[11px] font-bold text-slate-700">
                                                    <svg class="w-3.5 h-3.5 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    <span>{{ number_format($product->rating_cached, 1) }}</span>
                                                    <span class="text-slate-400 font-normal">({{ $product->reviews_count }})</span>
                                                </div>
                                            </div>

                                            <h2 class="mt-2 text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition line-clamp-2">
                                                <a href="{{ route('catalog.show', $product->slug) }}">
                                                    {{ $product->name }}
                                                </a>
                                            </h2>

                                            <!-- Store Info -->
                                            <p class="mt-1 text-xs text-slate-500 flex items-center gap-1.5">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span class="truncate">{{ $product->seller->sellerProfile->store_name ?? $product->seller->name }}</span>
                                            </p>
                                        </div>

                                        <!-- Price & CTA Footer -->
                                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                            <div>
                                                <span class="text-[10px] text-slate-500 block font-medium">Mulai dari</span>
                                                <div class="text-base font-extrabold text-indigo-700">
                                                    Rp {{ number_format($product->min_price, 0, ',', '.') }}
                                                </div>
                                            </div>

                                            <a
                                                href="{{ route('catalog.show', $product->slug) }}"
                                                class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-xl bg-slate-100 group-hover:bg-indigo-600 text-slate-700 group-hover:text-white text-xs font-bold transition-all shadow-sm"
                                            >
                                                Detail &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination Links -->
                        <div class="pt-8">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Filter Offcanvas Drawer -->
    <div
        x-show="filterDrawerOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 lg:hidden flex"
        style="display: none;"
    >
        <!-- Overlay -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" x-on:click="filterDrawerOpen = false"></div>

        <!-- Sliding Panel -->
        <div class="relative ml-auto w-full max-w-xs bg-white h-full shadow-2xl p-6 overflow-y-auto space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="font-bold text-sm text-slate-900">Filter Katalog</h3>
                <button type="button" x-on:click="filterDrawerOpen = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Categories Mobile -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold text-slate-500 uppercase">Kategori</h4>
                <div class="space-y-1">
                    <button
                        wire:click="$set('categoryId', null); filterDrawerOpen = false"
                        type="button"
                        class="w-full text-left px-3 py-2 rounded-xl text-xs {{ is_null($categoryId) ? 'bg-indigo-600 text-white font-bold' : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        Semua Kategori
                    </button>
                    @foreach($categories as $cat)
                        <button
                            wire:click="$set('categoryId', {{ $cat->id }}); filterDrawerOpen = false"
                            type="button"
                            class="w-full text-left px-3 py-2 rounded-xl text-xs {{ $categoryId === $cat->id ? 'bg-indigo-600 text-white font-bold' : 'text-slate-700 hover:bg-slate-50' }}"
                        >
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Price Range Mobile -->
            <div class="space-y-3 pt-4 border-t border-slate-200">
                <h4 class="text-xs font-bold text-slate-500 uppercase">Rentang Harga</h4>
                <input
                    type="number"
                    wire:model.live.debounce.500ms="minPrice"
                    placeholder="Harga Minimum (Rp)"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800"
                >
                <input
                    type="number"
                    wire:model.live.debounce.500ms="maxPrice"
                    placeholder="Harga Maksimum (Rp)"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800"
                >
            </div>

            <div class="pt-6">
                <x-ui.button
                    type="button"
                    x-on:click="filterDrawerOpen = false"
                    variant="primary"
                    size="md"
                    class="w-full"
                >
                    Terapkan Filter
                </x-ui.button>
            </div>
        </div>
    </div>
</div>
