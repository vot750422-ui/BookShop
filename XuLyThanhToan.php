<?php
session_start();
require_once 'config.php';

// Chỉ cho phép truy cập qua phương thức POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

// Yêu cầu phải đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$userID   = $_SESSION['user_id'];
$tongTien = (float)($_POST['tongTien'] ?? 0);

// Kiểm tra giỏ hàng có rỗng không
$cart = $_SESSION['cart'] ?? [];
if (empty($cart) || $tongTien <= 0) {
    header("Location: thanhtoan.php?error=Giỏ hàng trống hoặc tổng tiền không hợp lệ!");
    exit();
}

// Nhận thông tin giao hàng từ Form (Nối thành 1 chuỗi địa chỉ hoàn chỉnh)
$hoTen     = trim($_POST['HoTen'] ?? '');
$phone     = trim($_POST['Phone'] ?? '');
$diaChiDay = trim($_POST['DiaChiDay'] ?? '');
$phuong    = trim($_POST['PhuongXa'] ?? '');
$quan      = trim($_POST['QuanHuyen'] ?? '');
$tinh      = trim($_POST['TinhTP'] ?? '');
$ghiChu    = trim($_POST['GhiChu'] ?? '');

$diaChiGiaoHang = "$diaChiDay, $phuong, $quan, $tinh";

try {
    // 1. Bắt đầu Transaction (Đảm bảo an toàn dữ liệu: nếu lỗi thì hủy toàn bộ)
    $conn->beginTransaction();

    // 2. Insert vào bảng orders
    // Lưu ý: Cần kiểm tra xem bảng orders của bạn có cột DiaChiGiaoHang, PhoneGiaoHang, GhiChu không. 
    // Nếu có, hãy thêm vào câu SQL này. Ở đây tôi lưu tạm trạng thái là 'Chờ xác nhận'.
    $sqlOrder = "INSERT INTO orders (UserID, TongTien, TrangThai) VALUES (?, ?, 'Chờ xác nhận')";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([$userID, $tongTien]);

    $orderID = $conn->lastInsertId();

    // 3. Insert chi tiết từng sách vào bảng orderdetails
    $sqlDetail = "INSERT INTO orderdetails (OrderID, BookID, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlDetail);

    // Lấy thông tin giá mới nhất từ DB để phòng trường hợp giá bị sửa trong Session
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

    // 4. Hoàn tất Transaction
    $conn->commit();

    // Xóa giỏ hàng sau khi đặt thành công
    unset($_SESSION['cart']);

    // Chuyển hướng đến trang thành công
    header("Location: thanhtoanthanhcong.php?orderID=" . $orderID);
    exit();

} catch (PDOException $e) {
    // Nếu có lỗi ở bất kỳ bước nào, rollback (quay lại) trạng thái ban đầu
    $conn->rollBack();
    $errorMsg = urlencode("Lỗi hệ thống trong quá trình đặt hàng: " . $e->getMessage());
    header("Location: thanhtoan.php?error=" . $errorMsg);
    exit();
}
?>