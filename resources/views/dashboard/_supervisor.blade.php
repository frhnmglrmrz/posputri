<!-- ==================== TAMPILAN KHUSUS SUPERVISOR ==================== -->
<div class="space-y-6">
    <!-- Top 4 KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Omzet Toko Hari Ini -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Omzet Outlet Hari Ini</span>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono text-emerald-400">
                Rp{{ number_format($todayRevenue, 0, ',', '.') }}
            </div>
            <div class="text-xs text-slate-400 mt-1">Total penjualan sukses hari ini</div>
        </div>

        <!-- 2. Jumlah Transaksi -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Transaksi Selesai</span>
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-white">{{ $todayTxCount }} Struk</div>
            <div class="text-xs text-slate-400 mt-1">Dari seluruh kasir aktif</div>
        </div>

        <!-- 3. Rata-rata Basket Size -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rata-rata Basket</span>
                <span class="p-2 rounded-xl bg-cyan-500/10 text-cyan-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono text-cyan-400">
                Rp{{ number_format($avgBasket, 0, ',', '.') }}
            </div>
            <div class="text-xs text-slate-400 mt-1">Rata-rata belanja per struk</div>
        </div>

        <!-- 4. Transaksi Dibatalkan (Void) -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Transaksi Dibatalkan</span>
                <span class="p-2 rounded-xl {{ $todayVoidCount > 0 ? 'bg-rose-500/20 text-rose-400' : 'bg-slate-800 text-slate-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold {{ $todayVoidCount > 0 ? 'text-rose-400' : 'text-slate-300' }}">
                {{ $todayVoidCount }} Transaksi
            </div>
            <div class="text-xs text-slate-400 mt-1">Pembatalan struk / void hari ini</div>
        </div>
    </div>

    <!-- Active Shifts & Peringatan Stok Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kasir Bertugas Saat Ini -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg lg:col-span-1 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Kasir Sedang Bertugas</span>
                    </h3>
                    <span class="text-xs text-slate-400">{{ $activeShifts->count() }} Aktif</span>
                </div>

                <div class="space-y-3">
                    @forelse($activeShifts as $shift)
                    <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-white">{{ $shift->cashier->name ?? 'Kasir' }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/50">
                                Buka
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-1">
                            Shift #{{ $shift->id }} &bull; Buka {{ $shift->opened_at ? $shift->opened_at->format('H:i') : '-' }} WIB
                        </div>
                        <div class="text-[11px] text-slate-300 mt-0.5 font-mono">
                            Modal Kas: Rp{{ number_format($shift->opening_cash, 0, ',', '.') }}
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-500 text-xs">
                        Tidak ada kasir yang sedang membuka shift di outlet ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-800">
                <a href="{{ route('shifts.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 flex items-center justify-between">
                    <span>Lihat Semua Shift Kasir</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Peringatan Stok Kritis (Outlet Ini)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Produk dengan stok &le; 10 unit yang memerlukan restock segera.</p>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                        Penyesuaian Stok &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="text-[10px] uppercase bg-slate-950/60 text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-3 py-2.5">Produk</th>
                                <th class="px-3 py-2.5">Kategori</th>
                                <th class="px-3 py-2.5 text-center">Sisa Stok</th>
                                <th class="px-3 py-2.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70">
                            @forelse($lowStockProducts as $stockItem)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-3 py-2.5">
                                    <div class="font-semibold text-white">{{ $stockItem->product->name ?? 'Produk' }}</div>
                                    <div class="text-[10px] font-mono text-slate-500">{{ $stockItem->product->sku ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-2.5 text-slate-400">{{ $stockItem->product->category->name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded font-mono font-bold text-xs {{ $stockItem->quantity <= 0 ? 'bg-rose-950 text-rose-300 border border-rose-800' : ($stockItem->quantity <= 5 ? 'bg-amber-950 text-amber-300 border border-amber-800' : 'bg-indigo-950 text-indigo-300 border border-indigo-800') }}">
                                        {{ $stockItem->quantity }} pcs
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    <a href="{{ route('inventory.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                                        Sesuaikan
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-slate-500">
                                    Seluruh produk dalam kondisi stok aman (&gt; 10 unit).
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Toko Terbaru & Otorisasi Void -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-sm font-bold text-white tracking-tight">Transaksi Terakhir di Outlet</h3>
                <p class="text-xs text-slate-400">Pengawasan transaksi kasir secara realtime & otorisasi pembatalan (*Void*).</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                Kelola Semua Transaksi &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[10px] uppercase bg-slate-950/60 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">No. Transaksi</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Kasir</th>
                        <th class="px-4 py-3">Metode Bayar</th>
                        <th class="px-4 py-3 text-right">Total Transaksi</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3 font-mono font-bold text-white">{{ $tx->transaction_number }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $tx->transaction_at ? $tx->transaction_at->format('d/m H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $tx->cashier->name ?? 'Kasir' }}</td>
                        <td class="px-4 py-3 font-mono uppercase font-semibold text-indigo-400">
                            {{ $tx->payments->first()->payment_method ?? 'cash' }}
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-emerald-400 text-right">
                            Rp{{ number_format($tx->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $tx->status->value === 'completed' ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/50' : 'bg-rose-950/60 text-rose-400 border border-rose-800/50' }}">
                                {{ $tx->status->value }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                                Tinjau / Void
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada transaksi di outlet ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
