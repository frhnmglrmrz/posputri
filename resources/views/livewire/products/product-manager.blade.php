<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-white tracking-tight">Katalog Produk</h1>
            <p class="text-xs text-slate-400 mt-1">Kelola data produk, harga jual, barcode, dan stok untuk POS</p>
        </div>
        <button wire:click="openCreateModal"
            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-600/20 transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Produk</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="flex-1 max-w-sm">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, SKU, atau barcode..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="w-48">
            <select wire:model.live="categoryFilter" class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">SKU / Barcode</th>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Harga Beli</th>
                        <th class="px-4 py-3">Harga Jual</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 font-mono">
                                <div class="font-bold text-slate-200">{{ $product->sku }}</div>
                                <div class="text-[10px] text-slate-500">{{ $product->barcode ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-white">{{ $product->name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full bg-slate-800 text-indigo-300 text-[10px] font-medium">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-400">Rp{{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-emerald-400">Rp{{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ ($product->stocks->first()?->quantity ?? 0) > 10 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                    {{ $product->stocks->first()?->quantity ?? 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($product->is_active)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-500 text-[10px] font-bold">Non-aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button wire:click="edit({{ $product->id }})" class="text-indigo-400 hover:text-indigo-300 font-medium">Edit</button>
                                <button wire:click="delete({{ $product->id }})" wire:confirm="Hapus produk ini?" class="text-rose-400 hover:text-rose-300 font-medium">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">Tidak ada produk ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white">{{ $productId ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Kategori</label>
                        <select wire:model="category_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Nama Produk</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">SKU</label>
                            <input type="text" wire:model="sku" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('sku') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Barcode (Opsional)</label>
                            <input type="text" wire:model="barcode" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('barcode') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Harga Beli (Rp)</label>
                            <input type="number" wire:model="purchase_price" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('purchase_price') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Harga Jual (Rp)</label>
                            <input type="number" wire:model="selling_price" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('selling_price') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if(!$productId)
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Stok Awal</label>
                        <input type="number" wire:model="initial_stock" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_active" id="prod_is_active" class="rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <label for="prod_is_active" class="font-medium text-slate-300">Status Aktif</label>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow-md shadow-indigo-600/30">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
