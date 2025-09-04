/**
 * Index Page Data Loader
 * Loads and applies configuration from dashboard to index.html
 */

class IndexDataLoader {
    constructor() {
        this.config = this.loadConfig();
        this.init();
    }

    // Load configuration from localStorage
    loadConfig() {
        const defaultConfig = {
            title: "8Qbet.com Kiểm Tra Độ An Toàn Link Truy Cập",
            marquee: "Chào mừng đến với 8Qbet.com – Ưu đãi nạp đầu, Ngày hội thành viên hàng tháng, Cấp độ VIP đặc biệt, Thưởng giới thiệu bạn bè, Nạp rút tiền siêu nhanh – Bảo mật an toàn, không rủi ro!",
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
                { name: "Link1", showUrl: "NHẬN QUÀ NGAY", jumpUrl: "https://www.93375347.com/", icon: "asset/images/8q.png" },
                { name: "Link2", showUrl: "ĐÁNG TIN CẬY", jumpUrl: "https://www.93375347.com/", icon: "asset/images/8q.png" },
                { name: "Link3", showUrl: "BẢO MẬT CAO", jumpUrl: "https://www.93375347.com/", icon: "asset/images/8q.png" },
                { name: "Link4", showUrl: "RÚT TIỀN NHANH", jumpUrl: "https://www.93375347.com/", icon: "asset/images/8q.png" }
            ]
        };

        const savedConfig = localStorage.getItem('dashboard_config');
        return savedConfig ? { ...defaultConfig, ...JSON.parse(savedConfig) } : defaultConfig;
    }

    // Initialize and apply configuration
    init() {
        this.updatePageTitle();
        this.updateLogo();
        this.updateMarquee();
        this.updateBanners();
        this.updateLinks();
        this.updateFooter();
        this.updateColors();
        this.updateAppLinks();
        this.updateFavicon();
    }

    // Update page title
    updatePageTitle() {
        if (this.config.title) {
            document.title = this.config.title;
        }
    }

    // Update logo
    updateLogo() {
        const logoImg = document.querySelector('.tops img');
        if (logoImg && this.config.logo_size) {
            logoImg.style.width = this.config.logo_size + '%';
        }
    }

    // Update marquee content
    updateMarquee() {
        const marqueeElement = document.querySelector('#marquee marquee');
        if (marqueeElement && this.config.marquee) {
            marqueeElement.textContent = this.config.marquee;
        }
    }

    // Update banner images
    updateBanners() {
        const bannerSlides = document.querySelectorAll('.swiper-slide img');
        if (bannerSlides && this.config.banners) {
            this.config.banners.forEach((bannerData, index) => {
                if (bannerSlides[index]) {
                    // Handle both string (old format) and object (new format)
                    const bannerSrc = typeof bannerData === 'string' ? bannerData : bannerData.src;
                    bannerSlides[index].src = bannerSrc;
                }
            });
        }
    }

    // Update links
    updateLinks() {
        const linkItems = document.querySelectorAll('.lines .item');
        if (linkItems && this.config.links) {
            this.config.links.forEach((link, index) => {
                if (linkItems[index]) {
                    const item = linkItems[index];
                    
                    // Update link name
                    const nameElement = item.querySelector('.txts');
                    if (nameElement) {
                        nameElement.textContent = link.name;
                    }
                    
                    // Update link text
                    const textElement = item.querySelector('.webs a');
                    if (textElement) {
                        textElement.textContent = link.showUrl;
                    }
                    
                    // Update all links
                    const allLinks = item.querySelectorAll('a');
                    allLinks.forEach(a => {
                        a.href = link.jumpUrl;
                    });
                    
                    // Update icon
                    const iconImg = item.querySelector('.ico img');
                    if (iconImg) {
                        iconImg.src = link.icon;
                    }
                }
            });
        }
    }

    // Update footer
    updateFooter() {
        const footerLink = document.querySelector('.footer a');
        if (footerLink && this.config.footer_link) {
            footerLink.href = this.config.footer_link;
        }
        
        // Update footer image
        if (this.config.footer_image) {
            const footerImg = footerLink.querySelector('img');
            if (footerImg) {
                footerImg.src = this.config.footer_image;
            }
        }
    }

    // Update colors
    updateColors() {
        // Update background color
        if (this.config.background_color) {
            document.body.style.backgroundColor = this.config.background_color;
        }
        
        // Update container background color
        if (this.config.container_bg_color) {
            const mobileWrap = document.querySelector('.mobile-wrap');
            if (mobileWrap) {
                mobileWrap.style.backgroundColor = this.config.container_bg_color;
            }
        }
        
        // Update marquee color
        if (this.config.marquee_color) {
            const marquee = document.querySelector('.marquee');
            if (marquee) {
                marquee.style.setProperty('color', this.config.marquee_color, 'important');
            }
        }
    }

    // Update app download and customer service links
    updateAppLinks() {
        // Update customer service link
        const customerServiceLink = document.querySelector('.function .item:first-child a');
        if (customerServiceLink && this.config.serviceUrl) {
            customerServiceLink.href = this.config.serviceUrl;
        }
        
        // Update app download link
        const appDownloadLink = document.querySelector('.function .item:last-child a');
        if (appDownloadLink && this.config.appUrl) {
            appDownloadLink.href = this.config.appUrl;
        }
    }

    // Update favicon
    updateFavicon() {
        if (this.config.favicon) {
            const favicon = document.querySelector('link[rel="icon"]');
            if (favicon) {
                favicon.href = this.config.favicon;
            }
        }
    }

    // Method to refresh data (can be called from dashboard)
    refresh() {
        this.config = this.loadConfig();
        this.init();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.indexDataLoader = new IndexDataLoader();
});

// Listen for storage changes to auto-refresh
window.addEventListener('storage', function(e) {
    if (e.key === 'dashboard_config' && window.indexDataLoader) {
        window.indexDataLoader.refresh();
    }
});
