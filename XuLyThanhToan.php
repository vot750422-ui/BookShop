<?php
session_start();
require_once 'Config.php';

// Chỉ cho phép POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

// Kiểm tra giỏ hàng
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: GioHang.php");
    exit();
}

// Lấy thông tin giao hàng từ form
$userID    = $_POST['userID']    ?? null;   // NULL nếu khách vãng lai
$tongTien  = (int)($_POST['tongTien'] ?? 0);
$hoTen     = trim($_POST['HoTen']     ?? '');
$phone     = trim($_POST['Phone']     ?? '');
$email     = trim($_POST['Email']     ?? '');
$diaChiDay = trim($_POST['DiaChiDay'] ?? '');
$phuongXa  = trim($_POST['PhuongXa']  ?? '');
$quanHuyen = trim($_POST['QuanHuyen'] ?? '');
$tinhTP    = trim($_POST['TinhTP']    ?? '');
$ghiChu    = trim($_POST['GhiChu']    ?? '');

// Kiểm tra dữ liệu bắt buộc
if (empty($hoTen) || empty($phone) || empty($diaChiDay) ||
    empty($phuongXa) || empty($quanHuyen) || empty($tinhTP)) {
    header("Location: Thanhtoan.php?error=Vui lòng điền đầy đủ thông tin giao hàng!");
    exit();
}

if ($tongTien <= 0) {
    header("Location: Thanhtoan.php?error=Tổng tiền không hợp lệ!");
    exit();
}

try {
    // ✅ INSERT đúng vào bảng Orders với đầy đủ thông tin giao hàng
    $sql = "INSERT INTO Orders
                (UserID, TongTien, NgayDat, TrangThai,
                 HoTen, Email, Phone, DiaChiDay, PhuongXa, QuanHuyen, TinhTP, GhiChu)
            VALUES
                (?, ?, NOW(), 'Chờ xác nhận',
                 ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $userID ?: null,   // NULL nếu khách vãng lai
        $tongTien,
        $hoTen,
        $email,
        $phone,
        $diaChiDay,
        $phuongXa,
        $quanHuyen,
        $tinhTP,
        $ghiChu,
    ]);

    $orderID = $conn->lastInsertId();

    // ✅ Xoá giỏ hàng sau khi đặt hàng thành công
    $_SESSION['cart'] = [];

    header("Location: ThanhToanThanhCong.php?orderID=" . $orderID);
    exit();

} catch (PDOException $e) {
    $errorMsg = urlencode("Lỗi hệ thống: " . $e->getMessage());
    header("Location: Thanhtoan.php?error=" . $errorMsg);
    exit();
}
?>