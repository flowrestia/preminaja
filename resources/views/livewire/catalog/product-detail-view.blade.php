<div class="space-y-10">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('catalog.index') }}" class="hover:text-white transition">Katalog</a>
        <span>&rsaquo;</span>
        <span class="text-slate-300">{{ $product->category->name ?? 'Kategori' }}</span>
        <span>&rsaquo;</span>
        <span class="text-white truncate max-w-xs">{{ e($product->name) }}</span>
    </nav>

    <!-- Product Main Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Thumbnail -->
        <div class="lg:col-span-1 space-y-4">
            <div class="aspect-video lg:aspect-square rounded-2xl overflow-hidden bg-slate-900 border border-slate-800 relative">
                @if($product->featured_image)
                    <img src="{{ Str::startsWith($product->featured_image, 'http') ? $product->featured_image : asset('storage/' . $product->featured_image) }}"
                        alt="{{ e($product->name) }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-700 bg-slate-950">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
                <div class="absolute top-4 right-4">
                    @livewire('catalog.wishlist-button', [
                        'productId' => $product->id,
                        'isWishlisted' => $isWishlisted
                    ], key('wishlist-detail-'.$product->id))
                </div>
            </div>

            <!-- Seller Public Profile Card (No phone, email, or bank info) -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
                <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Dijual oleh Penjual Resmi</span>
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-white text-sm">{{ e($product->seller->store_name ?? 'Toko') }}</h4>
                        <div class="flex items-center gap-1.5 text-xs text-amber-400 mt-0.5">
                            ★ {{ number_format($product->seller->rating_avg ?? 5.0, 1) }}
                            <span class="text-slate-500">({{ $product->seller->rating_count ?? 0 }} ulasan)</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        Terverifikasi
                    </span>
                </div>
                @if($product->seller->bio)
                    <p class="text-xs text-slate-400 border-t border-slate-800/80 pt-2">{{ e($product->seller->bio) }}</p>
                @endif
            </div>
        </div>

        <!-- Details & Variant Selection -->
        <div class="lg:col-span-2 space-y-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $product->delivery_type->value === 'automated' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' }}">
                        {{ $product->delivery_type->value === 'automated' ? 'Pengiriman Instan Otomatis' : 'Pengiriman Manual' }}
                    </span>
                    <span class="text-xs text-slate-400">&bull; {{ $product->category->name }}</span>
                </div>
                <h1 class="text-2xl font-bold text-white">{{ e($product->name) }}</h1>
                <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
                    <span class="flex items-center gap-1 text-amber-400 font-semibold">
                        ★ {{ number_format($product->rating_avg, 1) }}
                    </span>
                    <span>&bull;</span>
                    <span>{{ $product->rating_count }} ulasan</span>
                    <span>&bull;</span>
                    <span>{{ $product->sold_count }} transaksi sukses</span>
                </div>
            </div>

            <!-- Variant Selector -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-semibold text-white">Pilih Varian Durasi:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($product->variants as $variant)
                        <button type="button" wire:click="selectVariant({{ $variant->id }})"
                            class="p-4 rounded-xl text-left border transition duration-200 {{ $selectedVariantId === $variant->id ? 'bg-indigo-600/10 border-indigo-500 shadow-md' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700' }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-sm text-white">{{ e($variant->name) }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $variant->duration_days }} hari masa aktif</p>
                                </div>
                                <span class="text-sm font-bold text-emerald-400">
                                    Rp {{ number_format($variant->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>

                @if($selectedVariant)
                    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs text-slate-400 block">Stok Tersedia:</span>
                            @if($product->delivery_type->value === 'automated')
                                <span class="text-sm font-semibold {{ $selectedVariant->stock > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    {{ $selectedVariant->stock > 0 ? $selectedVariant->stock . ' akun siap kirim' : 'Stok habis' }}
                                </span>
                            @else
                                <span class="text-sm font-semibold text-blue-400">
                                    Diproses Manual oleh Toko
                                </span>
                            @endif
                        </div>

                        <div class="text-right">
                            <span class="text-xs text-slate-400 block">Total Harga:</span>
                            <span class="text-2xl font-bold text-emerald-400">
                                Rp {{ number_format($selectedVariant->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Product Description (Secured with Blade escaping) -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-base font-semibold text-white">Deskripsi Produk</h3>
                <div class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>
            </div>

            <!-- Customer Reviews (Escaped XSS protection) -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-base font-semibold text-white">Ulasan Pembeli ({{ $product->reviews->count() }})</h3>
                <div class="divide-y divide-slate-800 space-y-4">
                    @forelse($product->reviews as $review)
                        <div class="pt-4 first:pt-0 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-white">{{ $review->user->name ?? 'Pembeli' }}</span>
                                <span class="text-xs text-amber-400">★ {{ $review->rating }}</span>
                            </div>
                            <p class="text-xs text-slate-300">{{ $review->comment }}</p>
                            @if($review->seller_reply)
                                <div class="p-3 mt-2 rounded-lg bg-slate-950 border border-slate-800/80 text-xs">
                                    <span class="text-indigo-400 font-semibold block mb-0.5">Balasan Penjual:</span>
                                    <span class="text-slate-400">{{ $review->seller_reply }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 py-4">Belum ada ulasan untuk produk ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
