// Cấu hình website 8Qbet
const websiteConfig = {
    title: "8Qbet.com Kiểm Tra Độ An Toàn Link Truy Cập",
    favicon: "asset/images/68b29b5d9a666.png",
    logo: "asset/images/68b29a71536f9.png",
    logo_size: 50,
    background: "asset/images/background.jpg",
    background_type: "color", // 'image' hoặc 'color'
    background_color: "#121712",
    container_bg_color: "#5a1616",
    marquee_color: "#ffffff",
    footer_image: "asset/images/68b29a2d69d64.jpeg",
    footer_link: "https://www.93375347.com/",
    banners: [
        "asset/images/68b29ac2d3515.jpg",
        "asset/images/68b29ace6e010.jpg",
        "asset/images/68b29ad604fe7.jpg",
        "asset/images/68b29addeda4e.jpg"
    ],
    marquee: "Chào mừng đến với 8Qbet.com – Ưu đãi nạp đầu, Ngày hội thành viên hàng tháng, Cấp độ VIP đặc biệt, Thưởng giới thiệu bạn bè, Nạp rút tiền siêu nhanh – Bảo mật an toàn, không rủi ro!",
    links: [
        {
            name: "Link1",
            showUrl: "NHẬN QUÀ NGAY",
            jumpUrl: "https://www.93375347.com/",
            icon: "asset/images/8q.png"
        },
        {
            name: "Link2",
            showUrl: "ĐÁNG TIN CẬY",
            jumpUrl: "https://www.93375347.com/",
            icon: "asset/images/8q.png"
        },
        {
            name: "Link3",
            showUrl: "BẢO MẬT CAO",
            jumpUrl: "https://www.93375347.com/",
            icon: "asset/images/8q.png"
        },
        {
            name: "Link4",
            showUrl: "RÚT TIỀN NHANH",
            jumpUrl: "https://www.93375347.com/",
            icon: "asset/images/8q.png"
        }
    ],
    appUrl: "https://www.93375347.com/down",
    serviceUrl: "https://xzjhsn5po7.lyau1vg8.com/chatwindow.aspx?siteId=65002665&planId=54c0e15d-3356-4460-abe0-648cd6d0bbee"
};

// Hàm tạo thời gian phản hồi ngẫu nhiên
function getRandomResponseTime() {
    return Math.floor(Math.random() * (60 - 20 + 1)) + 20;
}

// Hàm khởi tạo trang
function initializePage() {
    // Cập nhật title
    document.title = websiteConfig.title;
    
    // Cập nhật favicon
    const favicon = document.querySelector('link[rel="icon"]');
    if (favicon) {
        favicon.href = websiteConfig.favicon;
    }
    
    // Cập nhật logo
    const logo = document.querySelector('.tops img');
    if (logo) {
        logo.src = websiteConfig.logo;
        logo.style.width = websiteConfig.logo_size + '%';
    }
    
    // Cập nhật background
    const body = document.body;
    if (websiteConfig.background_type === 'image') {
        body.style.backgroundImage = `url('${websiteConfig.background}')`;
        body.style.backgroundSize = 'cover';
        body.style.backgroundPosition = 'center';
        body.style.backgroundRepeat = 'no-repeat';
        body.style.backgroundAttachment = 'fixed';
    } else {
        body.style.backgroundColor = websiteConfig.background_color;
    }
    
    // Cập nhật container background
    const container = document.querySelector('.mobile-wrap');
    if (container) {
        container.style.backgroundColor = websiteConfig.container_bg_color;
    }
    
    // Cập nhật marquee
    const marquee = document.querySelector('.marquee');
    if (marquee) {
        marquee.style.color = websiteConfig.marquee_color + ' !important';
        const marqueeContent = marquee.querySelector('marquee');
        if (marqueeContent) {
            marqueeContent.textContent = websiteConfig.marquee;
        }
    }
    
    // Cập nhật banners
    const swiperWrapper = document.getElementById('swiper-wrapper');
    if (swiperWrapper) {
        swiperWrapper.innerHTML = '';
        websiteConfig.banners.forEach((banner, index) => {
            const slide = document.createElement('li');
            slide.className = 'swiper-slide';
            slide.setAttribute('data-swiper-slide-index', index);
            slide.style.width = '710px';
            slide.innerHTML = `<img src="${banner}" alt="8Qbet">`;
            swiperWrapper.appendChild(slide);
        });
    }
    
    // Cập nhật pagination
    const pagination = document.querySelector('.swiper-pagination');
    if (pagination) {
        pagination.innerHTML = '';
        websiteConfig.banners.forEach((_, index) => {
            const bullet = document.createElement('span');
            bullet.className = 'swiper-pagination-bullet';
            bullet.setAttribute('tabindex', '0');
            bullet.setAttribute('role', 'button');
            bullet.setAttribute('aria-label', `Go to slide ${index + 1}`);
            pagination.appendChild(bullet);
        });
    }
    
    // Cập nhật links
    const linesContainer = document.getElementById('lines');
    if (linesContainer) {
        linesContainer.innerHTML = '';
        websiteConfig.links.forEach(link => {
            const item = document.createElement('div');
            item.className = 'item';
            item.innerHTML = `
                <a href="${link.jumpUrl}" target="_blank" rel="noopener noreferrer">
                    <div class="ico fl">
                        <img src="${link.icon}" alt="8Qbet">
                    </div>
                    <div class="txts fl">
                        ${link.name}
                    </div>
                    <div class="ms fl">${getRandomResponseTime()}ms</div>
                </a>
                <div class="webs fl">
                    <a href="${link.jumpUrl}" target="_blank" rel="noopener noreferrer">
                        ${link.showUrl}
                    </a>
                </div>
                <div class="btns fr">
                    <a href="${link.jumpUrl}" target="_blank" rel="noopener noreferrer">
                        <button>ĐI VÀO</button>
                    </a>
                </div>
            `;
            linesContainer.appendChild(item);
        });
    }
    
    // Cập nhật footer
    const footer = document.querySelector('.footer a');
    if (footer) {
        footer.href = websiteConfig.footer_link;
        const footerImg = footer.querySelector('img');
        if (footerImg) {
            footerImg.src = websiteConfig.footer_image;
        }
    }
    
    // Cập nhật function buttons
    const functionItems = document.querySelectorAll('.function .item a');
    if (functionItems.length >= 2) {
        functionItems[0].href = websiteConfig.serviceUrl; // Customer service
        functionItems[1].href = websiteConfig.appUrl; // App download
    }
}
