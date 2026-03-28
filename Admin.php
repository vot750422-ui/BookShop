<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: dangnhap.php");
    exit();
}

require_once 'config.php';

try {
    $tongSP       = $conn->query("SELECT COUNT(*) FROM books")->fetchColumn();
    $tongKH       = $conn->query("SELECT COUNT(*) FROM users WHERE `Role` = 'Customer'")->fetchColumn();
    $tongDH       = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
} catch (PDOException $e) {
    $tongSP = $tongKH = $tongDH  = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookStore</title>
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<div class="admin-navbar">
    <div class="admin-navbar-left">
        <span class="admin-logo">ADMIN</span>
    </div>
    <ul class="admin-menu">
        <li><a href="admin.php" class="active"> Dashboard</a></li>
        <li><a href="admin_sanpham.php"> Quản lý sản phẩm</a></li>
        <li><a href="admin_khachhang.php"> Quản lý khách hàng</a></li>
        <li><a href="admin_donhang.php"> Quản lý đơn hàng</a></li>
        <li><a href="index.php"> Trang chủ</a></li>
        <li><a href="logout.php" class="btn-logout"> Đăng xuất</a></li>
    </ul>
</div>

<div class="admin-content">
    <h1> Trang Quản Trị</h1>

    <div class="stat-grid">
        <div class="stat-card">

            <div class="stat-info">
                <p class="stat-label">Tổng sản phẩm</p>
                <p class="stat-number"><?= number_format($tongSP) ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-info">
                <p class="stat-label">Khách hàng</p>
                <p class="stat-number"><?= number_format($tongKH) ?></p>
            </div>
        </div>
        <div class="stat-card">

            <div class="stat-info">
                <p class="stat-label">Đơn hàng</p>
                <p class="stat-number"><?= number_format($tongDH) ?></p>
            </div>
        </div>
    </div>

    <h2 style="margin:30px 0 15px; color:#2c1a0e;"> Truy cập nhanh</h2>
    <div class="quick-links">
        <a href="admin_sanpham.php" class="quick-card"><p>Quản lý sản phẩm</p></a>
        <a href="admin_khachhang.php" class="quick-card"><p>Quản lý khách hàng</p></a>
        <a href="admin_donhang.php" class="quick-card"><p>Quản lý đơn hàng</p></a>
    </div>
</div>

</body>
</html>