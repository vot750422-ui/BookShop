<?php
session_start();
require_once 'Config.php';

// Kiểm tra giỏ hàng có hàng không
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: GioHang.php");
    exit();
}

// Tính tổng tiền từ giỏ hàng thật
$tongTien = 0;
$items    = [];

$ids  = implode(',', array_map('intval', array_keys($cart)));
$stmt = $conn->query("SELECT * FROM Books WHERE BookID IN ($ids)");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $slg       = $cart[$row['BookID']]['slg'] ?? 1;
    $thanhTien = $row['Price'] * $slg;
    $tongTien += $thanhTien;
    $items[]   = [
        'BookID'    => $row['BookID'],
        'Title'     => $row['Title'],
        'Price'     => $row['Price'],
        'slg'       => $slg,
        'ThanhTien' => $thanhTien,
    ];
}

// Điền sẵn nếu đã đăng nhập
$userID   = $_SESSION['user_id']   ?? null;
$userName = $_SESSION['user_name'] ?? '';
$userInfo = null;

if ($userID) {
    try {
        $stmt = $conn->prepare("SELECT FullName, Address, Phone FROM Users WHERE UserID = ?");
        $stmt->execute([$userID]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { $userInfo = null; }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/thanhtoan.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="thanhtoan-wrapper">
<div class="thanhtoan-container">
    <h2>🛒 Trang Thanh Toán</h2>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <form action="XuLyThanhToan.php" method="POST">

        <!-- THÔNG TIN NGƯỜI NHẬN -->
        <div class="info-box">
            <h3>📋 Thông tin người nhận</h3>

            <div class="form-row">
                <div class="form-group">
                    <label>Họ tên *</label>
                    <input type="text" name="HoTen" required
                           placeholder="Nhập họ tên..."
                           value="<?php echo htmlspecialchars($userInfo['FullName'] ?? $userName); ?>">
                </div>
                <div class="form-group">
                    <label>Số điện thoại *</label>
                    <input type="text" name="Phone" required
                           placeholder="Nhập số điện thoại..."
                           value="<?php echo htmlspecialchars($userInfo['Phone'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="Email" placeholder="Nhập email...">
            </div>

            <div class="form-group">
                <label>Số nhà, tên đường *</label>
                <input type="text" name="DiaChiDay" required
                       placeholder="Ví dụ: 256 Nguyễn Văn Cừ..."
                       value="<?php echo htmlspecialchars($userInfo['Address'] ?? ''); ?>">
            </div>

            <div class="form-row three-col">
                <div class="form-group">
                    <label>Tỉnh/Thành phố *</label>
                    <select id="tinh" name="TinhTP" required onchange="loadQuan()">
                        <option value="">-- Chọn Tỉnh/TP --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quận/Huyện *</label>
                    <select id="quan" name="QuanHuyen" required onchange="loadPhuong()">
                        <option value="">-- Chọn Quận/Huyện --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phường/Xã *</label>
                    <select id="phuong" name="PhuongXa" required>
                        <option value="">-- Chọn Phường/Xã --</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Ghi chú</label>
                <textarea name="GhiChu" rows="3"
                          placeholder="Ghi chú thêm cho đơn hàng (nếu có)..."></textarea>
            </div>
        </div>

        <!-- SẢN PHẨM ĐÃ CHỌN -->
        <div class="info-box">
            <h3>📚 Sản phẩm đã chọn</h3>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Tên sách</th>
                        <th>Đơn giá</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['Title']); ?></td>
                        <td><?php echo number_format($item['Price'], 0, ',', '.'); ?> đ</td>
                        <td><?php echo $item['slg']; ?></td>
                        <td><?php echo number_format($item['ThanhTien'], 0, ',', '.'); ?> đ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- TỔNG TIỀN -->
        <div class="info-box">
            <h3>💰 Tổng tiền</h3>
            <p class="tong-tien"><?php echo number_format($tongTien, 0, ',', '.'); ?> VNĐ</p>
        </div>

        <input type="hidden" name="tongTien" value="<?php echo $tongTien; ?>">
        <?php if ($userID): ?>
            <input type="hidden" name="userID" value="<?php echo $userID; ?>">
        <?php endif; ?>

        <button type="submit" class="btn-thanhtoan">✅ Xác nhận đặt hàng</button>
    </form>

    <a href="GioHang.php" class="back-link">← Quay lại giỏ hàng</a>
</div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script>
const API = 'https://provinces.open-api.vn/api';

window.onload = async function () {
    try {
        const res  = await fetch(`${API}/p/`);
        const data = await res.json();
        const sel  = document.getElementById('tinh');
        data.forEach(t => {
            sel.innerHTML += `<option value="${t.name}" data-code="${t.code}">${t.name}</option>`;
        });
    } catch (e) { console.log('Lỗi load tỉnh'); }
};

async function loadQuan() {
    const tinhSel   = document.getElementById('tinh');
    const quanSel   = document.getElementById('quan');
    const phuongSel = document.getElementById('phuong');

    quanSel.innerHTML   = '<option value="">-- Chọn Quận/Huyện --</option>';
    phuongSel.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

    const code = tinhSel.selectedOptions[0]?.dataset.code;
    if (!code) return;

    try {
        const res  = await fetch(`${API}/p/${code}?depth=2`);
        const data = await res.json();
        data.districts.forEach(q => {
            quanSel.innerHTML += `<option value="${q.name}" data-code="${q.code}">${q.name}</option>`;
        });
    } catch (e) { console.log('Lỗi load quận'); }
}

async function loadPhuong() {
    const quanSel   = document.getElementById('quan');
    const phuongSel = document.getElementById('phuong');

    phuongSel.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

    const code = quanSel.selectedOptions[0]?.dataset.code;
    if (!code) return;

    try {
        const res  = await fetch(`${API}/d/${code}?depth=2`);
        const data = await res.json();
        data.wards.forEach(p => {
            phuongSel.innerHTML += `<option value="${p.name}">${p.name}</option>`;
        });
    } catch (e) { console.log('Lỗi load phường'); }
}
</script>

</body>
</html>