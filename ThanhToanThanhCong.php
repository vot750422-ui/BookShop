<?php
session_start();

$orderID  = $_GET['orderID'] ?? null;
$userName = $_SESSION['user_name'] ?? 'Quý khách';

if (!$orderID) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Hàng Thành Công - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .success-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
            background: #fdf6ec;
        }
        .success-box {
            background: white;
            border-radius: 12px;
            padding: 50px 60px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-top: 5px solid #c9a96e;
            max-width: 500px;
            width: 100%;
        }
        .success-icon { font-size: 70px; margin-bottom: 15px; }
        .success-box h2 { color: #2c1a0e; font-size: 24px; margin-bottom: 10px; }
        .success-box p  { color: #7b4f1e; margin-bottom: 8px; font-size: 15px; }
        .order-id {
            background: #fdf6ec;
            border: 1px solid #c9a96e;
            border-radius: 6px;
            padding: 12px 20px;
            margin: 20px 0;
            font-size: 16px;
            color: #2c1a0e;
        }
        .order-id strong { color: #c9a96e; font-size: 20px; }
        .btn-home {
            display: inline-block;
            background: #2c1a0e;
            color: #f0e6d3;
            padding: 13px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .btn-home:hover { background: #c9a96e; }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="success-wrapper">
    <div class="success-box">
        <div class="success-icon">✅</div>
        <h2>Đặt Hàng Thành Công!</h2>
        <p>Cảm ơn <strong><?php echo htmlspecialchars($userName); ?></strong> đã đặt hàng!</p>
        <p>Chúng tôi sẽ liên hệ xác nhận đơn hàng sớm nhất.</p>
        <div class="order-id">
            Mã đơn hàng: <strong>#<?php echo htmlspecialchars($orderID); ?></strong>
        </div>
        <a href="index.php" class="btn-home">🏠 Quay về trang chủ</a>
    </div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

</body>
</html>