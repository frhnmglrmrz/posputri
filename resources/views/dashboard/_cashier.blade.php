<!-- ==================== TAMPILAN KHUSUS KASIR (SQUARE / SHOPIFY POS LIGHT MODE) ==================== -->
<div class="space-y-6">
    <!-- Top Action & Shift Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- 1. Status Kasir & Operasional -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-mono">Status Operasional</span>
                    <span class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        SIAP TRANSAKSI
                    </span>
                </div>

                <div class="space-y-2">
                    <div class="text-xl font-bold text-slate-900">{{ $user->name }}</div>
                    <div class="text-xs text-slate-500">
                        Outlet: <strong class="text-slate-800">{{ $outlet->name ?? 'Outlet Utama' }}</strong>
                    </div>
                    <div class="text-xs text-slate-600 font-medium">
                        Tanggal: <strong class="text-emerald-700 font-mono">{{ now()->translatedFormat('l, d F Y') }}</strong>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-200">
                <a href="{{ route('pos.index') }}" class="w-full py-2 px-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-98">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <span>Mulai Penjualan Langsung</span>
                </a>
            </div>
        </div>

        <!-- 2. Omzet Kasir Hari Ini -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-mono">Penjualan Saya Hari Ini</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-extrabold font-mono tabular-nums text-emerald-700">
                    Rp{{ number_format($cashierRevenue, 0, ',', '.') }}
                </div>
                <div class="text-xs text-slate-500 mt-1 font-medium">
                    Dari <strong>{{ $cashierTxCount }} transaksi</strong> diselesaikan
                </div>

                <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-slate-200 text-[11px]">
                    <div>
                        <span class="text-slate-500 block">Uang Tunai:</span>
                        <span class="font-mono tabular-nums font-bold text-slate-800">Rp{{ number_format($cashierCashTotal, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Non-Tunai:</span>
                        <span class="font-mono tabular-nums font-bold text-slate-800">Rp{{ number_format($cashierNonCashTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-200">
                <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold flex items-center justify-between transition">
                    <span>Riwayat Transaksi Lengkap</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- 3. Tombol Utama POS Kasir -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-700 mb-1">Terminal Kasir</div>
                <h3 class="text-lg font-bold text-slate-900">Buka Antarmuka POS</h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Mulai melayani pembayaran pelanggan. Dukungan pencarian cepat (&lt;30ms) nama & SKU produk secara offline.
                </p>
            </div>

            <div class="mt-6 space-y-2">
                <a href="{{ route('pos.index') }}" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold text-center flex items-center justify-center gap-2 shadow-sm transition active:scale-98">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <span>BUKA KASIR SEKARANG</span>
                </a>
                <div class="text-[10px] text-center text-slate-500 font-mono flex items-center justify-center gap-1.5 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    <span>Offline-Ready (IndexedDB)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi Kasir Ini -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900 tracking-tight">Transaksi Terakhir Yang Anda Layani</h3>
                <p class="text-xs text-slate-500">Struk transaksi yang berhasil dibuat dari akun kasir ini.</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold self-start sm:self-auto transition">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="text-[10px] uppercase font-mono bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2.5 font-semibold">No. Transaksi</th>
                        <th class="px-4 py-2.5 font-semibold">Waktu</th>
                        <th class="px-4 py-2.5 font-semibold">Pelanggan</th>
                        <th class="px-4 py-2.5 font-semibold">Metode Bayar</th>
                        <th class="px-4 py-2.5 text-right font-semibold">Total Transaksi</th>
                        <th class="px-4 py-2.5 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-4 py-2.5 font-mono font-bold text-slate-900">{{ $tx->transaction_number }}</td>
                        <td class="px-4 py-2.5 text-slate-500 font-mono">{{ $tx->transaction_at ? $tx->transaction_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-2.5 text-slate-700 font-medium">{{ $tx->customer->name ?? 'Umum (Guest)' }}</td>
                        <td class="px-4 py-2.5 font-mono uppercase font-semibold text-slate-600">
                            {{ $tx->payments->first()->payment_method ?? 'cash' }}
                        </td>
                        <td class="px-4 py-2.5 font-mono tabular-nums font-bold text-emerald-700 text-right">
                            Rp{{ number_format($tx->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $tx->status->value === 'completed' ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-300' }}">
                                {{ $tx->status->value }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                            Belum ada transaksi yang dibuat hari ini. Klik "Buka Terminal Kasir" untuk mulai melayani pelanggan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tips Singkat Kasir (Ergonomi Kerja) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <div class="font-bold text-emerald-700 mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Operasi Offline Penuh</span>
            </div>
            <p class="text-slate-600 leading-relaxed">Transaksi tetap dapat diproses dan struk dapat langsung dicetak walaupun internet atau server tidak terjangkau.</p>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <div class="font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Pencarian Cepat SKU</span>
            </div>
            <p class="text-slate-600 leading-relaxed">Ketik nama atau kode SKU produk di terminal dan tekan Enter untuk langsung menambahkan barang ke keranjang.</p>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <div class="font-bold text-emerald-700 mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Pemindaian Barcode</span>
            </div>
            <p class="text-slate-600 leading-relaxed">Gunakan scanner barcode USB/Bluetooth atau kamera perangkat untuk mempercepat input produk secara akurat.</p>
        </div>
    </div>
</div>
