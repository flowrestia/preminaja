<x-layouts.auth title="Masuk ke Akun - PreminAja">
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-white">Selamat Datang Kembali</h1>
            <p class="text-sm text-slate-400 mt-1">Masuk untuk mengakses akun, pesanan, atau toko Anda</p>
        </div>

        @if (session('status'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-5 p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm">
                {{ session('warning') }}
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

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <div class="relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                        placeholder="contoh@domain.com">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Kata Sandi</label>
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                        Lupa kata sandi?
                    </a>
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900">
                    <span class="text-xs text-slate-400 select-none">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" id="btn-login"
                class="w-full mt-2 bg-indigo-600 hover:bg-indigo-500 active:scale-[0.99] text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800 text-center">
            <p class="text-xs text-slate-400">
                Belum memiliki akun PreminAja?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 ml-1">
                    Daftar akun baru
                </a>
            </p>
        </div>
    </div>
</x-layouts.auth>
