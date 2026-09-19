<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Laravel Offline-First POS') }}</title>

    <!-- Tailwind & App JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 selection:bg-indigo-500 selection:text-white flex flex-col">
    <!-- Topbar Navigation -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Brand & Navigation -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-lg text-indigo-400 hover:text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>POS Putri</span>
                    </a>

                    <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('pos.index') }}" class="px-3 py-2 rounded-md text-emerald-400 bg-emerald-950/40 border border-emerald-800/40 hover:bg-emerald-900/60 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            POS Kasir
                        </a>
                        @hasanyrole('Admin|Supervisor')
                        <a href="{{ route('products.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('products.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Produk
                        </a>
                        <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Kategori
                        </a>
                        <a href="{{ route('inventory.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('inventory.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Inventaris
                        </a>
                        @endhasanyrole
                        <a href="{{ route('transactions.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('transactions.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Transaksi
                        </a>
                        <a href="{{ route('shifts.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('shifts.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Shift
                        </a>
                        <a href="{{ route('customers.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('customers.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Pelanggan
                        </a>
                        <a href="{{ route('reports.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Laporan
                        </a>
                        <a href="{{ route('sync.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('sync.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Sync Center
                        </a>
                        @role('Admin')
                        <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('users.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Pengguna
                        </a>
                        <a href="{{ route('settings.index') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Outlet & Perangkat
                        </a>
                        @endrole
                    </nav>
                </div>

                <!-- Right: Status, Outlet, User & Logout -->
                <div class="flex items-center gap-4">
                    <!-- Status Indicator -->
                    <div id="connectivity-indicator" class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-950/60 text-emerald-400 border border-emerald-800/50">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>ONLINE</span>
                    </div>

                    <!-- Outlet Badge -->
                    @if(auth()->user() && auth()->user()->outlet)
                    <div class="hidden lg:flex items-center gap-1.5 text-xs text-slate-400 bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/50">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ auth()->user()->outlet->name }}</span>
                    </div>
                    @endif

                    <!-- User & Role -->
                    @if(auth()->check())
                    <div class="flex items-center gap-2">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-semibold text-slate-200">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] uppercase font-bold tracking-wider text-indigo-400">
                                {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-md transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="mb-4 p-4 rounded-lg bg-emerald-950/50 border border-emerald-800/60 text-emerald-300 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-rose-950/50 border border-rose-800/60 text-rose-300 text-sm flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {!! $slot ?? '' !!}
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
