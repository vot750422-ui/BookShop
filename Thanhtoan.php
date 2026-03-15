<?php
session_start();
require_once 'Config.php'; // Kết nối PDO dùng chung toàn hệ thống

// Lấy thông tin khách hàng từ Session (đã đăng nhập)
$userID   = $_SESSION['user_id']   ?? null;
$userName = $_SESSION['user_name'] ?? '';

// Nếu chưa đăng nhập thì chuyển về trang đăng nhập
if (!$userID) {
    header("Location: Dangnhap.php");
    exit();
}

// Lấy thêm thông tin địa chỉ từ database
try {
    $stmt = $conn->prepare("SELECT FullName, Address, Phone FROM users WHERE UserID = ?");
    $stmt->execute([$userID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - BookStore</title>
    <link rel="stylesheet" href="assets/css/Thanhtoan.css">
</head>
<body>
<div class="container">
    <h2>🛒 Trang Thanh Toán</h2>

    <!-- Thông báo lỗi nếu có -->
    <?php if (!empty($_GET['error'])): ?>
        <div style="background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;
                    padding:10px 14px; border-radius:6px; margin-bottom:15px; text-align:center;">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Thông tin khách hàng -->
    <div class="box">
        <h3>📋 Thông tin khách hàng</h3>
        <p><b>Họ tên:</b> <?php echo htmlspecialchars($user['FullName'] ?? $userName); ?></p>
        <p><b>Địa chỉ:</b> <?php echo htmlspecialchars($user['Address'] ?? 'Chưa cập nhật'); ?></p>
        <p><b>Điện thoại:</b> <?php echo htmlspecialchars($user['Phone'] ?? 'Chưa cập nhật'); ?></p>
    </div>

    <!-- Sản phẩm đã chọn -->
    <div class="box">
        <h3>📚 Sản phẩm đã chọn</h3>
        <p>Sách - <i>(Danh sách sẽ được cập nhật từ giỏ hàng)</i></p>
    </div>

    <!-- Tổng tiền -->
    <div class="box">
        <h3>💰 Tổng tiền</h3>
        <p class="total"><b>200.000 VNĐ</b></p>
    </div>

    <!-- Form xác nhận thanh toán -->
    <form action="XuLyThanhToan.php" method="POST">
        <input type="hidden" name="userID"    value="<?php echo $userID; ?>">
        <input type="hidden" name="tongTien"  value="200000">
        <button type="submit">✅ Xác nhận thanh toán</button>
    </form>

    <p style="text-align:center; margin-top:15px;">
        <a href="index.php" style="color:#0f3a63; text-decoration:none; font-size:14px;">
            ← Tiếp tục mua sắm
        </a>
    </p>
</div>
</body>
</html>