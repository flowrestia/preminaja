<x-layouts.auth title="Masuk ke Akun - PreminAja">
    <x-ui.card padding="p-6 sm:p-8" class="border-slate-200 shadow-lg">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Selamat Datang Kembali</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Masuk untuk mengakses akun, pesanan, atau toko Anda</p>
        </div>

        @if (session('status'))
            <x-ui.alert variant="success" class="mb-5">
                {{ session('status') }}
            </x-ui.alert>
        @endif

        @if (session('success'))
            <x-ui.alert variant="success" class="mb-5">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        @if (session('warning'))
            <x-ui.alert variant="warning" class="mb-5">
                {{ session('warning') }}
            </x-ui.alert>
        @endif

        @if ($errors->any())
            <x-ui.alert variant="danger" class="mb-5">
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <div class="relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                        placeholder="contoh@domain.com">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                        Lupa kata sandi?
                    </a>
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-white border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs text-slate-600 select-none">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <x-ui.button type="submit" id="btn-login" variant="primary" size="md" class="w-full mt-2">
                <span>Masuk Sekarang</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </x-ui.button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Belum memiliki akun PreminAja?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 ml-1">
                    Daftar akun baru
                </a>
            </p>
        </div>
    </x-ui.card>
</x-layouts.auth>
