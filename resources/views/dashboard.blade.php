@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header: Clean Square POS White Card -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang, {{ auth()->user()->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-300">
                    {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                </span>
            </div>
            <p class="text-xs text-slate-500">
                Outlet Operasional: <strong class="text-slate-800">{{ auth()->user()->outlet->name ?? 'Outlet Utama' }}</strong> — Sistem POS Offline-First Berkah Mart.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @role('Owner')
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 active:scale-98">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Lihat Laporan Penjualan</span>
            </a>
            @elserole('Cashier|Supervisor|Admin')
            <a href="{{ route('pos.index') }}" class="px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 active:scale-98">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <span>Buka Terminal Kasir</span>
            </a>
            @else
            <a href="{{ route('products.index') }}" class="px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 active:scale-98">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Kelola Produk</span>
            </a>
            @endrole
        </div>
    </div>

    <!-- Role-Specific Dashboard Views -->
    @if(($roleView ?? 'admin') === 'cashier')
        @include('dashboard._cashier')
    @elseif(($roleView ?? 'admin') === 'supervisor')
        @include('dashboard._supervisor')
    @elseif(($roleView ?? 'admin') === 'inventory')
        @include('dashboard._inventory')
    @elseif(($roleView ?? 'admin') === 'owner')
        @include('dashboard._owner')
    @else
        @include('dashboard._admin')
    @endif
</div>
@endsection
