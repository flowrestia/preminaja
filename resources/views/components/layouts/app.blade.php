<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'PreminAja - Marketplace Akun Premium & Produk Digital Terpercaya' }}</title>
    <meta name="description" content="{{ $description ?? 'Beli akun streaming, voucher game, dan tools produktivitas otomatis instan dan bergaransi penuh di PreminAja.' }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="text-xl font-bold bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                    PreminAja
                </a>
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('catalog.index') }}" class="text-slate-300 hover:text-white transition">Katalog</a>
                    <a href="{{ route('wishlist.index') }}" class="text-slate-300 hover:text-white transition">Wishlist</a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    @if(auth()->user()->hasRole('seller'))
                        <a href="{{ route('seller.dashboard') }}" class="text-xs px-3 py-1.5 rounded-lg bg-indigo-950/60 border border-indigo-700/50 text-indigo-300 hover:bg-indigo-900 transition">
                            Seller Center
                        </a>
                    @elseif(auth()->user()->hasRole('superadmin'))
                        <a href="{{ url('/admin') }}" class="text-xs px-3 py-1.5 rounded-lg bg-red-950/60 border border-red-700/50 text-red-300 hover:bg-red-900 transition">
                            Admin Panel
                        </a>
                    @endif

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400 hidden sm:inline">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800 transition">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-300 hover:text-white transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-xs font-semibold px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition shadow-lg shadow-indigo-600/20">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900/60 border-t border-slate-800 py-8 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} PreminAja Marketplace. Transaksi aman dengan sistem garansi escrow otomatis.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
