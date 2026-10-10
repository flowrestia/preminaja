<x-seller-layout title="Dashboard Toko" header="Dashboard Penjualan">
    <div class="space-y-8">
        <!-- Financial & Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Saldo Tersedia</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($activeBalance, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Dapat ditarik ke rekening</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Saldo Tertahan (Escrow)</p>
                <h3 class="text-2xl font-black text-amber-600 mt-2">Rp {{ number_format($pendingBalance, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Menunggu masa garansi pesanan</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Penjualan</p>
                <h3 class="text-2xl font-black text-indigo-600 mt-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $totalOrdersCount }} transaksi selesai</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Produk</p>
                <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $productCounts['total'] }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $productCounts['published'] }} aktif di katalog</p>
            </div>
        </div>

        <!-- Product Status Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
                <span class="text-xs text-emerald-700 font-bold uppercase tracking-wider">Published</span>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $productCounts['published'] }}</p>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
                <span class="text-xs text-amber-700 font-bold uppercase tracking-wider">Pending Review</span>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $productCounts['pending_review'] }}</p>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Draft</span>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $productCounts['draft'] }}</p>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
                <span class="text-xs text-rose-700 font-bold uppercase tracking-wider">Ditolak</span>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $productCounts['rejected'] }}</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Pesanan Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Order ID</th>
                            <th class="px-6 py-3">Produk & Varian</th>
                            <th class="px-6 py-3">Pembeli</th>
                            <th class="px-6 py-3">Penghasilan Seller</th>
                            <th class="px-6 py-3">Status Pengiriman</th>
                            <th class="px-6 py-3">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600">#{{ $item->order_id }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $item->product_name }}</div>
                                    <div class="text-xs text-slate-400">{{ $item->variant_name }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs">{{ $item->order->buyer->name ?? 'Pembeli' }}</td>
                                <td class="px-6 py-4 font-bold text-emerald-600">Rp {{ number_format($item->subtotal - $item->platform_fee, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStyles = [
                                            'delivered' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'processing' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                            'failed' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusStyles[$item->delivery_status->value] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($item->delivery_status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400">{{ $item->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400">
                                    Belum ada transaksi pesanan yang masuk ke toko Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-seller-layout>
