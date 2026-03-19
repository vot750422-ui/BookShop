<?php
session_start();
require_once 'Config.php';

// Chỉ cho phép POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

// Phải login
if (!isset($_SESSION['user_id'])) {
    header("Location: Dangnhap.php");
    exit();
}

$userID   = $_SESSION['user_id'];
$tongTien = $_POST['tongTien'] ?? 0;

// ✅ VALIDATE TRƯỚC
if ($tongTien <= 0) {
    header("Location: Thanhtoan.php?error=Tổng tiền không hợp lệ!");
    exit();
}

try {
    // ✅ INSERT ĐƠN HÀNG
    $sql = "INSERT INTO Orders (UserID, TongTien, TrangThai)
            VALUES (?, ?, 'Đã thanh toán')";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userID, $tongTien]);

    // ✅ LẤY ID
    $orderID = $conn->lastInsertId();

    // ✅ (optional) xóa giỏ hàng
    unset($_SESSION['cart']);

    // chuyển trang
    header("Location: ThanhToanThanhCong.php?orderID=" . $orderID);
    exit();

} catch (PDOException $e) {
    $errorMsg = urlencode("Lỗi hệ thống: " . $e->getMessage());
    header("Location: Thanhtoan.php?error=" . $errorMsg);
    exit();
}
?>