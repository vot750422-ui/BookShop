<?php
session_start();
require_once 'config.php';
// Khởi tạo giỏ hàng
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$BookID = (int)($_POST['BookID'] ?? $_GET['BookID'] ?? 0);
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$qty    = (int)($_POST['qty'] ?? 1); // Lấy số lượng người dùng chọn (nếu có)

switch ($action) {
    case 'them':
        if ($BookID > 0) {
            // Chỉ cần lấy thông tin cơ bản để lưu vào Session
            $stmt = $conn->prepare("SELECT Title, Price, ImageURL FROM books WHERE BookID = ?");
            $stmt->execute([$BookID]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($book) {
                if (isset($_SESSION['cart'][$BookID])) {
                    // Nếu đã có trong giỏ, cộng thêm số lượng mới vào
                    $_SESSION['cart'][$BookID]['slg'] += $qty;
                } else {
                    // Nếu chưa có, thêm mới hoàn toàn
                    $_SESSION['cart'][$BookID] = [
                        'Title'    => $book['Title'],
                        'Price'    => $book['Price'],
                        'ImageURL' => $book['ImageURL'],
                        'slg'      => $qty
                    ];
                }
            }
        }
        // Chuyển hướng về giỏ hàng để xem kết quả ngay cho sướng
        header("Location: giohang.php?status=success");
        exit();
        break; // <--- Cực kỳ quan trọng, đừng quên cái này!

    case 'increase':
        if (isset($_SESSION['cart'][$BookID])) {
            $_SESSION['cart'][$BookID]['slg']++;
        }
        header("Location: giohang.php");
        exit();
        break;

    case 'decrease':
        if (isset($_SESSION['cart'][$BookID])) {
            $_SESSION['cart'][$BookID]['slg']--;
            if ($_SESSION['cart'][$BookID]['slg'] <= 0) {
                unset($_SESSION['cart'][$BookID]);
            }
        }
        header("Location: giohang.php");
        exit();
        break;

    case 'xoa-het':
        $_SESSION['cart'] = [];
        header("Location: giohang.php");
        exit();
        break;

    default:
        header("Location: index.php");
        exit();
}
?>