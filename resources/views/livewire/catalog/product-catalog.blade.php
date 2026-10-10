<div class="space-y-8">
    <!-- Header & Search Bar -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="w-full md:w-96 relative">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari akun streaming, apps, games..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            <svg class="w-5 h-5 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <label class="text-xs text-slate-400 whitespace-nowrap">Urutkan:</label>
            <select wire:model.live="sort" class="px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="terbaru">Terbaru</option>
                <option value="harga_terendah">Harga Terendah</option>
                <option value="harga_tertinggi">Harga Tertinggi</option>
                <option value="rating">Rating Tertinggi</option>
                <option value="terlaris">Paling Laris</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <aside class="space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-semibold text-sm text-white">Filter Katalog</h3>
                    @if($search || $categoryId || $minPrice || $maxPrice || $sort !== 'terbaru')
                        <button wire:click="resetFilters" type="button" class="text-xs text-indigo-400 hover:underline">
                            Reset
                        </button>
                    @endif
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Kategori</h4>
                    <div class="space-y-1">
                        <button wire:click="$set('categoryId', null)" type="button"
                            class="w-full text-left px-3 py-1.5 rounded-lg text-xs transition {{ is_null($categoryId) ? 'bg-indigo-600 text-white font-medium' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                            Semua Kategori
                        </button>
                        @foreach($categories as $cat)
                            <button wire:click="$set('categoryId', {{ $cat->id }})" type="button"
                                class="w-full text-left px-3 py-1.5 rounded-lg text-xs transition {{ $categoryId === $cat->id ? 'bg-indigo-600 text-white font-medium' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                                {{ $cat->name }}
                            </button>
                            @if($cat->children->isNotEmpty())
                                <div class="pl-4 space-y-1">
                                    @foreach($cat->children as $child)
                                        <button wire:click="$set('categoryId', {{ $child->id }})" type="button"
                                            class="w-full text-left px-2 py-1 rounded-md text-xs transition {{ $categoryId === $child->id ? 'bg-indigo-600/80 text-white font-medium' : 'text-slate-500 hover:text-slate-300' }}">
                                            &bull; {{ $child->name }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Rentang Harga (Rp)</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="Min"
                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="Max"
                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden hover:border-slate-700 transition duration-300 flex flex-col group relative">
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
                                <img src="{{ Str::startsWith($product->featured_image, 'http') ? $product->featured_image : asset('storage/' . $product->featured_image) }}"
                                    alt="{{ e($product->name) }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-700 bg-slate-950">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif

                            <div class="absolute bottom-2 left-2 flex gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold tracking-wider uppercase backdrop-blur-md {{ $product->delivery_type->value === 'automated' ? 'bg-purple-500/80 text-white' : 'bg-blue-500/80 text-white' }}">
                                    {{ $product->delivery_type->value === 'automated' ? 'Instan' : 'Manual' }}
                                </span>
                            </div>
                        </a>

                        <!-- Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <span class="text-xs text-indigo-400 font-medium">{{ $product->category->name ?? 'Digital' }}</span>
                                <h3 class="font-semibold text-white mt-1 line-clamp-2 hover:text-indigo-400 transition">
                                    <a href="{{ route('catalog.show', $product->slug) }}">{{ e($product->name) }}</a>
                                </h3>
                                <div class="flex items-center gap-2 mt-2 text-xs text-slate-400">
                                    <span class="flex items-center gap-1 text-amber-400">
                                        ★ {{ number_format($product->rating_avg, 1) }}
                                    </span>
                                    <span>&bull;</span>
                                    <span>{{ $product->sold_count }} terjual</span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Mulai dari</span>
                                    <span class="text-base font-bold text-emerald-400">
                                        Rp {{ number_format($product->minPrice(), 0, ',', '.') }}
                                    </span>
                                </div>

                                <span class="text-xs text-slate-400 truncate max-w-[100px]" title="{{ e($product->seller->store_name ?? 'Toko') }}">
                                    {{ e($product->seller->store_name ?? 'Toko') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-slate-900/40 rounded-2xl border border-slate-800">
                        <p class="text-slate-400 text-sm">Tidak ada produk yang cocok dengan pencarian atau filter Anda.</p>
                        <button wire:click="resetFilters" type="button" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold">
                            Reset Semua Filter
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="p-4 bg-slate-900 border border-slate-800 rounded-xl">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
