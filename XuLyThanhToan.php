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
    header("Location: thanhtoan.php?error=" . urlencode("Giỏ hàng trống hoặc không có sản phẩm!"));
    exit();
}

$hoTen     = trim($_POST['HoTen'] ?? '');
$phone     = trim($_POST['Phone'] ?? '');
$diaChiDay = trim($_POST['DiaChiDay'] ?? '');
$phuong    = trim($_POST['PhuongXa'] ?? '');
$quan      = trim($_POST['QuanHuyen'] ?? '');
$tinh      = trim($_POST['TinhTP'] ?? '');
$email     = trim($_POST['Email'] ?? '');
$ghiChu    = trim($_POST['GhiChu'] ?? '');

try {
    $conn->beginTransaction();

    // Lấy giá chuẩn từ Database và tự tính tổng tiền 
    $tongTien = 0;
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $stmtbooks = $conn->query("SELECT BookID, Price FROM books WHERE BookID IN ($ids)");
    $booksDB = [];
    while ($row = $stmtbooks->fetch(PDO::FETCH_ASSOC)) {
        $booksDB[$row['BookID']] = $row['Price'];
    }

    foreach ($cart as $bookID => $item) {
        $soLuong = $item['slg'] ?? 1;
        $donGia  = $booksDB[$bookID] ?? 0;
        $tongTien += ($donGia * $soLuong);
    }

    $sqlOrder = "INSERT INTO orders (UserID, TongTien, TrangThai, HoTen, Phone, DiaChiDay, PhuongXa, QuanHuyen, TinhTP, Email, GhiChu) 
                 VALUES (?, ?, 'Chờ xác nhận', ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([$userID, $tongTien, $hoTen, $phone, $diaChiDay, $phuong, $quan, $tinh, $email, $ghiChu]);
    $orderID = $conn->lastInsertId();

    $sqlDetail = "INSERT INTO orderdetails (OrderID, BookID, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
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

    header("Location: index.php?success=" . urlencode("Đặt hàng thành công."));
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    header("Location: thanhtoan.php?error=" . urlencode("Lỗi hệ thống: " . $e->getMessage()));
    exit();
}
?>