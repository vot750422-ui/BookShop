<?php
session_start();

// Khởi tạo giỏ hàng
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$BookID = (int)($_POST['BookID'] ?? $_GET['BookID'] ?? 0);
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($BookID <= 0) {
    header("Location: index.php");
    exit();
}

switch ($action) {

    // Thêm vào giỏ (từ index.php hoặc chitietsach.php)
    case 'them':
        require_once 'Config.php';
        $stmt = $conn->prepare("SELECT BookID, Title, Price FROM Books WHERE BookID = ?");
        $stmt->execute([$BookID]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($book) {
            if (isset($_SESSION['cart'][$BookID])) {
                // Đã có → tăng số lượng
                $_SESSION['cart'][$BookID]['slg']++;
            } else {
                // Chưa có → thêm mới
                $_SESSION['cart'][$BookID] = [
                    'Title' => $book['Title'],
                    'Price' => $book['Price'],
                    'slg'   => 1,
                ];
            }
        }
        header("Location: index.php?them=1");
        exit();

    // Xoá toàn bộ giỏ
    case 'xoa-het':
        $_SESSION['cart'] = [];
        header("Location: GioHang.php");
        exit();

    default:
        header("Location: GioHang.php");
        exit();
}
?>