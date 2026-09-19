<!-- ==================== TAMPILAN KHUSUS KASIR (MODERN CLEAN POS) ==================== -->
<div class="space-y-6">
    <!-- Top Action & Shift Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- 1. Status Shift Card -->
        <div class="p-5 rounded-xl bg-zinc-900 border border-zinc-800 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400 font-mono">Status Shift Anda</span>
                    <span class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold {{ $activeShift ? 'bg-emerald-950/70 text-emerald-400 border border-emerald-800/60' : 'bg-amber-950/70 text-amber-400 border border-amber-800/60' }}">
                        <span class="w-2 h-2 rounded-full {{ $activeShift ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
                        {{ $activeShift ? 'SHIFT AKTIF' : 'BELUM BUKA SHIFT' }}
                    </span>
                </div>

                @if($activeShift)
                <div class="space-y-2">
                    <div class="text-xl font-bold text-white">Shift #{{ $activeShift->id }}</div>
                    <div class="text-xs text-zinc-400">
                        Dibuka pukul <strong class="text-zinc-200">{{ $activeShift->opened_at ? $activeShift->opened_at->format('H:i') : '-' }} WIB</strong>
                        ({{ $activeShift->opened_at ? $activeShift->opened_at->diffForHumans(null, true) : 'Baru dibuka' }})
                    </div>
                    <div class="text-xs text-zinc-400">
                        Modal Kas Awal: <strong class="text-emerald-400 font-mono tabular-nums">Rp{{ number_format($activeShift->opening_cash, 0, ',', '.') }}</strong>
                    </div>
                </div>
                @else
                <div class="space-y-1 py-1">
                    <p class="text-sm font-semibold text-zinc-200">Anda belum membuka shift kasir.</p>
                    <p class="text-xs text-zinc-400">Buka shift kasir terlebih dahulu untuk memasukkan modal kas awal.</p>
                </div>
                @endif
            </div>

            <div class="mt-5 pt-3 border-t border-zinc-800">
                <a href="{{ route('shifts.index') }}" class="w-full py-2 px-3 rounded-lg {{ $activeShift ? 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' : 'bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-bold' }} text-xs font-semibold transition flex items-center justify-center gap-1.5 active:scale-98">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $activeShift ? 'Kelola / Tutup Shift' : 'Buka Shift Sekarang' }}</span>
                </a>
            </div>
        </div>

        <!-- 2. Omzet Kasir Hari Ini -->
        <div class="p-5 rounded-xl bg-zinc-900 border border-zinc-800 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400 font-mono">Penjualan Saya Hari Ini</span>
                    <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-extrabold font-mono tabular-nums text-emerald-400">
                    Rp{{ number_format($cashierRevenue, 0, ',', '.') }}
                </div>
                <div class="text-xs text-zinc-400 mt-1">
                    Dari <strong>{{ $cashierTxCount }} transaksi</strong> diselesaikan
                </div>

                <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-zinc-800 text-[11px]">
                    <div>
                        <span class="text-zinc-500 block">Uang Tunai:</span>
                        <span class="font-mono tabular-nums font-bold text-zinc-200">Rp{{ number_format($cashierCashTotal, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block">Non-Tunai:</span>
                        <span class="font-mono tabular-nums font-bold text-zinc-200">Rp{{ number_format($cashierNonCashTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-zinc-800">
                <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center justify-between transition">
                    <span>Riwayat Transaksi Lengkap</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- 3. Tombol Utama POS Kasir -->
        <div class="p-5 rounded-xl bg-zinc-900 border border-zinc-800 shadow-xl flex flex-col justify-between">
            <div>
                <div class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-400 mb-1">Terminal Kasir</div>
                <h3 class="text-lg font-bold text-white">Buka Antarmuka POS</h3>
                <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                    Mulai melayani pembayaran pelanggan. Dukungan pencarian cepat (&lt;30ms) & pemindaian barcode USB secara offline.
                </p>
            </div>

            <div class="mt-6 space-y-2">
                <a href="{{ route('pos.index') }}" class="w-full py-3 px-4 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-sm font-bold text-center flex items-center justify-center gap-2 shadow-md shadow-emerald-500/20 transition active:scale-98">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <span>BUKA KASIR SEKARANG</span>
                </a>
                <div class="text-[10px] text-center text-zinc-500 font-mono flex items-center justify-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Offline-Ready (IndexedDB)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi Kasir Ini -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-sm font-bold text-white tracking-tight">Transaksi Terakhir Yang Anda Layani</h3>
                <p class="text-xs text-zinc-400">Struk transaksi yang berhasil dibuat dari akun kasir ini.</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold self-start sm:self-auto transition">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-300">
                <thead class="text-[10px] uppercase font-mono bg-zinc-950 text-zinc-400 border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-2.5">No. Transaksi</th>
                        <th class="px-4 py-2.5">Waktu</th>
                        <th class="px-4 py-2.5">Pelanggan</th>
                        <th class="px-4 py-2.5">Metode Bayar</th>
                        <th class="px-4 py-2.5 text-right">Total Transaksi</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/80">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-zinc-800/40 transition">
                        <td class="px-4 py-2.5 font-mono font-bold text-white">{{ $tx->transaction_number }}</td>
                        <td class="px-4 py-2.5 text-zinc-400 font-mono">{{ $tx->transaction_at ? $tx->transaction_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-2.5 text-zinc-300">{{ $tx->customer->name ?? 'Umum (Guest)' }}</td>
                        <td class="px-4 py-2.5 font-mono uppercase font-semibold text-zinc-300">
                            {{ $tx->payments->first()->payment_method ?? 'cash' }}
                        </td>
                        <td class="px-4 py-2.5 font-mono tabular-nums font-bold text-emerald-400 text-right">
                            Rp{{ number_format($tx->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold {{ $tx->status->value === 'completed' ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/50' : 'bg-rose-950/60 text-rose-400 border border-rose-800/50' }}">
                                {{ $tx->status->value }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-zinc-500">
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
        <div class="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800">
            <div class="font-bold text-emerald-400 mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Operasi Offline Penuh</span>
            </div>
            <p class="text-zinc-400 leading-relaxed">Transaksi tetap dapat diproses dan struk dapat langsung dicetak walaupun internet atau server tidak terjangkau.</p>
        </div>

        <div class="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800">
            <div class="font-bold text-zinc-200 mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span>Barcode Scanner USB</span>
            </div>
            <p class="text-zinc-400 leading-relaxed">Arahkan scanner ke barcode produk, produk otomatis masuk ke keranjang belanja tanpa perlu klik mouse.</p>
        </div>

        <div class="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800">
            <div class="font-bold text-amber-400 mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Tutup Shift Tepat Waktu</span>
            </div>
            <p class="text-zinc-400 leading-relaxed">Hitung uang tunai fisik di laci kasir saat pergantian jam kerja untuk menjaga akurasi rekonsiliasi kas.</p>
        </div>
    </div>
</div>
