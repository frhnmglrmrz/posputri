<!-- ==================== TAMPILAN KHUSUS SUPERVISOR (SQUARE / SHOPIFY POS LIGHT MODE) ==================== -->
<div class="space-y-6">
    <!-- Top 4 KPIs (Clean White Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Omzet Toko Hari Ini -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-slate-500">Omzet Outlet Hari Ini</span>
                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono tabular-nums text-emerald-700">
                Rp{{ number_format($todayRevenue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Total penjualan sukses hari ini</div>
        </div>

        <!-- 2. Jumlah Transaksi -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-slate-500">Transaksi Selesai</span>
                <span class="p-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-mono text-slate-900">{{ $todayTxCount }} Struk</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Dari seluruh kasir aktif</div>
        </div>

        <!-- 3. Rata-rata Basket Size -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-slate-500">Rata-rata Basket</span>
                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono tabular-nums text-slate-900">
                Rp{{ number_format($avgBasket, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Rata-rata belanja per transaksi</div>
        </div>

        <!-- 4. Transaksi Dibatalkan (Void) -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-slate-500">Transaksi Void</span>
                <span class="p-1.5 rounded-lg {{ $todayVoidCount > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-mono {{ $todayVoidCount > 0 ? 'text-rose-700' : 'text-slate-800' }}">
                {{ $todayVoidCount }} Transaksi
            </div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Pembatalan struk oleh kasir</div>
        </div>
    </div>

    <!-- Active Shifts & Peringatan Stok Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Kasir Bertugas Saat Ini -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm lg:col-span-1 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold font-mono uppercase text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        <span>Kasir Sedang Bertugas</span>
                    </h3>
                    <span class="text-[11px] font-mono text-slate-500 font-semibold">{{ $activeShifts->count() }} Aktif</span>
                </div>

                <div class="space-y-2.5 mt-3">
                    @forelse($activeShifts as $shift)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900">{{ $shift->cashier->name ?? 'Kasir' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Shift #{{ $shift->id }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1">
                            Buka {{ $shift->opened_at ? $shift->opened_at->format('H:i') : '-' }} WIB
                        </div>
                        <div class="text-[11px] text-slate-700 mt-0.5 font-mono tabular-nums font-semibold">
                            Kas Awal: Rp{{ number_format($shift->opening_cash, 0, ',', '.') }}
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-500 text-xs">
                        Tidak ada kasir yang sedang membuka shift di outlet ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-200">
                <a href="{{ route('shifts.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center justify-between transition">
                    <span>Lihat Semua Shift Kasir</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-xs font-bold font-mono uppercase text-slate-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Peringatan Stok Kritis (&le; 10 Unit)</span>
                        </h3>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold transition">
                        Sesuaikan Stok &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto mt-2">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="text-[10px] uppercase font-mono bg-slate-50 text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2.5 font-semibold">Produk</th>
                                <th class="px-3 py-2.5 font-semibold">Kategori</th>
                                <th class="px-3 py-2.5 text-center font-semibold">Sisa Stok</th>
                                <th class="px-3 py-2.5 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($lowStockProducts as $stockItem)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-2.5">
                                    <div class="font-semibold text-slate-900">{{ $stockItem->product->name ?? 'Produk' }}</div>
                                    <div class="text-[10px] font-mono text-slate-500">{{ $stockItem->product->sku ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $stockItem->product->category->name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded font-mono tabular-nums font-bold text-xs {{ $stockItem->quantity <= 0 ? 'bg-rose-100 text-rose-800 border border-rose-200' : ($stockItem->quantity <= 5 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                        {{ $stockItem->quantity }} unit
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    <a href="{{ route('inventory.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold transition">
                                        Restock
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
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
            <div>
                <h3 class="text-xs font-bold font-mono uppercase text-slate-800 tracking-tight">Transaksi Terakhir di Outlet</h3>
                <p class="text-xs text-slate-500">Pengawasan transaksi kasir secara realtime & otorisasi pembatalan (Void).</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold transition">
                Kelola Semua Transaksi &rarr;
            </a>
        </div>

        <div class="overflow-x-auto mt-2">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="text-[10px] uppercase font-mono bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2.5 font-semibold">No. Transaksi</th>
                        <th class="px-3 py-2.5 font-semibold">Waktu</th>
                        <th class="px-3 py-2.5 font-semibold">Kasir</th>
                        <th class="px-3 py-2.5 font-semibold">Metode Bayar</th>
                        <th class="px-3 py-2.5 text-right font-semibold">Total Transaksi</th>
                        <th class="px-3 py-2.5 text-center font-semibold">Status</th>
                        <th class="px-3 py-2.5 text-right font-semibold">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-3 py-2.5 font-mono font-bold text-slate-900">{{ $tx->transaction_number }}</td>
                        <td class="px-3 py-2.5 text-slate-500 font-mono">{{ $tx->transaction_at ? $tx->transaction_at->format('d/m H:i') : '-' }}</td>
                        <td class="px-3 py-2.5 text-slate-700 font-medium">{{ $tx->cashier->name ?? 'Kasir' }}</td>
                        <td class="px-3 py-2.5 font-mono uppercase font-semibold text-slate-600">
                            {{ $tx->payments->first()->payment_method ?? 'cash' }}
                        </td>
                        <td class="px-3 py-2.5 font-mono tabular-nums font-bold text-emerald-700 text-right">
                            Rp{{ number_format($tx->total, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $tx->status->value === 'completed' ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-300' }}">
                                {{ $tx->status->value }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 text-right">
                            <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold transition">
                                Tinjau / Void
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-slate-500">Belum ada transaksi di outlet ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
