<?php
session_start();
require_once 'Config.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: Dangnhap.php");
    exit();
}

$userID  = $_SESSION['user_id'];
$orderID = $_GET['id'] ?? 0;

try {
    // 2. Lấy thông tin tổng quát của đơn hàng (Chỉ lấy nếu đúng của User này)
    $sqlOrder = "SELECT * FROM Orders WHERE OrderID = ? AND UserID = ?";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([$orderID, $userID]);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Đơn hàng không tồn tại hoặc bạn không có quyền xem.");
    }

    // 3. Lấy chi tiết các sản phẩm trong đơn hàng
    $sqlItems = "SELECT od.*, b.Title, b.ImageURL 
                 FROM OrderDetails od 
                 JOIN Books b ON od.BookID = b.BookID 
                 WHERE od.OrderID = ?";
    $stmtItems = $conn->prepare($sqlItems);
    $stmtItems->execute([$orderID]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $orderID ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .order-detail-container { max-width: 800px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .detail-header { border-bottom: 2px solid #c9a96e; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box h4 { margin-top: 0; color: #2c1a0e; border-left: 4px solid #c9a96e; padding-left: 10px; }
        .item-table { width: 100%; border-collapse: collapse; }
        .item-table th { background: #fdf6ec; padding: 12px; text-align: left; border-bottom: 2px solid #eee; }
        .item-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .total-row { font-weight: bold; font-size: 18px; text-align: right; margin-top: 20px; color: #e74c3c; }
        .status-tag { padding: 5px 10px; border-radius: 4px; font-size: 14px; font-weight: bold; background: #eee; }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="order-detail-container">
    <div class="detail-header">
        <h2>Chi tiết đơn hàng #<?= $orderID ?></h2>
        <span class="status-tag"><?= htmlspecialchars($order['TrangThai']) ?></span>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h4>Thông tin người nhận</h4>
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['TenNguoiNhan'] ?? $_SESSION['user_name']) ?></p>
            <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['SDT'] ?? '') ?></p>
            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['OrderDate'] ?? 'now')) ?></p>
        </div>
        <div class="info-box">
            <h4>Địa chỉ giao hàng</h4>
            <p><?= nl2br(htmlspecialchars($order['DiaChi'] ?? '')) ?></p>
            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['GhiChu'] ?? 'Không có') ?></p>
        </div>
    </div>

    <h4>Danh sách sản phẩm</h4>
    <table class="item-table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>SL</th>
                <th style="text-align: right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="assets/images/<?= htmlspecialchars($item['ImageURL'] ?? 'book-default.jpg') ?>" width="40">
                        <?= htmlspecialchars($item['Title']) ?>
                    </div>
                </td>
                <td><?= number_format($item['DonGia'], 0, ',', '.') ?> đ</td>
                <td><?= $item['SoLuong'] ?></td>
                <td style="text-align: right;"><?php echo number_format($item['DonGia'] * $item['SoLuong'], 0, ',', '.'); ?> đ</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-row">
        Tổng cộng: <?= number_format($order['TongTien'], 0, ',', '.') ?> VNĐ
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <a href="profile.php?tab=orders" class="btn-save" style="text-decoration: none; padding: 10px 20px;"> quay lại lịch sử đơn hàng</a>
    </div>
</div>

<?php include 'components/footer.html'; ?>

</body>
</html>