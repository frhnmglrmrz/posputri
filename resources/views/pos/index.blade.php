@extends('layouts.app')

@section('content')
<div x-data="createPosComponent({
    outletId: {{ auth()->user()->outlet_id ?? 1 }},
    outletName: '{{ auth()->user()->outlet->name ?? 'Outlet Utama' }}',
    cashierId: {{ auth()->id() }},
    cashierName: '{{ auth()->user()->name }}',
    shiftId: {{ \App\Models\Shift::where('cashier_id', auth()->id())->where('status', 'open')->value('id') ?? 'null' }}
})" class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-7rem)] select-none">

    <!-- LEFT PANEL: Category Filter, Search Bar & Product Grid -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        
        <!-- Search & Barcode Input Bar -->
        <div class="p-4 border-b border-slate-800 bg-slate-900/90 flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <input type="text"
                    id="pos-barcode-input"
                    x-model="searchQuery"
                    @input="filterProducts()"
                    placeholder="Cari produk berdasarkan nama, SKU, atau scan barcode USB..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <div class="absolute left-3.5 top-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Sync & Local Status Chips -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                    @click="window.posSyncEngine && window.posSyncEngine.syncPendingTransactions()"
                    class="px-3 py-1.5 rounded-xl border text-xs font-semibold flex items-center gap-2 transition"
                    :class="pendingSyncCount > 0 ? 'bg-amber-950/60 border-amber-800/80 text-amber-300 hover:bg-amber-900/60' : 'bg-slate-800/60 border-slate-700 text-slate-400'">
                    <span class="w-2 h-2 rounded-full" :class="isSyncing ? 'bg-indigo-400 animate-spin' : (pendingSyncCount > 0 ? 'bg-amber-400' : 'bg-emerald-400')"></span>
                    <span x-text="isSyncing ? 'Sinkronisasi...' : (pendingSyncCount > 0 ? pendingSyncCount + ' Pending Sync' : 'Semua Tersinkron')"></span>
                </button>
            </div>
        </div>

        <!-- Categories Filter Bar -->
        <div class="px-4 py-2.5 border-b border-slate-800/80 bg-slate-950/40 flex items-center gap-2 overflow-x-auto no-scrollbar">
            <button type="button"
                @click="selectCategory('all')"
                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition"
                :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'">
                Semua Produk
            </button>
            <template x-for="category in categories" :key="category.id">
                <button type="button"
                    @click="selectCategory(category.id)"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition"
                    :class="selectedCategory === category.id ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                    x-text="category.name">
                </button>
            </template>
        </div>

        <!-- Product Grid (Local IndexedDB Fast Rendering < 100ms) -->
        <div class="flex-1 p-4 overflow-y-auto">
            <div x-show="isLoading" class="flex items-center justify-center h-48 text-slate-400 text-sm">
                Memuat produk lokal...
            </div>

            <div x-show="!isLoading && filteredProducts.length === 0" class="flex flex-col items-center justify-center h-48 text-slate-400 text-sm">
                <p>Tidak ada produk yang cocok dengan pencarian.</p>
            </div>

            <div x-show="!isLoading && filteredProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="addToCart(product)"
                        class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/90 hover:border-indigo-500/60 hover:bg-slate-950 cursor-pointer transition active:scale-[0.98] flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono mb-1">
                                <span x-text="product.sku"></span>
                                <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300" x-text="'Stok: ' + (product.stock ?? 0)"></span>
                            </div>
                            <h4 class="text-xs font-semibold text-slate-100 group-hover:text-indigo-300 line-clamp-2 transition leading-snug" x-text="product.name"></h4>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-400 font-mono" x-text="formatMoney(product.selling_price)"></span>
                            <span class="p-1 rounded-lg bg-slate-800 group-hover:bg-indigo-600 text-white transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: CART & CHECKOUT -->
    <div class="w-full lg:w-96 flex flex-col bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <!-- Cart Header -->
        <div class="p-4 border-b border-slate-800 bg-slate-900/90 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-white text-sm">Keranjang Belanja</h3>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300" x-text="cart.reduce((a, b) => a + b.quantity, 0)"></span>
            </div>
            <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-400 hover:text-rose-300 font-medium">
                Kosongkan
            </button>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 p-4 overflow-y-auto space-y-3">
            <template x-if="cart.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-slate-500 text-xs py-12">
                    <svg class="w-12 h-12 mb-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Keranjang kosong</span>
                    <span class="text-[11px] text-slate-600 mt-1">Pilih produk atau scan barcode</span>
                </div>
            </template>

            <template x-for="item in cart" :key="item.product_id">
                <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/90 flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-medium text-slate-200 truncate" x-text="item.name"></h4>
                            <div class="text-[11px] text-slate-400 font-mono" x-text="formatMoney(item.price)"></div>
                        </div>
                        <button type="button" @click="removeFromCart(item)" class="text-slate-500 hover:text-rose-400 p-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-slate-800/60">
                        <div class="flex items-center gap-1.5 bg-slate-900 rounded-lg border border-slate-800 p-0.5">
                            <button type="button" @click="decreaseQty(item)" class="w-6 h-6 rounded flex items-center justify-center text-slate-300 hover:bg-slate-800 text-xs font-bold">-</button>
                            <span class="w-8 text-center text-xs font-bold text-white font-mono" x-text="item.quantity"></span>
                            <button type="button" @click="increaseQty(item)" class="w-6 h-6 rounded flex items-center justify-center text-slate-300 hover:bg-slate-800 text-xs font-bold">+</button>
                        </div>
                        <span class="text-xs font-bold text-emerald-400 font-mono" x-text="formatMoney(item.subtotal)"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Cart Summary & Checkout Button -->
        <div class="p-4 border-t border-slate-800 bg-slate-900/90 space-y-3">
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-400">
                    <span>Subtotal</span>
                    <span class="font-mono text-slate-200" x-text="formatMoney(subtotal)"></span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Diskon</span>
                    <span class="font-mono text-rose-400" x-text="'-' + formatMoney(totalDiscount)"></span>
                </div>
                <div class="flex justify-between text-sm font-bold text-white pt-2 border-t border-slate-800">
                    <span>TOTAL</span>
                    <span class="font-mono text-emerald-400 text-base" x-text="formatMoney(total)"></span>
                </div>
            </div>

            <button type="button"
                @click="openCheckout()"
                :disabled="cart.length === 0"
                class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 disabled:bg-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                <span>BAYAR / CHECKOUT</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>

    <!-- CHECKOUT MODAL -->
    <div x-show="isCheckoutOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div @click.away="closeCheckout()"
            class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-lg font-bold text-white">Konfirmasi Pembayaran</h3>
                <button type="button" @click="closeCheckout()" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Total Amount Card -->
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-center">
                <div class="text-xs text-slate-400 uppercase tracking-wider mb-1">Total Tagihan</div>
                <div class="text-3xl font-extrabold text-emerald-400 font-mono" x-text="formatMoney(total)"></div>
            </div>

            <!-- Select Payment Method (PRD Section 22: online-only disabled offline) -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="method in selectablePaymentMethods" :key="method.code">
                        <button type="button"
                            @click="selectedPaymentMethod = method.code"
                            class="p-2.5 rounded-xl border text-center transition"
                            :class="selectedPaymentMethod === method.code ? 'bg-indigo-600 border-indigo-500 text-white font-bold' : 'bg-slate-950 border-slate-800 text-slate-300 hover:bg-slate-800'">
                            <span class="text-xs truncate block" x-text="method.name"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Cash Input Field & Quick Amounts -->
            <div x-show="selectedPaymentMethod === 'cash'" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Uang Diterima (Rp)</label>
                    <input type="number"
                        x-model="paidAmount"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-lg font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex flex-wrap gap-1.5">
                    <button type="button" @click="setExactPayment()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-indigo-300">
                        Uang Pas
                    </button>
                    <template x-for="amt in quickCashAmounts" :key="amt">
                        <button type="button" @click="paidAmount = amt" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300" x-text="formatMoney(amt)">
                        </button>
                    </template>
                </div>

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center text-sm">
                    <span class="text-slate-400">Kembalian:</span>
                    <span class="font-bold font-mono text-base" :class="changeAmount >= 0 ? 'text-amber-400' : 'text-rose-400'" x-text="formatMoney(changeAmount)"></span>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-t border-slate-800 flex gap-3">
                <button type="button" @click="closeCheckout()" class="flex-1 py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm transition">
                    Batal
                </button>
                <button type="button" @click="confirmCheckout()" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <span>SELESAIKAN (SIMPAN)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- THERMAL RECEIPT MODAL (PRD Section 44, 45: Printable directly from IndexedDB) -->
    <div x-show="isReceiptOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div class="w-full max-w-sm bg-white text-slate-900 rounded-2xl shadow-2xl p-6 space-y-4 font-mono text-xs">
            <!-- Thermal Printable Container -->
            <div id="thermal-receipt" class="space-y-3">
                <div class="text-center pb-2 border-b border-dashed border-slate-400">
                    <h2 class="font-bold text-sm uppercase" x-text="currentReceipt?.outletName"></h2>
                    <p class="text-[10px] text-slate-600" x-text="currentReceipt?.outletAddress"></p>
                </div>

                <div class="text-[10px] space-y-0.5 border-b border-dashed border-slate-400 pb-2">
                    <div class="flex justify-between">
                        <span>No Transaksi:</span>
                        <span class="font-bold" x-text="currentReceipt?.transaction_number"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Waktu:</span>
                        <span x-text="new Date(currentReceipt?.transaction_at).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kasir:</span>
                        <span x-text="currentReceipt?.cashierName"></span>
                    </div>
                </div>

                <!-- Items List -->
                <div class="space-y-1.5 border-b border-dashed border-slate-400 pb-2">
                    <template x-for="item in currentReceipt?.items || []" :key="item.uuid">
                        <div>
                            <div class="font-bold truncate" x-text="item.product_name"></div>
                            <div class="flex justify-between text-[10px] text-slate-600">
                                <span x-text="item.quantity + ' x ' + formatMoney(item.price)"></span>
                                <span class="font-bold text-slate-900" x-text="formatMoney(item.subtotal)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Totals & Payment -->
                <div class="space-y-1 text-[11px] pt-1 border-b border-dashed border-slate-400 pb-2">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span x-text="formatMoney(currentReceipt?.subtotal)"></span>
                    </div>
                    <template x-if="currentReceipt?.discount > 0">
                        <div class="flex justify-between text-rose-600">
                            <span>Diskon:</span>
                            <span x-text="'-' + formatMoney(currentReceipt?.discount)"></span>
                        </div>
                    </template>
                    <div class="flex justify-between font-bold text-sm pt-1">
                        <span>TOTAL:</span>
                        <span x-text="formatMoney(currentReceipt?.total)"></span>
                    </div>
                    <div class="flex justify-between text-slate-700 pt-1">
                        <span>Bayar:</span>
                        <span x-text="formatMoney(currentReceipt?.paid_amount)"></span>
                    </div>
                    <div class="flex justify-between text-slate-700">
                        <span>Kembalian:</span>
                        <span x-text="formatMoney(currentReceipt?.change_amount)"></span>
                    </div>
                </div>

                <div class="text-center text-[10px] text-slate-500 pt-1">
                    <p>Terima kasih atas kunjungan Anda!</p>
                    <p class="text-[9px] text-slate-400 mt-1">Sistem POS Offline-First</p>
                </div>
            </div>

            <!-- Receipt Actions -->
            <div class="pt-2 flex gap-2">
                <button type="button" @click="printReceipt()" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Struk</span>
                </button>
                <button type="button" @click="closeReceipt()" class="py-2 px-3 rounded-xl bg-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
