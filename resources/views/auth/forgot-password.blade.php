<x-layouts.auth title="Lupa Kata Sandi - PreminAja">
    <x-ui.card padding="p-6 sm:p-8" class="border-slate-200 shadow-lg">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Lupa Kata Sandi?</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Masukkan alamat email Anda untuk menerima tautan pemulihan kata sandi</p>
        </div>

        @if (session('status'))
            <x-ui.alert variant="success" class="mb-5">
                {{ session('status') }}
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

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Terdaftar</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none shadow-sm"
                    placeholder="nama@email.com">
            </div>

            <x-ui.button type="submit" id="btn-forgot-password" variant="primary" size="md" class="w-full mt-2">
                <span>Kirim Tautan Pemulihan</span>
            </x-ui.button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                &larr; Kembali ke halaman Masuk
            </a>
        </div>
    </x-ui.card>
</x-layouts.auth>
