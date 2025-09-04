/**
 * Advanced Password Manager with Cross-Tab Synchronization
 * Uses BroadcastChannel API and sessionStorage for real-time sync
 */

class PasswordManager {
    constructor() {
        this.channel = null;
        this.channelName = 'password-sync-channel';
        this.init();
    }

    init() {
        // Initialize BroadcastChannel if supported
        if (typeof BroadcastChannel !== 'undefined') {
            this.channel = new BroadcastChannel(this.channelName);
            this.setupChannelListeners();
        }

        // Fallback to storage events
        this.setupStorageListeners();
        
        // Initialize default passwords
        this.initializeDefaultPasswords();
    }

    setupChannelListeners() {
        this.channel.addEventListener('message', (event) => {
            const { type, data } = event.data;
            
            switch (type) {
                case 'PASSWORD_CHANGED':
                    this.handlePasswordChange(data.username, data.newPassword);
                    break;
                case 'PASSWORD_SYNC_REQUEST':
                    this.handleSyncRequest(data.requestingTab);
                    break;
                case 'PASSWORD_SYNC_RESPONSE':
                    this.handleSyncResponse(data.passwords);
                    break;
            }
        });
    }

    setupStorageListeners() {
        // Listen for storage changes (fallback)
        window.addEventListener('storage', (e) => {
            if (e.key && e.key.startsWith('admin_password_')) {
                const username = e.key.replace('admin_password_', '');
                this.handlePasswordChange(username, e.newValue);
            }
        });
    }

    initializeDefaultPasswords() {
        const defaultPasswords = {
            'admin': 'password',
            'administrator': 'Manthuong63@'
        };

        // Set default passwords if not exists
        Object.keys(defaultPasswords).forEach(username => {
            const key = `admin_password_${username}`;
            if (!sessionStorage.getItem(key)) {
                sessionStorage.setItem(key, defaultPasswords[username]);
            }
        });
    }

    // Get current password for a user
    getPassword(username) {
        return sessionStorage.getItem(`admin_password_${username}`);
    }

    // Set new password and broadcast to all tabs
    setPassword(username, newPassword) {
        // Store in sessionStorage
        sessionStorage.setItem(`admin_password_${username}`, newPassword);
        
        // Broadcast to other tabs
        this.broadcastPasswordChange(username, newPassword);
        
        // Also store in localStorage as backup
        localStorage.setItem(`admin_password_${username}`, newPassword);
        
        console.log(`Password updated for ${username} and broadcasted to all tabs`);
    }

    // Broadcast password change to all tabs
    broadcastPasswordChange(username, newPassword) {
        if (this.channel) {
            this.channel.postMessage({
                type: 'PASSWORD_CHANGED',
                data: {
                    username: username,
                    newPassword: newPassword,
                    timestamp: Date.now()
                }
            });
        }
        
        // Show notification
        this.showPasswordChangeNotification(username);
    }

    // Handle password change from other tabs
    handlePasswordChange(username, newPassword) {
        // Update sessionStorage
        sessionStorage.setItem(`admin_password_${username}`, newPassword);
        
        // Update localStorage as backup
        localStorage.setItem(`admin_password_${username}`, newPassword);
        
        console.log(`Password synced for ${username} from another tab`);
        
        // Show notification
        this.showPasswordSyncNotification(username);
    }

    // Request password sync from other tabs
    requestPasswordSync() {
        if (this.channel) {
            this.channel.postMessage({
                type: 'PASSWORD_SYNC_REQUEST',
                data: {
                    requestingTab: Date.now()
                }
            });
        }
    }

    // Handle sync request from other tabs
    handleSyncRequest(requestingTab) {
        // Send current passwords to requesting tab
        const passwords = {};
        const keys = Object.keys(sessionStorage);
        
        keys.forEach(key => {
            if (key.startsWith('admin_password_')) {
                const username = key.replace('admin_password_', '');
                passwords[username] = sessionStorage.getItem(key);
            }
        });

        if (this.channel) {
            this.channel.postMessage({
                type: 'PASSWORD_SYNC_RESPONSE',
                data: {
                    passwords: passwords,
                    respondingTab: Date.now()
                }
            });
        }
    }

    // Handle sync response from other tabs
    handleSyncResponse(passwords) {
        Object.keys(passwords).forEach(username => {
            const currentPassword = this.getPassword(username);
            const syncedPassword = passwords[username];
            
            if (currentPassword !== syncedPassword) {
                sessionStorage.setItem(`admin_password_${username}`, syncedPassword);
                localStorage.setItem(`admin_password_${username}`, syncedPassword);
                console.log(`Password synced for ${username} from another tab`);
            }
        });
    }

    // Show notification when password is changed
    showPasswordChangeNotification(username) {
        this.showNotification(
            `Mật khẩu cho ${username} đã được thay đổi`,
            'success'
        );
    }

    // Show notification when password is synced
    showPasswordSyncNotification(username) {
        this.showNotification(
            `Mật khẩu cho ${username} đã được đồng bộ từ tab khác`,
            'info'
        );
    }

    // Show notification
    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.password-notification');
        existingNotifications.forEach(notification => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        });

        // Create new notification
        const notification = document.createElement('div');
        notification.className = 'password-notification';
        
        const colors = {
            'success': '#28a745',
            'info': '#17a2b8',
            'warning': '#ffc107',
            'error': '#dc3545'
        };
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${colors[type] || colors.info};
            color: white;
            padding: 12px 16px;
            border-radius: 6px;
            z-index: 10000;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-width: 300px;
            word-wrap: break-word;
            animation: slideIn 0.3s ease-out;
        `;
        
        notification.textContent = message;
        
        // Add animation styles
        if (!document.querySelector('#password-notification-styles')) {
            const style = document.createElement('style');
            style.id = 'password-notification-styles';
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        // Remove notification after 4 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }
        }, 4000);
    }

    // Get all stored passwords
    getAllPasswords() {
        const passwords = {};
        const keys = Object.keys(sessionStorage);
        
        keys.forEach(key => {
            if (key.startsWith('admin_password_')) {
                const username = key.replace('admin_password_', '');
                passwords[username] = sessionStorage.getItem(key);
            }
        });
        
        return passwords;
    }

    // Clear all passwords
    clearAllPasswords() {
        const keys = Object.keys(sessionStorage);
        keys.forEach(key => {
            if (key.startsWith('admin_password_')) {
                sessionStorage.removeItem(key);
                localStorage.removeItem(key);
            }
        });
    }

    // Check if BroadcastChannel is supported
    isBroadcastChannelSupported() {
        return typeof BroadcastChannel !== 'undefined';
    }

    // Get connection status
    getConnectionStatus() {
        return {
            broadcastChannel: this.isBroadcastChannelSupported(),
            channel: this.channel !== null,
            sessionStorage: typeof sessionStorage !== 'undefined',
            localStorage: typeof localStorage !== 'undefined'
        };
    }

    // Cleanup
    destroy() {
        if (this.channel) {
            this.channel.close();
            this.channel = null;
        }
    }
}

// Initialize global password manager
window.passwordManager = new PasswordManager();

// Request sync when page loads
window.addEventListener('load', () => {
    setTimeout(() => {
        window.passwordManager.requestPasswordSync();
    }, 1000);
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    window.passwordManager.destroy();
});
