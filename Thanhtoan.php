<?php
session_start();
require_once 'config.php';

$isBuyNow = isset($_GET['type']) && $_GET['type'] === 'buynow';

if ($isBuyNow) {
    $cart = $_SESSION['buy_now'] ?? [];
} else {
    $cart = $_SESSION['cart'] ?? [];
}

if (empty($cart)) {
    header("Location: giohang.php");
    exit();
}

$tongTien = 0;
$items    = [];
$ids  = implode(',', array_map('intval', array_keys($cart)));
$stmt = $conn->query("SELECT * FROM books WHERE BookID IN ($ids)");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $slg       = $cart[$row['BookID']]['slg'] ?? 1;
    $thanhTien = $row['Price'] * $slg;
    $tongTien += $thanhTien;
    $items[]   = [
        'BookID'    => $row['BookID'],
        'Title'     => $row['Title'],
        'Price'     => $row['Price'],
        'ImageURL'  => $row['ImageURL'] ?? 'book-default.jpg',
        'slg'       => $slg,
        'ThanhTien' => $thanhTien,
    ];
}

$userID   = $_SESSION['user_id']   ?? null;
$userInfo = null;

if ($userID) {
    try {
        $stmt = $conn->prepare("SELECT FullName, Phone, Address FROM users WHERE UserID = ?");
        $stmt->execute([$userID]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

$preHoTen = $userInfo['FullName'] ?? '';
$prePhone = $userInfo['Phone']    ?? '';
$preAddr  = $userInfo['Address']  ?? '';
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
    <h2>Trang Thanh Toán</h2>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="XuLyThanhToan.php" method="POST" onsubmit="return validateForm(event)">
        <input type="hidden" name="checkout_type" value="<?= $isBuyNow ? 'buynow' : 'cart' ?>">

        <div class="info-box">
            <h3>Thông tin người nhận</h3>

            <div class="form-row">
                <div class="form-group">
                    <label>Họ tên *</label>
                    <input type="text" name="HoTen" required placeholder="Nhập họ tên..."
                           value="<?= htmlspecialchars($preHoTen) ?>">
                </div>
                <div class="form-group">
                    <label>Số điện thoại *</label>
                    <input type="tel" name="Phone" id="phoneInput" required maxlength="10"
                           placeholder="Nhập số điện thoại..."
                           value="<?= htmlspecialchars($prePhone) ?>"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                </div>
            </div>

            <div class="form-group">
                <label>Địa chỉ giao hàng *</label>
                <input type="text" name="DiaChiDay" required
                       placeholder="Số nhà, tên đường, phường, quận, tỉnh..."
                       value="<?= htmlspecialchars($preAddr) ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="Email" placeholder="Nhập email...">
            </div>
            <div class="form-group">
                <label>Ghi chú</label>
                <textarea name="GhiChu" rows="3" placeholder="Ghi chú thêm cho đơn hàng..."></textarea>
            </div>
        </div>

        <div class="info-box">
            <h3>Sản phẩm đã chọn</h3>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img src="assets/images/<?= htmlspecialchars($item['ImageURL']) ?>"
                                     onerror="this.src='assets/images/book-default.jpg'"
                                     alt="<?= htmlspecialchars($item['Title']) ?>">
                                <span><?= htmlspecialchars($item['Title']) ?></span>
                            </div>
                        </td>
                        <td><?= number_format($item['Price'], 0, ',', '.') ?> đ</td>
                        <td><?= $item['slg'] ?></td>
                        <td><?= number_format($item['ThanhTien'], 0, ',', '.') ?> đ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="info-box">
            <h3>Tổng tiền</h3>
            <p class="tong-tien"><?= number_format($tongTien, 0, ',', '.') ?> VNĐ</p>
        </div>

        <button type="submit" class="btn-thanhtoan">Xác nhận đặt hàng</button>
    </form>

    <a href="GioHang.php" class="back-link">Quay lại giỏ hàng</a>
</div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script>
function validateForm(event) {
    const phone = document.getElementById('phoneInput').value.trim();
    if (phone.length < 10) {
        event.preventDefault();
        showPopup('Số điện thoại phải đủ 10 chữ số!', 'error');
        return false;
    }
    return true;
}
</script>

</body>
</html>
