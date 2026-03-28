<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$BookID = (int)($_POST['BookID'] ?? $_GET['BookID'] ?? 0);
$action = $_POST['action']   ?? $_GET['action']   ?? '';
$qty    = max(1, (int)($_POST['qty'] ?? 1));
$isAjax = isset($_POST['ajax']);

switch ($action) {
    case 'them':
    case 'increase':
    case 'decrease':
        if ($BookID > 0) {

            $stmt = $conn->prepare("SELECT Title, Price, ImageURL, Stock FROM books WHERE BookID = ? AND trangthai = 1");
            $stmt->execute([$BookID]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($book) {
                $stock = (int)$book['Stock'];
                if ($action === 'them' || $action === 'increase') {
                    $currentQty = $_SESSION['cart'][$BookID]['slg'] ?? 0;
                    $addQty = ($action === 'them') ? $qty : 1;
                    

                    $newQty = $currentQty + $addQty;
                    if ($newQty > $stock) {
                        $newQty = $stock; 
                    }

                    $_SESSION['cart'][$BookID] = [
                        'Title' => $book['Title'],
                        'Price' => $book['Price'],
                        'ImageURL' => $book['ImageURL'],
                        'slg' => $newQty
                    ];
                } elseif ($action === 'decrease') {
                    if (isset($_SESSION['cart'][$BookID])) {
                        $_SESSION['cart'][$BookID]['slg']--;
                        if ($_SESSION['cart'][$BookID]['slg'] <= 0) unset($_SESSION['cart'][$BookID]);
                    }
                }
            } else {

                if (isset($_SESSION['cart'][$BookID])) {
                    unset($_SESSION['cart'][$BookID]);
                }
            }
        }
        break;

    case 'muangay':
        if ($BookID > 0) {
            $stmt = $conn->prepare("SELECT Stock FROM books WHERE BookID = ? AND trangthai = 1");
            $stmt->execute([$BookID]);
            $bookCheck = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($bookCheck && $bookCheck['Stock'] >= $qty) {
                $_SESSION['buy_now'] = [$BookID => ['slg' => $qty]];
                if (!$isAjax) { header("Location: thanhtoan.php?type=buynow"); exit(); }
            } else {
                if (!$isAjax) { header("Location: chitietsach.php?id=$BookID&error=Sản phẩm không đủ số lượng!"); exit(); }
            }
        }
        break;

    case 'xoa-het':
        $_SESSION['cart'] = [];
        if (!$isAjax) { header("Location: giohang.php?success=" . urlencode("Đã dọn sạch!")); exit(); }
        break;
}

if ($isAjax) {
    $itemQty = $_SESSION['cart'][$BookID]['slg'] ?? 0;
    $itemPrice = $book['Price'] ?? 0;
    
    $totalAll = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalAll += ($item['slg'] * $item['Price']);
    }

    echo json_encode([
        'status' => 'success',
        'newQty' => $itemQty,
        'newSubtotal' => number_format($itemQty * $itemPrice, 0, ',', '.'),
        'newTotal' => number_format($totalAll, 0, ',', '.')
    ]);
    exit();
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: " . $referer);
exit();