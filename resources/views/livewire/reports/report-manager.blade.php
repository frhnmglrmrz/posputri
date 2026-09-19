<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Laporan & Analitik POS</h1>
            <p class="text-xs text-slate-500 mt-1">Laporan penjualan, produk terlaris, dan metode pembayaran (PRD Section 55, 56)</p>
        </div>

        <!-- Date Range Filter -->
        <div class="flex items-center gap-2 self-start sm:self-auto text-xs">
            <div>
                <input type="date" wire:model.live="startDate" class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
            </div>
            <span class="text-slate-400 font-medium">s/d</span>
            <div>
                <input type="date" wire:model.live="endDate" class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
            </div>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pendapatan</span>
            <div class="text-2xl font-extrabold text-emerald-700 font-mono mt-1 tabular-nums">
                Rp{{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1">Periode terpilih</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jumlah Transaksi</span>
            <div class="text-2xl font-extrabold text-slate-900 font-mono mt-1 tabular-nums">
                {{ number_format($totalTransactions, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1">Transaksi sukses</div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rata-rata Transaksi</span>
            <div class="text-2xl font-extrabold text-slate-900 font-mono mt-1 tabular-nums">
                Rp{{ number_format($avgTransaction, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1">Basket size rata-rata</div>
        </div>
    </div>

    <!-- Report Tabs -->
    <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200 mb-4 max-w-fit">
        <button type="button" wire:click="$set('reportType', 'sales')"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
            :class="$wire.reportType === 'sales' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'">
            Produk Terlaris
        </button>
        <button type="button" wire:click="$set('reportType', 'payments')"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
            :class="$wire.reportType === 'payments' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'">
            Metode Pembayaran
        </button>
        <button type="button" wire:click="$set('reportType', 'cashiers')"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
            :class="$wire.reportType === 'cashiers' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'">
            Kinerja Kasir
        </button>
    </div>

    @if($reportType === 'sales')
        <!-- Top Products Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-200 text-xs font-bold text-slate-900">Top 10 Produk Paling Laris</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Peringkat</th>
                            <th class="px-4 py-3">Nama Produk</th>
                            <th class="px-4 py-3">Jumlah Terjual</th>
                            <th class="px-4 py-3 text-right">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topProducts as $index => $prod)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-500">#{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $prod->product_name }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-emerald-700">{{ $prod->total_qty }} pcs</td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-900 text-right">Rp{{ number_format($prod->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada data penjualan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($reportType === 'payments')
        <!-- Payments Breakdown Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-200 text-xs font-bold text-slate-900">Rincian Pembayaran Berdasarkan Metode</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Metode Pembayaran</th>
                            <th class="px-4 py-3">Jumlah Transaksi</th>
                            <th class="px-4 py-3 text-right">Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($paymentBreakdown as $pay)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-semibold text-slate-900 uppercase">{{ $pay->payment_method }}</td>
                                <td class="px-4 py-3 font-mono text-slate-600">{{ $pay->count }} kali</td>
                                <td class="px-4 py-3 font-mono font-bold text-emerald-700 text-right">Rp{{ number_format($pay->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-500">Belum ada pembayaran pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Cashier Performance Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-200 text-xs font-bold text-slate-900">Kinerja Penjualan Kasir</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Nama Kasir</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Transaksi Sukses</th>
                            <th class="px-4 py-3 text-right">Total Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($cashierStats as $cashier)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $cashier->name }}</td>
                                <td class="px-4 py-3 text-slate-500 font-mono">{{ $cashier->email }}</td>
                                <td class="px-4 py-3 font-mono text-slate-600">{{ $cashier->transactions_count }} transaksi</td>
                                <td class="px-4 py-3 font-mono font-bold text-emerald-700 text-right">
                                    Rp{{ number_format($cashier->transactions_sum_total ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada kasir terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
