<div class="inline-flex items-center">
    <button wire:click="toggleArchive" type="button" class="text-xs px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700">
        {{ $product->status->value === 'archived' ? 'Aktifkan Kembali' : 'Arsipkan' }}
    </button>
</div>
