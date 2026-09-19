import Dexie from 'dexie';

export const db = new Dexie('PosPutriDB');

db.version(1).stores({
    products: '++id, uuid, barcode, sku, name, category_id, is_active',
    categories: '++id, uuid, slug, name, is_active',
    customers: '++id, uuid, phone, name',
    payment_methods: '++id, uuid, code, name, offline_available',
    transactions: '++id, uuid, transaction_number, status, transaction_at, synced_at',
    transaction_items: '++id, uuid, transaction_uuid, product_id',
    payments: '++id, uuid, transaction_uuid, payment_method',
    shifts: '++id, uuid, cashier_id, status',
    sync_queue: '++id, entity_type, entity_uuid, operation, status, retry_count, created_at',
    settings: 'key, value',
});

/**
 * Hydrate master data from server into IndexedDB.
 */
export async function saveBootstrapData(data) {
    await db.transaction('rw', [db.products, db.categories, db.payment_methods, db.customers, db.settings], async () => {
        if (data.categories?.length) {
            await db.categories.clear();
            await db.categories.bulkAdd(data.categories);
        }

        if (data.products?.length) {
            await db.products.clear();
            await db.products.bulkAdd(data.products);
        }

        if (data.payment_methods?.length) {
            await db.payment_methods.clear();
            await db.payment_methods.bulkAdd(data.payment_methods);
        }

        if (data.customers?.length) {
            await db.customers.clear();
            await db.customers.bulkAdd(data.customers);
        }

        if (data.settings) {
            await db.settings.clear();
            const settingEntries = Object.entries(data.settings).map(([key, value]) => ({ key, value }));
            await db.settings.bulkAdd(settingEntries);
        }
    });
}

/**
 * Save transaction atomically into IndexedDB and queue it for sync.
 * PRD Section 29: Local First atomic save.
 */
export async function saveOfflineTransaction(transactionData) {
    const uuid = transactionData.uuid || crypto.randomUUID();
    transactionData.uuid = uuid;

    return await db.transaction('rw', [db.transactions, db.transaction_items, db.payments, db.sync_queue, db.products], async () => {
        // 1. Insert Transaction
        await db.transactions.put({
            uuid: uuid,
            transaction_number: transactionData.transaction_number,
            outlet_id: transactionData.outlet_id,
            device_uuid: transactionData.device_uuid,
            cashier_id: transactionData.cashier_id,
            shift_id: transactionData.shift_id,
            customer_id: transactionData.customer_id,
            subtotal: transactionData.subtotal,
            discount: transactionData.discount,
            tax: transactionData.tax,
            total: transactionData.total,
            paid_amount: transactionData.paid_amount,
            change_amount: transactionData.change_amount,
            status: 'PENDING',
            transaction_at: transactionData.transaction_at || new Date().toISOString(),
            synced_at: null,
            notes: transactionData.notes || null,
        });

        // 2. Insert Items & Decrement Local Stock
        for (const item of transactionData.items || []) {
            const itemUuid = item.uuid || crypto.randomUUID();
            await db.transaction_items.put({
                uuid: itemUuid,
                transaction_uuid: uuid,
                product_id: item.product_id,
                sku: item.sku,
                product_name: item.product_name,
                price: item.price,
                quantity: item.quantity,
                discount: item.discount || 0,
                tax: item.tax || 0,
                subtotal: item.subtotal,
            });

            // Local stock reduction
            const localProduct = await db.products.where('uuid').equals(item.product_uuid || '').first()
                || await db.products.where('id').equals(item.product_id).first();
            if (localProduct && typeof localProduct.stock === 'number') {
                localProduct.stock -= item.quantity;
                await db.products.put(localProduct);
            }
        }

        // 3. Insert Payments
        for (const pay of transactionData.payments || []) {
            const payUuid = pay.uuid || crypto.randomUUID();
            await db.payments.put({
                uuid: payUuid,
                transaction_uuid: uuid,
                payment_method: pay.payment_method,
                amount: pay.amount,
                reference: pay.reference || null,
                status: 'PAID',
            });
        }

        // 4. Queue for Server Sync
        await db.sync_queue.put({
            entity_type: 'TRANSACTION',
            entity_uuid: uuid,
            operation: 'CREATE',
            payload: transactionData,
            status: 'PENDING',
            retry_count: 0,
            created_at: new Date().toISOString(),
        });

        return transactionData;
    });
}

/**
 * Get all pending items in sync queue.
 */
export async function getPendingSyncQueue() {
    return await db.sync_queue.where('status').equals('PENDING').toArray();
}

/**
 * Mark transaction as successfully synced.
 */
export async function markTransactionSynced(uuid, serverTransaction = null) {
    await db.transaction('rw', [db.transactions, db.sync_queue], async () => {
        const tx = await db.transactions.where('uuid').equals(uuid).first();
        if (tx) {
            tx.status = 'SYNCED';
            tx.synced_at = new Date().toISOString();
            if (serverTransaction?.transaction_number) {
                tx.transaction_number = serverTransaction.transaction_number;
            }
            await db.transactions.put(tx);
        }

        const queueItem = await db.sync_queue.where('entity_uuid').equals(uuid).first();
        if (queueItem) {
            queueItem.status = 'SYNCED';
            queueItem.updated_at = new Date().toISOString();
            await db.sync_queue.put(queueItem);
        }
    });
}
