<?php
session_start();

// Chỉ admin mới vào được
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: Dangnhap.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookStore</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<!-- NAVBAR ADMIN -->
<div class="admin-navbar">
    <div class="admin-navbar-left">
        <span class="admin-logo">👑 ADMIN</span>
        <span class="admin-user">Xin chào, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
    </div>
    <ul class="admin-menu">
        <li><a href="admin.php" class="active">📊 Dashboard</a></li>
        <li><a href="admin_sanpham.php">📚 Quản lý sản phẩm</a></li>
        <li><a href="admin_khachhang.php">👥 Quản lý khách hàng</a></li>
        <li><a href="admin_donhang.php">📦 Quản lý đơn hàng</a></li>
        <li><a href="index.php">🏠 Trang chủ</a></li>
        <li><a href="logout.php" class="btn-logout">🚪 Đăng xuất</a></li>
    </ul>
</div>

<!-- NỘI DUNG -->
<div class="admin-content">
    <h1>📊 Trang Quản Trị</h1>
    <p class="admin-subtitle">Chào mừng bạn đến trang quản trị hệ thống BookStore.</p>

    <!-- Thống kê nhanh -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <p class="stat-label">Tổng sản phẩm</p>
                <p class="stat-number">10</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <p class="stat-label">Khách hàng</p>
                <p class="stat-number">--</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <p class="stat-label">Đơn hàng</p>
                <p class="stat-number">--</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <p class="stat-label">Doanh thu</p>
                <p class="stat-number">--</p>
            </div>
        </div>
    </div>

    <!-- Truy cập nhanh -->
    <h2 style="margin: 30px 0 15px; color:#2c1a0e;">⚡ Truy cập nhanh</h2>
    <div class="quick-links">
        <a href="admin_sanpham.php" class="quick-card">
            <span>📚</span>
            <p>Quản lý sản phẩm</p>
        </a>
        <a href="admin_khachhang.php" class="quick-card">
            <span>👥</span>
            <p>Quản lý khách hàng</p>
        </a>
        <a href="admin_donhang.php" class="quick-card">
            <span>📦</span>
            <p>Quản lý đơn hàng</p>
        </a>
    </div>
</div>

</body>
</html>