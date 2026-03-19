<?php
session_start();
require_once 'Config.php';

// check admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: Dangnhap.php");
    exit();
}

// lấy danh sách user (không lấy admin)
$stmt = $conn->query("SELECT * FROM Users WHERE Role != 'Admin'");
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
                    <th>Ngày sinh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['UserID']) ?></td>
                    <td><?= htmlspecialchars($u['FullName']) ?></td>
                    <td><?= htmlspecialchars($u['Email']) ?></td>
                    <td><?= htmlspecialchars($u['Phone']) ?></td>
                    <td><?= htmlspecialchars($u['BirthDate'] ?? '') ?></td>
                    <td>
                        <a href="xoa_user.php?id=<?= $u['UserID'] ?>" class="btn-customer"
                           onclick="return confirm('Xóa user này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>