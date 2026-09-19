import { db, getPendingSyncQueue, markTransactionSynced, saveBootstrapData } from './db';

/**
 * Sync Engine coordinating client-to-server data synchronization.
 * PRD Section 30, 32, 64, 65.
 */
export class SyncEngine {
    constructor(options = {}) {
        this.syncUrl = options.syncUrl || '/api/sync/transactions';
        this.bootstrapUrl = options.bootstrapUrl || '/api/sync/bootstrap';
        this.changesUrl = options.changesUrl || '/api/sync/changes';
        this.isSyncing = false;

        this.init();
    }

    init() {
        // Automatically sync whenever server connection is restored
        window.addEventListener('pos:server-restored', () => {
            this.syncPendingTransactions();
        });

        // Background sync trigger every 30 seconds
        setInterval(() => {
            this.syncPendingTransactions();
        }, 30000);
    }

    /**
     * Initial download of all master data into IndexedDB.
     */
    async bootstrapMasterData() {
        try {
            const response = await fetch(this.bootstrapUrl, {
                headers: { 'Accept': 'application/json' },
            });
            if (!response.ok) return false;

            const json = await response.json();
            if (json.success && json.data) {
                await saveBootstrapData(json.data);
                localStorage.setItem('pos:last_sync_at', json.data.timestamp || new Date().toISOString());
                window.dispatchEvent(new CustomEvent('pos:master-data-hydrated'));
                return true;
            }
            return false;
        } catch {
            return false;
        }
    }

    /**
     * Check and download incremental changes since last sync.
     */
    async fetchIncrementalChanges() {
        const lastSync = localStorage.getItem('pos:last_sync_at');
        if (!lastSync) {
            return this.bootstrapMasterData();
        }

        try {
            const url = `${this.changesUrl}?since=${encodeURIComponent(lastSync)}`;
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' },
            });
            if (!response.ok) return false;

            const json = await response.json();
            if (json.success && json.data) {
                const { products, categories, payment_methods, timestamp } = json.data;

                await db.transaction('rw', [db.products, db.categories, db.payment_methods], async () => {
                    for (const cat of categories || []) {
                        if (cat.deleted_at) {
                            await db.categories.where('uuid').equals(cat.uuid).delete();
                        } else {
                            await db.categories.put(cat);
                        }
                    }

                    for (const prod of products || []) {
                        if (prod.deleted_at) {
                            await db.products.where('uuid').equals(prod.uuid).delete();
                        } else {
                            await db.products.put(prod);
                        }
                    }

                    for (const pay of payment_methods || []) {
                        await db.payment_methods.put(pay);
                    }
                });

                localStorage.setItem('pos:last_sync_at', timestamp);
                window.dispatchEvent(new CustomEvent('pos:master-data-updated'));
                return true;
            }
        } catch {
            return false;
        }
    }

    /**
     * Synchronize all pending transactions in the sync queue.
     */
    async syncPendingTransactions() {
        if (this.isSyncing) return;
        this.isSyncing = true;

        try {
            const pendingQueue = await getPendingSyncQueue();
            if (!pendingQueue.length) {
                this.updateSyncBadge(0);
                this.isSyncing = false;
                return;
            }

            this.updateSyncBadge(pendingQueue.length, true);

            const transactionsPayload = pendingQueue.map(item => item.payload);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch(this.syncUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    transactions: transactionsPayload,
                }),
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.result) {
                    for (const item of data.result.synced || []) {
                        await markTransactionSynced(item.uuid, item);
                    }

                    for (const fail of data.result.failed || []) {
                        const queueItem = await db.sync_queue.where('entity_uuid').equals(fail.uuid).first();
                        if (queueItem) {
                            queueItem.retry_count = (queueItem.retry_count || 0) + 1;
                            queueItem.last_error = fail.error;
                            if (queueItem.retry_count > 5) {
                                queueItem.status = 'FAILED';
                            }
                            await db.sync_queue.put(queueItem);
                        }
                    }
                }
            }

            const remaining = await getPendingSyncQueue();
            this.updateSyncBadge(remaining.length, false);

            window.dispatchEvent(new CustomEvent('pos:sync-completed', {
                detail: { remaining: remaining.length }
            }));
        } catch (e) {
            console.warn('Sync pending transactions encountered an error:', e);
        } finally {
            this.isSyncing = false;
        }
    }

    updateSyncBadge(pendingCount, isSyncing = false) {
        window.dispatchEvent(new CustomEvent('pos:sync-status-changed', {
            detail: { pendingCount, isSyncing }
        }));
    }
}
