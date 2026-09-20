@extends('layouts.guest')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
    <!-- Header & Brand Logo -->
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex mb-4 group">
            <x-application-logo class="w-16 h-16 shadow-md group-hover:scale-105 transition duration-150" />
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">POS Putri Offline-First</h2>
        <p class="text-xs text-slate-500 mt-1">Masuk untuk membuka shift kasir atau mengelola outlet</p>
    </div>

    <!-- Login Card: Clean Light Commercial Grade (Square Standard) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Petugas</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', 'admin@posputri.test') }}"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition"
                    placeholder="petugas@toko.com">
                @error('email')
                    <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    <span class="text-[11px] font-mono text-slate-500">Default: password</span>
                </div>
                <input id="password" name="password" type="password" autocomplete="current-password" required value="password"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition font-mono">
                @error('password')
                    <p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Status -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded bg-slate-100 border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button (Bold, Tactile) -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition flex items-center justify-center gap-2 active:scale-98">
                    <span>Masuk ke Sistem</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>

        <!-- Quick 1-Click Role Switcher for Demo -->
        <div class="mt-6 pt-6 border-t border-slate-200 text-xs">
            <p class="text-slate-600 font-medium mb-3 text-center">Akses Cepat Akun Demo (Klik untuk isi otomatis):</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="document.getElementById('email').value='kasir@posputri.test'" class="p-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-center transition active:scale-95 group">
                    <div class="font-bold text-emerald-800">Kasir</div>
                    <div class="text-[10px] text-emerald-600 truncate">kasir@...</div>
                </button>
                <button type="button" onclick="document.getElementById('email').value='supervisor@posputri.test'" class="p-2.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-300 text-center transition active:scale-95 group">
                    <div class="font-bold text-amber-900">Supervisor</div>
                    <div class="text-[10px] text-amber-700 truncate">spv@...</div>
                </button>
                <button type="button" onclick="document.getElementById('email').value='admin@posputri.test'" class="p-2.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-300 text-center transition active:scale-95 group">
                    <div class="font-bold text-indigo-900">Admin</div>
                    <div class="text-[10px] text-indigo-700 truncate">admin@...</div>
                </button>
            </div>
        </div>
    </div>

    <!-- Back to Welcome Link -->
    <div class="text-center mt-6">
        <a href="{{ url('/') }}" class="text-xs text-slate-500 hover:text-slate-800 transition flex items-center justify-center gap-1.5 font-medium">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Beranda POS Putri</span>
        </a>
    </div>
</div>
@endsection
