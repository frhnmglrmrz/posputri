<div>
    <x-alert />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Laporan & Analitik POS</h1>
            <p class="text-xs text-slate-500 mt-1">Laporan penjualan, produk terlaris, dan metode pembayaran (PRD Section 55, 56)</p>
        </div>

        <!-- Date Range Filter & Export Actions -->
        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto text-xs">
            <div class="flex items-center gap-1.5 bg-white border border-slate-300 rounded-xl px-2.5 py-1 shadow-2xs">
                <input type="date" wire:model.live="startDate" class="bg-transparent text-slate-900 text-xs focus:outline-none">
                <span class="text-slate-400 font-medium">s/d</span>
                <input type="date" wire:model.live="endDate" class="bg-transparent text-slate-900 text-xs focus:outline-none">
            </div>

            <!-- Print Button -->
            <button type="button" onclick="window.print()" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 transition flex items-center gap-1.5 active:scale-95 shadow-2xs print:hidden">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak</span>
            </button>

            <!-- Export CSV Button -->
            <button type="button" wire:click="exportCsv" wire:loading.attr="disabled" class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition flex items-center gap-1.5 active:scale-95 shadow-2xs print:hidden">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span wire:loading.remove wire:target="exportCsv">Ekspor CSV</span>
                <span wire:loading wire:target="exportCsv">Mengunduh...</span>
            </button>
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
