<?php
// Cấu hình website có thể chỉnh sửa
$website_config_file = 'config/website_data.json';

// Đọc cấu hình website
function getWebsiteConfig() {
    global $website_config_file;
    
    if (file_exists($website_config_file)) {
        $config = json_decode(file_get_contents($website_config_file), true);
    } else {
        // Cấu hình mặc định
        $config = [
            'title' => 'UU88.com Kiểm Tra Độ An Toàn Link Truy Cập',
            'favicon' => 'asset/images/favicon.ico',
            'logo' => 'asset/images/logotu.png',
            'logo_size' => 100, // Kích thước logo (%)
            'background' => 'asset/images/background.jpg',
            'background_type' => 'image', // 'image' hoặc 'color'
            'background_color' => '#667eea',
            'container_bg_color' => '#ffffff',
            'marquee_color' => '#ffffff',
            'footer_image' => 'asset/images/ditu.jpg',
            'footer_link' => 'https://uu88.now/m/app',
            'banners' => [
                'asset/images/banner1.jpg',
                'asset/images/banner2.jpg', 
                'asset/images/banner3.jpg',
                'asset/images/banner4.jpg'
            ],
            'marquee' => 'Chào mừng đến với UU88.com – Ưu đãi nạp đầu, Ngày hội thành viên hàng tháng, Cấp độ VIP đặc biệt, Thưởng giới thiệu bạn bè, Nạp rút tiền siêu nhanh – Bảo mật an toàn, không rủi ro!',
            'links' => [
                [
                    'name' => 'Link1',
                    'showUrl' => 'NHẬN QUÀ NGAY',
                    'jumpUrl' => 'https://16uu88.com/',
                    'icon' => 'asset/images/uu88.png'
                ],
                [
                    'name' => 'Link2', 
                    'showUrl' => 'ĐÁNG TIN CẬY',
                    'jumpUrl' => 'https://17uu88.com/',
                    'icon' => 'asset/images/uu88.png'
                ],
                [
                    'name' => 'Link3',
                    'showUrl' => 'BẢO MẬT CAO', 
                    'jumpUrl' => 'https://18uu88.com/',
                    'icon' => 'asset/images/uu88.png'
                ],
                [
                    'name' => 'Link4',
                    'showUrl' => 'RÚT TIỀN NHANH',
                    'jumpUrl' => 'https://19uu88.com/',
                    'icon' => 'asset/images/uu88.png'
                ]
            ],
            'appUrl' => 'https://uu88.now/m/app',
            'serviceUrl' => 'https://vm.providesupport.com/1mj35zszcixsn1bgfeu2ulkfvq'
        ];
        
        // Lưu cấu hình mặc định
        saveWebsiteConfig($config);
    }
    
    return $config;
}

// Lưu cấu hình website
function saveWebsiteConfig($config) {
    global $website_config_file;
    
    $json_data = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($website_config_file, $json_data);
}

// Upload file
function uploadFile($file, $target_dir) {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'ico'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'message' => 'Chỉ cho phép file ảnh (jpg, jpeg, png, gif, ico)'];
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        return ['success' => false, 'message' => 'File quá lớn (tối đa 5MB)'];
    }
    
    $new_filename = uniqid() . '.' . $file_extension;
    $target_path = $target_dir . '/' . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => $target_path];
    } else {
        return ['success' => false, 'message' => 'Lỗi upload file'];
    }
}
?>
