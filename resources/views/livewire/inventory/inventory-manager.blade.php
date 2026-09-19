<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-white tracking-tight">Inventaris & Mutasi Stok</h1>
            <p class="text-xs text-slate-400 mt-1">Pemantauan stok barang dan riwayat pergerakan stok (PRD Section 37-41)</p>
        </div>

        <div class="flex items-center gap-2 bg-slate-900 p-1 rounded-xl border border-slate-800 self-start sm:self-auto">
            <button type="button" wire:click="$set('tab', 'stocks')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
                :class="$wire.tab === 'stocks' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'">
                Stok Produk
            </button>
            <button type="button" wire:click="$set('tab', 'movements')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
                :class="$wire.tab === 'movements' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'">
                Riwayat Mutasi
            </button>
        </div>
    </div>

    @if($tab === 'stocks')
        <!-- Search Box -->
        <div class="mb-4 max-w-sm">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau SKU..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <!-- Stock Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Nama Produk</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Stok Saat Ini</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($stocks as $product)
                            @php
                                $qty = $product->stocks->first()?->quantity ?? 0;
                            @endphp
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 font-mono text-slate-400">{{ $product->sku }}</td>
                                <td class="px-4 py-3 font-semibold text-white">{{ $product->name }}</td>
                                <td class="px-4 py-3">{{ $product->category->name }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-base {{ $qty > 10 ? 'text-emerald-400' : ($qty > 0 ? 'text-amber-400' : 'text-rose-400') }}">
                                    {{ $qty }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($qty > 10)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold">Aman</span>
                                    @elseif($qty > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-bold">Menipis</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold">Habis</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="openAdjustModal({{ $product->id }})" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-indigo-300 text-xs font-semibold transition">
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
            <div class="p-4 border-t border-slate-800">
                {{ $stocks->links() }}
            </div>
        </div>
    @else
        <!-- Movements Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Jenis Mutasi</th>
                            <th class="px-4 py-3">Jumlah Perubahan</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($movements as $m)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 text-slate-400 font-mono text-[11px]">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-semibold text-white">{{ $m->product->name ?? 'Produk' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->type->value === 'SALE' ? 'bg-rose-500/10 text-rose-400' : 'bg-emerald-500/10 text-emerald-400' }}">
                                        {{ $m->type->value }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold {{ $m->quantity > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}
                                </td>
                                <td class="px-4 py-3 text-slate-400 truncate max-w-xs">{{ $m->notes ?: '-' }}</td>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white">Sesuaikan Stok Produk</h3>
                    <button wire:click="$set('isAdjustModalOpen', false)" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit="saveAdjustment" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Tipe Penyesuaian</label>
                        <select wire:model="movementType" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="ADJUSTMENT">Penyesuaian (Adjustment)</option>
                            <option value="PURCHASE">Barang Masuk / Pembelian (Purchase)</option>
                            <option value="RETURN">Retur Barang (Return)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Jumlah Perubahan (+ atau -)</label>
                        <input type="number" wire:model="quantityChange" placeholder="Contoh: 10 atau -5" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('quantityChange') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        <p class="text-[11px] text-slate-500 mt-1">Masukkan angka positif untuk menambah stok, atau angka negatif untuk mengurangi stok.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Keterangan / Alasan</label>
                        <input type="text" wire:model="notes" placeholder="Contoh: Stok opname berkala" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                        <button type="button" wire:click="$set('isAdjustModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow-md shadow-indigo-600/30">Simpan Penyesuaian</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
