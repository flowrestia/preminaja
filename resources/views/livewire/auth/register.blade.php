<x-ui.card padding="p-6 sm:p-8" class="border-slate-200 shadow-lg">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat Akun PreminAja</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Pilih jenis akun yang sesuai dengan kebutuhan Anda</p>
    </div>

    <!-- Role Selection Tabs -->
    <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 border border-slate-200 rounded-2xl mb-6">
        <button type="button" wire:click="selectRole('buyer')"
            class="py-2.5 px-3 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-2 {{ $role === 'buyer' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span>Akun Pembeli</span>
        </button>

        <button type="button" wire:click="selectRole('seller')"
            class="py-2.5 px-3 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-2 {{ $role === 'seller' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Buka Toko (Seller)</span>
        </button>
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

    <form wire:submit.prevent="register" class="space-y-4">
        <!-- Common User Fields -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
            <input type="text" wire:model.defer="name" required
                class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                placeholder="cth. Sonny Pratama">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
            <input type="email" wire:model.defer="email" required
                class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                placeholder="nama@email.com">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp / HP</label>
            <input type="tel" wire:model.defer="phone"
                class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                placeholder="08123456789">
        </div>

        <!-- Seller Specific Fields -->
        @if ($role === 'seller')
            <div class="pt-3 border-t border-slate-200 space-y-4">
                <x-ui.alert variant="info">
                    Akun toko Anda akan berstatus <strong>Menunggu Verifikasi (Pending)</strong> setelah daftar, dan siap berjualan setelah diverifikasi tim PreminAja.
                </x-ui.alert>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Toko Digital</label>
                    <input type="text" wire:model.defer="store_name" required
                        class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                        placeholder="cth. Alpha Digital Official">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Bank</label>
                        <input type="text" wire:model.defer="bank_name" required
                            class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                            placeholder="cth. BCA / Mandiri / BRI">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                        <input type="text" wire:model.defer="bank_account_number" required
                            class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                            placeholder="cth. 1234567890">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pemilik Rekening</label>
                    <input type="text" wire:model.defer="bank_account_holder" required
                        class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                        placeholder="Harus sesuai nama rekening buku tabungan">
                </div>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
            <input type="password" wire:model.defer="password" required autocomplete="new-password"
                class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                placeholder="Minimal 8 karakter">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi</label>
            <input type="password" wire:model.defer="password_confirmation" required autocomplete="new-password"
                class="w-full bg-white border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-all outline-none shadow-sm"
                placeholder="Ulangi kata sandi">
        </div>

        <x-ui.button type="submit" id="btn-register" variant="primary" size="md" class="w-full mt-2">
            <span>Daftar Akun Sekarang</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </x-ui.button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 ml-1">
                Masuk di sini
            </a>
        </p>
    </div>
</x-ui.card>
