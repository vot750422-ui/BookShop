<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: Dangnhap.php");
    exit();
}

$sql = "SELECT o.OrderID, u.FullName, o.TongTien, o.NgayDat, o.TrangThai
        FROM Orders o
        JOIN Users u ON o.UserID = u.UserID
        ORDER BY o.OrderID DESC";

$stmt = $conn->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý đơn hàng</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/admin_donhang.css">
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
        <li><a href="admin_khachhang.php">Quản lý khách hàng</a></li>
        <li><a href="admin_donhang.php" class="active">Quản lý đơn hàng</a></li>
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="logout.php" class="btn-logout">Đăng xuất</a></li>
    </ul>
</div>

<!-- CONTENT -->
<div class="admin-content">
    <h1>Quản lý đơn hàng</h1>
    <p class="admin-subtitle">Danh sách đơn hàng</p>

    <div class="order-box">
        <table class="order-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Ngày</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= $o['OrderID'] ?></td>
                    <td><?= htmlspecialchars($o['FullName']) ?></td>
                    <td><?= number_format($o['TongTien']) ?> VND</td>

                    <!-- FORMAT NGÀY -->
                    <td>
                        <?= $o['NgayDat'] 
                            ? date("d/m/Y H:i", strtotime($o['NgayDat'])) 
                            : '---' ?>
                    </td>

                    <!-- TRẠNG THÁI -->
                    <td>
                        <?php
                            $class = "status-pending";
                            if ($o['TrangThai'] == "Đang giao") $class = "status-shipping";
                            if ($o['TrangThai'] == "Đã giao") $class = "status-done";
                            if ($o['TrangThai'] == "Đã hủy") $class = "status-cancel";
                        ?>
                        <span class="status <?= $class ?>">
                            <?= $o['TrangThai'] ?>
                        </span>
                    </td>

                    <!-- UPDATE -->
                    <td>
                        <form method="POST" action="update_trangthai.php">
                            <input type="hidden" name="order_id" value="<?= $o['OrderID'] ?>">

                            <select name="trangthai"
                                onchange="if(confirm('Đổi trạng thái đơn này?')) this.form.submit()">

                                <option value="Chờ xử lý" <?= $o['TrangThai']=='Chờ xử lý'?'selected':'' ?>>Chờ xử lý</option>
                                <option value="Đang giao" <?= $o['TrangThai']=='Đang giao'?'selected':'' ?>>Đang giao</option>
                                <option value="Đã giao" <?= $o['TrangThai']=='Đã giao'?'selected':'' ?>>Đã giao</option>
                                <option value="Đã hủy" <?= $o['TrangThai']=='Đã hủy'?'selected':'' ?>>Đã hủy</option>
                            </select>

                        </form>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>