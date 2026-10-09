<x-layouts.auth title="Toko Menunggu Verifikasi - PreminAja">
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-white mb-2">Toko Anda Sedang Diverifikasi</h1>
        <p class="text-sm text-slate-300 mb-6">
            Pendaftaran toko <strong>{{ auth()->user()->sellerProfile?->store_name }}</strong> telah berhasil kami terima.
            Tim kepatuhan PreminAja sedang memverifikasi data dan rekening bank Anda demi menjaga keamanan ekosistem transaksi.
        </p>

        <div class="bg-slate-950/80 border border-slate-800 rounded-xl p-4 text-left text-xs space-y-2 mb-6">
            <div class="flex justify-between items-center text-slate-400">
                <span>Status Akun:</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-semibold uppercase text-[10px]">Aktif</span>
            </div>
            <div class="flex justify-between items-center text-slate-400">
                <span>Status Toko:</span>
                <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 font-semibold uppercase text-[10px]">Menunggu Verifikasi</span>
            </div>
            <div class="flex justify-between items-center text-slate-400">
                <span>Estimasi Waktu:</span>
                <span class="text-slate-200 font-medium">Maks. 1x24 Jam Kerja</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold transition-all">
                Jelajahi Produk
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-800 hover:bg-slate-800 text-slate-400 hover:text-white text-sm font-semibold transition-all">
                    Keluar Akun
                </button>
            </form>
        </div>
    </div>
</x-layouts.auth>
