<!-- ==================== TAMPILAN KHUSUS STAFF INVENTORY & GUDANG ==================== -->
<div class="space-y-6">
    <!-- Top 4 Inventory KPIs (Clean White Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Produk Katalog -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-slate-500">Total Katalog Produk</span>
                <span class="p-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono text-slate-900">{{ number_format($totalProducts) }} SKU</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $totalCategories }} Kategori aktif terdaftar</div>
        </div>

        <!-- 2. Total Unit Stok Fisik -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-slate-500">Total Unit Stok</span>
                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold font-mono tabular-nums text-emerald-700">{{ number_format($totalStock) }} Unit</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $outlet->name ?? 'Seluruh Outlet' }}</div>
        </div>

        <!-- 3. Stok Menipis (<= 10 Unit) -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-slate-500">Stok Menipis (&le; 10)</span>
                <span class="p-1.5 rounded-lg {{ $lowStockCount > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-mono {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-slate-900' }}">
                {{ $lowStockCount }} Produk
            </div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Perlu segera restock suplai</div>
        </div>

        <!-- 4. Stok Habis (0 Unit) -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase font-mono tracking-wider text-slate-500">Stok Habis (Kosong)</span>
                <span class="p-1.5 rounded-lg {{ $outOfStockCount > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold font-mono {{ $outOfStockCount > 0 ? 'text-rose-700' : 'text-slate-800' }}">
                {{ $outOfStockCount }} SKU
            </div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Tidak dapat dijual di POS</div>
        </div>
    </div>

    <!-- Quick Action Shortcuts -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('products.index') }}" class="p-4 rounded-xl bg-white border border-slate-200 hover:border-slate-300 hover:shadow-xs transition group flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-xs text-slate-900 group-hover:text-blue-700 transition">Input & Kelola Produk</div>
                    <div class="text-[11px] text-slate-500">Tambah SKU, harga beli & jual</div>
                </div>
            </div>
            <span class="text-slate-400 group-hover:text-blue-700 transition text-sm">&rarr;</span>
        </a>

        <a href="{{ route('inventory.index') }}" class="p-4 rounded-xl bg-white border border-slate-200 hover:border-slate-300 hover:shadow-xs transition group flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-xs text-slate-900 group-hover:text-emerald-700 transition">Penyesuaian & Opname Stok</div>
                    <div class="text-[11px] text-slate-500">Update fisik stok, barang masuk/rusak</div>
                </div>
            </div>
            <span class="text-slate-400 group-hover:text-emerald-700 transition text-sm">&rarr;</span>
        </a>

        <a href="{{ route('categories.index') }}" class="p-4 rounded-xl bg-white border border-slate-200 hover:border-slate-300 hover:shadow-xs transition group flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-xs text-slate-900 group-hover:text-purple-700 transition">Kategori Produk</div>
                    <div class="text-[11px] text-slate-500">Kelompokkan produk dan tata letak POS</div>
                </div>
            </div>
            <span class="text-slate-400 group-hover:text-purple-700 transition text-sm">&rarr;</span>
        </a>
    </div>

    <!-- Main Content: Low Stock List & Recent Products / Movements -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Peringatan Stok Kritis -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold font-mono uppercase text-slate-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Peringatan Stok Kritis (&le; 10 Unit)</span>
                    </h3>
                    <a href="{{ route('inventory.index') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold transition">
                        Semua Stok &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
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
                                    <div class="text-[10px] font-mono text-slate-400">{{ $stockItem->product->sku ?? '-' }}</div>
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
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                    Stok semua produk dalam kondisi aman (&gt; 10 unit).
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-200">
                <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center justify-between transition">
                    <span>Buka Lembar Penyesuaian Stok Opname</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- 2. Produk Terdaftar Terbaru -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold font-mono uppercase text-slate-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Produk Terdaftar Terbaru</span>
                    </h3>
                    <a href="{{ route('products.index') }}" class="text-xs text-blue-700 hover:text-blue-800 font-semibold transition">
                        Lihat Katalog &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="text-[10px] uppercase font-mono bg-slate-50 text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2.5 font-semibold">Produk</th>
                                <th class="px-3 py-2.5 font-semibold">Harga Jual</th>
                                <th class="px-3 py-2.5 text-center font-semibold">Stok</th>
                                <th class="px-3 py-2.5 text-right font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentProducts as $prod)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-2.5">
                                    <div class="font-semibold text-slate-900">{{ $prod->name }}</div>
                                    <div class="text-[10px] font-mono text-slate-400">{{ $prod->sku }}</div>
                                </td>
                                <td class="px-3 py-2.5 font-mono text-slate-800 font-semibold">
                                    Rp{{ number_format($prod->selling_price, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-center font-mono">
                                    {{ $prod->stocks->sum('quantity') }} unit
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    @if($prod->is_active)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                        Aktif
                                    </span>
                                    @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Nonaktif
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada produk yang didaftarkan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-200">
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-800 flex items-center justify-between transition">
                    <span>Input atau Edit Data Produk</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
