<x-seller-layout title="Dashboard Toko" header="Dashboard Penjualan">
    <div class="space-y-8">
        <!-- Financial & Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-xl bg-slate-900 border border-slate-800">
                <p class="text-sm font-medium text-slate-400">Saldo Tersedia</p>
                <h3 class="text-2xl font-bold text-emerald-400 mt-2">Rp {{ number_format($activeBalance, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">Dapat ditarik ke rekening</p>
            </div>

            <div class="p-6 rounded-xl bg-slate-900 border border-slate-800">
                <p class="text-sm font-medium text-slate-400">Saldo Tertahan (Escrow)</p>
                <h3 class="text-2xl font-bold text-amber-400 mt-2">Rp {{ number_format($pendingBalance, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">Menunggu masa garansi pesanan</p>
            </div>

            <div class="p-6 rounded-xl bg-slate-900 border border-slate-800">
                <p class="text-sm font-medium text-slate-400">Total Penjualan</p>
                <h3 class="text-2xl font-bold text-indigo-400 mt-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $totalOrdersCount }} transaksi selesai</p>
            </div>

            <div class="p-6 rounded-xl bg-slate-900 border border-slate-800">
                <p class="text-sm font-medium text-slate-400">Total Produk</p>
                <h3 class="text-2xl font-bold text-white mt-2">{{ $productCounts['total'] }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $productCounts['published'] }} aktif di katalog</p>
            </div>
        </div>

        <!-- Product Status Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-lg bg-slate-900/60 border border-slate-800/80">
                <span class="text-xs text-emerald-400 font-semibold uppercase">Published</span>
                <p class="text-xl font-bold text-white mt-1">{{ $productCounts['published'] }}</p>
            </div>
            <div class="p-4 rounded-lg bg-slate-900/60 border border-slate-800/80">
                <span class="text-xs text-amber-400 font-semibold uppercase">Pending Review</span>
                <p class="text-xl font-bold text-white mt-1">{{ $productCounts['pending_review'] }}</p>
            </div>
            <div class="p-4 rounded-lg bg-slate-900/60 border border-slate-800/80">
                <span class="text-xs text-slate-400 font-semibold uppercase">Draft</span>
                <p class="text-xl font-bold text-white mt-1">{{ $productCounts['draft'] }}</p>
            </div>
            <div class="p-4 rounded-lg bg-slate-900/60 border border-slate-800/80">
                <span class="text-xs text-red-400 font-semibold uppercase">Ditolak</span>
                <p class="text-xl font-bold text-white mt-1">{{ $productCounts['rejected'] }}</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-base font-semibold text-white">Pesanan Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-xs uppercase text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3">Order ID</th>
                            <th class="px-6 py-3">Produk & Varian</th>
                            <th class="px-6 py-3">Pembeli</th>
                            <th class="px-6 py-3">Penghasilan Seller</th>
                            <th class="px-6 py-3">Status Pengiriman</th>
                            <th class="px-6 py-3">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($recentOrders as $item)
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-6 py-4 font-mono text-xs text-indigo-400">#{{ $item->order_id }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $item->variant->product->name ?? 'Produk' }}</div>
                                    <div class="text-xs text-slate-400">{{ $item->variant->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs">{{ $item->order->buyer->name ?? 'Buyer' }}</td>
                                <td class="px-6 py-4 font-semibold text-emerald-400">Rp {{ number_format($item->seller_earning, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $item->delivery_status->value === 'delivered' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                        {{ $item->delivery_status->value }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400">{{ $item->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada transaksi masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-seller-layout>
