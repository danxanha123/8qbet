<?php
require_once 'config/auth.php';
require_once 'config/website_config.php';

// Bảo vệ trang admin
requireLogin();

$config = getWebsiteConfig();
$message = '';

// Xử lý cập nhật cấu hình
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = '<div class="alert alert-danger">Token bảo mật không hợp lệ!</div>';
    } else {
        switch ($_POST['action']) {
            case 'update_general':
                $config['title'] = $_POST['title'];
                $config['marquee'] = $_POST['marquee'];
                $config['appUrl'] = $_POST['appUrl'];
                $config['serviceUrl'] = $_POST['serviceUrl'];
                
                if (saveWebsiteConfig($config)) {
                    $message = '<div class="alert alert-success">Cập nhật thông tin chung thành công!</div>';
                } else {
                    $message = '<div class="alert alert-danger">Lỗi khi lưu cấu hình!</div>';
                }
                break;
                
            case 'update_logo':
                $upload_result = uploadFile($_FILES['logo'], 'asset/images');
                if ($upload_result['success']) {
                    $config['logo'] = $upload_result['path'];
                    if (saveWebsiteConfig($config)) {
                        $message = '<div class="alert alert-success">Cập nhật logo thành công!</div>';
                    }
                } else {
                    $message = '<div class="alert alert-danger">' . $upload_result['message'] . '</div>';
                }
                break;
                
            case 'update_logo_settings':
                $config['logo_size'] = (int)$_POST['logo_size'];
                if (saveWebsiteConfig($config)) {
                    $message = '<div class="alert alert-success">Cập nhật kích thước logo thành công!</div>';
                } else {
                    $message = '<div class="alert alert-danger">Lỗi khi lưu cấu hình!</div>';
                }
                break;
                
            case 'update_favicon':
                $upload_result = uploadFile($_FILES['favicon'], 'asset/images');
                if ($upload_result['success']) {
                    $config['favicon'] = $upload_result['path'];
                    if (saveWebsiteConfig($config)) {
                        $message = '<div class="alert alert-success">Cập nhật favicon thành công!</div>';
                    }
                } else {
                    $message = '<div class="alert alert-danger">' . $upload_result['message'] . '</div>';
                }
                break;
                
            case 'update_footer':
                $config['footer_link'] = $_POST['footer_link'];
                if (isset($_FILES['footer_image']) && $_FILES['footer_image']['size'] > 0) {
                    $upload_result = uploadFile($_FILES['footer_image'], 'asset/images');
                    if ($upload_result['success']) {
                        $config['footer_image'] = $upload_result['path'];
                    }
                }
                if (saveWebsiteConfig($config)) {
                    $message = '<div class="alert alert-success">Cập nhật footer thành công!</div>';
                } else {
                    $message = '<div class="alert alert-danger">Lỗi khi lưu cấu hình!</div>';
                }
                break;
                
            case 'update_colors':
                $config['background_type'] = $_POST['background_type'];
                $config['background_color'] = $_POST['background_color'];
                $config['container_bg_color'] = $_POST['container_bg_color'];
                $config['marquee_color'] = $_POST['marquee_color'];
                if (saveWebsiteConfig($config)) {
                    $message = '<div class="alert alert-success">Cập nhật màu sắc thành công!</div>';
                } else {
                    $message = '<div class="alert alert-danger">Lỗi khi lưu cấu hình!</div>';
                }
                break;
                
            case 'update_background':
                $upload_result = uploadFile($_FILES['background'], 'asset/images');
                if ($upload_result['success']) {
                    $config['background'] = $upload_result['path'];
                    if (saveWebsiteConfig($config)) {
                        $message = '<div class="alert alert-success">Cập nhật background thành công!</div>';
                    }
                } else {
                    $message = '<div class="alert alert-danger">' . $upload_result['message'] . '</div>';
                }
                break;
                
            case 'update_links':
                $config['links'] = [];
                foreach ($_POST['links'] as $link) {
                    if (!empty($link['name']) && !empty($link['jumpUrl'])) {
                        $config['links'][] = [
                            'name' => $link['name'],
                            'showUrl' => $link['showUrl'],
                            'jumpUrl' => $link['jumpUrl'],
                            'icon' => $link['icon']
                        ];
                    }
                }
                
                if (saveWebsiteConfig($config)) {
                    $message = '<div class="alert alert-success">Cập nhật danh sách link thành công!</div>';
                } else {
                    $message = '<div class="alert alert-danger">Lỗi khi lưu cấu hình!</div>';
                }
                break;
                
            case 'upload_image':
                $upload_result = uploadFile($_FILES['image'], 'asset/images');
                if ($upload_result['success']) {
                    $config['banners'][] = $upload_result['path'];
                    if (saveWebsiteConfig($config)) {
                        $message = '<div class="alert alert-success">Upload ảnh thành công!</div>';
                    }
                } else {
                    $message = '<div class="alert alert-danger">' . $upload_result['message'] . '</div>';
                }
                break;
                
            case 'change_password':
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                
                if ($new_password !== $confirm_password) {
                    $message = '<div class="alert alert-danger">Mật khẩu mới và xác nhận mật khẩu không khớp!</div>';
                } elseif (strlen($new_password) < 6) {
                    $message = '<div class="alert alert-danger">Mật khẩu mới phải có ít nhất 6 ký tự!</div>';
                } else {
                    $result = changePassword($_SESSION['admin_username'], $current_password, $new_password);
                    if ($result['success']) {
                        $message = '<div class="alert alert-success">' . $result['message'] . '</div>';
                    } else {
                        $message = '<div class="alert alert-danger">' . $result['message'] . '</div>';
                    }
                }
                break;
        }
    }
}

// Xử lý xóa banner
if (isset($_GET['delete_banner']) && is_numeric($_GET['delete_banner'])) {
    $index = (int)$_GET['delete_banner'];
    if (isset($config['banners'][$index])) {
        $banner_path = $config['banners'][$index];
        unset($config['banners'][$index]);
        $config['banners'] = array_values($config['banners']); // Re-index array
        
        if (saveWebsiteConfig($config)) {
            $message = '<div class="alert alert-success">Xóa banner thành công!</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - 8qbet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 10px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .banner-preview {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e1e5e9;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .language-selector {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <img src="asset/images/68b29a71536f9.png" alt="8qbet" style="width: 60px; height: 60px;">
                        <h5 class="mt-2">Admin Panel</h5>
                        <small>Xin chào, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></small>
                    </div>
                    
                                         <nav class="nav flex-column">
                         <a class="nav-link active" href="#general" data-bs-toggle="tab">
                             <i class="fas fa-cog"></i> Thông tin chung
                         </a>
                         <a class="nav-link" href="#branding" data-bs-toggle="tab">
                             <i class="fas fa-palette"></i> Logo & Background
                         </a>
                         <a class="nav-link" href="#colors" data-bs-toggle="tab">
                             <i class="fas fa-paint-brush"></i> Màu sắc
                         </a>
                         <a class="nav-link" href="#favicon" data-bs-toggle="tab">
                             <i class="fas fa-star"></i> Favicon
                         </a>
                         <a class="nav-link" href="#footer" data-bs-toggle="tab">
                             <i class="fas fa-shoe-prints"></i> Footer
                         </a>
                         <a class="nav-link" href="#banners" data-bs-toggle="tab">
                             <i class="fas fa-images"></i> Quản lý banner
                         </a>
                         <a class="nav-link" href="#links" data-bs-toggle="tab">
                             <i class="fas fa-link"></i> Quản lý links
                         </a>
                         <a class="nav-link" href="#password" data-bs-toggle="tab">
                             <i class="fas fa-key"></i> Đổi mật khẩu
                         </a>
                         <a class="nav-link" href="default.php" target="_blank">
                             <i class="fas fa-eye"></i> Xem trang chủ
                         </a>
                         <a class="nav-link" href="/logout">
                             <i class="fas fa-sign-out-alt"></i> Đăng xuất
                         </a>
                     </nav>
                </div>
            </div>

                         <!-- Main Content -->
             <div class="col-md-9 col-lg-10">
                 <div class="main-content p-4">
                    <!-- Language Selector -->
                    <div class="language-selector">
                        <select class="form-select form-select-sm" id="languageSelector" onchange="changeLanguage(this.value)">
                            <option value="dashboard.php" selected>Tiếng Việt</option>
                            <option value="dashboardcn.php">中文</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                            <small class="text-muted">Đăng nhập với tài khoản: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></small>
                        </div>
                        <a href="default.php" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> Xem trang chủ
                        </a>
                    </div>

                    <?php echo $message; ?>

                    <div class="tab-content">
                        <!-- Thông tin chung -->
                        <div class="tab-pane fade show active" id="general">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-cog"></i> Thông tin chung</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_general">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tiêu đề trang</label>
                                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($config['title']); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">URL App</label>
                                                <input type="url" class="form-control" name="appUrl" value="<?php echo htmlspecialchars($config['appUrl']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nội dung chạy chữ</label>
                                            <textarea class="form-control" name="marquee" rows="3" required><?php echo htmlspecialchars($config['marquee']); ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">URL Customer Service</label>
                                            <input type="url" class="form-control" name="serviceUrl" value="<?php echo htmlspecialchars($config['serviceUrl']); ?>" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Lưu thay đổi
                                        </button>
                                    </form>
                                </div>
                            </div>
                                                 </div>

                         <!-- Quản lý Logo & Background -->
                         <div class="tab-pane fade" id="branding">
                             <div class="row">
                                 <!-- Logo Management -->
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-image"></i> Quản lý Logo</h5>
                                         </div>
                                         <div class="card-body">
                                             <div class="text-center mb-3">
                                                 <img src="<?php echo htmlspecialchars($config['logo']); ?>" alt="Current Logo" style="max-width: 200px; max-height: 100px; object-fit: contain;">
                                                 <p class="mt-2 text-muted">Logo hiện tại</p>
                                             </div>
                                             
                                             <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" name="action" value="update_logo">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">Upload logo mới</label>
                                                     <input type="file" class="form-control" name="logo" accept="image/*" required>
                                                     <small class="text-muted">Khuyến nghị: PNG với nền trong suốt, kích thước tối đa 200x100px</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-upload"></i> Cập nhật Logo
                                                 </button>
                                             </form>
                                             
                                             <hr>
                                             
                                             <form method="POST">
                                                 <input type="hidden" name="action" value="update_logo_settings">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">Kích thước logo: <span id="logo-size-value"><?php echo $config['logo_size']; ?>%</span></label>
                                                     <input type="range" class="form-range" name="logo_size" id="logo-size-slider" min="50" max="200" value="<?php echo $config['logo_size']; ?>" oninput="updateLogoSize(this.value)">
                                                     <small class="text-muted">Kéo thanh để điều chỉnh kích thước logo</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-success w-100">
                                                     <i class="fas fa-save"></i> Lưu kích thước
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <!-- Background Management -->
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-image"></i> Quản lý Background</h5>
                                         </div>
                                         <div class="card-body">
                                             <div class="text-center mb-3">
                                                 <img src="<?php echo htmlspecialchars($config['background']); ?>" alt="Current Background" style="max-width: 200px; max-height: 100px; object-fit: cover; border-radius: 5px;">
                                                 <p class="mt-2 text-muted">Background hiện tại</p>
                                             </div>
                                             
                                             <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" name="action" value="update_background">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">Upload background mới</label>
                                                     <input type="file" class="form-control" name="background" accept="image/*" required>
                                                     <small class="text-muted">Khuyến nghị: JPG/PNG, kích thước tối đa 1920x1080px</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-upload"></i> Cập nhật Background
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- Quản lý màu sắc -->
                         <div class="tab-pane fade" id="colors">
                             <div class="row">
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-paint-brush"></i> Màu sắc</h5>
                                         </div>
                                         <div class="card-body">
                                             <form method="POST">
                                                 <input type="hidden" name="action" value="update_colors">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">Loại background</label>
                                                     <select class="form-select" name="background_type" onchange="toggleBackgroundType(this.value)">
                                                         <option value="image" <?php echo $config['background_type'] === 'image' ? 'selected' : ''; ?>>Hình ảnh</option>
                                                         <option value="color" <?php echo $config['background_type'] === 'color' ? 'selected' : ''; ?>>Màu sắc</option>
                                                     </select>
                                                 </div>
                                                 
                                                 <div class="mb-3" id="background-color-group" style="display: <?php echo $config['background_type'] === 'color' ? 'block' : 'none'; ?>;">
                                                     <label class="form-label">Màu nền</label>
                                                     <input type="color" class="form-control form-control-color w-100" name="background_color" value="<?php echo $config['background_color']; ?>">
                                                 </div>
                                                 
                                                                                                   <div class="mb-3">
                                                      <label class="form-label">Màu nền container</label>
                                                      <input type="color" class="form-control form-control-color w-100" name="container_bg_color" value="<?php echo $config['container_bg_color']; ?>">
                                                  </div>
                                                  
                                                  <div class="mb-3">
                                                      <label class="form-label">Màu chữ chạy (Marquee)</label>
                                                      <input type="color" class="form-control form-control-color w-100" name="marquee_color" value="<?php echo $config['marquee_color']; ?>">
                                                  </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-save"></i> Lưu màu sắc
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- Quản lý Favicon -->
                         <div class="tab-pane fade" id="favicon">
                             <div class="row">
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-star"></i> Quản lý Favicon</h5>
                                         </div>
                                         <div class="card-body">
                                             <div class="text-center mb-3">
                                                 <img src="<?php echo htmlspecialchars($config['favicon']); ?>" alt="Current Favicon" style="width: 32px; height: 32px; object-fit: contain;">
                                                 <p class="mt-2 text-muted">Favicon hiện tại</p>
                                             </div>
                                             
                                             <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" name="action" value="update_favicon">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">Upload favicon mới</label>
                                                     <input type="file" class="form-control" name="favicon" accept="image/*,.ico" required>
                                                     <small class="text-muted">Hỗ trợ: PNG, ICO, kích thước tối đa 32x32px</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-upload"></i> Cập nhật Favicon
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- Quản lý Footer -->
                         <div class="tab-pane fade" id="footer">
                             <div class="row">
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-shoe-prints"></i> Quản lý Footer</h5>
                                         </div>
                                         <div class="card-body">
                                             <div class="text-center mb-3">
                                                 <img src="<?php echo htmlspecialchars($config['footer_image']); ?>" alt="Current Footer Image" style="max-width: 200px; max-height: 100px; object-fit: cover; border-radius: 5px;">
                                                 <p class="mt-2 text-muted">Ảnh footer hiện tại</p>
                                             </div>
                                             
                                             <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" name="action" value="update_footer">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">Link footer</label>
                                                     <input type="url" class="form-control" name="footer_link" value="<?php echo htmlspecialchars($config['footer_link']); ?>" required>
                                                 </div>
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">Upload ảnh footer mới (tùy chọn)</label>
                                                     <input type="file" class="form-control" name="footer_image" accept="image/*">
                                                     <small class="text-muted">Để trống nếu không muốn thay đổi ảnh</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-save"></i> Cập nhật Footer
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- Quản lý banner -->
                         <div class="tab-pane fade" id="banners">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-images"></i> Quản lý banner</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data" class="mb-4">
                                        <input type="hidden" name="action" value="upload_image">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-8">
                                                <label class="form-label">Upload ảnh banner mới</label>
                                                <input type="file" class="form-control" name="image" accept="image/*" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="submit" class="btn btn-primary d-block w-100">
                                                    <i class="fas fa-upload"></i> Upload
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <h6>Banner hiện tại:</h6>
                                    <div class="row">
                                        <?php foreach ($config['banners'] as $index => $banner): ?>
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                <img src="<?php echo htmlspecialchars($banner); ?>" class="card-img-top" alt="Banner <?php echo $index + 1; ?>">
                                                <div class="card-body p-2">
                                                    <a href="?delete_banner=<?php echo $index; ?>" class="btn btn-danger btn-sm w-100" onclick="return confirm('Bạn có chắc muốn xóa banner này?')">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quản lý links -->
                        <div class="tab-pane fade" id="links">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-link"></i> Quản lý links</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_links">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        
                                        <div id="links-container">
                                            <?php foreach ($config['links'] as $index => $link): ?>
                                            <div class="row mb-3 link-item">
                                                <div class="col-md-2">
                                                    <label class="form-label">Tên link</label>
                                                    <input type="text" class="form-control" name="links[<?php echo $index; ?>][name]" value="<?php echo htmlspecialchars($link['name']); ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Hiển thị</label>
                                                    <input type="text" class="form-control" name="links[<?php echo $index; ?>][showUrl]" value="<?php echo htmlspecialchars($link['showUrl']); ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">URL đích</label>
                                                    <input type="url" class="form-control" name="links[<?php echo $index; ?>][jumpUrl]" value="<?php echo htmlspecialchars($link['jumpUrl']); ?>" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Icon</label>
                                                    <input type="text" class="form-control" name="links[<?php echo $index; ?>][icon]" value="<?php echo htmlspecialchars($link['icon']); ?>" required>
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-danger btn-sm d-block w-100 remove-link">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <button type="button" class="btn btn-success mb-3" id="add-link">
                                            <i class="fas fa-plus"></i> Thêm link
                                        </button>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Lưu thay đổi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Thay đổi mật khẩu -->
                        <div class="tab-pane fade" id="password">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-key"></i> Thay đổi mật khẩu</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="change_password">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Tài khoản hiện tại</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['admin_username']); ?>" readonly>
                                                    <small class="text-muted">Tài khoản đang đăng nhập</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Mật khẩu hiện tại</label>
                                                    <input type="password" class="form-control" name="current_password" required>
                                                    <small class="text-muted">Nhập mật khẩu hiện tại để xác nhận</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Mật khẩu mới</label>
                                                    <input type="password" class="form-control" name="new_password" required>
                                                    <small class="text-muted">Mật khẩu mới phải có ít nhất 6 ký tự</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Xác nhận mật khẩu mới</label>
                                                    <input type="password" class="form-control" name="confirm_password" required>
                                                    <small class="text-muted">Nhập lại mật khẩu mới</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key"></i> Thay đổi mật khẩu
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Thêm link mới
        document.getElementById('add-link').addEventListener('click', function() {
            const container = document.getElementById('links-container');
            const linkCount = container.children.length;
            const newLink = document.createElement('div');
            newLink.className = 'row mb-3 link-item';
            newLink.innerHTML = `
                <div class="col-md-2">
                    <label class="form-label">Tên link</label>
                    <input type="text" class="form-control" name="links[${linkCount}][name]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hiển thị</label>
                    <input type="text" class="form-control" name="links[${linkCount}][showUrl]" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">URL đích</label>
                    <input type="url" class="form-control" name="links[${linkCount}][jumpUrl]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Icon</label>
                    <input type="text" class="form-control" name="links[${linkCount}][icon]" value="asset/images/8qbet.png" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm d-block w-100 remove-link">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(newLink);
        });

        // Xóa link
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-link') || e.target.closest('.remove-link')) {
                if (confirm('Bạn có chắc muốn xóa link này?')) {
                    e.target.closest('.link-item').remove();
                }
            }
        });

        // Cập nhật kích thước logo
        function updateLogoSize(value) {
            document.getElementById('logo-size-value').textContent = value + '%';
        }

        // Toggle background type
        function toggleBackgroundType(type) {
            const colorGroup = document.getElementById('background-color-group');
            if (type === 'color') {
                colorGroup.style.display = 'block';
            } else {
                colorGroup.style.display = 'none';
            }
        }

        // 切换语言
        function changeLanguage(selectedValue) {
            if (selectedValue !== window.location.pathname.split('/').pop()) {
                window.location.href = selectedValue;
            }
        }

        
    </script>
</body>
</html>
