<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: dangnhap.php");
    exit();
}

$sql = "SELECT o.OrderID, o.TongTien, o.NgayDat, o.TrangThai,
               o.HoTen, o.Phone,
               COALESCE(u.FullName, o.HoTen, 'Khach vang lai') AS TenKhach
        FROM orders o
        LEFT JOIN users u ON o.UserID = u.UserID
        ORDER BY o.OrderID DESC";

$stmt   = $conn->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quan ly don hang - Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/admin_donhang.css">
</head>
<body>

<div class="admin-navbar">
    <div class="admin-navbar-left">
        <span class="admin-logo">ADMIN</span>
     
    </div>
    <ul class="admin-menu">
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_sanpham.php">Quản lý sản phẩm</a></li>
        <li><a href="admin_khachhang.php">Quản lý khách hàng</a></li>
        <li><a href="admin_donhang.php" class="active">Quản lý đơn hàng</a></li>
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="logout.php" class="btn-logout">Đăng xuất</a></li>
    </ul>
</div>

<div class="admin-content">
    <h1>Quản Lý Đơn Hàng</h1>
    <p class="admin-subtitle">Danh sách tất cả đơn hàng trong hệ thống</p>

    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success">Cập nhật trạng thái thành công!</div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Mã Đơn Hàng</th>
                <th>Khách Hàng</th>
                <th>Số Điện Thoại</th>
                <th>Tổng Tiền</th>
                <th>Ngày Đặt</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><strong>#<?= $o['OrderID'] ?></strong></td>
                <td><?= htmlspecialchars($o['TenKhach']) ?></td>
                <td><?= htmlspecialchars($o['Phone'] ?? '') ?></td>
                <td><?= number_format($o['TongTien'], 0, ',', '.') ?> d</td>
                <td><?= $o['NgayDat'] ? date("d/m/Y H:i", strtotime($o['NgayDat'])) : '---' ?></td>
                <td>
                    <?php
                    $tt = $o['TrangThai'];
                    $colors = [
                        'Cho xac nhan'  => '#f39c12',
                        'Huy'           => '#e74c3c',
                    ];
                    $color = $colors[$tt] ?? '#888';
                    ?>
                    <span style="background:<?= $color ?>; color:white; padding:4px 10px;
                                 border-radius:4px; font-size:13px; font-weight:600;">
                        <?= htmlspecialchars($tt) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>