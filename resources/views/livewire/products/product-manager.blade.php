<div>
    <x-alert />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Katalog Produk</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data produk, harga jual, dan stok untuk POS</p>
        </div>
        @hasanyrole('Admin|Supervisor|Inventory')
        <button wire:click="openCreateModal"
            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 self-start sm:self-auto active:scale-98">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Produk</span>
        </button>
        @endhasanyrole
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
                        <th class="px-3 py-3 w-14 text-center">Foto</th>
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
                            <td class="px-3 py-3 text-center">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" class="w-10 h-10 object-cover rounded-lg border border-slate-200 mx-auto shadow-2xs">
                            </td>
                            <td class="px-4 py-3 font-mono">
                                <div class="font-bold text-slate-900">{{ $product->sku }}</div>
                                @if($product->barcode)
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $product->barcode }}</div>
                                @endif
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
                            <td class="px-4 py-3 text-right">
                                @hasanyrole('Admin|Supervisor|Inventory')
                                    <div class="space-x-2">
                                        <button wire:click="edit({{ $product->id }})" class="text-emerald-700 hover:text-emerald-900 font-bold">Edit</button>
                                        <button wire:click="delete({{ $product->id }})" wire:confirm="Hapus produk ini?" class="text-rose-600 hover:text-rose-800 font-medium">Hapus</button>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium text-[11px]">Lihat Saja</span>
                                @endhasanyrole
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">Tidak ada produk ditemukan.</td>
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

                <form wire:submit="save"
                    x-data="{
                        isUploading: false,
                        progress: 0,
                        uploadError: null,
                        clientPreviewUrl: null,
                        compressAndUpload(event) {
                            const file = event.target.files[0];
                            if (!file) return;

                            this.uploadError = null;

                            if (!file.type.match(/^image\/(jpeg|png|webp|jpg)$/i)) {
                                this.uploadError = 'Format file tidak didukung. Harap pilih gambar JPG, PNG, atau WebP.';
                                event.target.value = '';
                                return;
                            }

                            // Show instant preview immediately
                            try {
                                this.clientPreviewUrl = URL.createObjectURL(file);
                            } catch (e) {}

                            this.isUploading = true;
                            this.progress = 15;

                            // Compress client-side using native HTML5 Canvas
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const img = new Image();
                                img.onload = () => {
                                    const maxDim = 1024;
                                    let width = img.width;
                                    let height = img.height;
                                    if (width > maxDim || height > maxDim) {
                                        if (width > height) {
                                            height = Math.round((height * maxDim) / width);
                                            width = maxDim;
                                        } else {
                                            width = Math.round((width * maxDim) / height);
                                            height = maxDim;
                                        }
                                    }
                                    const canvas = document.createElement('canvas');
                                    canvas.width = width;
                                    canvas.height = height;
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(img, 0, 0, width, height);

                                    canvas.toBlob((blob) => {
                                        if (blob) {
                                            const finalFile = new File([blob], (file.name || 'product.jpg').replace(/\.[^/.]+$/, '') + '.jpg', { type: 'image/jpeg' });
                                            this.progress = 40;
                                            @this.upload('imageFile', finalFile,
                                                () => { this.isUploading = false; this.progress = 100; },
                                                () => { this.isUploading = false; this.uploadError = 'Gagal mengunggah foto. Pastikan ukuran file wajar.'; },
                                                (p) => { this.progress = p.detail?.progress || 60; }
                                            );
                                        } else {
                                            @this.upload('imageFile', file,
                                                () => { this.isUploading = false; this.progress = 100; },
                                                () => { this.isUploading = false; this.uploadError = 'Gagal mengunggah foto.'; },
                                                (p) => { this.progress = p.detail?.progress || 60; }
                                            );
                                        }
                                    }, 'image/jpeg', 0.85);
                                };
                                img.onerror = () => {
                                    @this.upload('imageFile', file,
                                        () => { this.isUploading = false; },
                                        () => { this.isUploading = false; this.uploadError = 'Gagal memproses gambar.'; }
                                    );
                                };
                                img.src = e.target.result;
                            };
                            reader.onerror = () => {
                                this.isUploading = false;
                                this.uploadError = 'Gagal membaca berkas gambar.';
                            };
                            reader.readAsDataURL(file);
                        }
                    }"
                    class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Foto Produk</label>
                        <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="relative w-16 h-16 rounded-lg border border-slate-200 overflow-hidden bg-white flex items-center justify-center shrink-0 shadow-2xs">
                                <template x-if="clientPreviewUrl">
                                    <img :src="clientPreviewUrl" alt="Preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!clientPreviewUrl">
                                    <div class="w-full h-full flex items-center justify-center">
                                        @if($imageFile)
                                            @php
                                                try {
                                                    $previewUrl = method_exists($imageFile, 'temporaryUrl') ? $imageFile->temporaryUrl() : null;
                                                } catch (\Throwable $e) {
                                                    $previewUrl = null;
                                                }
                                            @endphp
                                            @if($previewUrl)
                                                <img src="{{ $previewUrl }}" alt="Preview" class="w-full h-full object-cover">
                                            @else
                                                <img src="{{ asset('images/product-placeholder.svg') }}" alt="Placeholder" class="w-full h-full object-cover opacity-60">
                                            @endif
                                        @elseif($currentImage)
                                            <img src="{{ asset('storage/' . $currentImage) }}" alt="Current" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/product-placeholder.svg') }}" alt="Placeholder" class="w-full h-full object-cover opacity-60">
                                        @endif
                                    </div>
                                </template>
                                <div x-show="isUploading" x-cloak class="absolute inset-0 bg-slate-900/70 flex flex-col items-center justify-center text-white">
                                    <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mb-0.5"></div>
                                    <span class="text-[9px] font-mono font-bold" x-text="progress + '%'"></span>
                                </div>
                            </div>
                            <div class="flex-1 space-y-1">
                                <input type="file"
                                    @change="compressAndUpload($event)"
                                    accept="image/png,image/jpeg,image/webp"
                                    class="block w-full text-xs text-slate-500 file:mr-2.5 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                                <div class="flex items-center justify-between text-[10px] text-slate-400">
                                    <span>Format: JPG, PNG, WebP (Otomatis dikompres)</span>
                                    <button type="button" x-show="clientPreviewUrl || '{{ $imageFile || $currentImage }}'" @click="clientPreviewUrl = null; $wire.removeCurrentImage()" class="text-rose-600 hover:text-rose-800 font-semibold underline">Hapus Foto</button>
                                </div>
                                <template x-if="uploadError">
                                    <span class="text-rose-600 text-xs block font-medium" x-text="uploadError"></span>
                                </template>
                                @error('imageFile') <span class="text-rose-600 text-xs block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

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

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kode Barcode (Opsional)</label>
                        <input type="text" wire:model="barcode" placeholder="Scan atau ketik kode barcode (misal: 8990000000001)..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('barcode') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
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
                        <button type="submit"
                            :disabled="isUploading"
                            wire:loading.attr="disabled"
                            wire:target="save,imageFile"
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold shadow-xs flex items-center gap-1.5 transition">
                            <span wire:loading wire:target="save" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span x-show="isUploading">Mengunggah Foto...</span>
                            <span x-show="!isUploading">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
