<x-seller-layout title="Produk Saya" header="Daftar Produk">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-400">Kelola semua listing produk digital dan varian harga Anda.</p>
            <a href="{{ route('seller.products.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-semibold transition">
                + Tambah Produk Baru
            </a>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-xs uppercase text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3">Produk</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Varian</th>
                            <th class="px-6 py-3">Tipe Kirim</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $product->name }}</div>
                                    <div class="text-xs text-slate-500">Slug: {{ $product->slug }}</div>
                                    @if($product->status->value === 'rejected' && $product->rejection_reason)
                                        <div class="text-xs text-red-400 mt-1">Alasan ditolak: {{ $product->rejection_reason }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs font-mono">{{ $product->variants_count }} varian</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs {{ $product->delivery_type->value === 'automated' ? 'bg-purple-500/10 text-purple-400' : 'bg-blue-500/10 text-blue-400' }}">
                                        {{ $product->delivery_type->value === 'automated' ? 'Otomatis' : 'Manual' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeStyles = [
                                            'published' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                            'pending_review' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                            'draft' => 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
                                            'rejected' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                                            'archived' => 'bg-zinc-500/10 text-zinc-400 border border-zinc-500/20',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeStyles[$product->status->value] ?? '' }}">
                                        {{ ucfirst($product->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @livewire('seller.product-quick-status', ['product' => $product], key('status-'.$product->id))

                                    <a href="{{ route('seller.products.edit', $product) }}" class="text-xs px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-indigo-400 border border-slate-700">
                                        Edit
                                    </a>

                                    <form action="{{ route('seller.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs px-2.5 py-1 rounded bg-slate-800 hover:bg-red-900/40 text-red-400 border border-slate-700">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    Belum ada produk. Klik "+ Tambah Produk Baru" untuk mulai berjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-seller-layout>
