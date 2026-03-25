<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$BookID   = (int)($_POST['BookID'] ?? $_GET['BookID'] ?? 0);
$action   = $_POST['action']   ?? $_GET['action']   ?? '';
$qty      = max(1, (int)($_POST['qty'] ?? 1));
$redirect = $_POST['redirect'] ?? '';

$referer  = $_SERVER['HTTP_REFERER'] ?? 'index.php';

switch ($action) {

    case 'them':
        if ($BookID > 0) {
            $stmt = $conn->prepare("SELECT Title, Price, ImageURL, Stock FROM books WHERE BookID = ?");
            $stmt->execute([$BookID]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($book) {

                $stock = (int)$book['Stock'];
                if (isset($_SESSION['cart'][$BookID])) {
                    $newQty = $_SESSION['cart'][$BookID]['slg'] + $qty;
                    $_SESSION['cart'][$BookID]['slg'] = min($newQty, $stock);
                } else {
                    $_SESSION['cart'][$BookID] = [
                        'Title'    => $book['Title'],
                        'Price'    => $book['Price'],
                        'ImageURL' => $book['ImageURL'],
                        'slg'      => min($qty, $stock),
                    ];
                }
            }
        }

        if ($action === 'muangay') {
    $_SESSION['buy_now'] = [
        $bookID => [
            'slg' => $soLuong 
        ]
    ];

    header("Location: thanhtoan.php?type=buynow");
    exit();
}

  
        if (!empty($referer) && strpos($referer, 'xulygiohang.php') === false) {

            $referer = preg_replace('/(&|\?)msg=[^&]*/', '', $referer);
            $separator = (parse_url($referer, PHP_URL_QUERY) == NULL) ? '?' : '&';
            header("Location: " . $referer . $separator . "msg=added");
        } else {
            header("Location: index.php?msg=added");
        }
        exit();

    case 'increase':
        if (isset($_SESSION['cart'][$BookID])) {
            $_SESSION['cart'][$BookID]['slg']++;
        }
        header("Location: giohang.php");
        exit();

    case 'decrease':
        if (isset($_SESSION['cart'][$BookID])) {
            $_SESSION['cart'][$BookID]['slg']--;
            if ($_SESSION['cart'][$BookID]['slg'] <= 0) {
                unset($_SESSION['cart'][$BookID]);
            }
        }
        header("Location: giohang.php");
        exit();

    case 'xoa-het':
        $_SESSION['cart'] = [];
        header("Location: giohang.php");
        exit();

    default:
        header("Location: index.php");
        exit();
}
?>