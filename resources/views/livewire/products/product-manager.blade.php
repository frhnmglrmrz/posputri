<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Katalog Produk</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data produk, harga jual, dan stok untuk POS</p>
        </div>
        <button wire:click="openCreateModal"
            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 self-start sm:self-auto active:scale-98">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Produk</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="flex-1 max-w-sm">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau SKU produk..."
                class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
        </div>
        <div class="w-48">
            <select wire:model.live="categoryFilter" class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Kode SKU</th>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Harga Beli</th>
                        <th class="px-4 py-3">Harga Jual</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono">
                                <div class="font-bold text-slate-900">{{ $product->sku }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $product->name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-medium">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">Rp{{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-emerald-700">Rp{{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ ($product->stocks->first()?->quantity ?? 0) > 10 ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-300' }}">
                                    {{ $product->stocks->first()?->quantity ?? 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($product->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 text-[10px] font-bold">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold">Non-aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button wire:click="edit({{ $product->id }})" class="text-emerald-700 hover:text-emerald-900 font-bold">Edit</button>
                                <button wire:click="delete({{ $product->id }})" wire:confirm="Hapus produk ini?" class="text-rose-600 hover:text-rose-800 font-medium">Hapus</button>
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
        <div class="p-4 border-t border-slate-200 bg-white">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="w-full max-w-lg bg-white border border-slate-200 rounded-2xl shadow-xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-900">{{ $productId ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kategori</label>
                        <select wire:model="category_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Produk</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('name') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Harga Beli (Rp)</label>
                            <input type="number" wire:model="purchase_price" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('purchase_price') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Harga Jual (Rp)</label>
                            <input type="number" wire:model="selling_price" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('selling_price') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if(!$productId)
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Stok Awal</label>
                        <input type="number" wire:model="initial_stock" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_active" id="prod_is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="prod_is_active" class="font-medium text-slate-700">Status Aktif</label>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end gap-2">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-xs">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
