<?php
session_start();
require_once 'Config.php';

// check admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: Dangnhap.php");
    exit();
}

// lấy danh sách toàn bộ user (không lấy admin)
$stmt = $conn->query("SELECT * FROM Users WHERE Role != 'Admin' ORDER BY UserID DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý khách hàng</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/admin_khachhang.css">
</head>
<body>
<!-- NAVBAR -->
<div class="admin-navbar">
    <div class="admin-navbar-left">
        <span class="admin-logo"> ADMIN</span>
        <span class="admin-user">
            Xin chào, 
            <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong>
        </span>
    </div>
    <ul class="admin-menu">
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_sanpham.php">Quản lý sản phẩm</a></li>
        <li><a href="admin_khachhang.php" class="active">Quản lý khách hàng</a></li>
        <li><a href="admin_donhang.php">Quản lý đơn hàng</a></li>
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="logout.php" class="btn-logout">Đăng xuất</a></li>
    </ul>
</div>

<div class="admin-content">
    <h1>Quản lý khách hàng</h1>
    <p class="admin-subtitle">Danh sách khách hàng hiện có</p>

    <div class="customer-box">
        <a href="them_user.php" class="btn-add-customer">+ Thêm khách hàng</a>

        <table class="customer-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <?php 
                    // Mặc định là 1 (hoạt động) nếu cột này bị null
                    $trangThai = $u['TrangThai'] ?? 1; 
                ?>
                <tr>
                    <td><?= htmlspecialchars($u['UserID']) ?></td>
                    <td><?= htmlspecialchars($u['FullName']) ?></td>
                    <td><?= htmlspecialchars($u['Email']) ?></td>
                    <td><?= htmlspecialchars($u['Phone']) ?></td>
                    
                    <!-- Cột hiển thị trạng thái -->
                    <td>
                        <?php if ($trangThai == 1): ?>
                            <span style="color: green; font-weight: bold;">Hoạt động</span>
                        <?php else: ?>
                            <span style="color: red; font-weight: bold;">Đã khóa</span>
                        <?php endif; ?>
                    </td>

                    <!-- Nút hành động thay đổi theo trạng thái -->
                    <td>
                        <?php if ($trangThai == 1): ?>
                            <a href="admin_khoakhachhang.php?id=<?= $u['UserID'] ?>&action=lock" class="btn-customer" style="background: #e74c3c; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px;" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản này?')">Khóa</a>
                        <?php else: ?>
                            <a href="admin_khoakhachhang.php?id=<?= $u['UserID'] ?>&action=unlock" class="btn-customer" style="background: #2ecc71; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px;" onclick="return confirm('Mở khóa cho tài khoản này?')">Mở khóa</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>