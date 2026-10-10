<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PreminAja - Marketplace Akun Premium & Produk Digital' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen antialiased flex flex-col justify-between selection:bg-indigo-500 selection:text-white">

    <header class="border-b border-slate-200 bg-white sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md shadow-indigo-600/20 group-hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-black tracking-tight text-slate-900">Premin<span class="text-indigo-600">Aja</span></span>
                    <span class="text-[10px] text-slate-500 -mt-1 tracking-wider uppercase font-semibold">Digital Marketplace</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors px-3 py-1.5">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl shadow-sm transition-all">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md sm:max-w-lg my-6">
            {{ $slot }}
        </div>
    </main>

    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} PreminAja Marketplace. Seluruh hak cipta dilindungi undang-undang.</p>
    </footer>

    @livewireScripts
</body>
</html>
