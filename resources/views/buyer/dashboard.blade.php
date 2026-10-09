<x-layouts.auth title="Dashboard Pembeli - PreminAja">
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
            <div>
                <h1 class="text-xl font-bold text-white">Dashboard Pembeli</h1>
                <p class="text-xs text-slate-400">Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-medium">Keluar</button>
            </form>
        </div>

        <div class="p-4 bg-slate-950/80 rounded-xl border border-slate-800 text-xs text-slate-300 space-y-2">
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Peran:</strong> {{ auth()->user()->role->label() }}</p>
            <p><strong>Status Akun:</strong> {{ auth()->user()->status->label() }}</p>
        </div>
    </div>
</x-layouts.auth>
