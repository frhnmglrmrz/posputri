@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-slate-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-white tracking-tight">Selamat Datang, {{ auth()->user()->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                </span>
            </div>
            <p class="text-sm text-slate-400">
                Outlet Aktif: <strong class="text-slate-200">{{ auth()->user()->outlet->name ?? 'Outlet Utama' }}</strong> — Sistem Point of Sale berbasis Offline-First Laravel.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('pos.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm shadow-lg shadow-emerald-600/25 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buka POS Kasir</span>
            </a>
        </div>
    </div>

    <!-- Role-Specific Dashboard Views -->
    @if(($roleView ?? 'admin') === 'cashier')
        @include('dashboard._cashier')
    @elseif(($roleView ?? 'admin') === 'supervisor')
        @include('dashboard._supervisor')
    @else
        @include('dashboard._admin')
    @endif
</div>
@endsection
