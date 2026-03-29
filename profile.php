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

$stmtAddress = $conn->prepare("SELECT * FROM useraddresses WHERE UserID = ? ORDER BY IsDefault DESC, AddressID DESC");
$stmtAddress->execute([$userID]);
$addresses = $stmtAddress->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản - BookStore</title>
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
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
            <li><a href="?tab=info" class="<?= $tab=='info'?'active':'' ?>">Thông tin cá nhân</a></li>
            <li><a href="?tab=address" class="<?= $tab=='address'?'active':'' ?>">Sổ địa chỉ</a></li>
            <li><a href="?tab=orders" class="<?= $tab=='orders'?'active':'' ?>">Đơn đặt hàng</a></li>
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
                    <input type="tel" name="Phone" id="infoPhone" value="<?= htmlspecialchars($user['Phone'] ?? '') ?>" required maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" name="birthdate" value="<?= htmlspecialchars($user['BirthDate'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-save">Lưu thay đổi</button>
            </form>

        <?php elseif ($tab == 'address'): ?>
            <h2>Sổ địa chỉ</h2>
            <div class="add-address-form">
                <h4>Thêm địa chỉ mới</h4>
                <form action="xulydiachi.php" method="POST" onsubmit="return validateAddrForm(event)">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="redirect" value="<?= isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'profile' ?>">
                    <div class="addr-row">
                        <div class="form-group"><label>Tên người nhận *</label><input type="text" name="receiver_name" required></div>
                        <div class="form-group"><label>Số điện thoại *</label><input type="tel" name="receiver_phone" id="addrPhone" required maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');"></div>
                    </div>
                    <div class="addr-row">
                        <div class="form-group">
                            <label>Tỉnh/TP *</label>
                            <select id="tinh-addr" name="tinh" required onchange="loadQuanAddr()"><option value="">-- Chọn Tỉnh/TP --</option></select>
                        </div>
                        <div class="form-group">
                            <label>Quận/Huyện *</label>
                            <select id="quan-addr" name="quan" required onchange="loadPhuongAddr()"><option value="">-- Chọn Quận/Huyện --</option></select>
                        </div>
                        <div class="form-group">
                            <label>Phường/Xã *</label>
                            <select id="phuong-addr" name="phuong" required><option value="">-- Chọn Phường/Xã --</option></select>
                        </div>
                    </div>
                    <div class="addr-row">
                        <div class="form-group" style="flex:100%;">
                            <label>Số nhà, tên đường *</label><input type="text" name="detail_address" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_default" value="1"> Đặt làm địa chỉ mặc định</label>
                    </div>
                    <button type="submit" class="btn-save">Thêm địa chỉ</button>
                </form>
            </div>

            <?php if (empty($addresses)): ?>
                <p style="color:#999;">Bạn chưa lưu địa chỉ nào.</p>
            <?php else: ?>
                <?php foreach ($addresses as $addr): ?>
                <div class="address-card">
                    <p><strong><?= htmlspecialchars($addr['ReceiverName']) ?></strong> | <?= htmlspecialchars($addr['ReceiverPhone']) ?></p>
                    <p><?= htmlspecialchars($addr['DiaChiDay']) ?>, <?= htmlspecialchars($addr['PhuongXa']) ?>, <?= htmlspecialchars($addr['QuanHuyen']) ?>, <?= htmlspecialchars($addr['TinhTP']) ?></p>
                    <?php if ($addr['IsDefault'] == 1): ?><span class="badge-default">Mặc định</span><?php endif; ?>
                    <form action="xulydiachi.php" method="POST" id="form-xoa-addr-<?= $addr['AddressID'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="address_id" value="<?= $addr['AddressID'] ?>">
                        <button type="Submit" class="btn-xoa-addr" onclick="confirmXoaAddr(<?= $addr['AddressID'] ?>)">Xoá</button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php elseif ($tab == 'orders'): ?>
            <h2>Đơn đặt hàng</h2>
            <?php if (empty($orders)): ?>
                <p style="color:#999;">Bạn chưa có đơn hàng nào.</p>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th>Chi tiết</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= $o['NgayDat'] ? date('d/m/Y H:i', strtotime($o['NgayDat'])) : '---' ?></td>
                            <td><?= number_format($o['TongTien'],0,',','.') ?> đ</td>
                            <td>
                                <?php $c = (mb_strtolower(trim($o['TrangThai'])) === 'da huy' || mb_strtolower(trim($o['TrangThai'])) === 'hủy') ? '#e74c3c' : '#f39c12'; ?>
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
<script src="assets/js/address.js"></script>
<script src="assets/js/popup.js"></script>
</body>
</html>