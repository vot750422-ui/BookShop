<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$userID = $_SESSION['user_id'];

$stmtUser = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
$stmtUser->execute([$userID]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

$stmtOrders = $conn->prepare("SELECT * FROM orders WHERE UserID = ? ORDER BY NgayDat DESC");
$stmtOrders->execute([$userID]);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="profile-container">
    <div class="profile-sidebar">
        <h3>Tài khoản của tôi</h3>
        <?php $tab = $_GET['tab'] ?? 'info'; ?>
        <ul class="profile-menu">
            <li><a href="?tab=info"   class="<?= $tab=='info'   ? 'active' : '' ?>">Thông tin cá nhân</a></li>
            <li><a href="?tab=orders" class="<?= $tab=='orders' ? 'active' : '' ?>">Đơn đặt hàng</a></li>
        </ul>
    </div>

    <div class="profile-content">
        <?php if ($tab == 'info'): ?>
            <h2>Hồ sơ cá nhân</h2>
            <form action="capnhatinfo.php" method="POST" onsubmit="return validateInfoForm(event)">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['FullName']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($user['Email']) ?>" disabled style="background:#f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="tel" name="Phone" id="infoPhone"
                           value="<?= htmlspecialchars($user['Phone'] ?? '') ?>"
                           required maxlength="10"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($user['Address'] ?? '') ?>" placeholder="Số nhà, tên đường, phường, quận, tỉnh...">
                </div>
                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" name="birthdate" value="<?= htmlspecialchars($user['BirthDate'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-save">Lưu thay đổi</button>
            </form>

        <?php elseif ($tab == 'orders'): ?>
            <h2>Đơn đặt hàng</h2>
            <?php if (empty($orders)): ?>
                <p style="color:#999;">Bạn chưa có đơn hàng nào.</p>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><strong>#<?= $o['OrderID'] ?></strong></td>
                            <td><?= $o['NgayDat'] ? date('d/m/Y H:i', strtotime($o['NgayDat'])) : '---' ?></td>
                            <td><?= number_format($o['TongTien'], 0, ',', '.') ?> đ</td>
                            <td>
                                <?php
                                $tt = mb_strtolower(trim($o['TrangThai']));
                                $c  = ($tt === 'da huy' || $tt === 'hủy') ? '#e74c3c' : '#f39c12';
                                ?>
                                <span class="tt-tag" style="background:<?= $c ?>;"><?= htmlspecialchars($o['TrangThai']) ?></span>
                            </td>
                            <td><a href="chitietdonhang.php?id=<?= $o['OrderID'] ?>" class="btn-xemct">Xem chi tiết</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>
<script src="assets/js/popup.js"></script>
</body>
</html>
