<?php
session_start();
require_once 'Config.php'; // Kết nối PDO dùng chung toàn hệ thống

// Bảo vệ: chỉ cho phép POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

// Bảo vệ: phải đăng nhập mới được thanh toán
if (!isset($_SESSION['user_id'])) {
    header("Location: Dangnhap.php");
    exit();
}

// Lấy dữ liệu từ form Thanhtoan.php
$userID   = $_SESSION['user_id'];               // Lấy từ Session cho an toàn
$tongTien = $_POST['tongTien'] ?? 0;

// Kiểm tra dữ liệu hợp lệ
if ($tongTien <= 0) {
    header("Location: Thanhtoan.php?error=Tổng tiền không hợp lệ!");
    exit();
}

try {
    /*
     * Lưu đơn hàng vào database.
     * Ghi chú: Bảng Orders cần được tạo trước với cấu trúc:
     *   Orders(OrderID INT IDENTITY PK, UserID INT FK, TongTien DECIMAL, NgayDat DATETIME, TrangThai NVARCHAR)
     * Nếu chưa có bảng, sinh viên cần chạy script tạo bảng trước.
     */
    $sql  = "UPDATE Books
                     SET Title=?, Author=?, TheLoai=?, Price=?, Stock=?, ImageURL=?, Description=?
                     WHERE BookID=?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$userID, $tongTien]);

    // Lấy ID đơn hàng vừa tạo
    $orderID = $conn->lastInsertId();

    // Chuyển đến trang thành công
    header("Location: ThanhToanThanhCong.php?orderID=" . $orderID);
    exit();

} catch (PDOException $e) {
    // Nếu bảng Orders chưa tồn tại hoặc lỗi khác, thông báo rõ ràng
    $errorMsg = urlencode("Lỗi hệ thống: " . $e->getMessage());
    header("Location: Thanhtoan.php?error=" . $errorMsg);
    exit();
}
?>
