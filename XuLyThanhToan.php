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
$email     = trim($_POST['Email'] ?? '');
$ghiChu    = trim($_POST['GhiChu'] ?? '');

try {
    $conn->beginTransaction();

    $tongTien = 0;
    $cartItemsToInsert = [];


    foreach ($cart as $bookID => $item) {
        $soLuong = $item['slg'] ?? 1;
        
        $stmtCheck = $conn->prepare("SELECT Price, Stock, Title FROM books WHERE BookID = ? AND trangthai = 1 FOR UPDATE");
        $stmtCheck->execute([$bookID]);
        $bookData = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$bookData || $bookData['Stock'] < $soLuong) {
            $conn->rollBack();
            $tenSach = $bookData ? $bookData['Title'] : 'Sách đã bị ẩn';
            header("Location: giohang.php?error=" . urlencode("Sản phẩm '$tenSach' không đủ số lượng tồn kho!"));
            exit();
        }
        
        $tongTien += ($bookData['Price'] * $soLuong);
        $cartItemsToInsert[] = [
            'BookID' => $bookID,
            'SoLuong' => $soLuong,
            'DonGia' => $bookData['Price']
        ];
    }


    $sqlOrder = "INSERT INTO orders (UserID, TongTien, TrangThai, HoTen, Phone, DiaChiDay, Email, GhiChu) 
                 VALUES (?, ?, 'Chờ xác nhận', ?, ?, ?, ?, ?)";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([$userID, $tongTien, $hoTen, $phone, $diaChiDay, $email, $ghiChu]);
    $orderID = $conn->lastInsertId();

    // 3. Tạo chi tiết đơn hàng & TRỪ TỒN KHO
    $sqlDetail = "INSERT INTO orderdetails (OrderID, BookID, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlDetail);

    $sqlUpdateStock = "UPDATE books SET Stock = Stock - ? WHERE BookID = ?";
    $stmtUpdateStock = $conn->prepare($sqlUpdateStock);

    foreach ($cartItemsToInsert as $cItem) {
        $stmtDetail->execute([$orderID, $cItem['BookID'], $cItem['SoLuong'], $cItem['DonGia']]);
        $stmtUpdateStock->execute([$cItem['SoLuong'], $cItem['BookID']]); // Lệnh trừ kho
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