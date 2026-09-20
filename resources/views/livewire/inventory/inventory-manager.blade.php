<div>
    <x-alert />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Inventaris & Mutasi Stok</h1>
            <p class="text-xs text-slate-500 mt-1">Pemantauan stok barang dan riwayat pergerakan stok (PRD Section 37-41)</p>
        </div>

        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200 self-start sm:self-auto">
            <button type="button" wire:click="$set('tab', 'stocks')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
                :class="$wire.tab === 'stocks' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'">
                Stok Produk
            </button>
            <button type="button" wire:click="$set('tab', 'movements')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
                :class="$wire.tab === 'movements' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'">
                Riwayat Mutasi
            </button>
        </div>
    </div>

    @if($tab === 'stocks')
        <!-- Search Box -->
        <div class="mb-4 max-w-sm">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau SKU..."
                class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
        </div>

        <!-- Stock Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Nama Produk</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Stok Saat Ini</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($stocks as $product)
                            @php
                                $qty = $product->stocks->first()?->quantity ?? 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $product->sku }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->category->name }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-base {{ $qty > 10 ? 'text-emerald-700' : ($qty > 0 ? 'text-amber-700' : 'text-rose-700') }}">
                                    {{ $qty }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($qty > 10)
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 text-[10px] font-bold">Aman</span>
                                    @elseif($qty > 0)
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-300 text-[10px] font-bold">Menipis</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-300 text-[10px] font-bold">Habis</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="openAdjustModal({{ $product->id }})" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-emerald-800 border border-slate-200 text-xs font-bold transition active:scale-95">
                                        Sesuaikan Stok
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">Tidak ada produk ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200 bg-white">
                {{ $stocks->links() }}
            </div>
        </div>
    @else
        <!-- Movements Table -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Jenis Mutasi</th>
                            <th class="px-4 py-3">Jumlah Perubahan</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($movements as $m)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 text-slate-500 font-mono text-[11px]">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $m->product->name ?? 'Produk' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $m->type->value === 'SALE' ? 'bg-rose-50 text-rose-800 border border-rose-300' : 'bg-emerald-50 text-emerald-800 border border-emerald-300' }}">
                                        {{ $m->type->value }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold {{ $m->quantity > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}
                                </td>
                                <td class="px-4 py-3 text-slate-500 truncate max-w-xs">{{ $m->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada mutasi stok tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Stock Adjustment Modal -->
    @if($isAdjustModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-xl p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-900">Sesuaikan Stok Produk</h3>
                    <button wire:click="$set('isAdjustModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit="saveAdjustment" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tipe Penyesuaian</label>
                        <select wire:model="movementType" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="ADJUSTMENT">Penyesuaian (Adjustment)</option>
                            <option value="PURCHASE">Barang Masuk / Pembelian (Purchase)</option>
                            <option value="RETURN">Retur Barang (Return)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jumlah Perubahan (+ atau -)</label>
                        <input type="number" wire:model="quantityChange" placeholder="Contoh: 10 atau -5" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('quantityChange') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        <p class="text-[11px] text-slate-500 mt-1">Masukkan angka positif untuk menambah stok, atau angka negatif untuk mengurangi stok.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Keterangan / Alasan</label>
                        <input type="text" wire:model="notes" placeholder="Contoh: Stok opname berkala" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end gap-2">
                        <button type="button" wire:click="$set('isAdjustModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-xs">Simpan Penyesuaian</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
