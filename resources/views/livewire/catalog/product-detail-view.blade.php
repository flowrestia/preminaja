<div class="space-y-8" x-data="{ activeTab: 'description' }">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="{{ url('/') }}" class="hover:text-indigo-600 transition">Beranda</a>
        <span>&rsaquo;</span>
        <a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition">Katalog</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700 truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Product Hero / Overview Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Media Gallery (Col 5) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="relative aspect-video sm:aspect-square bg-slate-100 border border-slate-200/90 rounded-2xl overflow-hidden shadow-sm">
                @if($product->thumbnail_url)
                    <img
                        src="{{ $product->thumbnail_url }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover"
                    >
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 p-6">
                        <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs font-semibold uppercase tracking-wider">Pratinjau Gambar Tidak Tersedia</span>
                    </div>
                @endif

                <!-- Delivery Badge -->
                <div class="absolute top-3 left-3">
                    @if($product->delivery_type->value === 'automated')
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-md">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                            Pengiriman Otomatis Instan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-800 text-white shadow-md">
                            Pengiriman Manual
                        </span>
                    @endif
                </div>
            </div>

            <!-- Trust Guarantee Callouts -->
            <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center gap-3.5 text-xs text-indigo-900 shadow-sm">
                <svg class="w-6 h-6 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                <div>
                    <h4 class="font-bold text-indigo-950">Jaminan Garansi 100% PreminAja</h4>
                    <p class="text-[11px] text-indigo-800 mt-0.5">Uang Anda ditahan di rekening bersama kami sampai akun terbukti aktif dan sesuai masa durasi.</p>
                </div>
            </div>
        </div>

        <!-- Product Details & Variant Picker (Col 7) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Header Titles -->
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                        {{ $product->category->name ?? 'Digital' }}
                    </span>
                    <span class="text-slate-300">&bull;</span>
                    <div class="flex items-center gap-1 text-xs font-bold text-slate-700">
                        <svg class="w-4 h-4 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span>{{ number_format($product->rating_cached, 1) }}</span>
                        <span class="text-slate-400 font-normal">({{ $product->reviews_count }} Ulasan Pembeli)</span>
                    </div>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    {{ $product->name }}
                </h1>

                <!-- Seller Profile Snippet (Aman tanpa data sensitif kredensial) -->
                @php
                    $sellerStoreName = $product->seller->store_name ?? $product->seller->sellerProfile->store_name ?? $product->seller->name ?? 'Penjual';
                    $sellerBio = $product->seller->bio ?? $product->seller->sellerProfile->bio ?? 'Toko Resmi Terverifikasi • Respon Cepat';
                @endphp
                <div class="mt-4 flex items-center justify-between p-3.5 rounded-xl bg-white border border-slate-200/90 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 font-bold text-sm">
                            {{ substr($sellerStoreName, 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-xs font-bold text-slate-900">{{ $sellerStoreName }}</h3>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Penjual Aktif"></span>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $sellerBio }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variant Selector Card -->
            <x-ui.card padding="p-5 sm:p-6" class="space-y-5 border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Varian Durasi Akun:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($product->variants as $variant)
                        <button
                            type="button"
                            wire:click="selectVariant({{ $variant->id }})"
                            class="p-4 rounded-xl text-left border transition-all duration-200 {{ $selectedVariantId === $variant->id ? 'bg-indigo-50/70 border-indigo-500 ring-2 ring-indigo-500/20 shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300' }}"
                        >
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <p class="font-bold text-sm text-slate-900">{{ $variant->name }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $variant->duration_days }} hari garansi aktif</p>
                                </div>
                                <span class="text-sm font-black text-indigo-700 whitespace-nowrap">
                                    Rp {{ number_format($variant->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>

                @if($selectedVariant)
                    <div class="pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-xs text-slate-500 block font-medium">Ketersediaan Stok:</span>
                            @if($product->delivery_type->value === 'automated')
                                <span class="text-sm font-bold {{ $selectedVariant->stock > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $selectedVariant->stock > 0 ? $selectedVariant->stock . ' akun siap kirim instan' : 'Stok habis' }}
                                </span>
                            @else
                                <span class="text-sm font-bold text-indigo-600">
                                    Diproses Manual oleh Penjual Resmi
                                </span>
                            @endif
                        </div>

                        <div class="text-left sm:text-right">
                            <span class="text-xs text-slate-500 block font-medium">Harga Satuan:</span>
                            <span class="text-2xl sm:text-3xl font-black text-indigo-700">
                                Rp {{ number_format($selectedVariant->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- CTA Beli Button (Disabled sesuai spesifikasi) -->
                    <div class="pt-2">
                        <x-ui.button
                            type="button"
                            variant="primary"
                            size="lg"
                            class="w-full text-center"
                            disabled
                            title="Pembelian segera tersedia"
                        >
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            Beli Sekarang
                        </x-ui.button>
                        <p class="text-[11px] text-center text-slate-500 mt-2 font-medium">
                            Pembelian segera tersedia &bull; Transaksi dilindungi sistem escrow otomatis PreminAja.
                        </p>
                    </div>
                @endif
            </x-ui.card>

            <!-- Description & Reviews Tabs -->
            <x-ui.card padding="p-6" class="space-y-5 border-slate-200 shadow-sm">
                <!-- Tab Headers -->
                <div class="flex items-center gap-6 border-b border-slate-200 pb-3">
                    <button
                        type="button"
                        x-on:click="activeTab = 'description'"
                        class="text-sm font-bold pb-2 -mb-3 transition-colors border-b-2"
                        :class="activeTab === 'description' ? 'text-indigo-600 border-indigo-600' : 'text-slate-500 border-transparent hover:text-slate-900'"
                    >
                        Deskripsi Produk
                    </button>
                    <button
                        type="button"
                        x-on:click="activeTab = 'reviews'"
                        class="text-sm font-bold pb-2 -mb-3 transition-colors border-b-2 flex items-center gap-1.5"
                        :class="activeTab === 'reviews' ? 'text-indigo-600 border-indigo-600' : 'text-slate-500 border-transparent hover:text-slate-900'"
                    >
                        <span>Ulasan Pelanggan</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold">
                            {{ $product->reviews_count }}
                        </span>
                    </button>
                </div>

                <!-- Tab: Description (Escaped and formatted cleanly) -->
                <div x-show="activeTab === 'description'" class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>

                <!-- Tab: Reviews -->
                <div x-show="activeTab === 'reviews'" style="display: none;" class="space-y-4">
                    @forelse($product->reviews as $review)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                        {{ substr($review->user->name ?? 'P', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-800">{{ $review->user->name ?? 'Pembeli' }}</span>
                                </div>
                                <div class="flex items-center text-amber-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                            <span class="text-[10px] text-slate-400 block">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400">
                            Belum ada ulasan untuk produk ini.
                        </div>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
