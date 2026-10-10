<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Wishlist Saya</h1>
            <p class="text-xs text-slate-400 mt-1">Daftar produk dan akun digital incaran Anda yang siap dibeli kapan saja.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">
            &larr; Lanjut Belanja
        </a>
    </div>

    @if($wishlists->isEmpty())
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-800/80 flex items-center justify-center text-slate-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-white">Wishlist Anda Masih Kosong</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Simpan akun dan produk impian dengan menekan ikon hati pada katalog produk.</p>
            <div class="mt-6">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition">
                    Jelajahi Katalog
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($wishlists as $item)
                @php
                    $prod = $item->product;
                    $cheapest = $prod->variants->sortBy('price')->first();
                @endphp
                <div class="group relative bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden hover:border-slate-700 transition flex flex-col">
                    <div class="relative aspect-video bg-slate-800 overflow-hidden">
                        @if($prod->thumbnail_url)
                            <img src="{{ $prod->thumbnail_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs">
                                Tanpa Gambar
                            </div>
                        @endif

                        <button wire:click="removeFromWishlist({{ $prod->id }})" title="Hapus dari Wishlist" class="absolute top-2.5 right-2.5 p-2 rounded-xl bg-slate-900/80 backdrop-blur text-pink-500 hover:bg-slate-800 transition">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-medium text-indigo-400 bg-indigo-950/60 px-2 py-0.5 rounded-full border border-indigo-800/40">
                                {{ $prod->category?->name }}
                            </span>

                            <h3 class="mt-2 text-sm font-semibold text-white line-clamp-2">
                                <a href="{{ route('catalog.show', $prod->slug) }}" class="hover:text-indigo-400 transition">
                                    {{ $prod->name }}
                                </a>
                            </h3>

                            <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
                                <span>{{ $prod->seller?->store_name }}</span>
                                <div class="flex items-center gap-1 text-amber-400">
                                    <span>&#9733;</span>
                                    <span>{{ number_format($prod->seller?->rating_avg ?? 5.0, 1) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400">Mulai dari</span>
                                <div class="text-sm font-bold text-emerald-400">
                                    Rp {{ number_format($cheapest?->price ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <a href="{{ route('catalog.show', $prod->slug) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg transition">
                                Beli
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $wishlists->links() }}
        </div>
    @endif
</div>
