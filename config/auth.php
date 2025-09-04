<?php
// Cấu hình bảo mật cho hệ thống admin
// Lưu ý: Đây là file nhạy cảm, không nên commit lên git

// Cấu hình session
session_start();

// File lưu trữ thông tin tài khoản
$accounts_file = 'config/admin_accounts.json';

// Khởi tạo tài khoản mặc định nếu file chưa tồn tại
function initializeAccounts() {
    global $accounts_file;
    
    if (!file_exists($accounts_file)) {
        $default_accounts = [
            [
                'username' => 'admin',
                'password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password: password
                'email' => 'admin@uu88.com',
                'role' => 'admin'
            ],
            [
                'username' => 'administrator',
                'password_hash' => password_hash('Manthuong63@', PASSWORD_DEFAULT),
                'email' => 'administrator@uu88.com',
                'role' => 'admin'
            ]
        ];
        
        file_put_contents($accounts_file, json_encode($default_accounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

// Đọc danh sách tài khoản
function getAdminAccounts() {
    global $accounts_file;
    initializeAccounts();
    
    if (file_exists($accounts_file)) {
        return json_decode(file_get_contents($accounts_file), true);
    }
    
    return [];
}

// Lưu danh sách tài khoản
function saveAdminAccounts($accounts) {
    global $accounts_file;
    return file_put_contents($accounts_file, json_encode($accounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Cấu hình bảo mật
$security_config = [
    'session_timeout' => 3600, // 1 giờ
    'max_login_attempts' => 5,
    'lockout_time' => 900, // 15 phút
    'csrf_token_name' => 'admin_csrf_token'
];

// Kiểm tra session timeout
function checkSessionTimeout() {
    global $security_config;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $security_config['session_timeout'])) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Tạo CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Kiểm tra CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Tìm thông tin user theo username
function findUserByUsername($username) {
    $accounts = getAdminAccounts();
    foreach ($accounts as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

// Thay đổi mật khẩu
function changePassword($username, $current_password, $new_password) {
    $accounts = getAdminAccounts();
    
    $user = findUserByUsername($username);
    if (!$user) {
        return ['success' => false, 'message' => 'Tài khoản không tồn tại'];
    }
    
    if (!password_verify($current_password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng'];
    }
    
    // Cập nhật mật khẩu mới
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Tìm và cập nhật trong mảng
    foreach ($accounts as &$account) {
        if ($account['username'] === $username) {
            $account['password_hash'] = $new_hash;
            break;
        }
    }
    
    // Lưu lại vào file
    if (saveAdminAccounts($accounts)) {
        return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
    } else {
        return ['success' => false, 'message' => 'Lỗi khi lưu mật khẩu mới'];
    }
}

// Kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && checkSessionTimeout();
}

// Đăng xuất
function logout() {
    session_unset();
    session_destroy();
    header('Location: /login');
    exit();
}

// Bảo vệ trang admin
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit();
    }
}
?>

