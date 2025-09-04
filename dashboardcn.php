<?php
require_once 'config/auth.php';
require_once 'config/website_config.php';

// 保护管理页面
requireLogin();

$config = getWebsiteConfig();
$message = '';

// 处理配置更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = '<div class="alert alert-danger">安全令牌无效！</div>';
    } else {
        switch ($_POST['action']) {
            case 'update_general':
                $config['title'] = $_POST['title'];
                $config['marquee'] = $_POST['marquee'];
                $config['appUrl'] = $_POST['appUrl'];
                $config['serviceUrl'] = $_POST['serviceUrl'];
                
                if (saveWebsiteConfig($config)) {
                    $message = '<div class="alert alert-success">更新一般信息成功！</div>';
                } else {
                    $message = '<div class="alert alert-danger">保存配置时出错！</div>';
                }
                break;
                
            case 'update_logo':
                $upload_result = uploadFile($_FILES['logo'], 'asset/images');
                if ($upload_result['success']) {
                    $config['logo'] = $upload_result['path'];
                    if (saveWebsiteConfig($config)) {
                        $message = '<div class="alert alert-success">更新Logo成功！</div>';
                    }
                } else {
                    $message = '<div class="alert alert-danger">' . $upload_result['message'] . '</div>';
                }
                break;
                
            case 'update_logo_settings':
                $config['logo_size'] = (int)$_POST['logo_size'];
                if (saveWebsiteConfig($config)) {
                    $message = '<div class="alert alert-success">更新Logo尺寸成功！</div>';
                } else {
                    $message = '<div class="alert alert-danger">保存配置时出错！</div>';
                }
                break;
                
            case 'update_favicon':
                $upload_result = uploadFile($_FILES['favicon'], 'asset/images');
                if ($upload_result['success']) {
                    $config['favicon'] = $upload_result['path'];
                    if (saveWebsiteConfig($config)) {
                        $message = '<div class="alert alert-success">更新Favicon成功！</div>';
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
                    $message = '<div class="alert alert-success">更新页脚成功！</div>';
                } else {
                    $message = '<div class="alert alert-danger">保存配置时出错！</div>';
                }
                break;
                
            case 'update_colors':
                $config['background_type'] = $_POST['background_type'];
                $config['background_color'] = $_POST['background_color'];
                $config['container_bg_color'] = $_POST['container_bg_color'];
                $config['marquee_color'] = $_POST['marquee_color'];
                if (saveWebsiteConfig($config)) {
                    $message = '<div class="alert alert-success">更新颜色成功！</div>';
                } else {
                    $message = '<div class="alert alert-danger">保存配置时出错！</div>';
                }
                break;
                
            case 'update_background':
                $upload_result = uploadFile($_FILES['background'], 'asset/images');
                if ($upload_result['success']) {
                    $config['background'] = $upload_result['path'];
                    if (saveWebsiteConfig($config)) {
                        $message = '<div class="alert alert-success">更新背景成功！</div>';
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
                    $message = '<div class="alert alert-success">更新链接列表成功！</div>';
                } else {
                    $message = '<div class="alert alert-danger">保存配置时出错！</div>';
                }
                break;
                
            case 'upload_image':
                $upload_result = uploadFile($_FILES['image'], 'asset/images');
                if ($upload_result['success']) {
                    $config['banners'][] = $upload_result['path'];
                    if (saveWebsiteConfig($config)) {
                        $message = '<div class="alert alert-success">上传图片成功！</div>';
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
                    $message = '<div class="alert alert-danger">新密码和确认密码不匹配！</div>';
                } elseif (strlen($new_password) < 6) {
                    $message = '<div class="alert alert-danger">新密码必须至少6个字符！</div>';
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

// 处理删除横幅
if (isset($_GET['delete_banner']) && is_numeric($_GET['delete_banner'])) {
    $index = (int)$_GET['delete_banner'];
    if (isset($config['banners'][$index])) {
        $banner_path = $config['banners'][$index];
        unset($config['banners'][$index]);
        $config['banners'] = array_values($config['banners']); // 重新索引数组
        
        if (saveWebsiteConfig($config)) {
            $message = '<div class="alert alert-success">删除横幅成功！</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理仪表板 - 8qbet</title>
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
                        <h5 class="mt-2">管理面板</h5>
                        <small>您好，<?php echo htmlspecialchars($_SESSION['admin_username']); ?></small>
                    </div>
                    
                                         <nav class="nav flex-column">
                         <a class="nav-link active" href="#general" data-bs-toggle="tab">
                             <i class="fas fa-cog"></i> 一般信息
                         </a>
                         <a class="nav-link" href="#branding" data-bs-toggle="tab">
                             <i class="fas fa-palette"></i> Logo和背景
                         </a>
                         <a class="nav-link" href="#colors" data-bs-toggle="tab">
                             <i class="fas fa-paint-brush"></i> 颜色
                         </a>
                         <a class="nav-link" href="#favicon" data-bs-toggle="tab">
                             <i class="fas fa-star"></i> 网站图标
                         </a>
                         <a class="nav-link" href="#footer" data-bs-toggle="tab">
                             <i class="fas fa-shoe-prints"></i> 页脚
                         </a>
                         <a class="nav-link" href="#banners" data-bs-toggle="tab">
                             <i class="fas fa-images"></i> 横幅管理
                         </a>
                         <a class="nav-link" href="#links" data-bs-toggle="tab">
                             <i class="fas fa-link"></i> 链接管理
                         </a>
                         <a class="nav-link" href="#password" data-bs-toggle="tab">
                             <i class="fas fa-key"></i> 更改密码
                         </a>
                         <a class="nav-link" href="default.php" target="_blank">
                             <i class="fas fa-eye"></i> 查看主页
                         </a>
                         <a class="nav-link" href="/logout">
                             <i class="fas fa-sign-out-alt"></i> 登出
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
                            <option value="dashboardcn.php" selected>中文</option>
                            <option value="dashboard.php">Tiếng Việt</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2><i class="fas fa-tachometer-alt"></i> 仪表板</h2>
                            <small class="text-muted">登录账户：<strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></small>
                        </div>
                        <a href="default.php" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> 查看主页
                        </a>
                    </div>

                    <?php echo $message; ?>

                    <div class="tab-content">
                        <!-- 一般信息 -->
                        <div class="tab-pane fade show active" id="general">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-cog"></i> 一般信息</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_general">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">页面标题</label>
                                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($config['title']); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">应用URL</label>
                                                <input type="url" class="form-control" name="appUrl" value="<?php echo htmlspecialchars($config['appUrl']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">滚动文字内容</label>
                                            <textarea class="form-control" name="marquee" rows="3" required><?php echo htmlspecialchars($config['marquee']); ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">客服URL</label>
                                            <input type="url" class="form-control" name="serviceUrl" value="<?php echo htmlspecialchars($config['serviceUrl']); ?>" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> 保存更改
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                         <!-- Logo和背景管理 -->
                         <div class="tab-pane fade" id="branding">
                             <div class="row">
                                 <!-- Logo管理 -->
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-image"></i> Logo管理</h5>
                                         </div>
                                         <div class="card-body">
                                             <div class="text-center mb-3">
                                                 <img src="<?php echo htmlspecialchars($config['logo']); ?>" alt="当前Logo" style="max-width: 200px; max-height: 100px; object-fit: contain;">
                                                 <p class="mt-2 text-muted">当前Logo</p>
                                             </div>
                                             
                                             <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" name="action" value="update_logo">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">上传新Logo</label>
                                                     <input type="file" class="form-control" name="logo" accept="image/*" required>
                                                     <small class="text-muted">建议：PNG格式透明背景，最大尺寸200x100px</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-upload"></i> 更新Logo
                                                 </button>
                                             </form>
                                             
                                             <hr>
                                             
                                             <form method="POST">
                                                 <input type="hidden" name="action" value="update_logo_settings">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">Logo尺寸：<span id="logo-size-value"><?php echo $config['logo_size']; ?>%</span></label>
                                                     <input type="range" class="form-range" name="logo_size" id="logo-size-slider" min="50" max="200" value="<?php echo $config['logo_size']; ?>" oninput="updateLogoSize(this.value)">
                                                     <small class="text-muted">拖动滑块调整Logo尺寸</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-success w-100">
                                                     <i class="fas fa-save"></i> 保存尺寸
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <!-- 背景管理 -->
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-image"></i> 背景管理</h5>
                                         </div>
                                         <div class="card-body">
                                             <div class="text-center mb-3">
                                                 <img src="<?php echo htmlspecialchars($config['background']); ?>" alt="当前背景" style="max-width: 200px; max-height: 100px; object-fit: cover; border-radius: 5px;">
                                                 <p class="mt-2 text-muted">当前背景</p>
                                             </div>
                                             
                                             <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" name="action" value="update_background">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">上传新背景</label>
                                                     <input type="file" class="form-control" name="background" accept="image/*" required>
                                                     <small class="text-muted">建议：JPG/PNG格式，最大尺寸1920x1080px</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-upload"></i> 更新背景
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- 颜色管理 -->
                         <div class="tab-pane fade" id="colors">
                             <div class="row">
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-paint-brush"></i> 颜色</h5>
                                         </div>
                                         <div class="card-body">
                                             <form method="POST">
                                                 <input type="hidden" name="action" value="update_colors">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">背景类型</label>
                                                     <select class="form-select" name="background_type" onchange="toggleBackgroundType(this.value)">
                                                         <option value="image" <?php echo $config['background_type'] === 'image' ? 'selected' : ''; ?>>图片</option>
                                                         <option value="color" <?php echo $config['background_type'] === 'color' ? 'selected' : ''; ?>>颜色</option>
                                                     </select>
                                                 </div>
                                                 
                                                 <div class="mb-3" id="background-color-group" style="display: <?php echo $config['background_type'] === 'color' ? 'block' : 'none'; ?>;">
                                                     <label class="form-label">背景颜色</label>
                                                     <input type="color" class="form-control form-control-color w-100" name="background_color" value="<?php echo $config['background_color']; ?>">
                                                 </div>
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">容器背景颜色</label>
                                                     <input type="color" class="form-control form-control-color w-100" name="container_bg_color" value="<?php echo $config['container_bg_color']; ?>">
                                                 </div>
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">滚动文字颜色</label>
                                                     <input type="color" class="form-control form-control-color w-100" name="marquee_color" value="<?php echo $config['marquee_color']; ?>">
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-save"></i> 保存颜色
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- 网站图标管理 -->
                         <div class="tab-pane fade" id="favicon">
                             <div class="row">
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-star"></i> 网站图标管理</h5>
                                         </div>
                                         <div class="card-body">
                                             <div class="text-center mb-3">
                                                 <img src="<?php echo htmlspecialchars($config['favicon']); ?>" alt="当前网站图标" style="width: 32px; height: 32px; object-fit: contain;">
                                                 <p class="mt-2 text-muted">当前网站图标</p>
                                             </div>
                                             
                                             <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" name="action" value="update_favicon">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">上传新网站图标</label>
                                                     <input type="file" class="form-control" name="favicon" accept="image/*,.ico" required>
                                                     <small class="text-muted">支持：PNG、ICO格式，最大尺寸32x32px</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-upload"></i> 更新网站图标
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- 页脚管理 -->
                         <div class="tab-pane fade" id="footer">
                             <div class="row">
                                 <div class="col-md-6 mb-4">
                                     <div class="card">
                                         <div class="card-header">
                                             <h5 class="mb-0"><i class="fas fa-shoe-prints"></i> 页脚管理</h5>
                                         </div>
                                         <div class="card-body">
                                             <div class="text-center mb-3">
                                                 <img src="<?php echo htmlspecialchars($config['footer_image']); ?>" alt="当前页脚图片" style="max-width: 200px; max-height: 100px; object-fit: cover; border-radius: 5px;">
                                                 <p class="mt-2 text-muted">当前页脚图片</p>
                                             </div>
                                             
                                             <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" name="action" value="update_footer">
                                                 <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">页脚链接</label>
                                                     <input type="url" class="form-control" name="footer_link" value="<?php echo htmlspecialchars($config['footer_link']); ?>" required>
                                                 </div>
                                                 
                                                 <div class="mb-3">
                                                     <label class="form-label">上传新页脚图片（可选）</label>
                                                     <input type="file" class="form-control" name="footer_image" accept="image/*">
                                                     <small class="text-muted">留空表示不更改图片</small>
                                                 </div>
                                                 
                                                 <button type="submit" class="btn btn-primary w-100">
                                                     <i class="fas fa-save"></i> 更新页脚
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                                                 <!-- 横幅管理 -->
                        <div class="tab-pane fade" id="banners">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-images"></i> 横幅管理</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data" class="mb-4">
                                        <input type="hidden" name="action" value="upload_image">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-8">
                                                <label class="form-label">上传新横幅图片</label>
                                                <input type="file" class="form-control" name="image" accept="image/*" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="submit" class="btn btn-primary d-block w-100">
                                                    <i class="fas fa-upload"></i> 上传
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <h6>当前横幅：</h6>
                                    <div class="row">
                                        <?php foreach ($config['banners'] as $index => $banner): ?>
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                <img src="<?php echo htmlspecialchars($banner); ?>" class="card-img-top" alt="横幅 <?php echo $index + 1; ?>">
                                                <div class="card-body p-2">
                                                    <a href="?delete_banner=<?php echo $index; ?>" class="btn btn-danger btn-sm w-100" onclick="return confirm('您确定要删除这个横幅吗？')">
                                                        <i class="fas fa-trash"></i> 删除
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 链接管理 -->
                        <div class="tab-pane fade" id="links">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-link"></i> 链接管理</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_links">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        
                                        <div id="links-container">
                                            <?php foreach ($config['links'] as $index => $link): ?>
                                            <div class="row mb-3 link-item">
                                                <div class="col-md-2">
                                                    <label class="form-label">链接名称</label>
                                                    <input type="text" class="form-control" name="links[<?php echo $index; ?>][name]" value="<?php echo htmlspecialchars($link['name']); ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">显示</label>
                                                    <input type="url" class="form-control" name="links[<?php echo $index; ?>][showUrl]" value="<?php echo htmlspecialchars($link['showUrl']); ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">目标URL</label>
                                                    <input type="url" class="form-control" name="links[<?php echo $index; ?>][jumpUrl]" value="<?php echo htmlspecialchars($link['jumpUrl']); ?>" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">图标</label>
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
                                            <i class="fas fa-plus"></i> 添加链接
                                        </button>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> 保存更改
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 更改密码 -->
                        <div class="tab-pane fade" id="password">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-key"></i> 更改密码</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="change_password">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">当前账户</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['admin_username']); ?>" readonly>
                                                    <small class="text-muted">当前登录账户</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">当前密码</label>
                                                    <input type="password" class="form-control" name="current_password" required>
                                                    <small class="text-muted">输入当前密码确认身份</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">新密码</label>
                                                    <input type="password" class="form-control" name="new_password" required>
                                                    <small class="text-muted">新密码必须至少6个字符</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">确认新密码</label>
                                                    <input type="password" class="form-control" name="confirm_password" required>
                                                    <small class="text-muted">重新输入新密码</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key"></i> 更改密码
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
        // 添加新链接
        document.getElementById('add-link').addEventListener('click', function() {
            const container = document.getElementById('links-container');
            const linkCount = container.children.length;
            const newLink = document.createElement('div');
            newLink.className = 'row mb-3 link-item';
            newLink.innerHTML = `
                <div class="col-md-2">
                    <label class="form-label">链接名称</label>
                    <input type="text" class="form-control" name="links[${linkCount}][name]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">显示</label>
                    <input type="text" class="form-control" name="links[${linkCount}][showUrl]" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">目标URL</label>
                    <input type="url" class="form-control" name="links[${linkCount}][jumpUrl]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">图标</label>
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

        // 删除链接
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-link') || e.target.closest('.remove-link')) {
                if (confirm('您确定要删除这个链接吗？')) {
                    e.target.closest('.link-item').remove();
                }
            }
        });

        // 更新Logo尺寸
        function updateLogoSize(value) {
            document.getElementById('logo-size-value').textContent = value + '%';
        }

        // 切换背景类型
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
