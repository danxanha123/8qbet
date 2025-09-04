/**
 * Password Synchronization System
 * Syncs password changes between tabs/windows
 */

class PasswordSync {
    constructor() {
        this.init();
    }

    init() {
        // Listen for storage changes from other tabs
        window.addEventListener('storage', (e) => {
            if (e.key && e.key.startsWith('admin_password_')) {
                this.handlePasswordChange(e.key, e.newValue);
            }
        });

        // Listen for custom events within the same tab
        window.addEventListener('passwordChanged', (e) => {
            this.handlePasswordChange(e.detail.key, e.detail.value);
        });
    }

    // Handle password change from other tabs
    handlePasswordChange(key, newValue) {
        const username = key.replace('admin_password_', '');
        console.log(`Password updated for ${username} from another tab`);
        
        // Show notification to user
        this.showPasswordSyncNotification(username);
    }

    // Show notification about password sync
    showPasswordSyncNotification(username) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'password-sync-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            z-index: 10000;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        `;
        notification.textContent = `Mật khẩu cho ${username} đã được cập nhật từ tab khác`;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Broadcast password change to other tabs
    static broadcastPasswordChange(username, newPassword) {
        // Update localStorage (this will trigger storage event in other tabs)
        localStorage.setItem('admin_password_' + username, newPassword);
        
        // Also dispatch custom event for same tab
        window.dispatchEvent(new CustomEvent('passwordChanged', {
            detail: {
                key: 'admin_password_' + username,
                value: newPassword
            }
        }));
    }

    // Get current password for a user
    static getCurrentPassword(username) {
        const storedPassword = localStorage.getItem('admin_password_' + username);
        const defaultPasswords = {
            'admin': 'password',
            'administrator': 'Manthuong63@'
        };
        
        return storedPassword || defaultPasswords[username];
    }

    // Check if password was recently changed
    static isPasswordRecentlyChanged(username, minutes = 5) {
        const loginTime = localStorage.getItem('login_time_' + username);
        if (!loginTime) return false;
        
        const timeDiff = Date.now() - parseInt(loginTime);
        return timeDiff < (minutes * 60 * 1000);
    }

    // Clear old login times
    static cleanupOldLoginTimes() {
        const keys = Object.keys(localStorage);
        keys.forEach(key => {
            if (key.startsWith('login_time_')) {
                const loginTime = parseInt(localStorage.getItem(key));
                const timeDiff = Date.now() - loginTime;
                
                // Remove login times older than 24 hours
                if (timeDiff > (24 * 60 * 60 * 1000)) {
                    localStorage.removeItem(key);
                }
            }
        });
    }
}

// Initialize password sync system
window.passwordSync = new PasswordSync();

// Cleanup old login times on page load
PasswordSync.cleanupOldLoginTimes();
