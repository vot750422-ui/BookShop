<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$userID  = $_SESSION['user_id'];
$orderID = (int)($_POST['orderID'] ?? 0);
$action  = $_POST['action'] ?? '';

if ($orderID <= 0) {
    header("Location: profile.php?tab=orders");
    exit();
}

try {
    $stmtCheck = $conn->prepare("SELECT TrangThai FROM orders WHERE OrderID = ? AND UserID = ?");
    $stmtCheck->execute([$orderID, $userID]);
    $order = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: profile.php?tab=orders");
        exit();
    }

    $trangThaiHienTai = mb_strtolower(trim($order['TrangThai']));
    if ($trangThaiHienTai === 'da huy' || $trangThaiHienTai === 'hủy' || $trangThaiHienTai === 'huy') {
        header("Location: chitietdonhang.php?id=$orderID");
        exit();
    }

    if ($action === 'capnhat') {
        $hoTen     = trim($_POST['HoTen'] ?? '');
        $phone     = trim($_POST['Phone'] ?? '');
        $diaChiDay = trim($_POST['DiaChiDay'] ?? '');
        $phuong    = trim($_POST['PhuongXa'] ?? '');
        $quan      = trim($_POST['QuanHuyen'] ?? '');
        $tinh      = trim($_POST['TinhTP'] ?? '');
        $email     = trim($_POST['Email'] ?? '');
        $ghiChu    = trim($_POST['GhiChu'] ?? '');

        $sql = "UPDATE orders SET HoTen=?, Phone=?, DiaChiDay=?, PhuongXa=?, QuanHuyen=?, TinhTP=?, Email=?, GhiChu=? WHERE OrderID=? AND UserID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$hoTen, $phone, $diaChiDay, $phuong, $quan, $tinh, $email, $ghiChu, $orderID, $userID]);
        
        header("Location: chitietdonhang.php?id=$orderID&success=Cập nhật đơn hàng thành công!");
        exit();
    } 
    
    elseif ($action === 'huy') {
        $sql = "UPDATE orders SET TrangThai = 'Da huy' WHERE OrderID = ? AND UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$orderID, $userID]);
        
        header("Location: chitietdonhang.php?id=$orderID&success=Đơn hàng đã được huỷ thành công!");
        exit();
    }

} catch (PDOException $e) {
    die("Lỗi hệ thống: " . $e->getMessage());
}

header("Location: chitietdonhang.php?id=$orderID");
exit();
?>