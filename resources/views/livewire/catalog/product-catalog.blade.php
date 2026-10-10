<div class="space-y-8" x-data="{ filterDrawerOpen: false }">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800/80">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Produk & Akun Digital</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Jelajahi akun streaming resmi, voucher game, dan tools produktivitas bergaransi penuh.</p>
        </div>

        <!-- Mobile Filter Button Trigger -->
        <div class="flex items-center gap-2.5 lg:hidden">
            <button
                type="button"
                x-on:click="filterDrawerOpen = true"
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs font-semibold text-slate-200 hover:text-white hover:bg-slate-800/80 transition"
            >
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Filter Katalog</span>
                @if($categoryId || $minPrice || $maxPrice)
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
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
                class="w-full pl-11 pr-4 py-3 bg-slate-900/90 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition shadow-inner"
            >
            <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <div class="flex items-center gap-3 justify-end shrink-0">
            <label for="sort-select" class="text-xs font-medium text-slate-400 whitespace-nowrap hidden sm:inline">Urutan:</label>
            <select
                id="sort-select"
                wire:model.live="sort"
                class="w-full sm:w-auto px-4 py-2.5 bg-slate-900/90 border border-slate-800 rounded-xl text-xs font-medium text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition"
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
            <div class="bg-slate-900/80 backdrop-blur border border-slate-800/80 rounded-2xl p-5 space-y-6 shadow-xl shadow-black/20">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-200 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        Filter Katalog
                    </h3>
                    @if($search || $categoryId || $minPrice || $maxPrice || $sort !== 'terbaru')
                        <button wire:click="resetFilters" type="button" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold transition">
                            Reset
                        </button>
                    @endif
                </div>

                <!-- Category Filter -->
                <div>
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">Kategori Produk</h4>
                    <div class="space-y-1 max-h-64 overflow-y-auto pr-1">
                        <button
                            wire:click="$set('categoryId', null)"
                            type="button"
                            class="w-full text-left px-3 py-2 rounded-xl text-xs transition {{ is_null($categoryId) ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}"
                        >
                            Semua Kategori
                        </button>
                        @foreach($categories as $cat)
                            <button
                                wire:click="$set('categoryId', {{ $cat->id }})"
                                type="button"
                                class="w-full text-left px-3 py-2 rounded-xl text-xs transition {{ $categoryId === $cat->id ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}"
                            >
                                {{ $cat->name }}
                            </button>
                            @if($cat->children->isNotEmpty())
                                <div class="pl-4 space-y-1 border-l border-slate-800/80 my-1 ml-2">
                                    @foreach($cat->children as $child)
                                        <button
                                            wire:click="$set('categoryId', {{ $child->id }})"
                                            type="button"
                                            class="w-full text-left px-2.5 py-1.5 rounded-lg text-xs transition {{ $categoryId === $child->id ? 'bg-indigo-600/80 text-white font-semibold' : 'text-slate-500 hover:text-slate-300' }}"
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
                <div class="pt-4 border-t border-slate-800">
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">Rentang Harga (Rp)</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <input
                            type="number"
                            wire:model.live.debounce.500ms="minPrice"
                            placeholder="Min"
                            aria-label="Harga Minimum"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        <input
                            type="number"
                            wire:model.live.debounce.500ms="maxPrice"
                            placeholder="Max"
                            aria-label="Harga Maksimum"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Filter Drawer -->
        <div
            x-show="filterDrawerOpen"
            style="display: none;"
            class="fixed inset-0 z-50 lg:hidden overflow-hidden"
            role="dialog"
            aria-modal="true"
        >
            <div
                x-show="filterDrawerOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-on:click="filterDrawerOpen = false"
                class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"
            ></div>

            <div
                x-show="filterDrawerOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 max-w-xs w-full bg-slate-900 border-l border-slate-800 p-6 flex flex-col justify-between shadow-2xl"
            >
                <div class="space-y-6 overflow-y-auto flex-1 pr-1">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <h3 class="font-bold text-sm text-white">Filter Katalog</h3>
                        <button type="button" x-on:click="filterDrawerOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Category in mobile -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kategori</h4>
                        <div class="space-y-1 max-h-52 overflow-y-auto">
                            <button wire:click="$set('categoryId', null)" type="button" class="w-full text-left px-3 py-2 rounded-xl text-xs {{ is_null($categoryId) ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:bg-slate-800' }}">
                                Semua Kategori
                            </button>
                            @foreach($categories as $cat)
                                <button wire:click="$set('categoryId', {{ $cat->id }})" type="button" class="w-full text-left px-3 py-2 rounded-xl text-xs {{ $categoryId === $cat->id ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:bg-slate-800' }}">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price in mobile -->
                    <div class="pt-4 border-t border-slate-800">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Rentang Harga (Rp)</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="Min" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white">
                            <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="Max" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center gap-3">
                    <button wire:click="resetFilters" type="button" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300 hover:bg-slate-800">
                        Reset
                    </button>
                    <button type="button" x-on:click="filterDrawerOpen = false" class="flex-1 px-4 py-2.5 rounded-xl bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-500 shadow-lg shadow-indigo-600/30">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="lg:col-span-3 space-y-6">
            <div wire:loading.class="opacity-50" class="transition-opacity duration-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products as $product)
                        <div class="group relative bg-slate-900/80 backdrop-blur-sm border border-slate-800/80 rounded-2xl overflow-hidden hover:border-slate-700 hover:shadow-2xl hover:shadow-indigo-500/5 transition-all duration-300 flex flex-col">
                            <!-- Wishlist Button -->
                            <div class="absolute top-3 right-3 z-10">
                                @livewire('catalog.wishlist-button', [
                                    'productId' => $product->id,
                                    'isWishlisted' => in_array($product->id, $userWishlistProductIds)
                                ], key('wishlist-'.$product->id))
                            </div>

                            <!-- Thumbnail -->
                            <a href="{{ route('catalog.show', $product->slug) }}" class="block aspect-video bg-slate-950 overflow-hidden relative">
                                @if($product->featured_image)
                                    <img
                                        src="{{ Str::startsWith($product->featured_image, 'http') ? $product->featured_image : asset('storage/' . $product->featured_image) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    >
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-700 bg-slate-950">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-[10px] text-slate-600 mt-1">PreminAja Verified</span>
                                    </div>
                                @endif

                                <div class="absolute bottom-2.5 left-2.5 flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase backdrop-blur-md {{ $product->delivery_type->value === 'automated' ? 'bg-purple-900/80 text-purple-200 border border-purple-500/30' : 'bg-blue-900/80 text-blue-200 border border-blue-500/30' }}">
                                        {{ $product->delivery_type->value === 'automated' ? '⚡ Instan' : '⏱ Manual' }}
                                    </span>
                                </div>
                            </a>

                            <!-- Card Body -->
                            <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                <div>
                                    <span class="text-[11px] font-semibold text-indigo-400 bg-indigo-950/60 px-2 py-0.5 rounded-full border border-indigo-800/40">
                                        {{ $product->category->name ?? 'Digital' }}
                                    </span>

                                    <h3 class="font-bold text-white text-sm sm:text-base mt-2 line-clamp-2 group-hover:text-indigo-400 transition-colors leading-snug">
                                        <a href="{{ route('catalog.show', $product->slug) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>

                                    <div class="flex items-center gap-3 mt-2.5 text-xs text-slate-400">
                                        <span class="flex items-center gap-1 text-amber-400 font-bold">
                                            ★ {{ number_format($product->rating_avg, 1) }}
                                        </span>
                                        <span>&bull;</span>
                                        <span>{{ $product->sold_count }} terjual</span>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Mulai dari</span>
                                        <span class="text-base sm:text-lg font-black text-emerald-400">
                                            Rp {{ number_format($product->minPrice(), 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-[11px] font-semibold text-slate-300 block truncate max-w-[110px]" title="{{ $product->seller->store_name ?? 'Toko' }}">
                                            {{ $product->seller->store_name ?? 'Toko' }}
                                        </span>
                                        <span class="text-[9px] text-emerald-400 font-medium">✓ Penjual Resmi</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <x-ui.empty-state
                                title="Tidak Ada Produk Ditemukan"
                                description="Kami tidak menemukan produk yang cocok dengan kata kunci pencarian atau filter harga Anda saat ini."
                            >
                                <x-slot:icon>
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </x-slot:icon>
                                <x-slot:action>
                                    <x-ui.button wire:click="resetFilters" variant="primary" size="sm">
                                        Reset Semua Filter
                                    </x-ui.button>
                                </x-slot:action>
                            </x-ui.empty-state>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination Bar -->
            @if($products->hasPages())
                <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-2xl flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
