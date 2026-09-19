import { db } from './pos/db';
import { ConnectivityManager } from './pos/connectivity';
import { SyncEngine } from './pos/sync';
import { createPosComponent } from './pos/cart';

// Expose DB & Pos Component to window for Alpine.js
window.posDb = db;
window.createPosComponent = createPosComponent;

// Initialize Connectivity & Sync Engine
document.addEventListener('DOMContentLoaded', () => {
    window.posConnectivity = new ConnectivityManager();
    window.posSyncEngine = new SyncEngine();

    // Register Service Worker for PWA (PRD Section 9, 10)
    if ('serviceWorker' in navigator && (window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')) {
        navigator.serviceWorker.register('/service-worker.js')
            .then((registration) => {
                registration.update();
                console.log('POS Service Worker registered with scope:', registration.scope);
            })
            .catch((error) => {
                console.warn('POS Service Worker registration failed:', error);
            });
    }
});
