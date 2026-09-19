<!-- ==================== TAMPILAN KHUSUS ADMINISTRATOR (MODERN CLEAN POS) ==================== -->
<div class="space-y-6">
    <!-- Top Enterprise 4 Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Omzet Hari Ini -->
        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-zinc-400">Omzet Toko Hari Ini</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono tabular-nums text-emerald-400">
                Rp{{ number_format($todayRevenue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-zinc-500 mt-1">Akumulasi seluruh cabang aktif</div>
        </div>

        <!-- 2. Omzet Bulan Ini -->
        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-zinc-400">Omzet Bulan Ini</span>
                <span class="p-1.5 rounded-lg bg-zinc-800 text-zinc-300 border border-zinc-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono tabular-nums text-white">
                Rp{{ number_format($monthRevenue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-zinc-500 mt-1">{{ $monthTxCount }} transaksi bulan ini</div>
        </div>

        <!-- 3. Valuasi Nilai Stok -->
        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-zinc-400">Valuasi Aset Stok</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono tabular-nums text-amber-400">
                Rp{{ number_format($totalInventoryValue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-zinc-500 mt-1">{{ $totalProducts }} jenis produk dalam stok</div>
        </div>

        <!-- 4. Outlet & Perangkat POS -->
        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-zinc-400">Infrastruktur Toko</span>
                <span class="p-1.5 rounded-lg bg-zinc-800 text-zinc-300 border border-zinc-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-mono text-white">
                {{ $totalOutlets }} Cabang / {{ $totalDevices }} POS
            </div>
            <div class="text-[11px] text-zinc-500 mt-1">{{ $totalUsers }} petugas terdaftar</div>
        </div>
    </div>

    <!-- Middle: Sync Health Banner & Quick Navigation -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Sync & System Health -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 shadow-lg lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold font-mono uppercase text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Status Sistem & Integrasi Sinkronisasi Offline</span>
                    </h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-950/60 text-emerald-400 border border-emerald-800/50">
                        MySQL Server Ready
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 rounded-lg bg-zinc-950 border border-zinc-800">
                        <div class="text-xs font-semibold text-zinc-300 mb-1">Penyimpanan Offline (IndexedDB)</div>
                        <p class="text-[11px] text-zinc-400 leading-relaxed">
                            Database browser kasir menyinkronkan data secara otomatis saat koneksi online dengan jaminan <em>zero transaction loss</em>.
                        </p>
                    </div>

                    <div class="p-3 rounded-lg bg-zinc-950 border border-zinc-800">
                        <div class="text-xs font-semibold text-zinc-300 mb-1">Status Log Sinkronisasi</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-2.5 h-2.5 rounded-full {{ $failedSyncCount > 0 ? 'bg-rose-400' : 'bg-emerald-400' }}"></span>
                            <span class="text-xs font-bold {{ $failedSyncCount > 0 ? 'text-rose-400' : 'text-zinc-200' }}">
                                {{ $failedSyncCount > 0 ? "{$failedSyncCount} Konflik Tercatat" : "Semua Sinkronisasi Lancar" }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-zinc-800 flex items-center justify-between text-xs">
                <span class="text-zinc-400">Monitoring antrean sinkronisasi & log</span>
                <a href="{{ route('sync.index') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 transition">
                    Buka Sync Center &rarr;
                </a>
            </div>
        </div>

        <!-- Menu Manajemen Khusus Admin -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 shadow-lg space-y-2">
            <h3 class="text-xs font-bold font-mono uppercase text-white mb-2">Pintasan Administrator</h3>
            <a href="{{ route('users.index') }}" class="p-2.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 flex items-center justify-between text-xs font-semibold text-zinc-300 hover:text-white transition active:scale-98">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Kelola Akun Pengguna</span>
                </span>
                <span class="text-zinc-500">&rarr;</span>
            </a>
            <a href="{{ route('settings.index') }}" class="p-2.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 flex items-center justify-between text-xs font-semibold text-zinc-300 hover:text-white transition active:scale-98">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Cabang Outlet & Perangkat Kasir</span>
                </span>
                <span class="text-zinc-500">&rarr;</span>
            </a>
            <a href="{{ route('products.index') }}" class="p-2.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 flex items-center justify-between text-xs font-semibold text-zinc-300 hover:text-white transition active:scale-98">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Katalog Produk & Harga</span>
                </span>
                <span class="text-zinc-500">&rarr;</span>
            </a>
            <a href="{{ route('reports.index') }}" class="p-2.5 rounded-lg bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 flex items-center justify-between text-xs font-semibold text-zinc-300 hover:text-white transition active:scale-98">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Laporan Keuangan & Analitik</span>
                </span>
                <span class="text-zinc-500">&rarr;</span>
            </a>
        </div>
    </div>

    <!-- Active Shifts & Transaksi Terbaru Seluruh Cabang -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Kasir Aktif di Semua Cabang -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 shadow-lg lg:col-span-1 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold font-mono uppercase text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Kasir Aktif (Semua Cabang)</span>
                    </h3>
                    <span class="text-[11px] font-mono text-zinc-400">{{ $activeShifts->count() }} Shift</span>
                </div>

                <div class="space-y-2.5">
                    @forelse($activeShifts as $shift)
                    <div class="p-3 rounded-lg bg-zinc-950 border border-zinc-800">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-white">{{ $shift->cashier->name ?? 'Kasir' }}</span>
                            <span class="text-[10px] font-mono text-emerald-400 font-semibold">{{ $shift->outlet->code ?? 'OUT' }}</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 mt-1">
                            {{ $shift->outlet->name ?? 'Outlet' }} &bull; Buka {{ $shift->opened_at ? $shift->opened_at->format('H:i') : '-' }} WIB
                        </div>
                        <div class="text-[11px] text-zinc-300 mt-0.5 font-mono tabular-nums">
                            Modal: Rp{{ number_format($shift->opening_cash, 0, ',', '.') }}
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-zinc-500 text-xs">
                        Tidak ada kasir yang sedang bertugas saat ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-zinc-800">
                <a href="{{ route('shifts.index') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 flex items-center justify-between transition">
                    <span>Semua Shift Kasir</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- 6 Transaksi Terbaru Seluruh Cabang -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 shadow-xl lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                    <div>
                        <h3 class="text-xs font-bold font-mono uppercase text-white tracking-tight">Transaksi Terakhir Masuk</h3>
                        <p class="text-xs text-zinc-400">Transaksi masuk tersinkronisasi dari seluruh terminal kasir.</p>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold transition">
                        Semua Transaksi &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead class="text-[10px] uppercase font-mono bg-zinc-950 text-zinc-400 border-b border-zinc-800">
                            <tr>
                                <th class="px-3 py-2.5">No. Transaksi</th>
                                <th class="px-3 py-2.5">Cabang</th>
                                <th class="px-3 py-2.5">Kasir</th>
                                <th class="px-3 py-2.5">Metode Bayar</th>
                                <th class="px-3 py-2.5 text-right">Total</th>
                                <th class="px-3 py-2.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/80">
                            @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-zinc-800/40 transition">
                                <td class="px-3 py-2.5 font-mono font-bold text-white">{{ $tx->transaction_number }}</td>
                                <td class="px-3 py-2.5 text-zinc-400">{{ $tx->outlet->code ?? 'OUT' }}</td>
                                <td class="px-3 py-2.5 text-zinc-300">{{ $tx->cashier->name ?? 'Kasir' }}</td>
                                <td class="px-3 py-2.5 font-mono uppercase font-semibold text-zinc-300">
                                    {{ $tx->payments->first()->payment_method ?? 'cash' }}
                                </td>
                                <td class="px-3 py-2.5 font-mono tabular-nums font-bold text-emerald-400 text-right">
                                    Rp{{ number_format($tx->total, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold {{ $tx->status->value === 'completed' ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/50' : 'bg-rose-950/60 text-rose-400 border border-rose-800/50' }}">
                                        {{ $tx->status->value }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-zinc-500">Belum ada transaksi tersimpan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
