import { db, saveOfflineTransaction } from './db';
import { initBarcodeScanner } from './scanner';

/**
 * Alpine.js POS component data factory.
 * PRD Section 17, 18, 20, 21, 22, 44, 45, 59.
 */
export function createPosComponent(config = {}) {
    return {
        outletId: config.outletId || 1,
        cashierId: config.cashierId || 1,
        cashierName: config.cashierName || 'Kasir',
        shiftId: config.shiftId || null,
        deviceUuid: config.deviceUuid || (localStorage.getItem('pos:device_uuid') || 'POS-DEVICE-01'),

        // Master data in memory (sourced from IndexedDB)
        products: [],
        categories: [],
        paymentMethods: [],
        filteredProducts: [],

        // UI State
        selectedCategory: 'all',
        searchQuery: '',
        isOnline: navigator.onLine,
        isSyncing: false,
        pendingSyncCount: 0,
        isLoading: true,

        // Cart State
        cart: [],
        cartDiscountAmount: 0,
        cartTaxPercent: 0,

        // Checkout Modal State
        isCheckoutOpen: false,
        selectedPaymentMethod: 'cash',
        paidAmount: 0,
        quickCashAmounts: [10000, 20000, 50000, 100000, 200000],
        paymentReference: '',

        // Receipt Modal State
        isReceiptOpen: false,
        currentReceipt: null,

        async init() {
            if (!localStorage.getItem('pos:device_uuid')) {
                localStorage.setItem('pos:device_uuid', 'DEVICE-' + crypto.randomUUID().slice(0, 8).toUpperCase());
            }
            this.deviceUuid = localStorage.getItem('pos:device_uuid');

            // 1. Load data from IndexedDB
            await this.loadLocalData();

            // 2. Setup Barcode Scanner
            initBarcodeScanner((barcode) => {
                this.handleBarcodeScanned(barcode);
            });

            // 3. Listen for Connectivity & Sync events
            window.addEventListener('pos:connectivity-change', (e) => {
                this.isOnline = e.detail.isOnline;
            });

            window.addEventListener('pos:sync-status-changed', (e) => {
                this.pendingSyncCount = e.detail.pendingCount;
                this.isSyncing = e.detail.isSyncing;
            });

            window.addEventListener('pos:master-data-hydrated', () => {
                this.loadLocalData();
            });

            window.addEventListener('pos:master-data-updated', () => {
                this.loadLocalData();
            });

            this.updatePendingCount();
        },

        async loadLocalData() {
            this.isLoading = true;
            try {
                this.categories = await db.categories.where('is_active').equals(1).toArray();
                this.products = await db.products.where('is_active').equals(1).toArray();
                this.paymentMethods = await db.payment_methods.where('is_active').equals(1).toArray();

                // If local database is empty and online, hydrate from bootstrap API
                if (this.products.length === 0 && this.isOnline) {
                    if (window.posSyncEngine) {
                        await window.posSyncEngine.bootstrapMasterData();
                        this.categories = await db.categories.where('is_active').equals(1).toArray();
                        this.products = await db.products.where('is_active').equals(1).toArray();
                        this.paymentMethods = await db.payment_methods.where('is_active').equals(1).toArray();
                    }
                }

                this.filterProducts();
            } finally {
                this.isLoading = false;
            }
        },

        async updatePendingCount() {
            try {
                this.pendingSyncCount = await db.sync_queue.where('status').equals('PENDING').count();
            } catch {
                this.pendingSyncCount = 0;
            }
        },

        filterProducts() {
            const query = this.searchQuery.trim().toLowerCase();
            const catId = this.selectedCategory;

            this.filteredProducts = this.products.filter((p) => {
                const matchCategory = (catId === 'all' || p.category_id === parseInt(catId));
                if (!matchCategory) return false;

                if (!query) return true;

                return (p.name && p.name.toLowerCase().includes(query))
                    || (p.sku && p.sku.toLowerCase().includes(query))
                    || (p.barcode && p.barcode.toLowerCase().includes(query));
            });
        },

        selectCategory(catId) {
            this.selectedCategory = catId;
            this.filterProducts();
        },

        handleBarcodeScanned(barcode) {
            const cleanBarcode = barcode.trim();
            const found = this.products.find(p => p.barcode === cleanBarcode || p.sku === cleanBarcode);
            if (found) {
                this.addToCart(found);
                this.showToast(`Produk ditambahkan: ${found.name}`);
            } else {
                this.showToast(`Barcode ${cleanBarcode} tidak ditemukan!`, 'error');
            }
        },

        // Cart Actions
        addToCart(product) {
            const existingIndex = this.cart.findIndex(item => item.product_id === product.id);

            if (existingIndex > -1) {
                this.cart[existingIndex].quantity += 1;
                this.recalculateItemSubtotal(this.cart[existingIndex]);
            } else {
                const item = {
                    product_id: product.id,
                    product_uuid: product.uuid,
                    sku: product.sku,
                    barcode: product.barcode,
                    name: product.name,
                    price: parseFloat(product.selling_price) || 0,
                    quantity: 1,
                    discount: 0,
                    tax: 0,
                    subtotal: parseFloat(product.selling_price) || 0,
                };
                this.cart.push(item);
            }
        },

        increaseQty(item) {
            item.quantity += 1;
            this.recalculateItemSubtotal(item);
        },

        decreaseQty(item) {
            if (item.quantity > 1) {
                item.quantity -= 1;
                this.recalculateItemSubtotal(item);
            } else {
                this.removeFromCart(item);
            }
        },

        removeFromCart(item) {
            const index = this.cart.indexOf(item);
            if (index > -1) {
                this.cart.splice(index, 1);
            }
        },

        clearCart() {
            this.cart = [];
            this.cartDiscountAmount = 0;
        },

        recalculateItemSubtotal(item) {
            item.subtotal = (item.price * item.quantity) - item.discount + item.tax;
        },

        // Cart Totals
        get subtotal() {
            return this.cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
        },

        get totalDiscount() {
            const itemDiscounts = this.cart.reduce((acc, item) => acc + (item.discount || 0), 0);
            return itemDiscounts + (parseFloat(this.cartDiscountAmount) || 0);
        },

        get total() {
            const t = this.subtotal - this.totalDiscount;
            return Math.max(0, t);
        },

        get changeAmount() {
            const paid = parseFloat(this.paidAmount) || 0;
            return Math.max(0, paid - this.total);
        },

        // Checkout & Offline Sync
        openCheckout() {
            if (!this.cart.length) return;
            this.paidAmount = this.total;
            this.isCheckoutOpen = true;
        },

        closeCheckout() {
            this.isCheckoutOpen = false;
        },

        setExactPayment() {
            this.paidAmount = this.total;
        },

        get selectablePaymentMethods() {
            if (!this.paymentMethods.length) {
                return [{ code: 'cash', name: 'Tunai', offline_available: true }];
            }
            if (this.isOnline) {
                return this.paymentMethods;
            }
            // PRD Section 22: Disable online-only payments when offline
            return this.paymentMethods.filter(pm => pm.offline_available);
        },

        async confirmCheckout() {
            if (this.cart.length === 0) return;

            const paid = parseFloat(this.paidAmount) || 0;
            if (this.selectedPaymentMethod === 'cash' && paid < this.total) {
                this.showToast('Jumlah uang yang dibayarkan kurang!', 'error');
                return;
            }

            const txUuid = crypto.randomUUID();
            const now = new Date();
            const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
            const randCode = Math.floor(1000 + Math.random() * 9000);
            const tempNumber = `POS-${dateStr}-${randCode}`;

            const transactionData = {
                uuid: txUuid,
                transaction_number: tempNumber,
                outlet_id: this.outletId,
                device_uuid: this.deviceUuid,
                cashier_id: this.cashierId,
                shift_id: this.shiftId,
                subtotal: this.subtotal,
                discount: this.totalDiscount,
                tax: 0,
                total: this.total,
                paid_amount: paid,
                change_amount: this.changeAmount,
                transaction_at: now.toISOString(),
                items: this.cart.map(item => ({
                    uuid: crypto.randomUUID(),
                    product_id: item.product_id,
                    product_uuid: item.product_uuid,
                    sku: item.sku,
                    product_name: item.name,
                    price: item.price,
                    quantity: item.quantity,
                    discount: item.discount,
                    tax: item.tax,
                    subtotal: item.subtotal,
                })),
                payments: [{
                    uuid: crypto.randomUUID(),
                    payment_method: this.selectedPaymentMethod,
                    amount: this.total,
                    reference: this.paymentReference || null,
                }],
            };

            // 1. Save Locally (Local First, PRD Section 2)
            await saveOfflineTransaction(transactionData);

            // 2. Setup current receipt for printing
            this.currentReceipt = {
                ...transactionData,
                cashierName: this.cashierName,
                outletName: config.outletName || 'Outlet Utama',
                outletAddress: config.outletAddress || 'Jl. Boulevard Raya No. 1, Jakarta',
            };

            this.closeCheckout();
            this.clearCart();
            this.isReceiptOpen = true;

            await this.updatePendingCount();

            // 3. Attempt Server Sync if online
            if (this.isOnline && window.posSyncEngine) {
                window.posSyncEngine.syncPendingTransactions();
            }
        },

        printReceipt() {
            window.print();
        },

        closeReceipt() {
            this.isReceiptOpen = false;
            this.currentReceipt = null;
        },

        showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 z-50 px-4 py-2.5 rounded-xl shadow-xl text-xs font-semibold text-white flex items-center gap-2 transition transform duration-300 ${
                type === 'error' ? 'bg-rose-600' : 'bg-emerald-600'
            }`;
            toast.innerHTML = `<span>${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(amount || 0);
        }
    };
}
