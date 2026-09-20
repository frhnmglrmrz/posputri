<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Berkah Mart — Offline-First POS') }}</title>

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-900 selection:bg-emerald-600 selection:text-white flex flex-col">
    <!-- Topbar Navigation: Clean Square / Shopify POS Light Mode with Structured Sub-Navs -->
    <header x-data="{ mobileMenuOpen: false }" class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Brand & Grouped Navigation Menu -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <x-application-logo class="w-8 h-8 rounded-xl shadow-xs group-hover:scale-105 transition duration-150" />
                        <div class="flex flex-col">
                            <span class="font-bold text-sm tracking-tight text-slate-900 leading-none group-hover:text-emerald-700 transition">BERKAH MART</span>
                            <span class="text-[10px] font-mono font-semibold text-slate-400 leading-tight mt-0.5 tracking-wider">OFFLINE-FIRST</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation with Clean Sub-Nav Dropdowns -->
                    <nav class="hidden md:flex items-center gap-1.5 text-xs font-semibold">
                        <!-- 1. Dashboard -->
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Dashboard
                        </a>

                        <!-- 2. Terminal POS (Cashier, Supervisor, Admin) -->
                        @hasanyrole('Admin|Supervisor|Cashier')
                        <a href="{{ route('pos.index') }}" class="px-3 py-1.5 rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 font-bold flex items-center gap-1.5 transition active:scale-98">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            Terminal POS
                        </a>
                        @endhasanyrole

                        <!-- 3. Sub-Nav: Katalog & Stok (Inventory, Supervisor, Admin) -->
                        @hasanyrole('Admin|Supervisor|Inventory')
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                            <button type="button" @click="open = !open"
                                class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ (request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('inventory.*')) ? 'bg-slate-100 text-slate-900 border border-slate-300 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span>Katalog & Stok</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 mt-2 w-52 rounded-xl bg-white border border-slate-200 shadow-xl p-1.5 z-50 text-left">
                                <a href="{{ route('products.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('products.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Daftar Produk</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Katalog & harga jual</span>
                                </a>
                                <a href="{{ route('categories.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('categories.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Kategori Produk</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Pengelompokan menu</span>
                                </a>
                                <a href="{{ route('inventory.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('inventory.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Inventaris & Stok</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Audit stok & restock</span>
                                </a>
                            </div>
                        </div>
                        @endhasanyrole

                        <!-- 4. Sub-Nav: Operasional (Cashier, Supervisor, Admin) -->
                        @hasanyrole('Admin|Supervisor|Cashier')
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                            <button type="button" @click="open = !open"
                                class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ (request()->routeIs('transactions.*') || request()->routeIs('shifts.*') || request()->routeIs('customers.*')) ? 'bg-slate-100 text-slate-900 border border-slate-300 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span>Operasional</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 mt-2 w-52 rounded-xl bg-white border border-slate-200 shadow-xl p-1.5 z-50 text-left">
                                <a href="{{ route('transactions.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('transactions.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Riwayat Transaksi</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Daftar penjualan & struk</span>
                                </a>
                                <a href="{{ route('shifts.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('shifts.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Shift Kasir</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Buka / tutup shift & kas</span>
                                </a>
                                <a href="{{ route('customers.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('customers.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Data Pelanggan</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Database kontak pelanggan</span>
                                </a>
                            </div>
                        </div>
                        @endhasanyrole

                        <!-- 5. Sub-Nav: Laporan & Data (Supervisor & Admin) -->
                        @hasanyrole('Admin|Supervisor')
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                            <button type="button" @click="open = !open"
                                class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ (request()->routeIs('reports.*') || request()->routeIs('sync.*')) ? 'bg-slate-100 text-slate-900 border border-slate-300 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span>Laporan & Sync</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 mt-2 w-52 rounded-xl bg-white border border-slate-200 shadow-xl p-1.5 z-50 text-left">
                                <a href="{{ route('reports.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Laporan Penjualan</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Analitik omzet & laba</span>
                                </a>
                                <a href="{{ route('sync.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('sync.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Sync Center</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Status offline & antrean</span>
                                </a>
                            </div>
                        </div>
                        @endhasanyrole

                        <!-- 6. Sub-Nav: Pengaturan (Admin Only) -->
                        @role('Admin')
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                            <button type="button" @click="open = !open"
                                class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ (request()->routeIs('users.*') || request()->routeIs('settings.*')) ? 'bg-slate-100 text-slate-900 border border-slate-300 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span>Pengaturan</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 sm:left-0 mt-2 w-52 rounded-xl bg-white border border-slate-200 shadow-xl p-1.5 z-50 text-left">
                                <a href="{{ route('users.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('users.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Manajemen Pengguna</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Kelola staf, kasir & hak akses</span>
                                </a>
                                <a href="{{ route('settings.index') }}" @click="open = false" class="flex flex-col px-3 py-2 rounded-lg transition {{ request()->routeIs('settings.*') ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="text-xs font-bold">Outlet & Perangkat</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Cabang toko & terminal POS</span>
                                </a>
                            </div>
                        </div>
                        @endrole
                    </nav>
                </div>

                <!-- Right: Status Pill, Outlet Info, User & Logout & Mobile Toggle -->
                <div class="flex items-center gap-3">
                    <!-- Connectivity Pill with Dynamic Sync Status -->
                    <div id="connectivity-indicator" class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-mono font-medium bg-emerald-50 text-emerald-800 border border-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span>ONLINE</span>
                    </div>

                    <!-- Outlet Badge -->
                    @if(auth()->user() && auth()->user()->outlet)
                    <div class="hidden lg:flex items-center gap-1.5 text-xs text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 font-mono">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ auth()->user()->outlet->name }}</span>
                    </div>
                    @endif

                    <!-- User Info & Logout -->
                    @if(auth()->check())
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-bold text-slate-900">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] uppercase font-mono font-bold tracking-wider text-emerald-700">
                                {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar dari Akun" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endif

                    <!-- Mobile Hamburger Toggle Button -->
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition"
                        aria-label="Buka Menu Navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Accordion Drawer -->
        <div x-show="mobileMenuOpen"
            x-cloak
            @click.outside="mobileMenuOpen = false"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-3 shadow-lg">
            
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('dashboard') }}" class="p-2.5 rounded-lg text-center text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'bg-slate-50 text-slate-700 border border-slate-200' }}">
                    Dashboard
                </a>
                @hasanyrole('Admin|Supervisor|Cashier')
                <a href="{{ route('pos.index') }}" class="p-2.5 rounded-lg text-center text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    Terminal POS
                </a>
                @endhasanyrole
            </div>

            @hasanyrole('Admin|Supervisor|Inventory')
            <div class="pt-2 border-t border-slate-100">
                <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 mb-1.5">Katalog & Stok</div>
                <div class="grid grid-cols-3 gap-1.5 text-xs">
                    <a href="{{ route('products.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Produk
                    </a>
                    <a href="{{ route('categories.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Kategori
                    </a>
                    <a href="{{ route('inventory.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Inventaris
                    </a>
                </div>
            </div>
            @endhasanyrole

            @hasanyrole('Admin|Supervisor|Cashier')
            <div class="pt-2 border-t border-slate-100">
                <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 mb-1.5">Operasional Kasir</div>
                <div class="grid grid-cols-3 gap-1.5 text-xs">
                    <a href="{{ route('transactions.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Transaksi
                    </a>
                    <a href="{{ route('shifts.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Shift
                    </a>
                    <a href="{{ route('customers.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Pelanggan
                    </a>
                </div>
            </div>
            @endhasanyrole

            @hasanyrole('Admin|Supervisor')
            <div class="pt-2 border-t border-slate-100">
                <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 mb-1.5">Laporan & Sinkronisasi</div>
                <div class="grid grid-cols-2 gap-1.5 text-xs">
                    <a href="{{ route('reports.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Laporan Penjualan
                    </a>
                    <a href="{{ route('sync.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Sync Center
                    </a>
                </div>
            </div>
            @endhasanyrole

            @role('Admin')
            <div class="pt-2 border-t border-slate-100">
                <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 mb-1.5">Pengaturan Sistem</div>
                <div class="grid grid-cols-2 gap-1.5 text-xs">
                    <a href="{{ route('users.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Kelola Pengguna
                    </a>
                    <a href="{{ route('settings.index') }}" class="p-2 rounded-lg bg-slate-50 text-center font-medium text-slate-700 hover:bg-slate-100 border border-slate-200">
                        Outlet & Alat
                    </a>
                </div>
            </div>
            @endrole
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(!isset($slot) || empty(trim($slot)))
            <x-alert />
        @endif

        {!! $slot ?? '' !!}
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
