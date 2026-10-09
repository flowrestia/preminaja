<div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-white">Buat Akun PreminAja</h1>
        <p class="text-sm text-slate-400 mt-1">Pilih jenis akun yang sesuai dengan kebutuhan Anda</p>
    </div>

    <!-- Role Selection Tabs -->
    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-950/80 border border-slate-800 rounded-xl mb-6">
        <button type="button" wire:click="selectRole('buyer')"
            class="py-2 px-3 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-2 {{ $role === 'buyer' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span>Akun Pembeli</span>
        </button>

        <button type="button" wire:click="selectRole('seller')"
            class="py-2 px-3 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-2 {{ $role === 'seller' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Buka Toko (Seller)</span>
        </button>
    </div>

    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="register" class="space-y-4">
        <!-- Common User Fields -->
        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
            <input type="text" wire:model.defer="name" required
                class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                placeholder="cth. Sonny Pratama">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
            <input type="email" wire:model.defer="email" required
                class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                placeholder="nama@email.com">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nomor WhatsApp / HP</label>
            <input type="tel" wire:model.defer="phone"
                class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                placeholder="08123456789">
        </div>

        <!-- Seller Specific Fields -->
        @if ($role === 'seller')
            <div class="pt-2 border-t border-slate-800/80 space-y-4">
                <div class="p-3 bg-indigo-950/30 border border-indigo-800/40 rounded-xl">
                    <p class="text-xs text-indigo-300 flex items-start gap-1.5">
                        <svg class="w-4 h-4 text-indigo-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Akun toko Anda akan berstatus <strong>Menunggu Verifikasi (Pending)</strong> setelah daftar, dan siap berjualan setelah diverifikasi tim PreminAja.</span>
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Toko Digital</label>
                    <input type="text" wire:model.defer="store_name" required
                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                        placeholder="cth. Alpha Digital Official">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Bio / Deskripsi Singkat Toko</label>
                    <textarea wire:model.defer="bio" rows="2"
                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                        placeholder="Jual langganan premium garansi resmi dan proses kilat..."></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Bank Penarikan</label>
                        <select wire:model.defer="bank_name"
                            class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-3 py-2.5 text-sm text-slate-100 outline-none">
                            <option value="BCA">BCA</option>
                            <option value="Bank Mandiri">Mandiri</option>
                            <option value="BRI">BRI</option>
                            <option value="BNI">BNI</option>
                            <option value="SeaBank">SeaBank</option>
                            <option value="Jago">Bank Jago</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                        <input type="text" wire:model.defer="bank_account_number"
                            class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none"
                            placeholder="Nomor rekening Anda">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Pemilik Rekening</label>
                    <input type="text" wire:model.defer="bank_account_holder"
                        class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none"
                        placeholder="Sesuai buku tabungan">
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <input type="password" wire:model.defer="password" required autocomplete="new-password"
                    class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                    placeholder="Min. 8 karakter">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Ulangi Sandi</label>
                <input type="password" wire:model.defer="password_confirmation" required autocomplete="new-password"
                    class="w-full bg-slate-950/80 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 transition-all outline-none"
                    placeholder="Konfirmasi sandi">
            </div>
        </div>

        <button type="submit" id="btn-register"
            class="w-full mt-3 bg-indigo-600 hover:bg-indigo-500 active:scale-[0.99] text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
            <span wire:loading.remove>Daftar Sebagai {{ $role === 'seller' ? 'Penjual' : 'Pembeli' }}</span>
            <span wire:loading>Memproses Pendaftaran...</span>
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-800 text-center">
        <p class="text-xs text-slate-400">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 ml-1">
                Masuk di sini
            </a>
        </p>
    </div>
</div>
