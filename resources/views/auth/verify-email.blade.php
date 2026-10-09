<x-layouts.auth title="Verifikasi Email - PreminAja">
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-white mb-2">Verifikasi Alamat Email</h1>
        <p class="text-sm text-slate-400 mb-6">
            Terima kasih telah bergabung di PreminAja! Silakan klik tautan verifikasi yang baru saja kami kirimkan ke alamat email Anda sebelum melanjutkan.
        </p>

        @if (session('message'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-indigo-600/30 transition-all">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-slate-400 hover:text-white transition-colors py-2">
                    Keluar dari akun
                </button>
            </form>
        </div>
    </div>
</x-layouts.auth>
