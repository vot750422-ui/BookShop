<?php
session_start();
require_once 'config.php';


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$userID   = $_SESSION['user_id'] ?? null;
$tongTien = (float)($_POST['tongTien'] ?? 0);
$checkoutType = $_POST['checkout_type'] ?? 'cart';

if ($checkoutType === 'buynow') {
    $cart = $_SESSION['buy_now'] ?? [];
} else {
    $cart = $_SESSION['cart'] ?? [];
}

if (empty($cart) || $tongTien <= 0) {
    header("Location: thanhtoan.php?error=Không có sản phẩm để thanh toán!");
    exit();
}

if (empty($cart) || $tongTien <= 0) {
    header("Location: thanhtoan.php?error=Giỏ hàng trống hoặc tổng tiền không hợp lệ!");
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

    $sqlOrder = "INSERT INTO orders (UserID, TongTien, TrangThai, HoTen, Phone, DiaChiDay, PhuongXa, QuanHuyen, TinhTP, Email, GhiChu) 
                 VALUES (?, ?, 'Chờ xác nhận', ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([$userID, $tongTien, $hoTen, $phone, $diaChiDay, $phuong, $quan, $tinh, $email, $ghiChu]);

    $orderID = $conn->lastInsertId();

    $sqlDetail = "INSERT INTO orderdetails (OrderID, BookID, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlDetail);

    $ids = implode(',', array_map('intval', array_keys($cart)));
    $stmtbooks = $conn->query("SELECT BookID, Price FROM books WHERE BookID IN ($ids)");
    $booksDB = [];
    while ($row = $stmtbooks->fetch(PDO::FETCH_ASSOC)) {
        $booksDB[$row['BookID']] = $row['Price'];
    }

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

    header("Location: index.php?msg=order_success");
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    $errorMsg = urlencode("Lỗi hệ thống trong quá trình đặt hàng: " . $e->getMessage());
    header("Location: thanhtoan.php?error=" . $errorMsg);
    exit();
}
?>