<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $BookID = (int)($_POST['BookID'] ?? 0);
    $action = $_POST['action'];

    if ($action === 'xoa-het') {
        $_SESSION['cart'] = [];
        header("Location: giohang.php?success=" . urlencode("Đã dọn sạch giỏ hàng!"));
        exit();
    }
}

$TongTien = 0;
$items    = [];

if (!empty($_SESSION['cart'])) {
    $ids  = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $stmt = $conn->query("SELECT * FROM books WHERE BookID IN ($ids)");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $slg       = $_SESSION['cart'][$row['BookID']]['slg'] ?? 1;
        $ThanhTien = $row['Price'] * $slg;
        $TongTien += $ThanhTien;

        $items[] = [
            'BookID'    => $row['BookID'],
            'Title'     => $row['Title'],
            'Author'    => $row['Author'],
            'Price'     => $row['Price'],
            'ImageURL'  => $row['ImageURL'],
            'slg'       => $slg,
            'ThanhTien' => $ThanhTien,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/giohang.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<main class="main-content">
    <h1>Giỏ Hàng</h1>

    <?php if (empty($items)): ?>
        <div class="empty-cart">
            <p>Giỏ hàng trống!</p>
            <a href="index.php" class="btn-thanhtoan">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sách</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr class="cart-item" data-id="<?= $item['BookID'] ?>">
                    <td>
                        <img src="assets/images/<?= htmlspecialchars($item['ImageURL'] ?? 'book-default.jpg') ?>" 
                             onerror="this.src='assets/images/book-default.jpg'" width="50">
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($item['Title']) ?></strong><br>
                        <small><?= htmlspecialchars($item['Author']) ?></small>
                    </td>
                    <td><?= number_format($item['Price'], 0, ',', '.') ?> đ</td>
                    <td>
                        <div class="qty-box">
                            <button type="button" class="btn-qty-ajax" data-id="<?= $item['BookID'] ?>" data-action="decrease">−</button>
                            <span class="qty-display"><?= $item['slg'] ?></span>
                            <button type="button" class="btn-qty-ajax" data-id="<?= $item['BookID'] ?>" data-action="increase">+</button>
                        </div>
                    </td>
                    <td class="subtotal-display">
                        <?= number_format($item['ThanhTien'], 0, ',', '.') ?> đ
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tong-tien-box">
            <p>Tổng cộng: <span class="total-all-display"><?= number_format($TongTien, 0, ',', '.') ?> đ</span></p>
        </div>

        <div class="btn-group">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="xoa-het">
                <button type="submit" class="btn-xoa">Xoá tất cả</button>
            </form>
            <div style="display:flex; gap:10px;">
                <a href="index.php" class="btn-tieptuc">Tiếp tục mua sắm</a>
                <a href="thanhtoan.php" class="btn-thanhtoan">Thanh toán</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script src="assets/js/popup.js"></script>
<script src="assets/js/cart.js"></script>

</body>
</html>