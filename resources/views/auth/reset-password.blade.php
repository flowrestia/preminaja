<x-layouts.auth title="Perbarui Kata Sandi - PreminAja">
    <x-ui.card padding="p-6 sm:p-8" class="border-slate-200 shadow-lg">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat Kata Sandi Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Masukkan kata sandi baru untuk akun Anda</p>
        </div>

        @if ($errors->any())
            <x-ui.alert variant="danger" class="mb-5">
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                    class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none shadow-sm">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none shadow-sm"
                    placeholder="Min. 8 karakter">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Ulangi Kata Sandi Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none shadow-sm"
                    placeholder="Konfirmasi sandi">
            </div>

            <x-ui.button type="submit" id="btn-reset-password" variant="primary" size="md" class="w-full mt-2">
                <span>Perbarui Kata Sandi</span>
            </x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.auth>
