<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'POS Putri — Offline-First POS') }}</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-900 selection:bg-emerald-600 selection:text-white flex flex-col">
    <!-- Topbar Navigation: Clean Square / Shopify POS Light Mode -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Brand & Navigation Menu -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-extrabold text-sm shadow-xs group-hover:scale-105 transition">
                            P
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm tracking-tight text-slate-900 leading-none">POS PUTRI</span>
                            <span class="text-[10px] font-mono text-slate-500 leading-tight mt-0.5">OFFLINE-FIRST</span>
                        </div>
                    </a>

                    <nav class="hidden md:flex items-center gap-1 text-xs font-semibold">
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('pos.index') }}" class="px-3 py-1.5 rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 font-bold flex items-center gap-1.5 transition active:scale-98">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            Terminal POS
                        </a>
                        @hasanyrole('Admin|Supervisor')
                        <a href="{{ route('products.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('products.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Produk
                        </a>
                        <a href="{{ route('categories.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('categories.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Kategori
                        </a>
                        <a href="{{ route('inventory.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('inventory.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Inventaris
                        </a>
                        @endhasanyrole
                        <a href="{{ route('transactions.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('transactions.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Transaksi
                        </a>
                        <a href="{{ route('shifts.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('shifts.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Shift
                        </a>
                        <a href="{{ route('customers.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('customers.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Pelanggan
                        </a>
                        <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Laporan
                        </a>
                        <a href="{{ route('sync.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('sync.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Sync Center
                        </a>
                        @role('Admin')
                        <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('users.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Pengguna
                        </a>
                        <a href="{{ route('settings.index') }}" class="px-3 py-1.5 rounded-lg transition {{ request()->routeIs('settings.*') ? 'bg-slate-100 text-slate-900 border border-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            Outlet & Alat
                        </a>
                        @endrole
                    </nav>
                </div>

                <!-- Right: Status Pill, Outlet Info, User & Logout -->
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
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-medium flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-800 text-xs font-medium flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {!! $slot ?? '' !!}
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
