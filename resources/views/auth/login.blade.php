@extends('layouts.guest')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 mb-4 shadow-lg shadow-indigo-500/10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-white">POS Putri Offline-First</h2>
        <p class="text-sm text-slate-400 mt-1">Masuk untuk memulai shift atau mengelola sistem</p>
    </div>

    <div class="bg-slate-800/80 backdrop-blur border border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', 'admin@posputri.test') }}"
                    class="w-full px-4 py-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition"
                    placeholder="nama@toko.com">
                @error('email')
                    <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required value="password"
                    class="w-full px-4 py-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition">
                @error('password')
                    <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
                    <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900">
                    <span>Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition flex items-center justify-center gap-2">
                    <span>Masuk ke Sistem</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>

        <!-- Quick Demo Credentials -->
        <div class="mt-6 pt-6 border-t border-slate-700/60 text-xs">
            <p class="text-slate-400 font-medium mb-3 text-center">Pilih Akun Demo Cepat (Password: <code class="text-indigo-300">password</code>):</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="document.getElementById('email').value='admin@posputri.test'" class="p-2 rounded-lg bg-slate-900 hover:bg-slate-700 border border-slate-700 text-center transition">
                    <div class="font-bold text-indigo-400">Admin</div>
                    <div class="text-[10px] text-slate-400 truncate">admin@...</div>
                </button>
                <button type="button" onclick="document.getElementById('email').value='supervisor@posputri.test'" class="p-2 rounded-lg bg-slate-900 hover:bg-slate-700 border border-slate-700 text-center transition">
                    <div class="font-bold text-amber-400">Supervisor</div>
                    <div class="text-[10px] text-slate-400 truncate">supervisor@...</div>
                </button>
                <button type="button" onclick="document.getElementById('email').value='kasir@posputri.test'" class="p-2 rounded-lg bg-slate-900 hover:bg-slate-700 border border-slate-700 text-center transition">
                    <div class="font-bold text-emerald-400">Kasir</div>
                    <div class="text-[10px] text-slate-400 truncate">kasir@...</div>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
