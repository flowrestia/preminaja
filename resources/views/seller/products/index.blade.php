<x-seller-layout title="Produk Saya" header="Daftar Produk">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-500">Kelola semua listing produk digital dan varian harga toko Anda.</p>
            <a href="{{ route('seller.products.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                + Tambah Produk Baru
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Produk</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Varian</th>
                            <th class="px-6 py-3">Tipe Kirim</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $product->name }}</div>
                                    <div class="text-xs text-slate-400">Slug: {{ $product->slug }}</div>
                                    @if($product->status->value === 'rejected' && $product->rejection_reason)
                                        <div class="text-xs text-rose-600 mt-1 font-medium">Alasan ditolak: {{ $product->rejection_reason }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-600">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs font-mono font-semibold">{{ $product->variants_count }} varian</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold {{ $product->delivery_type->value === 'automated' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $product->delivery_type->value === 'automated' ? 'Otomatis' : 'Manual' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeStyles = [
                                            'published' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'pending_review' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'draft' => 'bg-slate-100 text-slate-600 border border-slate-200',
                                            'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                            'archived' => 'bg-zinc-100 text-zinc-600 border border-zinc-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeStyles[$product->status->value] ?? '' }}">
                                        {{ ucfirst($product->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @if($product->delivery_type->value === 'automated' && $product->variants->first())
                                        <a href="{{ route('seller.products.credentials.index', [$product, $product->variants->first()]) }}" class="text-xs px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold border border-indigo-200 transition">
                                            Kelola Stok
                                        </a>
                                    @endif

                                    <a href="{{ route('seller.products.edit', $product) }}" class="text-xs px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                                        Edit
                                    </a>

                                    @if(in_array($product->status->value, ['draft', 'rejected']))
                                        <form action="{{ route('seller.products.submit-review', $product) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold transition">
                                                Ajukan Review
                                            </button>
                                        </form>
                                    @endif

                                    @if($product->status->value === 'published')
                                        <a href="{{ route('catalog.show', $product->slug) }}" target="_blank" class="text-xs px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold transition">
                                            Lihat Publik &nearr;
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400">
                                    Anda belum memiliki produk. Klik "Tambah Produk Baru" untuk mulai berjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-seller-layout>
