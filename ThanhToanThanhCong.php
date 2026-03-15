<?php
session_start();

// Phải đăng nhập mới được vào trang này
if (!isset($_SESSION['user_id'])) {
    header("Location: Dangnhap.php");
    exit();
}

$orderID  = $_GET['orderID'] ?? null;
$userName = $_SESSION['user_name'] ?? 'Khách hàng';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Thành Công - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/thanhtoan.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="thanhtoan-wrapper">
    <div class="thanhtoan-container" style="text-align:center;">
        <div style="font-size:60px; margin-bottom:15px;">✅</div>
        <h2 style="color:#28a745;">Thanh Toán Thành Công!</h2>

        <div class="alert alert-success" style="margin-top:20px;">
            Cảm ơn <strong><?php echo htmlspecialchars($userName); ?></strong> đã đặt hàng!<br>
            <?php if ($orderID): ?>
                Mã đơn hàng của bạn: <strong>#<?php echo htmlspecialchars($orderID); ?></strong>
            <?php endif; ?>
        </div>

        <a href="index.php" class="btn-thanhtoan" style="display:block; text-decoration:none; margin-top:10px;">
            🏠 Quay về trang chủ
        </a>
    </div>
</div>

<?php include 'components/footer.html'; ?>


</body>
</html>
