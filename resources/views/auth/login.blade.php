@extends('layouts.guest')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
    <!-- Header & Brand Logo -->
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-emerald-600 text-white font-extrabold text-2xl mb-4 shadow-lg shadow-emerald-600/20 hover:scale-105 transition">
            P
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-white">POS Putri Offline-First</h2>
        <p class="text-xs text-zinc-400 mt-1">Masuk untuk memulai shift atau mengelola sistem</p>
    </div>

    <!-- Login Card: Clean 1px Zinc Border, High Tactile Contrast -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 sm:p-8 shadow-2xl">
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Email Petugas</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', 'admin@posputri.test') }}"
                    class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-700/80 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition"
                    placeholder="petugas@toko.com">
                @error('email')
                    <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider">Kata Sandi</label>
                    <span class="text-[11px] font-mono text-zinc-500">Default: password</span>
                </div>
                <input id="password" name="password" type="password" autocomplete="current-password" required value="password"
                    class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-700/80 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition font-mono">
                @error('password')
                    <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Status -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 text-xs text-zinc-400 cursor-pointer">
                    <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded bg-zinc-950 border-zinc-700 text-emerald-500 focus:ring-emerald-500">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button (Bold, Tactile) -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-600/20 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition flex items-center justify-center gap-2 active:scale-98">
                    <span>Masuk ke Sistem</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>

        <!-- Quick 1-Click Role Switcher for Demo -->
        <div class="mt-6 pt-6 border-t border-zinc-800 text-xs">
            <p class="text-zinc-400 font-medium mb-3 text-center">Akses Cepat Akun Demo (Klik untuk isi otomatis):</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="document.getElementById('email').value='kasir@posputri.test'" class="p-2.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-emerald-500/40 text-center transition active:scale-95 group">
                    <div class="font-bold text-emerald-400 group-hover:text-emerald-300">Kasir</div>
                    <div class="text-[10px] text-zinc-500 truncate">kasir@...</div>
                </button>
                <button type="button" onclick="document.getElementById('email').value='supervisor@posputri.test'" class="p-2.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 text-center transition active:scale-95 group">
                    <div class="font-bold text-amber-400 group-hover:text-amber-300">Supervisor</div>
                    <div class="text-[10px] text-zinc-500 truncate">spv@...</div>
                </button>
                <button type="button" onclick="document.getElementById('email').value='admin@posputri.test'" class="p-2.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 text-center transition active:scale-95 group">
                    <div class="font-bold text-indigo-400 group-hover:text-indigo-300">Admin</div>
                    <div class="text-[10px] text-zinc-500 truncate">admin@...</div>
                </button>
            </div>
        </div>
    </div>

    <!-- Back to Welcome Link -->
    <div class="text-center mt-6">
        <a href="{{ url('/') }}" class="text-xs text-zinc-500 hover:text-zinc-300 transition flex items-center justify-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Beranda POS Putri</span>
        </a>
    </div>
</div>
@endsection
