<!-- ==================== TAMPILAN KHUSUS OWNER (EXECUTIVE ANALYTICS & CHARTS) ==================== -->
<div class="space-y-6" x-data="{ chartMetric: 'revenue' }">
    <!-- Period Filter & Action Header -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </span>
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Ringkasan Analisis Performa Bisnis</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">Periode aktif: <strong class="text-slate-800">{{ $periodLabel }}</strong> (Data aktual tersinkron)</p>
        </div>

        <!-- Period Selector Pills -->
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex flex-wrap items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-semibold">
                <a href="{{ route('dashboard', ['period' => 'today']) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ $period === 'today' ? 'bg-white text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Hari Ini
                </a>
                <a href="{{ route('dashboard', ['period' => '7_days']) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ $period === '7_days' ? 'bg-white text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    7 Hari
                </a>
                <a href="{{ route('dashboard', ['period' => '30_days']) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ $period === '30_days' ? 'bg-white text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    30 Hari
                </a>
                <a href="{{ route('dashboard', ['period' => 'this_month']) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ $period === 'this_month' ? 'bg-white text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('dashboard', ['period' => 'this_year']) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ $period === 'this_year' ? 'bg-white text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Tahun Ini
                </a>
            </div>

            <!-- Link to Full Reports -->
            <a href="{{ route('reports.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh Laporan</span>
            </a>
        </div>
    </div>

    <!-- Custom Date Filter Bar (Toggleable / Secondary) -->
    <form method="GET" action="{{ route('dashboard') }}" class="bg-white border border-slate-200 rounded-xl px-4 py-3 flex flex-wrap items-center gap-3 text-xs shadow-2xs">
        <input type="hidden" name="period" value="custom">
        <span class="font-semibold text-slate-700">Filter Tanggal Khusus:</span>
        <div class="flex items-center gap-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500">
            <span class="text-slate-400">s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500">
        </div>
        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition active:scale-95">
            Terapkan
        </button>
    </form>

    <!-- 5 Executive KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Pendapatan -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Omzet</span>
                    <span class="p-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold font-mono tabular-nums text-emerald-700">
                    Rp{{ number_format($periodRevenue, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center gap-1.5 text-[11px]">
                @if($revenueGrowth >= 0)
                    <span class="font-bold text-emerald-600 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        +{{ $revenueGrowth }}%
                    </span>
                @else
                    <span class="font-bold text-rose-600 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        {{ $revenueGrowth }}%
                    </span>
                @endif
                <span class="text-slate-400">vs periode lalu</span>
            </div>
        </div>

        <!-- 2. Jumlah Transaksi Selesai -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Transaksi</span>
                    <span class="p-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold font-mono tabular-nums text-slate-900">
                    {{ number_format($periodTxCount, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center gap-1.5 text-[11px]">
                @if($txGrowth >= 0)
                    <span class="font-bold text-emerald-600 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        +{{ $txGrowth }}%
                    </span>
                @else
                    <span class="font-bold text-rose-600 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        {{ $txGrowth }}%
                    </span>
                @endif
                <span class="text-slate-400">volume penjualan</span>
            </div>
        </div>

        <!-- 3. Rata-rata Basket Size (AOV) -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Rata-Rata Transaksi</span>
                    <span class="p-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold font-mono tabular-nums text-slate-900">
                    Rp{{ number_format($periodAvgBasket, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500">
                Nilai belanja per pelanggan
            </div>
        </div>

        <!-- 4. Estimasi Laba Kotor & Margin -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Estimasi Margin Laba</span>
                    <span class="p-1 rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold font-mono tabular-nums text-amber-700">
                    Rp{{ number_format($estimatedProfit, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] font-semibold text-emerald-700">
                Margin kotor: {{ $profitMarginPercent }}%
            </div>
        </div>

        <!-- 5. Total Barang Terjual -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Produk Terjual</span>
                    <span class="p-1 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold font-mono tabular-nums text-slate-900">
                    {{ number_format($itemsSold, 0, ',', '.') }} <span class="text-xs text-slate-400 font-normal">pcs</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-500">
                Akumulasi seluruh item
            </div>
        </div>
    </div>

    <!-- MAIN VISUALIZATION: Grafik Tren Penjualan & Transaksi -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Grafik Tren Penjualan & Transaksi</h3>
                <p class="text-xs text-slate-500">Visualisasi data aktual pergerakan omzet dan frekuensi transaksi</p>
            </div>

            <!-- Metric Toggle (Omzet / Transaksi) -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-semibold self-start sm:self-auto border border-slate-200">
                <button type="button" @click="chartMetric = 'revenue'"
                    class="px-3 py-1.5 rounded-lg transition"
                    :class="chartMetric === 'revenue' ? 'bg-white text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                    Nominal Omzet (Rp)
                </button>
                <button type="button" @click="chartMetric = 'tx'"
                    class="px-3 py-1.5 rounded-lg transition"
                    :class="chartMetric === 'tx' ? 'bg-white text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                    Jumlah Transaksi
                </button>
            </div>
        </div>

        @if(count($trendPoints) === 0 || ($maxRevenueInPoint == 0 && $maxTxInPoint == 0))
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 text-slate-400 text-center">
                <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h4 class="text-sm font-bold text-slate-700">Belum Ada Transaksi Pada Periode Ini</h4>
                <p class="text-xs text-slate-400 mt-1">Pilih rentang tanggal lain untuk memuat grafik penjualan historis.</p>
            </div>
        @else
            <!-- Responsive Interactive SVG Column & Line Chart -->
            <div class="relative pt-2">
                <!-- Peak Info Pill -->
                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                    <span class="font-mono text-[11px]">Puncak Tertinggi:
                        <strong class="text-slate-900 font-bold" x-show="chartMetric === 'revenue'">
                            Rp{{ number_format($maxRevenueInPoint, 0, ',', '.') }}
                        </strong>
                        <strong class="text-slate-900 font-bold" x-show="chartMetric === 'tx'">
                            {{ $maxTxInPoint }} Transaksi
                        </strong>
                    </span>
                    <span class="text-[11px] font-mono text-slate-400">Total {{ count($trendPoints) }} Titik Waktu</span>
                </div>

                <!-- SVG Visual Chart Container -->
                <div class="h-64 sm:h-72 w-full flex items-end gap-1 sm:gap-2 pt-6 pb-6 border-b border-slate-200">
                    @foreach($trendPoints as $pt)
                        @php
                            $revHeight = $maxRevenueInPoint > 0 ? max(4, round(($pt['revenue'] / $maxRevenueInPoint) * 100)) : 4;
                            $txHeight = $maxTxInPoint > 0 ? max(4, round(($pt['tx_count'] / $maxTxInPoint) * 100)) : 4;
                        @endphp
                        <div class="flex-1 h-full flex flex-col justify-end items-center group relative cursor-pointer">
                            <!-- Tooltip On Hover -->
                            <div class="absolute bottom-full mb-2 hidden group-hover:flex flex-col items-center z-30 pointer-events-none">
                                <div class="bg-slate-900 text-white text-[10px] rounded-lg px-2.5 py-1.5 shadow-xl whitespace-nowrap text-center font-mono">
                                    <div class="font-bold text-slate-200">{{ $pt['full_label'] }}</div>
                                    <div class="text-emerald-400 font-extrabold mt-0.5">Rp{{ number_format($pt['revenue'], 0, ',', '.') }}</div>
                                    <div class="text-slate-300 text-[9px]">{{ $pt['tx_count'] }} transaksi</div>
                                </div>
                                <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-0.5"></div>
                            </div>

                            <!-- Bar Indicator for Revenue -->
                            <div x-show="chartMetric === 'revenue'"
                                 class="w-full max-w-[36px] rounded-t-lg bg-emerald-500 group-hover:bg-emerald-600 transition-all duration-300 shadow-2xs relative"
                                 style="height: {{ $revHeight }}%;">
                                <div class="absolute inset-x-0 top-0 h-1 bg-emerald-300 rounded-t-lg opacity-80"></div>
                            </div>

                            <!-- Bar Indicator for Transactions -->
                            <div x-show="chartMetric === 'tx'"
                                 class="w-full max-w-[36px] rounded-t-lg bg-blue-500 group-hover:bg-blue-600 transition-all duration-300 shadow-2xs relative"
                                 style="height: {{ $txHeight }}%;">
                                <div class="absolute inset-x-0 top-0 h-1 bg-blue-300 rounded-t-lg opacity-80"></div>
                            </div>

                            <!-- X Axis Date Label -->
                            <span class="text-[10px] font-mono text-slate-500 mt-2 truncate max-w-full text-center">
                                {{ $pt['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Secondary Grids: Diagram Produk Terlaris & Metode Pembayaran -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- 1. Diagram Top Produk Terlaris -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Diagram Produk Terlaris</h3>
                    <p class="text-xs text-slate-500">Peringkat produk berdasarkan volume penjualan dalam periode</p>
                </div>
                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-mono font-bold">Top 6</span>
            </div>

            @if($topProducts->isEmpty())
                <div class="py-10 text-center text-xs text-slate-400">Belum ada data produk terjual pada periode ini.</div>
            @else
                <div class="space-y-3">
                    @foreach($topProducts as $index => $prod)
                        @php
                            $percentage = $maxProductQty > 0 ? round(($prod->total_qty / $maxProductQty) * 100) : 0;
                        @endphp
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 truncate max-w-[70%]">
                                    <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-700 font-mono font-bold text-[10px] flex items-center justify-center shrink-0">
                                        #{{ $index + 1 }}
                                    </span>
                                    <span class="font-bold text-slate-800 truncate">{{ $prod->product_name }}</span>
                                </div>
                                <div class="text-right font-mono shrink-0">
                                    <span class="font-extrabold text-slate-900">{{ $prod->total_qty }} pcs</span>
                                    <span class="text-slate-400 text-[10px] ml-1">(Rp{{ number_format($prod->total_subtotal, 0, ',', '.') }})</span>
                                </div>
                            </div>
                            <!-- Progress Bar Indicator -->
                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $percentage }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 2. Diagram Distribusi Metode Pembayaran -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Distribusi Metode Pembayaran</h3>
                    <p class="text-xs text-slate-500">Perbandingan transaksi tunai vs nontunai (QRIS, Transfer, Debit)</p>
                </div>
                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 text-[10px] font-mono font-bold">Transparan</span>
            </div>

            @if($paymentMethods->isEmpty())
                <div class="py-10 text-center text-xs text-slate-400">Belum ada pembayaran pada periode ini.</div>
            @else
                <div class="space-y-3">
                    @foreach($paymentMethods as $pm)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 font-mono font-bold text-xs flex items-center justify-center">
                                    {{ substr($pm['method'], 0, 2) }}
                                </span>
                                <div>
                                    <div class="font-bold text-xs text-slate-900">{{ $pm['method'] }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $pm['count'] }} transaksi ({{ $pm['percentage'] }}%)</div>
                                </div>
                            </div>
                            <div class="text-right font-mono font-bold text-xs text-emerald-700">
                                Rp{{ number_format($pm['total'], 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Completed Transactions (Read-Only Audit Trail) -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Aktivitas Transaksi Terbaru</h3>
                <p class="text-xs text-slate-500">Transaksi selesai tercatat dalam sistem (Read-only)</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 transition">
                <span>Lihat Seluruh Transaksi</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">No Transaksi</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Kasir</th>
                        <th class="px-4 py-3">Metode Bayar</th>
                        <th class="px-4 py-3 text-right">Total Belanja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                {{ $tx->transaction_number }}
                            </td>
                            <td class="px-4 py-3 font-mono text-[11px] text-slate-500">
                                {{ $tx->transaction_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                {{ $tx->cashier->name ?? 'Kasir' }}
                            </td>
                            <td class="px-4 py-3 font-mono uppercase text-[10px] font-bold text-slate-700">
                                {{ $tx->payments->pluck('payment_method')->join(', ') ?: 'Cash' }}
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-emerald-700 text-right">
                                Rp{{ number_format($tx->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada transaksi tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
