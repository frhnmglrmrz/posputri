@extends('layouts.app')

@section('content')
<div x-data="createPosComponent({
    outletId: {{ auth()->user()->outlet_id ?? 1 }},
    outletName: '{{ auth()->user()->outlet->name ?? 'Outlet Utama' }}',
    cashierId: {{ auth()->id() }},
    cashierName: '{{ auth()->user()->name }}',
    shiftId: {{ \App\Models\Shift::where('cashier_id', auth()->id())->where('status', 'open')->value('id') ?? 'null' }}
})" class="flex flex-col lg:flex-row gap-5 h-[calc(100vh-7rem)] select-none">

    <!-- LEFT PANEL: Clean Touch Product Catalog & Barcode Scanner (Square POS Standard) -->
    <div class="flex-1 flex flex-col min-w-0 bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        
        <!-- Search & Barcode Input Bar -->
        <div class="p-3.5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <input type="text"
                    id="pos-barcode-input"
                    x-model="searchQuery"
                    @input="filterProducts()"
                    placeholder="Cari produk berdasarkan nama, SKU, atau scan barcode scanner USB..."
                    class="w-full pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-medium transition shadow-2xs">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Sync & Local Status Chips -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                    @click="window.posSyncEngine && window.posSyncEngine.syncPendingTransactions()"
                    class="px-3 py-1.5 rounded-xl border text-xs font-mono font-medium flex items-center gap-2 transition active:scale-95 shadow-2xs"
                    :class="pendingSyncCount > 0 ? 'bg-amber-50 border-amber-300 text-amber-900 hover:bg-amber-100' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50'">
                    <span class="w-2 h-2 rounded-full" :class="isSyncing ? 'bg-emerald-600 animate-spin' : (pendingSyncCount > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-600')"></span>
                    <span x-text="isSyncing ? 'Menyinkronkan...' : (pendingSyncCount > 0 ? pendingSyncCount + ' Tertunda' : 'Sync 100%')"></span>
                </button>
            </div>
        </div>

        <!-- Categories Filter Pills -->
        <div class="px-3.5 py-2.5 border-b border-slate-200 bg-white flex items-center gap-1.5 overflow-x-auto no-scrollbar">
            <button type="button"
                @click="selectCategory('all')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition active:scale-95 shadow-2xs"
                :class="selectedCategory === 'all' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'">
                Semua Produk
            </button>
            <template x-for="category in categories" :key="category.id">
                <button type="button"
                    @click="selectCategory(category.id)"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition active:scale-95 shadow-2xs"
                    :class="selectedCategory === category.id ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    x-text="category.name">
                </button>
            </template>
        </div>

        <!-- Product Grid (Sub-30ms IndexedDB Local Rendering) -->
        <div class="flex-1 p-3.5 overflow-y-auto bg-slate-50/50">
            <div x-show="isLoading" class="flex items-center justify-center h-48 text-slate-500 text-xs font-mono">
                Memuat katalog lokal IndexedDB...
            </div>

            <div x-show="!isLoading && filteredProducts.length === 0" class="flex flex-col items-center justify-center h-48 text-slate-500 text-xs">
                <p>Tidak ada produk yang cocok dengan pencarian.</p>
            </div>

            <div x-show="!isLoading && filteredProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="addToCart(product)"
                        class="p-3.5 rounded-2xl bg-white border border-slate-200 hover:border-emerald-600 hover:shadow-md cursor-pointer transition active:scale-[0.98] flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mb-1.5">
                                <span x-text="product.sku" class="truncate max-w-[80px]"></span>
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 font-semibold" x-text="'Stok: ' + (product.stock ?? 0)"></span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 line-clamp-2 transition leading-snug" x-text="product.name"></h4>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-700 font-mono tabular-nums" x-text="formatMoney(product.selling_price)"></span>
                            <span class="p-1 rounded-lg bg-slate-100 group-hover:bg-emerald-600 group-hover:text-white text-slate-600 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: HIGH DENSITY CART & TACTILE CHECKOUT -->
    <div class="w-full lg:w-96 flex flex-col bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <!-- Cart Header -->
        <div class="p-3.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-slate-900 text-sm">Pesanan Kasir</h3>
                <span class="px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-emerald-100 text-emerald-800 border border-emerald-200" x-text="cart.reduce((a, b) => a + b.quantity, 0)"></span>
            </div>
            <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-600 hover:text-rose-700 font-semibold transition">
                Kosongkan
            </button>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 p-3.5 overflow-y-auto space-y-2">
            <template x-if="cart.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-slate-400 text-xs py-12">
                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="font-semibold text-slate-600">Keranjang Kosong</span>
                    <span class="text-[11px] text-slate-400 mt-0.5">Sentuh produk di kiri atau scan barcode</span>
                </div>
            </template>

            <template x-for="item in cart" :key="item.product_id">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-1.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate" x-text="item.name"></h4>
                            <div class="text-[11px] text-slate-500 font-mono tabular-nums" x-text="formatMoney(item.price)"></div>
                        </div>
                        <button type="button" @click="removeFromCart(item)" class="text-slate-400 hover:text-rose-600 p-0.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between pt-1.5 border-t border-slate-200">
                        <div class="flex items-center gap-1 bg-white rounded-lg border border-slate-300 p-0.5 shadow-2xs">
                            <button type="button" @click="decreaseQty(item)" class="w-6 h-6 rounded flex items-center justify-center text-slate-700 hover:bg-slate-100 text-xs font-bold active:scale-95">-</button>
                            <span class="w-7 text-center text-xs font-bold text-slate-900 font-mono tabular-nums" x-text="item.quantity"></span>
                            <button type="button" @click="increaseQty(item)" class="w-6 h-6 rounded flex items-center justify-center text-slate-700 hover:bg-slate-100 text-xs font-bold active:scale-95">+</button>
                        </div>
                        <span class="text-xs font-bold text-emerald-700 font-mono tabular-nums" x-text="formatMoney(item.subtotal)"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Cart Summary & Checkout Button (Transparent Breakdown) -->
        <div class="p-4 border-t border-slate-200 bg-white space-y-2.5">
            <div class="space-y-1 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-mono tabular-nums text-slate-900 font-medium" x-text="formatMoney(subtotal)"></span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Diskon</span>
                    <span class="font-mono tabular-nums text-rose-600 font-medium" x-text="'-' + formatMoney(totalDiscount)"></span>
                </div>
                <div class="flex justify-between text-xs text-slate-600">
                    <span>PPN (11% Transparan)</span>
                    <span class="font-mono tabular-nums text-slate-900 font-medium" x-text="formatMoney(Math.round(subtotal * 0.11))"></span>
                </div>
                <div class="flex justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-200">
                    <span>TOTAL BAYAR</span>
                    <span class="font-mono tabular-nums text-emerald-700 text-lg font-extrabold" x-text="formatMoney(total)"></span>
                </div>
            </div>

            <button type="button"
                @click="openCheckout()"
                :disabled="cart.length === 0"
                class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-extrabold text-sm shadow-xs transition flex items-center justify-center gap-2 active:scale-98">
                <span>SELESAIKAN PEMBAYARAN</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>

    <!-- CHECKOUT MODAL: CRISP, TACTILE CASH TENDER -->
    <div x-show="isCheckoutOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div @click.away="closeCheckout()"
            class="w-full max-w-lg bg-white border border-slate-200 rounded-3xl shadow-2xl p-6 space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">Pembayaran Kasir</h3>
                <button type="button" @click="closeCheckout()" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Total Amount Card -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                <div class="text-xs text-slate-500 uppercase font-mono tracking-wider mb-1 font-semibold">Total Tagihan</div>
                <div class="text-3xl font-extrabold text-emerald-700 font-mono tabular-nums" x-text="formatMoney(total)"></div>
            </div>

            <!-- Select Payment Method -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Pilih Metode Pembayaran</label>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="method in selectablePaymentMethods" :key="method.code">
                        <button type="button"
                            @click="selectedPaymentMethod = method.code"
                            class="p-3 rounded-xl border text-center transition active:scale-95 shadow-2xs"
                            :class="selectedPaymentMethod === method.code ? 'bg-emerald-600 border-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                            <span class="text-xs truncate block font-semibold" x-text="method.name"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Cash Input Field & Quick Amounts -->
            <div x-show="selectedPaymentMethod === 'cash'" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Uang Diterima dari Pelanggan (Rp)</label>
                    <input type="number"
                        x-model="paidAmount"
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono tabular-nums text-xl font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                </div>

                <div class="flex flex-wrap gap-1.5">
                    <button type="button" @click="setExactPayment()" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-bold text-emerald-700 active:scale-95">
                        Uang Pas
                    </button>
                    <template x-for="amt in quickCashAmounts" :key="amt">
                        <button type="button" @click="paidAmount = amt" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-mono text-slate-700 active:scale-95 font-semibold" x-text="formatMoney(amt)">
                        </button>
                    </template>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex justify-between items-center text-sm">
                    <span class="text-slate-500 text-xs uppercase font-mono font-semibold">Kembalian:</span>
                    <span class="font-bold font-mono tabular-nums text-lg" :class="changeAmount >= 0 ? 'text-slate-900' : 'text-rose-600'" x-text="formatMoney(changeAmount)"></span>
                </div>
            </div>

            <!-- Submit Action Buttons -->
            <div class="pt-3 border-t border-slate-200 flex gap-3">
                <button type="button" @click="closeCheckout()" class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </button>
                <button type="button" @click="confirmCheckout()" class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5 active:scale-98">
                    <span>Selesaikan Transaksi</span>
                </button>
            </div>
        </div>
    </div>

    <!-- THERMAL RECEIPT MODAL (Authentic 58mm Receipt Preview) -->
    <div x-show="isReceiptOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-sm bg-white text-slate-900 rounded-3xl shadow-2xl p-6 space-y-4 font-mono text-xs border border-slate-200">
            <!-- Thermal Printable Container -->
            <div id="thermal-receipt" class="space-y-3">
                <div class="text-center pb-2 border-b border-dashed border-slate-300">
                    <h2 class="font-bold text-sm uppercase text-slate-900" x-text="currentReceipt?.outletName"></h2>
                    <p class="text-[10px] text-slate-500" x-text="currentReceipt?.outletAddress"></p>
                </div>

                <div class="text-[10px] space-y-0.5 border-b border-dashed border-slate-300 pb-2">
                    <div class="flex justify-between">
                        <span>No Transaksi:</span>
                        <span class="font-bold text-slate-900" x-text="currentReceipt?.transaction_number"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Waktu:</span>
                        <span x-text="new Date(currentReceipt?.transaction_at).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Kasir:</span>
                        <span x-text="currentReceipt?.cashierName"></span>
                    </div>
                </div>

                <!-- Items List -->
                <div class="space-y-1.5 border-b border-dashed border-slate-300 pb-2">
                    <template x-for="item in currentReceipt?.items || []" :key="item.uuid">
                        <div>
                            <div class="font-bold truncate text-slate-900" x-text="item.product_name"></div>
                            <div class="flex justify-between text-[10px] text-slate-600">
                                <span x-text="item.quantity + ' x ' + formatMoney(item.price)"></span>
                                <span class="font-bold text-slate-900" x-text="formatMoney(item.subtotal)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Totals & Payment -->
                <div class="space-y-1 text-[11px] pt-1 border-b border-dashed border-slate-300 pb-2">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span x-text="formatMoney(currentReceipt?.subtotal)"></span>
                    </div>
                    <template x-if="currentReceipt?.discount > 0">
                        <div class="flex justify-between text-rose-600">
                            <span>Diskon:</span>
                            <span x-text="'-' + formatMoney(currentReceipt?.discount)"></span>
                        </div>
                    </template>
                    <div class="flex justify-between font-bold text-sm pt-1 text-slate-900">
                        <span>TOTAL:</span>
                        <span x-text="formatMoney(currentReceipt?.total)"></span>
                    </div>
                    <div class="flex justify-between text-slate-600 pt-1">
                        <span>Bayar:</span>
                        <span x-text="formatMoney(currentReceipt?.paid_amount)"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Kembalian:</span>
                        <span x-text="formatMoney(currentReceipt?.change_amount)"></span>
                    </div>
                </div>

                <div class="text-center text-[10px] text-slate-500 pt-1">
                    <p>Terima kasih atas kunjungan Anda!</p>
                    <p class="text-[9px] text-slate-400 mt-1">POS Putri — Offline-First System</p>
                </div>
            </div>

            <!-- Receipt Actions -->
            <div class="pt-2 flex gap-2">
                <button type="button" @click="printReceipt()" class="flex-1 py-2.5 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition flex items-center justify-center gap-1.5 active:scale-95 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Struk</span>
                </button>
                <button type="button" @click="closeReceipt()" class="py-2.5 px-3 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition active:scale-95 border border-slate-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
