/**
 * Simple Index Data Loader
 * Clean and simple implementation
 */

class SimpleIndexDataLoader {
    constructor() {
        this.config = this.loadConfig();
        this.init();
    }

    // Load configuration
    loadConfig() {
        const defaultConfig = {
            title: "8Qbet.com Kiểm Tra Độ An Toàn Link Truy Cập",
            marquee: "Chào mừng đến với 8Qbet.com – Ưu đãi nạp đầu, Ngày hội thành viên hàng tháng, Cấp độ VIP đặc biệt, Thưởng giới thiệu bạn bè, Nạp rút tiền siêu nhanh – Bảo mật an toàn, không rủi ro!",
            appUrl: "https://www.93375347.com/down",
            serviceUrl: "https://xzjhsn5po7.lyau1vg8.com/chatwindow.aspx?siteId=65002665&planId=54c0e15d-3356-4460-abe0-648cd6d0bbee",
            logo_size: 50,
            background_type: "color",
            background_color: "#121712",
            background_image: "",
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
        this.setupSync();
    }

    // Setup synchronization
    setupSync() {
        // Listen for config updates
        window.addEventListener('configUpdated', (e) => {
            if (e.detail && e.detail.config) {
                this.config = { ...this.config, ...e.detail.config };
                this.init();
            }
        });
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
        const swiperWrapper = document.getElementById('swiper-wrapper');
        const swiperPagination = document.querySelector('.swiper-pagination');
        
        if (!swiperWrapper || !this.config.banners || this.config.banners.length === 0) {
            const bannerSection = document.querySelector('.swiper-container.bann');
            if (bannerSection) {
                bannerSection.style.display = 'none';
            }
            return;
        }
        
        const bannerSection = document.querySelector('.swiper-container.bann');
        if (bannerSection) {
            bannerSection.style.display = 'block';
        }
        
        swiperWrapper.innerHTML = '';
        
        this.config.banners.forEach((bannerData, index) => {
            const bannerSrc = typeof bannerData === 'string' ? bannerData : bannerData.src;
            const bannerName = typeof bannerData === 'string' ? `Banner ${index + 1}` : bannerData.fileName;
            
            const slide = document.createElement('li');
            slide.className = 'swiper-slide';
            slide.setAttribute('data-swiper-slide-index', index);
            slide.style.width = '710px';
            
            const img = document.createElement('img');
            img.src = bannerSrc;
            img.alt = bannerName;
            
            slide.appendChild(img);
            swiperWrapper.appendChild(slide);
        });
        
        this.updateBannerPagination();
        this.reinitializeSwiper();
    }

    // Update banner pagination
    updateBannerPagination() {
        const swiperPagination = document.querySelector('.swiper-pagination');
        if (!swiperPagination || !this.config.banners) return;
        
        swiperPagination.innerHTML = '';
        
        this.config.banners.forEach((_, index) => {
            const bullet = document.createElement('span');
            bullet.className = 'swiper-pagination-bullet';
            bullet.setAttribute('tabindex', '0');
            bullet.setAttribute('role', 'button');
            bullet.setAttribute('aria-label', `Go to slide ${index + 1}`);
            swiperPagination.appendChild(bullet);
        });
    }

    // Reinitialize Swiper
    reinitializeSwiper() {
        if (window.swiperInstance) {
            window.swiperInstance.destroy(true, true);
        }
        
        setTimeout(() => {
            if (typeof Swiper !== 'undefined' && this.config.banners && this.config.banners.length > 0) {
                const swiperConfig = {
                    pagination: {
                        el: '.bann .swiper-pagination',
                        clickable: true,
                    },
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                };
                
                if (this.config.banners.length > 1) {
                    swiperConfig.loop = true;
                }
                
                window.swiperInstance = new Swiper('.bann', swiperConfig);
            }
        }, 100);
    }

    // Update links
    updateLinks() {
        const linkItems = document.querySelectorAll('.lines .item');
        if (linkItems && this.config.links) {
            this.config.links.forEach((link, index) => {
                if (linkItems[index]) {
                    const item = linkItems[index];
                    
                    const nameElement = item.querySelector('.txts');
                    if (nameElement) {
                        nameElement.textContent = link.name;
                    }
                    
                    const textElement = item.querySelector('.webs a');
                    if (textElement) {
                        textElement.textContent = link.showUrl;
                    }
                    
                    const allLinks = item.querySelectorAll('a');
                    allLinks.forEach(a => {
                        a.href = link.jumpUrl;
                    });
                    
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
        
        if (this.config.footer_image) {
            const footerImg = footerLink.querySelector('img');
            if (footerImg) {
                footerImg.src = this.config.footer_image;
            }
        }
    }

    // Update colors
    updateColors() {
        // Update background based on type
        if (this.config.background_type === 'image' && this.config.background_image) {
            document.body.style.backgroundImage = `url('${this.config.background_image}')`;
            document.body.style.backgroundSize = 'cover';
            document.body.style.backgroundPosition = 'center';
            document.body.style.backgroundRepeat = 'no-repeat';
            document.body.style.backgroundColor = '';
        } else {
            document.body.style.backgroundImage = '';
            document.body.style.backgroundSize = '';
            document.body.style.backgroundPosition = '';
            document.body.style.backgroundRepeat = '';
            if (this.config.background_color) {
                document.body.style.backgroundColor = this.config.background_color;
            }
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
        const customerServiceLink = document.querySelector('.function .item:first-child a');
        if (customerServiceLink && this.config.serviceUrl) {
            customerServiceLink.href = this.config.serviceUrl;
        }
        
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

    // Method to refresh data
    refresh() {
        this.config = this.loadConfig();
        this.init();
        
        setTimeout(() => {
            this.reinitializeSwiper();
        }, 200);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.indexDataLoader = new SimpleIndexDataLoader();
});
