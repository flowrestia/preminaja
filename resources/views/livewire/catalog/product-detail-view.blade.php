<div class="space-y-10" x-data="{ activeTab: 'description' }">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Katalog</a>
        <span>&rsaquo;</span>
        <span class="text-slate-300">{{ $product->category->name ?? 'Kategori' }}</span>
        <span>&rsaquo;</span>
        <span class="text-white font-medium truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Product Main Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 sm:gap-10 items-start">
        <!-- Thumbnail & Seller Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="aspect-video sm:aspect-square rounded-2xl overflow-hidden bg-slate-900/90 border border-slate-800/80 relative shadow-2xl shadow-black/40">
                @if($product->featured_image)
                    <img
                        src="{{ Str::startsWith($product->featured_image, 'http') ? $product->featured_image : asset('storage/' . $product->featured_image) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover"
                    >
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-700 bg-slate-950">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs text-slate-600 mt-2 font-medium">PreminAja Official Product</span>
                    </div>
                @endif

                <div class="absolute top-4 right-4 z-10">
                    @livewire('catalog.wishlist-button', [
                        'productId' => $product->id,
                        'isWishlisted' => $isWishlisted
                    ], key('wishlist-detail-'.$product->id))
                </div>
            </div>

            <!-- Seller Public Profile Card (Strictly without phone, email, or bank info) -->
            <x-ui.card padding="p-5" class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Profil Penjual Resmi</span>
                    <x-ui.badge variant="success" size="xs">
                        Terverifikasi
                    </x-ui.badge>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold text-base shadow-md">
                        {{ strtoupper(mb_substr($product->seller->store_name ?? 'T', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-white text-sm truncate">{{ $product->seller->store_name ?? 'Toko' }}</h4>
                        <div class="flex items-center gap-1.5 text-xs text-amber-400 mt-0.5">
                            <span class="font-bold">★ {{ number_format($product->seller->rating_avg ?? 5.0, 1) }}</span>
                            <span class="text-slate-500">({{ $product->seller->rating_count ?? 0 }} ulasan)</span>
                        </div>
                    </div>
                </div>

                @if($product->seller->bio)
                    <p class="text-xs text-slate-400 leading-relaxed pt-2 border-t border-slate-800/80">
                        {{ $product->seller->bio }}
                    </p>
                @endif

                <div class="pt-2 text-[11px] text-slate-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Dilindungi sistem garansi transaksi aman PreminAja</span>
                </div>
            </x-ui.card>
        </div>

        <!-- Details, Variant Selection, & Tabs -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Title & Summary Header -->
            <div class="space-y-2.5">
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge :variant="$product->delivery_type->value === 'automated' ? 'brand' : 'neutral'" size="sm">
                        {{ $product->delivery_type->value === 'automated' ? '⚡ Pengiriman Instan Otomatis' : '⏱ Pengiriman Manual' }}
                    </x-ui.badge>
                    <span class="text-xs text-slate-400">&bull; {{ $product->category->name }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    {{ $product->name }}
                </h1>

                <div class="flex items-center gap-4 text-xs text-slate-400 pt-1">
                    <span class="flex items-center gap-1 text-amber-400 font-bold">
                        ★ {{ number_format($product->rating_avg, 1) }}
                    </span>
                    <span>&bull;</span>
                    <span>{{ $product->rating_count }} ulasan</span>
                    <span>&bull;</span>
                    <span>{{ $product->sold_count }} transaksi sukses</span>
                </div>
            </div>

            <!-- Variant Selector Box -->
            <x-ui.card padding="p-6" class="space-y-5">
                <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Pilih Varian Durasi Akun:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($product->variants as $variant)
                        <button
                            type="button"
                            wire:click="selectVariant({{ $variant->id }})"
                            class="p-4 rounded-xl text-left border transition-all duration-200 {{ $selectedVariantId === $variant->id ? 'bg-indigo-600/10 border-indigo-500 ring-2 ring-indigo-500/30 shadow-md' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700' }}"
                        >
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <p class="font-bold text-sm text-white">{{ $variant->name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $variant->duration_days }} hari garansi aktif</p>
                                </div>
                                <span class="text-sm font-black text-emerald-400 whitespace-nowrap">
                                    Rp {{ number_format($variant->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>

                @if($selectedVariant)
                    <div class="pt-5 border-t border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-xs text-slate-400 block font-medium">Ketersediaan Stok:</span>
                            @if($product->delivery_type->value === 'automated')
                                <span class="text-sm font-bold {{ $selectedVariant->stock > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $selectedVariant->stock > 0 ? $selectedVariant->stock . ' akun siap kirim instan' : 'Stok habis' }}
                                </span>
                            @else
                                <span class="text-sm font-bold text-blue-400">
                                    Diproses Manual oleh Penjual Resmi
                                </span>
                            @endif
                        </div>

                        <div class="text-left sm:text-right">
                            <span class="text-xs text-slate-400 block font-medium">Harga Satuan:</span>
                            <span class="text-2xl sm:text-3xl font-black text-emerald-400">
                                Rp {{ number_format($selectedVariant->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- CTA Beli Button (Disabled sampai Fase 3 Checkout) -->
                    <div class="pt-2">
                        <x-ui.button
                            type="button"
                            variant="primary"
                            size="lg"
                            class="w-full text-center"
                            disabled
                            title="Pemesanan dan checkout akan dibuka pada Fase 3"
                        >
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            Beli Sekarang (Segera Dibuka di Fase 3)
                        </x-ui.button>
                        <p class="text-[11px] text-center text-slate-400 mt-2">
                            Transaksi dilindungi sistem escrow otomatis. Dana hanya diteruskan ke penjual setelah akun diverifikasi aktif.
                        </p>
                    </div>
                @endif
            </x-ui.card>

            <!-- Description & Reviews Tabs -->
            <x-ui.card padding="p-6" class="space-y-5">
                <!-- Tab Headers -->
                <div class="flex items-center gap-6 border-b border-slate-800 pb-3">
                    <button
                        type="button"
                        x-on:click="activeTab = 'description'"
                        class="text-sm font-bold pb-2 -mb-3 transition-colors border-b-2"
                        :class="activeTab === 'description' ? 'text-indigo-400 border-indigo-500' : 'text-slate-400 border-transparent hover:text-white'"
                    >
                        Deskripsi Produk
                    </button>
                    <button
                        type="button"
                        x-on:click="activeTab = 'reviews'"
                        class="text-sm font-bold pb-2 -mb-3 transition-colors border-b-2 flex items-center gap-1.5"
                        :class="activeTab === 'reviews' ? 'text-indigo-400 border-indigo-500' : 'text-slate-400 border-transparent hover:text-white'"
                    >
                        <span>Ulasan Pembeli</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 font-semibold">
                            {{ $product->reviews->count() }}
                        </span>
                    </button>
                </div>

                <!-- Tab 1: Description (Escaped) -->
                <div x-show="activeTab === 'description'" class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>

                <!-- Tab 2: Reviews (Escaped XSS Protection) -->
                <div x-show="activeTab === 'reviews'" style="display: none;" class="space-y-4">
                    <div class="divide-y divide-slate-800/80">
                        @forelse($product->reviews as $review)
                            <div class="pt-4 first:pt-0 pb-4 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white">{{ $review->user->name ?? 'Pembeli' }}</span>
                                    <span class="text-xs font-bold text-amber-400">★ {{ $review->rating }}</span>
                                </div>
                                <p class="text-xs text-slate-300 leading-relaxed">{{ $review->comment }}</p>
                                @if($review->seller_reply)
                                    <div class="p-3 mt-2 rounded-xl bg-slate-950/80 border border-slate-800/80 text-xs">
                                        <span class="text-indigo-400 font-bold block mb-0.5">Balasan Penjual:</span>
                                        <span class="text-slate-400">{{ $review->seller_reply }}</span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-8 text-center text-slate-500 text-xs">
                                Belum ada ulasan untuk produk ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
