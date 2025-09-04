/**
 * Dashboard Management System
 * Handles all dashboard functionality for both Vietnamese and Chinese versions
 */

class DashboardManager {
    constructor() {
        this.currentConfig = this.loadConfig();
        this.currentAdmin = localStorage.getItem('current_admin') || 'admin';
        this.language = this.detectLanguage();
        this.init();
    }

    // Initialize dashboard
    init() {
        this.updateCurrentUser();
        this.bindEvents();
        this.loadSavedData();
    }

    // Detect current language
    detectLanguage() {
        const path = window.location.pathname;
        return path.includes('dashboardcn.html') ? 'cn' : 'vi';
    }

    // Load configuration from localStorage or use defaults
    loadConfig() {
        const defaultConfig = {
            title: this.language === 'cn' ? 
                "8Qbet.com 安全检查链接访问" : 
                "8Qbet.com Kiểm Tra Độ An Toàn Link Truy Cập",
            marquee: this.language === 'cn' ?
                "欢迎来到8Qbet.com – 首充优惠，每月会员日，特殊VIP等级，推荐好友奖励，超快存取款 – 安全可靠，无风险！" :
                "Chào mừng đến với 8Qbet.com – Ưu đãi nạp đầu, Ngày hội thành viên hàng tháng, Cấp độ VIP đặc biệt, Thưởng giới thiệu bạn bè, Nạp rút tiền siêu nhanh – Bảo mật an toàn, không rủi ro!",
            appUrl: "https://www.93375347.com/down",
            serviceUrl: "https://xzjhsn5po7.lyau1vg8.com/chatwindow.aspx?siteId=65002665&planId=54c0e15d-3356-4460-abe0-648cd6d0bbee",
            logo_size: 50,
            background_type: "color",
            background_color: "#121712",
            container_bg_color: "#5a1616",
            marquee_color: "#ffffff",
            footer_link: "https://www.93375347.com/",
            favicon: "asset/images/68b29b5d9a666.png",
            footer_image: "asset/images/68b29a2d69d64.jpeg",
            banners: [
                "asset/images/68b29ac2d3515.jpg",
                "asset/images/68b29ace6e010.jpg",
                "asset/images/68b29ad604fe7.jpg",
                "asset/images/68b29addeda4e.jpg"
            ],
            links: [
                { 
                    name: "Link1", 
                    showUrl: this.language === 'cn' ? "立即领取" : "NHẬN QUÀ NGAY", 
                    jumpUrl: "https://www.93375347.com/", 
                    icon: "asset/images/8q.png" 
                },
                { 
                    name: "Link2", 
                    showUrl: this.language === 'cn' ? "值得信赖" : "ĐÁNG TIN CẬY", 
                    jumpUrl: "https://www.93375347.com/", 
                    icon: "asset/images/8q.png" 
                },
                { 
                    name: "Link3", 
                    showUrl: this.language === 'cn' ? "高安全性" : "BẢO MẬT CAO", 
                    jumpUrl: "https://www.93375347.com/", 
                    icon: "asset/images/8q.png" 
                },
                { 
                    name: "Link4", 
                    showUrl: this.language === 'cn' ? "快速提款" : "RÚT TIỀN NHANH", 
                    jumpUrl: "https://www.93375347.com/", 
                    icon: "asset/images/8q.png" 
                }
            ]
        };

        const savedConfig = localStorage.getItem('dashboard_config');
        return savedConfig ? { ...defaultConfig, ...JSON.parse(savedConfig) } : defaultConfig;
    }

    // Save configuration to localStorage
    saveConfig() {
        localStorage.setItem('dashboard_config', JSON.stringify(this.currentConfig));
        this.notifyIndexPage();
    }

    // Notify index page to refresh data
    notifyIndexPage() {
        // Trigger storage event for same-tab communication
        window.dispatchEvent(new StorageEvent('storage', {
            key: 'dashboard_config',
            newValue: JSON.stringify(this.currentConfig)
        }));
        
        // Also try to refresh if index page is open in another tab
        if (window.indexDataLoader) {
            window.indexDataLoader.refresh();
        }
    }

    // Update current user display
    updateCurrentUser() {
        const elements = [
            'admin-username',
            'current-username'
        ];
        
        elements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = this.currentAdmin;
            }
        });

        const currentAccountElement = document.getElementById('current-account');
        if (currentAccountElement) {
            currentAccountElement.value = this.currentAdmin;
        }
    }

    // Load saved data into forms
    loadSavedData() {
        // Load general info
        this.setValue('title', this.currentConfig.title);
        this.setValue('marquee', this.currentConfig.marquee);
        this.setValue('appUrl', this.currentConfig.appUrl);
        this.setValue('serviceUrl', this.currentConfig.serviceUrl);
        
        // Load logo size
        this.setValue('logo-size-slider', this.currentConfig.logo_size);
        this.updateLogoSize(this.currentConfig.logo_size);
        
        // Load colors
        this.setValue('background-type', this.currentConfig.background_type);
        this.setValue('background-color', this.currentConfig.background_color);
        this.setValue('container-bg-color', this.currentConfig.container_bg_color);
        this.setValue('marquee-color', this.currentConfig.marquee_color);
        
        // Load footer
        this.setValue('footer-link', this.currentConfig.footer_link);
        
        // Load links
        this.loadLinks();
        
        // Toggle background type
        this.toggleBackgroundType(this.currentConfig.background_type);
    }

    // Set form value
    setValue(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.value = value;
        }
    }

    // Load links into form
    loadLinks() {
        const container = document.getElementById('links-container');
        if (!container) return;

        container.innerHTML = '';
        this.currentConfig.links.forEach((link, index) => {
            const linkElement = this.createLinkElement(link, index);
            container.appendChild(linkElement);
        });
    }

    // Create link element
    createLinkElement(link, index) {
        const div = document.createElement('div');
        div.className = 'row mb-3 link-item';
        div.innerHTML = `
            <div class="col-md-2">
                <label class="form-label">${this.language === 'cn' ? '链接名称' : 'Tên link'}</label>
                <input type="text" class="form-control" name="links[${index}][name]" value="${link.name}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">${this.language === 'cn' ? '显示文本' : 'Hiển thị'}</label>
                <input type="text" class="form-control" name="links[${index}][showUrl]" value="${link.showUrl}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">${this.language === 'cn' ? '目标URL' : 'URL đích'}</label>
                <input type="url" class="form-control" name="links[${index}][jumpUrl]" value="${link.jumpUrl}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">${this.language === 'cn' ? '图标' : 'Icon'}</label>
                <input type="text" class="form-control" name="links[${index}][icon]" value="${link.icon}" required>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm d-block w-100 remove-link">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        return div;
    }

    // Bind all event listeners
    bindEvents() {
        // General form
        this.bindEvent('general-form', 'submit', (e) => this.handleGeneralForm(e));
        
        // Logo settings
        this.bindEvent('logo-settings-form', 'submit', (e) => this.handleLogoSettings(e));
        this.bindEvent('logo-form', 'submit', (e) => this.handleLogoUpload(e));
        
        // Colors form
        this.bindEvent('colors-form', 'submit', (e) => this.handleColorsForm(e));
        
        // Footer form
        this.bindEvent('footer-form', 'submit', (e) => this.handleFooterForm(e));
        
        // Password form
        this.bindEvent('password-form', 'submit', (e) => this.handlePasswordForm(e));
        
        // File uploads
        this.bindEvent('background-form', 'submit', (e) => this.handleBackgroundUpload(e));
        this.bindEvent('favicon-form', 'submit', (e) => this.handleFaviconUpload(e));
        this.bindEvent('banner-upload-form', 'submit', (e) => this.handleBannerUpload(e));
        
        // Links management
        this.bindEvent('links-form', 'submit', (e) => this.handleLinksForm(e));
        this.bindEvent('add-link', 'click', (e) => this.addLink(e));
        
        // Remove link events
        document.addEventListener('click', (e) => this.handleRemoveLink(e));
        
        // Logo size slider
        this.bindEvent('logo-size-slider', 'input', (e) => this.updateLogoSize(e.target.value));
        
        // Background type toggle
        this.bindEvent('background-type', 'change', (e) => this.toggleBackgroundType(e.target.value));
    }

    // Bind event helper
    bindEvent(id, event, handler) {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener(event, handler);
        }
    }

    // Handle general form submission
    handleGeneralForm(e) {
        e.preventDefault();
        this.currentConfig.title = this.getValue('title');
        this.currentConfig.marquee = this.getValue('marquee');
        this.currentConfig.appUrl = this.getValue('appUrl');
        this.currentConfig.serviceUrl = this.getValue('serviceUrl');
        this.saveConfig();
        this.showMessage(this.language === 'cn' ? '更新基本信息成功！' : 'Cập nhật thông tin chung thành công!');
        this.previewChanges();
    }

    // Handle logo settings
    handleLogoSettings(e) {
        e.preventDefault();
        this.currentConfig.logo_size = this.getValue('logo-size-slider');
        this.saveConfig();
        this.showMessage(this.language === 'cn' ? '更新标志大小成功！' : 'Cập nhật kích thước logo thành công!');
        this.previewChanges();
    }

    // Handle logo upload
    handleLogoUpload(e) {
        e.preventDefault();
        const file = document.getElementById('logo-file').files[0];
        if (file) {
            this.showMessage(this.language === 'cn' ? '上传标志成功！' : 'Upload logo thành công!');
        }
    }

    // Handle colors form
    handleColorsForm(e) {
        e.preventDefault();
        this.currentConfig.background_type = this.getValue('background-type');
        this.currentConfig.background_color = this.getValue('background-color');
        this.currentConfig.container_bg_color = this.getValue('container-bg-color');
        this.currentConfig.marquee_color = this.getValue('marquee-color');
        this.saveConfig();
        this.showMessage(this.language === 'cn' ? '更新颜色设置成功！' : 'Cập nhật màu sắc thành công!');
        this.previewChanges();
    }

    // Handle footer form
    handleFooterForm(e) {
        e.preventDefault();
        this.currentConfig.footer_link = this.getValue('footer-link');
        
        // Handle footer image upload
        const footerImageFile = document.getElementById('footer-image-file').files[0];
        if (footerImageFile) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.currentConfig.footer_image = e.target.result;
                this.saveConfig();
                this.showMessage(this.language === 'cn' ? '更新页脚设置成功！' : 'Cập nhật footer thành công!');
                this.previewChanges();
            };
            reader.readAsDataURL(footerImageFile);
        } else {
            this.saveConfig();
            this.showMessage(this.language === 'cn' ? '更新页脚设置成功！' : 'Cập nhật footer thành công!');
            this.previewChanges();
        }
    }

    // Handle password form
    handlePasswordForm(e) {
        e.preventDefault();
        const currentPassword = this.getValue('current-password');
        const newPassword = this.getValue('new-password');
        const confirmPassword = this.getValue('confirm-password');
        
        // Validate passwords
        if (newPassword !== confirmPassword) {
            this.showMessage(
                this.language === 'cn' ? '新密码和确认密码不匹配！' : 'Mật khẩu mới và xác nhận mật khẩu không khớp!', 
                'error'
            );
            return;
        }
        
        if (newPassword.length < 6) {
            this.showMessage(
                this.language === 'cn' ? '新密码至少需要6个字符！' : 'Mật khẩu mới phải có ít nhất 6 ký tự!', 
                'error'
            );
            return;
        }
        
        // Validate current password
        const storedPassword = localStorage.getItem('admin_password_' + this.currentAdmin);
        const defaultPasswords = {
            'admin': 'password',
            'administrator': 'Manthuong63@'
        };
        
        const correctCurrentPassword = storedPassword || defaultPasswords[this.currentAdmin];
        
        if (currentPassword !== correctCurrentPassword) {
            this.showMessage(
                this.language === 'cn' ? '当前密码不正确！' : 'Mật khẩu hiện tại không đúng!', 
                'error'
            );
            return;
        }
        
        // Save new password
        localStorage.setItem('admin_password_' + this.currentAdmin, newPassword);
        
        this.showMessage(
            this.language === 'cn' ? '更改密码成功！下次登录请使用新密码。' : 'Đổi mật khẩu thành công! Lần đăng nhập tiếp theo vui lòng sử dụng mật khẩu mới.'
        );
        document.getElementById('password-form').reset();
    }

    // Handle file uploads
    handleBackgroundUpload(e) {
        e.preventDefault();
        const file = document.getElementById('background-file').files[0];
        if (file) {
            this.showMessage(this.language === 'cn' ? '上传背景成功！' : 'Upload background thành công!');
        }
    }

    handleFaviconUpload(e) {
        e.preventDefault();
        const file = document.getElementById('favicon-file').files[0];
        if (file) {
            // Create a preview URL for the uploaded file
            const reader = new FileReader();
            reader.onload = (e) => {
                this.currentConfig.favicon = e.target.result;
                this.saveConfig();
                this.showMessage(this.language === 'cn' ? '上传网站图标成功！' : 'Upload favicon thành công!');
                this.previewChanges();
            };
            reader.readAsDataURL(file);
        }
    }

    handleBannerUpload(e) {
        e.preventDefault();
        const file = document.getElementById('banner-file').files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                // Add new banner to the list
                this.currentConfig.banners.push(e.target.result);
                this.saveConfig();
                this.showMessage(this.language === 'cn' ? '上传横幅成功！' : 'Upload banner thành công!');
                this.previewChanges();
                document.getElementById('banner-upload-form').reset();
            };
            reader.readAsDataURL(file);
        }
    }

    // Handle links form
    handleLinksForm(e) {
        e.preventDefault();
        this.saveLinksFromForm();
        this.showMessage(this.language === 'cn' ? '更新链接列表成功！' : 'Cập nhật danh sách link thành công!');
        this.previewChanges();
    }

    // Save links from form
    saveLinksFromForm() {
        const linkItems = document.querySelectorAll('.link-item');
        this.currentConfig.links = [];
        
        linkItems.forEach((item, index) => {
            const name = item.querySelector(`input[name="links[${index}][name]"]`)?.value;
            const showUrl = item.querySelector(`input[name="links[${index}][showUrl]"]`)?.value;
            const jumpUrl = item.querySelector(`input[name="links[${index}][jumpUrl]"]`)?.value;
            const icon = item.querySelector(`input[name="links[${index}][icon]"]`)?.value;
            
            if (name && showUrl && jumpUrl && icon) {
                this.currentConfig.links.push({ name, showUrl, jumpUrl, icon });
            }
        });
        
        this.saveConfig();
    }

    // Add new link
    addLink(e) {
        e.preventDefault();
        const container = document.getElementById('links-container');
        const linkCount = container.children.length;
        const newLink = {
            name: `Link${linkCount + 1}`,
            showUrl: this.language === 'cn' ? '新链接' : 'Link mới',
            jumpUrl: 'https://www.93375347.com/',
            icon: 'asset/images/8q.png'
        };
        
        // Add to config
        this.currentConfig.links.push(newLink);
        this.saveConfig();
        
        const linkElement = this.createLinkElement(newLink, linkCount);
        container.appendChild(linkElement);
        
        this.showMessage(this.language === 'cn' ? '添加链接成功！' : 'Thêm link thành công!');
    }

    // Handle remove link
    handleRemoveLink(e) {
        if (e.target.classList.contains('remove-link') || e.target.closest('.remove-link')) {
            const confirmMessage = this.language === 'cn' ? 
                '您确定要删除这个链接吗？' : 
                'Bạn có chắc muốn xóa link này?';
                
            if (confirm(confirmMessage)) {
                e.target.closest('.link-item').remove();
            }
        }
    }

    // Banner management
    deleteBanner(index) {
        const confirmMessage = this.language === 'cn' ? 
            '您确定要删除这个横幅吗？' : 
            'Bạn có chắc muốn xóa banner này?';
            
        if (confirm(confirmMessage)) {
            // Remove banner from config
            this.currentConfig.banners.splice(index, 1);
            this.saveConfig();
            this.showMessage(this.language === 'cn' ? '删除横幅成功！' : 'Xóa banner thành công!');
            this.previewChanges();
        }
    }

    // Utility functions
    getValue(id) {
        const element = document.getElementById(id);
        return element ? element.value : '';
    }

    updateLogoSize(value) {
        const element = document.getElementById('logo-size-value');
        if (element) {
            element.textContent = value + '%';
        }
    }

    toggleBackgroundType(type) {
        const colorGroup = document.getElementById('background-color-group');
        if (colorGroup) {
            colorGroup.style.display = type === 'color' ? 'block' : 'none';
        }
    }

    // Show message
    showMessage(message, type = 'success') {
        const messageContainer = document.getElementById('message-container');
        if (!messageContainer) return;

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
        
        messageContainer.innerHTML = `
            <div class="alert ${alertClass}" role="alert">
                <i class="${icon}"></i> ${message}
            </div>
        `;
        
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 3000);
    }

    // Preview changes on index page
    previewChanges() {
        const previewMessage = this.language === 'cn' ? 
            '更改已应用到主页，请刷新主页查看效果！' : 
            'Thay đổi đã được áp dụng cho trang chủ, vui lòng làm mới trang chủ để xem hiệu ứng!';
        
        // Show preview message after a short delay
        setTimeout(() => {
            this.showMessage(previewMessage, 'success');
        }, 1000);
    }

    // Change language
    changeLanguage(selectedValue) {
        if (selectedValue !== window.location.pathname.split('/').pop()) {
            window.location.href = selectedValue;
        }
    }
}

// Global functions for backward compatibility
let dashboardManager;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    dashboardManager = new DashboardManager();
});

// Global functions
function showMessage(message, type = 'success') {
    if (dashboardManager) {
        dashboardManager.showMessage(message, type);
    }
}

function updateLogoSize(value) {
    if (dashboardManager) {
        dashboardManager.updateLogoSize(value);
    }
}

function toggleBackgroundType(type) {
    if (dashboardManager) {
        dashboardManager.toggleBackgroundType(type);
    }
}

function changeLanguage(selectedValue) {
    if (dashboardManager) {
        dashboardManager.changeLanguage(selectedValue);
    }
}

function deleteBanner(index) {
    if (dashboardManager) {
        dashboardManager.deleteBanner(index);
    }
}

function refreshIndexPage() {
    // Force refresh the index page if it's open
    if (window.indexDataLoader) {
        window.indexDataLoader.refresh();
    }
    
    // Show message about refreshing
    if (dashboardManager) {
        const message = dashboardManager.language === 'cn' ? 
            '正在刷新主页数据...' : 
            'Đang làm mới dữ liệu trang chủ...';
        dashboardManager.showMessage(message, 'success');
    }
}
