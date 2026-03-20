<?php
session_start();
require_once 'Config.php';

// Bắt buộc đăng nhập mới được vào trang này
if (!isset($_SESSION['user_id'])) {
    header("Location: Dangnhap.php");
    exit();
}

$userID = $_SESSION['user_id'];

// 1. Lấy thông tin cá nhân
$stmtUser = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmtUser->execute([$userID]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// 2. Lấy lịch sử đơn hàng
$stmtOrders = $conn->prepare("SELECT * FROM Orders WHERE UserID = ? ORDER BY NgayDat DESC");
$stmtOrders->execute([$userID]);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
// 3. Lấy danh sách địa chỉ
$stmtAddress = $conn->prepare("SELECT * FROM UserAddresses WHERE UserID = ? ORDER BY IsDefault DESC, AddressID DESC");
$stmtAddress->execute([$userID]);
$addresses = $stmtAddress->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-container { max-width: 1000px; margin: 40px auto; display: flex; gap: 30px; }
        .profile-sidebar { flex: 0 0 250px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .profile-sidebar h3 { color: #2c1a0e; margin-bottom: 20px; font-size: 18px; border-bottom: 2px solid #c9a96e; padding-bottom: 10px; }
        .profile-menu { list-style: none; padding: 0; }
        .profile-menu li { margin-bottom: 10px; }
        .profile-menu a { text-decoration: none; color: #555; font-weight: 500; display: block; padding: 8px 10px; border-radius: 4px; transition: background 0.2s; }
        .profile-menu a:hover, .profile-menu a.active { background: #fdf6ec; color: #c9a96e; }
        
        .profile-content { flex: 1; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .profile-content h2 { margin-bottom: 20px; color: #2c1a0e; }
        
        /* Form thông tin */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; color: #333; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .btn-save { background: #2c1a0e; color: #f0e6d3; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-save:hover { background: #c9a96e; }

        /* Bảng đơn hàng */
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .order-table th, .order-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .order-table th { background: #fdf6ec; color: #2c1a0e; }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="profile-container">
    <!-- Cột Menu -->
    <div class="profile-sidebar">
        <h3>Tài khoản của tôi</h3>
        <ul class="profile-menu">
            <li><a href="?tab=info" class="<?= (!isset($_GET['tab']) || $_GET['tab'] == 'info') ? 'active' : '' ?>">Hồ sơ cá nhân</a></li>
            <li><a href="?tab=address" class="<?= (isset($_GET['tab']) && $_GET['tab'] == 'address') ? 'active' : '' ?>">Sổ địa chỉ</a></li>
            <li><a href="?tab=orders" class="<?= (isset($_GET['tab']) && $_GET['tab'] == 'orders') ? 'active' : '' ?>">Lịch sử đơn hàng</a></li>
        </ul>
    </div>

    <!-- Cột Nội dung -->
    <div class="profile-content">
        <?php $tab = $_GET['tab'] ?? 'info'; ?>

        <?php if ($tab == 'info'): ?>
            <!-- TAB: HỒ SƠ CÁ NHÂN -->
            <h2>Hồ sơ cá nhân</h2>
            <form action="CapNhatInfo.php" method="POST">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['FullName']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email (Không thể thay đổi)</label>
                    <input type="email" value="<?= htmlspecialchars($user['Email']) ?>" disabled style="background: #f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" name="birthdate" value="<?= htmlspecialchars($user['BirthDate'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-save">Lưu thay đổi</button>
            </form>

        <?php elseif ($tab == 'address'): ?>
            <!-- TAB: SỔ ĐỊA CHỈ -->
            <h2>Sổ địa chỉ</h2>
            
            <!-- Form thêm địa chỉ mới -->
            <div style="background: #fdf6ec; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h4 style="margin-top: 0; color: #2c1a0e;">+ Thêm địa chỉ mới</h4>
                <form action="XuLyDiaChi.php" method="POST" style="display: flex; flex-wrap: wrap; gap: 15px;">
                    <input type="hidden" name="action" value="add">
                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="receiver_name" placeholder="Tên người nhận" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="receiver_phone" placeholder="Số điện thoại" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <!-- Cụm 3 ô chọn Tỉnh/Quận/Phường -->
                    <div style="flex: 100%; display: flex; gap: 15px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 150px;">
                            <select id="tinh" name="tinh" required onchange="loadQuan()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Tỉnh/TP --</option>
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <select id="quan" name="quan" required onchange="loadPhuong()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Quận/Huyện --</option>
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <select id="phuong" name="phuong" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Phường/Xã --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Địa chỉ chi tiết -->
                    <div style="flex: 100%;">
                        <input type="text" name="detail_address" placeholder="Số nhà, tên đường, hẻm..." required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="flex: 100%;">
                        <label><input type="checkbox" name="is_default" value="1"> Đặt làm địa chỉ mặc định</label>
                    </div>
                    <button type="submit" class="btn-save">Thêm địa chỉ</button>
                </form>
            </div>

            <!-- Danh sách địa chỉ đã lưu -->
            <h4 style="color: #2c1a0e;">Địa chỉ của bạn</h4>
            <?php if (empty($addresses)): ?>
                <p>Bạn chưa lưu địa chỉ nào.</p>
            <?php else: ?>
                <?php foreach ($addresses as $addr): ?>
                    <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 15px; position: relative;">
                        <p style="margin: 0 0 5px;"><strong><?= htmlspecialchars($addr['ReceiverName']) ?></strong> | <?= htmlspecialchars($addr['ReceiverPhone']) ?></p>
                        <p style="margin: 0; color: #555;"><?= htmlspecialchars($addr['DetailAddress']) ?></p>
                        
                        <?php if ($addr['IsDefault'] == 1): ?>
                            <span style="color: green; font-size: 12px; font-weight: bold; margin-top: 5px; display: inline-block;">[Mặc định]</span>
                        <?php endif; ?>

                        <!-- Nút xoá địa chỉ -->
                        <form action="XuLyDiaChi.php" method="POST" style="position: absolute; top: 15px; right: 15px;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="address_id" value="<?= $addr['AddressID'] ?>">
                            <button type="submit" onclick="return confirm('Xóa địa chỉ này?')" style="background: none; border: none; color: #e74c3c; cursor: pointer; font-weight: bold;">Xóa</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php elseif ($tab == 'orders'): ?>
            <!-- TAB: LỊCH SỬ ĐƠN HÀNG -->
            <h2>Lịch sử đơn hàng</h2>
            <?php if (empty($orders)): ?>
                <p>Bạn chưa có đơn hàng nào.</p>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php foreach ($orders as $o): ?>
    <tr>
        <!-- Đưa mã đơn hàng vào đúng thẻ <td> để nó nằm đúng cột -->
        <td>
            <a href="chitietdonhang.php?id=<?= $o['OrderID'] ?>" style="color: #c9a96e; font-weight: bold; text-decoration: none;">
                #<?= $o['OrderID'] ?>
            </a>
        </td>
        
        <!-- Các thông tin khác giữ nguyên -->
        <td><?= date('d/m/Y H:i', strtotime($o['OrderDate'] ?? $o['NgayDat'] ?? 'now')) ?></td>
        <td><?= number_format($o['TongTien'], 0, ',', '.') ?> đ</td>
        <td><strong><?= htmlspecialchars($o['TrangThai']) ?></strong></td>
    </tr>
    <?php endforeach; ?>
</tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'components/footer.html'; ?>
<script src="assets/js/address.js"></script>
</body>
</html>