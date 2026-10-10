<div>
    <button wire:click="toggle" type="button" class="p-2 rounded-full backdrop-blur-md transition {{ $isWishlisted ? 'bg-red-500/20 text-red-400 hover:bg-red-500/30' : 'bg-slate-900/60 text-slate-400 hover:text-white hover:bg-slate-800' }}" title="{{ $isWishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}">
        <svg class="w-5 h-5 {{ $isWishlisted ? 'fill-current text-red-500' : 'fill-none stroke-current' }}" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
    </button>
</div>
