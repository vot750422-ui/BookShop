<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: dangnhap.php");
    exit();
}

// LEFT JOIN de hien ca don hang cua khach vang lai (UserID = NULL)
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
        <span class="admin-user">Xin chao, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
    </div>
    <ul class="admin-menu">
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_sanpham.php">Quan ly san pham</a></li>
        <li><a href="admin_khachhang.php">Quan ly khach hang</a></li>
        <li><a href="admin_donhang.php" class="active">Quan ly don hang</a></li>
        <li><a href="index.php">Trang chu</a></li>
        <li><a href="logout.php" class="btn-logout">Dang xuat</a></li>
    </ul>
</div>

<div class="admin-content">
    <h1>Quan Ly Don Hang</h1>
    <p class="admin-subtitle">Danh sach tat ca don hang trong he thong.</p>

    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success">Cap nhat trang thai thanh cong!</div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Ma DH</th>
                <th>Khach hang</th>
                <th>SDT</th>
                <th>Tong tien</th>
                <th>Ngay dat</th>
                <th>Trang thai</th>
                <th>Cap nhat</th>
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
                        'Dang giao'     => '#3498db',
                        'Da nhan'       => '#27ae60',
                        'Da thanh toan' => '#2ecc71',
                        'Huy'           => '#e74c3c',
                    ];
                    $color = $colors[$tt] ?? '#888';
                    ?>
                    <span style="background:<?= $color ?>; color:white; padding:4px 10px;
                                 border-radius:4px; font-size:13px; font-weight:600;">
                        <?= htmlspecialchars($tt) ?>
                    </span>
                </td>
                <td>
                    <form method="POST" action="update_trangthai.php">
                        <input type="hidden" name="order_id" value="<?= $o['OrderID'] ?>">
                        <select name="trangthai"
                                onchange="if(confirm('Doi trang thai don #<?= $o['OrderID'] ?>?')) this.form.submit()">
                            <option value="Cho xac nhan"  <?= $tt=='Cho xac nhan'  ? 'selected':'' ?>>Cho xac nhan</option>
                            <option value="Dang giao"     <?= $tt=='Dang giao'     ? 'selected':'' ?>>Dang giao</option>
                            <option value="Da nhan"       <?= $tt=='Da nhan'       ? 'selected':'' ?>>Da nhan</option>
                            <option value="Da thanh toan" <?= $tt=='Da thanh toan' ? 'selected':'' ?>>Da thanh toan</option>
                            <option value="Huy"           <?= $tt=='Huy'           ? 'selected':'' ?>>Huy</option>
                        </select>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>