<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-950 text-zinc-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'POS Putri — Offline-First POS') }}</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#09090b">

    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full font-sans antialiased bg-zinc-950 text-zinc-100 selection:bg-emerald-500 selection:text-white flex flex-col">
    <!-- Topbar Navigation: Crisp 1px Hairline Border, Modern POS Standard -->
    <header class="bg-zinc-950/90 backdrop-blur-md border-b border-zinc-800/80 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Brand & Navigation Menu -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-extrabold text-sm shadow-sm shadow-emerald-600/20 group-hover:scale-105 transition">
                            P
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm tracking-tight text-white leading-none">POS PUTRI</span>
                            <span class="text-[10px] font-mono text-zinc-500 leading-tight mt-0.5">OFFLINE-FIRST</span>
                        </div>
                    </a>

                    <nav class="hidden md:flex items-center gap-1 text-xs font-semibold">
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('pos.index') }}" class="px-3 py-1.5 rounded-lg text-emerald-400 bg-emerald-950/40 border border-emerald-800/60 hover:bg-emerald-900/50 font-bold flex items-center gap-1.5 transition active:scale-98">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Terminal POS
                        </a>
                        @hasanyrole('Admin|Supervisor')
                        <a href="{{ route('products.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('products.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Produk
                        </a>
                        <a href="{{ route('categories.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('categories.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Kategori
                        </a>
                        <a href="{{ route('inventory.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('inventory.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Inventaris
                        </a>
                        @endhasanyrole
                        <a href="{{ route('transactions.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('transactions.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Transaksi
                        </a>
                        <a href="{{ route('shifts.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('shifts.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Shift
                        </a>
                        <a href="{{ route('customers.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('customers.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Pelanggan
                        </a>
                        <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Laporan
                        </a>
                        <a href="{{ route('sync.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('sync.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Sync Center
                        </a>
                        @role('Admin')
                        <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('users.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Pengguna
                        </a>
                        <a href="{{ route('settings.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('settings.*') ? 'bg-zinc-800 text-white border border-zinc-700/60' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white' }}">
                            Outlet & Alat
                        </a>
                        @endrole
                    </nav>
                </div>

                <!-- Right: Status Pill, Outlet Info, User & Logout -->
                <div class="flex items-center gap-3">
                    <!-- Connectivity Pill with Dynamic Sync Status -->
                    <div id="connectivity-indicator" class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-mono font-medium bg-emerald-950/60 text-emerald-400 border border-emerald-800/60">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>ONLINE</span>
                    </div>

                    <!-- Outlet Badge -->
                    @if(auth()->user() && auth()->user()->outlet)
                    <div class="hidden lg:flex items-center gap-1.5 text-xs text-zinc-300 bg-zinc-900 px-2.5 py-1 rounded-lg border border-zinc-800 font-mono">
                        <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ auth()->user()->outlet->name }}</span>
                    </div>
                    @endif

                    <!-- User Info & Logout -->
                    @if(auth()->check())
                    <div class="flex items-center gap-2 pl-2 border-l border-zinc-800">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-semibold text-zinc-200">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] uppercase font-mono font-bold tracking-wider text-emerald-400">
                                {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar dari Akun" class="p-2 text-zinc-400 hover:text-rose-400 hover:bg-zinc-900 rounded-lg transition active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 text-xs font-medium flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3.5 rounded-xl bg-rose-950/60 border border-rose-800/80 text-rose-300 text-xs font-medium flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {!! $slot ?? '' !!}
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
