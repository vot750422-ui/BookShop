<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$userID       = $_SESSION['user_id'] ?? null;
$checkoutType = $_POST['checkout_type'] ?? 'cart';

if ($checkoutType === 'buynow') {
    $cart = $_SESSION['buy_now'] ?? [];
} else {
    $cart = $_SESSION['cart'] ?? [];
}

if (empty($cart)) {
    header("Location: thanhtoan.php?error=" . urlencode("Giỏ hàng trống!"));
    exit();
}

$hoTen = trim($_POST['HoTen'] ?? '');
$phone = trim($_POST['Phone'] ?? '');

if (empty($hoTen) || empty($phone)) {
    header("Location: thanhtoan.php?error=" . urlencode("Vui lòng điền đầy đủ thông tin giao hàng!"));
    exit();
}

if (strlen($phone) < 10) {
    header("Location: thanhtoan.php?error=" . urlencode("Số điện thoại phải đủ 10 chữ số!"));
    exit();
}

try {
    $conn->beginTransaction();

    $tongTien = 0;
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $stmtBooks = $conn->query("SELECT BookID, Price FROM books WHERE BookID IN ($ids)");
    $booksDB = [];
    while ($row = $stmtBooks->fetch(PDO::FETCH_ASSOC)) {
        $booksDB[$row['BookID']] = $row['Price'];
    }

    foreach ($cart as $bookID => $item) {
        $soLuong   = $item['slg'] ?? 1;
        $donGia    = $booksDB[$bookID] ?? 0;
        $tongTien += ($donGia * $soLuong);
    }

    $sqlOrder = "INSERT INTO orders (UserID, TongTien, TrangThai, HoTen, Phone)
                 VALUES (?, ?, 'Chờ xác nhận', ?, ?)";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([$userID, $tongTien, $hoTen, $phone]);
    $orderID = $conn->lastInsertId();

    $sqlDetail  = "INSERT INTO orderdetails (OrderID, BookID, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlDetail);
    foreach ($cart as $bookID => $item) {
        $soLuong = $item['slg'] ?? 1;
        $donGia  = $booksDB[$bookID] ?? 0;
        $stmtDetail->execute([$orderID, $bookID, $soLuong, $donGia]);
    }

    $conn->commit();

    if ($checkoutType === 'buynow') {
        unset($_SESSION['buy_now']);
    } else {
        unset($_SESSION['cart']);
    }

    header("Location: index.php?success=" . urlencode("Đặt hàng thành công!"));
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    header("Location: thanhtoan.php?error=" . urlencode("Lỗi hệ thống: " . $e->getMessage()));
    exit();
}
?>
