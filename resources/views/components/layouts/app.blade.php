<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'PreminAja - Marketplace Akun Premium & Produk Digital Terpercaya' }}</title>
    <meta name="description" content="{{ $description ?? 'Beli akun streaming, voucher game, dan tools produktivitas otomatis instan dan bergaransi penuh di PreminAja.' }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white" x-data="{ mobileMenuOpen: false }">
    <!-- Navbar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <div class="flex items-center gap-8 shrink-0">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md shadow-indigo-600/25 group-hover:bg-indigo-700 transition duration-200">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-black tracking-tight text-slate-900">Premin<span class="text-indigo-600">Aja</span></span>
                        <span class="text-[9px] text-slate-500 -mt-1 tracking-widest uppercase font-semibold">Digital Marketplace</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="{{ url('/') }}" class="transition-colors {{ request()->is('/') ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                        Beranda
                    </a>
                    <a href="{{ route('catalog.index') }}" class="transition-colors {{ request()->routeIs('catalog.*') ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                        Katalog
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="transition-colors flex items-center gap-1.5 {{ request()->routeIs('wishlist.*') ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        Wishlist
                    </a>
                </nav>
            </div>

            <!-- Quick Search Bar (Desktop) -->
            <form action="{{ route('catalog.index') }}" method="GET" class="hidden lg:flex flex-1 max-w-md mx-4">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari akun Netflix, Spotify, Canva..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition shadow-sm">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>

            <!-- User Auth Menu / Actions -->
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->hasRole('seller'))
                        <a href="{{ route('seller.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 transition shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            Seller Center
                        </a>
                    @elseif(auth()->user()->hasRole('superadmin'))
                        <a href="{{ url('/admin') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 transition shadow-sm">
                            Admin Panel
                        </a>
                    @endif

                    <div class="hidden sm:flex items-center gap-3 pl-2 border-l border-slate-200">
                        <span class="text-xs text-slate-700 font-medium max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-medium px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition shadow-sm">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition px-3 py-2">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-xs font-semibold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition shadow-md shadow-indigo-600/20">
                        Daftar
                    </a>
                @endauth

                <!-- Mobile Hamburger Button -->
                <button type="button" x-on:click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 transition" aria-label="Menu Navigasi">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;" class="md:hidden border-b border-slate-200 bg-white px-4 pt-3 pb-6 space-y-4 shadow-lg">
            <!-- Mobile Search -->
            <form action="{{ route('catalog.index') }}" method="GET">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari akun dan voucher..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 shadow-sm">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>

            <nav class="space-y-1 text-sm font-medium">
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                    Beranda
                </a>
                <a href="{{ route('catalog.index') }}" class="block px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                    Katalog Produk
                </a>
                <a href="{{ route('wishlist.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                    <span>Wishlist Saya</span>
                    <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </a>
            </nav>

            @auth
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <p class="text-xs text-slate-500 px-3">Masuk sebagai <strong class="text-slate-900">{{ auth()->user()->name }}</strong></p>
                    @if(auth()->user()->hasRole('seller'))
                        <a href="{{ route('seller.dashboard') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            Masuk ke Seller Center &rarr;
                        </a>
                    @elseif(auth()->user()->hasRole('superadmin'))
                        <a href="{{ url('/admin') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            Masuk ke Admin Panel &rarr;
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-xs text-rose-600 hover:bg-rose-50 rounded-xl transition">
                            Keluar Akun
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 pt-12 pb-8 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
            <div class="space-y-3 md:col-span-1">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold">
                        P
                    </div>
                    <span class="text-base font-bold text-slate-900 tracking-tight">Premin<span class="text-indigo-600">Aja</span></span>
                </div>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Marketplace produk dan akun digital dengan escrow system otomatis, menjamin keamanan transaksi 100%.
                </p>
            </div>

            <div>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Kategori Produk</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition">Akun Streaming Premium</a></li>
                    <li><a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition">Tools Produktivitas & AI</a></li>
                    <li><a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition">Voucher Game & Top-Up</a></li>
                    <li><a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition">Lisensi Perangkat Lunak</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Keamanan & Layanan</h4>
                <ul class="space-y-2">
                    <li class="flex items-center gap-1.5 text-emerald-600 font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Garansi Escrow Otomatis
                    </li>
                    <li class="flex items-center gap-1.5 text-indigo-600 font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                        Pengiriman Akun Instan
                    </li>
                    <li><a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition">Bantuan & Sengketa Pesanan</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Gabung Menjadi Penjual</h4>
                <p class="text-xs text-slate-500 leading-relaxed mb-3">
                    Buka tokomu sekarang dan jual produk digitalmu kepada ribuan pelanggan dengan komisi transparan.
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition shadow-sm">
                    Daftar Sebagai Seller &rarr;
                </a>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-slate-400">
            <p>&copy; {{ date('Y') }} PreminAja Marketplace. Seluruh hak cipta dilindungi undang-undang.</p>
            <div class="flex items-center gap-4 text-[11px]">
                <a href="#" class="hover:text-slate-600 transition">Syarat & Ketentuan</a>
                <span>&bull;</span>
                <a href="#" class="hover:text-slate-600 transition">Kebijakan Privasi</a>
                <span>&bull;</span>
                <a href="#" class="hover:text-slate-600 transition">Panduan Keamanan</a>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
