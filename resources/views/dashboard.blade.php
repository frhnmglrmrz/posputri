@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header: Clean, Bold & Direct -->
    <div class="p-5 rounded-xl bg-zinc-900 border border-zinc-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Selamat Datang, {{ auth()->user()->name }}</h1>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                </span>
            </div>
            <p class="text-xs text-zinc-400">
                Outlet Operasional: <strong class="text-zinc-200">{{ auth()->user()->outlet->name ?? 'Outlet Utama' }}</strong> — Sistem POS Offline-First POS Putri.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('pos.index') }}" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5 active:scale-98">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <span>Buka Terminal Kasir</span>
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
