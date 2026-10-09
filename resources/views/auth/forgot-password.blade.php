<x-layouts.auth title="Lupa Kata Sandi - PreminAja">
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-white">Lupa Kata Sandi?</h1>
            <p class="text-sm text-slate-400 mt-1">Masukkan alamat email Anda untuk menerima tautan pemulihan kata sandi</p>
        </div>

        @if (session('status'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email Terdaftar</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none"
                    placeholder="nama@email.com">
            </div>

            <button type="submit" id="btn-forgot-password"
                class="w-full mt-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                <span>Kirim Tautan Pemulihan</span>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800 text-center">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">
                &larr; Kembali ke halaman Masuk
            </a>
        </div>
    </div>
</x-layouts.auth>
