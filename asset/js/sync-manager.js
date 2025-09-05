/**
 * Simple Sync Manager
 * Unified solution for cross-tab synchronization
 */

class SyncManager {
    constructor() {
        this.configKey = 'dashboard_config';
        this.timestampKey = 'config_timestamp';
        this.syncEvent = 'configSync';
        this.init();
    }

    init() {
        // Listen for storage changes (cross-tab)
        window.addEventListener('storage', (e) => {
            if (e.key === this.configKey) {
                this.handleConfigChange();
            }
        });

        // Listen for custom events (same-tab)
        window.addEventListener(this.syncEvent, () => {
            this.handleConfigChange();
        });

        // Polling as fallback (every 500ms)
        setInterval(() => {
            this.checkForChanges();
        }, 500);
    }

    // Save configuration and notify all tabs
    saveConfig(config) {
        // Save to localStorage
        localStorage.setItem(this.configKey, JSON.stringify(config));
        localStorage.setItem(this.timestampKey, Date.now().toString());

        // Notify same tab
        window.dispatchEvent(new CustomEvent(this.syncEvent));

        // Notify other tabs via storage event
        window.dispatchEvent(new StorageEvent('storage', {
            key: this.configKey,
            newValue: JSON.stringify(config)
        }));
    }

    // Load configuration
    loadConfig() {
        const saved = localStorage.getItem(this.configKey);
        return saved ? JSON.parse(saved) : null;
    }

    // Handle configuration changes
    handleConfigChange() {
        const config = this.loadConfig();
        if (config) {
            // Notify all listeners
            window.dispatchEvent(new CustomEvent('configUpdated', {
                detail: { config: config }
            }));
        }
    }

    // Check for changes (polling fallback)
    checkForChanges() {
        const currentTimestamp = localStorage.getItem(this.timestampKey);
        if (currentTimestamp !== this.lastTimestamp) {
            this.lastTimestamp = currentTimestamp;
            this.handleConfigChange();
        }
    }

    // Get current timestamp
    getLastTimestamp() {
        return localStorage.getItem(this.timestampKey);
    }
}

// Global sync manager
window.syncManager = new SyncManager();
