<!-- ==================== TAMPILAN KHUSUS SUPERVISOR (MODERN CLEAN POS) ==================== -->
<div class="space-y-6">
    <!-- Top 4 KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Omzet Toko Hari Ini -->
        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-zinc-400">Omzet Outlet Hari Ini</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono tabular-nums text-emerald-400">
                Rp{{ number_format($todayRevenue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-zinc-500 mt-1">Total penjualan sukses hari ini</div>
        </div>

        <!-- 2. Jumlah Transaksi -->
        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-zinc-400">Transaksi Selesai</span>
                <span class="p-1.5 rounded-lg bg-zinc-800 text-zinc-300 border border-zinc-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-mono text-white">{{ $todayTxCount }} Struk</div>
            <div class="text-[11px] text-zinc-500 mt-1">Dari seluruh kasir aktif</div>
        </div>

        <!-- 3. Rata-rata Basket Size -->
        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-zinc-400">Rata-rata Basket</span>
                <span class="p-1.5 rounded-lg bg-zinc-800 text-emerald-400 border border-zinc-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono tabular-nums text-white">
                Rp{{ number_format($avgBasket, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-zinc-500 mt-1">Rata-rata belanja per transaksi</div>
        </div>

        <!-- 4. Transaksi Dibatalkan (Void) -->
        <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-zinc-400">Transaksi Void</span>
                <span class="p-1.5 rounded-lg {{ $todayVoidCount > 0 ? 'bg-rose-950/80 text-rose-400 border border-rose-800' : 'bg-zinc-800 text-zinc-400 border border-zinc-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-mono {{ $todayVoidCount > 0 ? 'text-rose-400' : 'text-zinc-300' }}">
                {{ $todayVoidCount }} Transaksi
            </div>
            <div class="text-[11px] text-zinc-500 mt-1">Pembatalan struk oleh kasir</div>
        </div>
    </div>

    <!-- Active Shifts & Peringatan Stok Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Kasir Bertugas Saat Ini -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 shadow-lg lg:col-span-1 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold font-mono uppercase text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Kasir Sedang Bertugas</span>
                    </h3>
                    <span class="text-[11px] font-mono text-zinc-400">{{ $activeShifts->count() }} Aktif</span>
                </div>

                <div class="space-y-2.5">
                    @forelse($activeShifts as $shift)
                    <div class="p-3 rounded-lg bg-zinc-950 border border-zinc-800">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-white">{{ $shift->cashier->name ?? 'Kasir' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/50">
                                Shift #{{ $shift->id }}
                            </span>
                        </div>
                        <div class="text-[11px] text-zinc-400 mt-1">
                            Buka {{ $shift->opened_at ? $shift->opened_at->format('H:i') : '-' }} WIB
                        </div>
                        <div class="text-[11px] text-zinc-300 mt-0.5 font-mono tabular-nums">
                            Kas Awal: Rp{{ number_format($shift->opening_cash, 0, ',', '.') }}
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-zinc-500 text-xs">
                        Tidak ada kasir yang sedang membuka shift di outlet ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-zinc-800">
                <a href="{{ route('shifts.index') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 flex items-center justify-between transition">
                    <span>Lihat Semua Shift Kasir</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 shadow-lg lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-xs font-bold font-mono uppercase text-white flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Peringatan Stok Kritis (&le; 10 Unit)</span>
                        </h3>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold transition">
                        Sesuaikan Stok &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead class="text-[10px] uppercase font-mono bg-zinc-950 text-zinc-400 border-b border-zinc-800">
                            <tr>
                                <th class="px-3 py-2">Produk</th>
                                <th class="px-3 py-2">Kategori</th>
                                <th class="px-3 py-2 text-center">Sisa Stok</th>
                                <th class="px-3 py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/80">
                            @forelse($lowStockProducts as $stockItem)
                            <tr class="hover:bg-zinc-800/30 transition">
                                <td class="px-3 py-2">
                                    <div class="font-semibold text-white">{{ $stockItem->product->name ?? 'Produk' }}</div>
                                    <div class="text-[10px] font-mono text-zinc-500">{{ $stockItem->product->sku ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-2 text-zinc-400">{{ $stockItem->product->category->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded font-mono tabular-nums font-bold text-xs {{ $stockItem->quantity <= 0 ? 'bg-rose-950 text-rose-300 border border-rose-800' : ($stockItem->quantity <= 5 ? 'bg-amber-950 text-amber-300 border border-amber-800' : 'bg-zinc-800 text-zinc-300 border border-zinc-700') }}">
                                        {{ $stockItem->quantity }} unit
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <a href="{{ route('inventory.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold transition">
                                        Restock
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-zinc-500">
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
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
            <div>
                <h3 class="text-xs font-bold font-mono uppercase text-white tracking-tight">Transaksi Terakhir di Outlet</h3>
                <p class="text-xs text-zinc-400">Pengawasan transaksi kasir secara realtime & otorisasi pembatalan (Void).</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold transition">
                Kelola Semua Transaksi &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-300">
                <thead class="text-[10px] uppercase font-mono bg-zinc-950 text-zinc-400 border-b border-zinc-800">
                    <tr>
                        <th class="px-3 py-2.5">No. Transaksi</th>
                        <th class="px-3 py-2.5">Waktu</th>
                        <th class="px-3 py-2.5">Kasir</th>
                        <th class="px-3 py-2.5">Metode Bayar</th>
                        <th class="px-3 py-2.5 text-right">Total Transaksi</th>
                        <th class="px-3 py-2.5 text-center">Status</th>
                        <th class="px-3 py-2.5 text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/80">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-zinc-800/40 transition">
                        <td class="px-3 py-2.5 font-mono font-bold text-white">{{ $tx->transaction_number }}</td>
                        <td class="px-3 py-2.5 text-zinc-400 font-mono">{{ $tx->transaction_at ? $tx->transaction_at->format('d/m H:i') : '-' }}</td>
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
                        <td class="px-3 py-2.5 text-right">
                            <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold transition">
                                Tinjau / Void
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-zinc-500">Belum ada transaksi di outlet ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
