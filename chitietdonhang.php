<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$userID  = $_SESSION['user_id'];
$orderID = (int)($_GET['id'] ?? 0);

try {
    // ✅ Lấy đúng tên cột theo DB đã tạo
    $stmtOrder = $conn->prepare("SELECT * FROM orders WHERE OrderID = ? AND UserID = ?");
    $stmtOrder->execute([$orderID, $userID]);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Đơn hàng không tồn tại hoặc bạn không có quyền xem.");
    }

    // Lấy chi tiết sản phẩm trong đơn
    $stmtItems = $conn->prepare(
        "SELECT od.*, b.Title, b.ImageURL
         FROM orderdetails od
         JOIN books b ON od.BookID = b.BookID
         WHERE od.OrderID = ?"
    );
    $stmtItems->execute([$orderID]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}

// ✅ Ghép địa chỉ từ các cột đúng tên
$diaChiDay = $order['DiaChiDay'] ?? '';
$phuongXa  = $order['PhuongXa']  ?? '';
$quanHuyen = $order['QuanHuyen'] ?? '';
$tinhTP    = $order['TinhTP']    ?? '';
$diaChiFull = implode(', ', array_filter([$diaChiDay, $phuongXa, $quanHuyen, $tinhTP]));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $orderID ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .order-detail-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .detail-header {
            border-bottom: 2px solid #c9a96e;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .detail-header h2 { color: #2c1a0e; margin: 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .info-box { background: #fdf6ec; padding: 15px; border-radius: 8px; }
        .info-box h4 {
            margin: 0 0 10px;
            color: #2c1a0e;
            border-left: 4px solid #c9a96e;
            padding-left: 10px;
        }
        .info-box p { margin: 6px 0; font-size: 14px; color: #555; }
        .item-table { width: 100%; border-collapse: collapse; }
        .item-table th { background: #2c1a0e; color: #f0e6d3; padding: 12px; text-align: left; }
        .item-table td { padding: 12px; border-bottom: 1px solid #f0e6d3; font-size: 14px; }
        .total-row { font-weight: bold; font-size: 18px; text-align: right; margin-top: 15px; color: #c9a96e; }
        .status-tag {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            background: #f0e6d3;
            color: #2c1a0e;
        }
        .btn-back {
            display: inline-block;
            background: #2c1a0e;
            color: #f0e6d3;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-back:hover { background: #c9a96e; }
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
            <!-- ✅ Dùng đúng tên cột HoTen, Phone, NgayDat -->
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['HoTen'] ?? $_SESSION['user_name']) ?></p>
            <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['Phone'] ?? '') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['Email'] ?? '') ?></p>
            <p><strong>Ngày đặt:</strong> <?= $order['NgayDat'] ? date('d/m/Y H:i', strtotime($order['NgayDat'])) : '---' ?></p>
        </div>
        <div class="info-box">
            <h4>Địa chỉ giao hàng</h4>
            <!-- ✅ Ghép từ các cột DiaChiDay, PhuongXa, QuanHuyen, TinhTP -->
            <p><?= htmlspecialchars($diaChiFull ?: 'Chưa có thông tin') ?></p>
            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['GhiChu'] ?? 'Không có') ?></p>
        </div>
    </div>

    <h4 style="color:#2c1a0e; margin-bottom:10px;"> Danh sách sản phẩm</h4>
    <table class="item-table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>SL</th>
                <th style="text-align:right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="assets/images/<?= htmlspecialchars($item['ImageURL'] ?? 'book-default.jpg') ?>"
                             width="45" height="55" style="object-fit:cover; border-radius:4px;"
                             onerror="this.src='assets/images/book-default.jpg'">
                        <?= htmlspecialchars($item['Title']) ?>
                    </div>
                </td>
                <td><?= number_format($item['DonGia'], 0, ',', '.') ?> đ</td>
                <td><?= $item['SoLuong'] ?></td>
                <td style="text-align:right;">
                    <?= number_format($item['DonGia'] * $item['SoLuong'], 0, ',', '.') ?> đ
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-row">
        Tổng cộng: <?= number_format($order['TongTien'], 0, ',', '.') ?> VNĐ
    </div>

    <div style="margin-top:25px; text-align:center;">
        <a href="profile.php?tab=orders" class="btn-back">← Quay lại lịch sử đơn hàng</a>
    </div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

</body>
</html>