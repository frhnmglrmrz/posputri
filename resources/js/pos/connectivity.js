/**
 * Connectivity detector that verifies true server reachability via /api/health
 * PRD Section 30, 31.
 */
export class ConnectivityManager {
    constructor(healthEndpoint = '/api/health', checkIntervalMs = 15000) {
        this.healthEndpoint = healthEndpoint;
        this.checkIntervalMs = checkIntervalMs;
        this.isOnline = navigator.onLine;
        this.intervalId = null;

        this.init();
    }

    init() {
        window.addEventListener('online', () => this.checkServerHealth());
        window.addEventListener('offline', () => this.setOffline());

        // Periodic check
        this.intervalId = setInterval(() => this.checkServerHealth(), this.checkIntervalMs);

        // Immediate check on startup
        this.checkServerHealth();
    }

    async checkServerHealth() {
        if (!navigator.onLine) {
            this.setOffline();
            return false;
        }

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 4000);

            const response = await fetch(this.healthEndpoint, {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                signal: controller.signal,
                cache: 'no-store',
            });

            clearTimeout(timeoutId);

            if (response.ok) {
                const data = await response.json();
                if (data.status === 'ok') {
                    this.setOnline();
                    return true;
                }
            }
            this.setOffline();
            return false;
        } catch {
            this.setOffline();
            return false;
        }
    }

    setOnline() {
        const wasOffline = !this.isOnline;
        this.isOnline = true;
        this.updateUI(true);

        window.dispatchEvent(new CustomEvent('pos:connectivity-change', {
            detail: { isOnline: true }
        }));

        if (wasOffline) {
            window.dispatchEvent(new CustomEvent('pos:server-restored'));
        }
    }

    setOffline() {
        this.isOnline = false;
        this.updateUI(false);

        window.dispatchEvent(new CustomEvent('pos:connectivity-change', {
            detail: { isOnline: false }
        }));
    }

    updateUI(isOnline) {
        const indicator = document.getElementById('connectivity-indicator');
        if (indicator) {
            if (isOnline) {
                indicator.className = 'hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-950/60 text-emerald-400 border border-emerald-800/50';
                indicator.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>ONLINE</span>';
            } else {
                indicator.className = 'hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-950/60 text-rose-400 border border-rose-800/50';
                indicator.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span><span>OFFLINE</span>';
            }
        }
    }
}
