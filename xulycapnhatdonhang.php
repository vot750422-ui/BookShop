<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php?tab=orders");
    exit();
}

$userID  = $_SESSION['user_id'];
$orderID = (int)($_POST['orderID'] ?? 0);
$action  = $_POST['action'] ?? 'capnhat';

if ($orderID <= 0) {
    header("Location: profile.php?tab=orders");
    exit();
}

// Kiem tra don hang co thuoc ve user nay khong
try {
    $check = $conn->prepare("SELECT OrderID FROM orders WHERE OrderID = ? AND UserID = ?");
    $check->execute([$orderID, $userID]);
    if (!$check->fetch()) {
        header("Location: profile.php?tab=orders");
        exit();
    }
} catch (PDOException $e) {
    header("Location: profile.php?tab=orders");
    exit();
}

// ===== HUY DON HANG =====
if ($action === 'huy') {
    try {
        $stmt = $conn->prepare("UPDATE orders SET TrangThai = 'Da huy' WHERE OrderID = ? AND UserID = ?");
        $stmt->execute([$orderID, $userID]);
        header("Location: chitietdonhang.php?id=$orderID&huyed=1");
        exit();
    } catch (PDOException $e) {
        header("Location: chitietdonhang.php?id=$orderID");
        exit();
    }
}

// ===== CAP NHAT THONG TIN GIAO HANG =====
$hoTen     = trim($_POST['HoTen']     ?? '');
$phone     = trim($_POST['Phone']     ?? '');
$email     = trim($_POST['Email']     ?? '');
$diaChiDay = trim($_POST['DiaChiDay'] ?? '');
$tinhTP    = trim($_POST['TinhTP']    ?? '');
$quanHuyen = trim($_POST['QuanHuyen'] ?? '');
$phuongXa  = trim($_POST['PhuongXa']  ?? '');
$ghiChu    = trim($_POST['GhiChu']    ?? '');

// Kiem tra cac truong bat buoc
if (empty($hoTen) || empty($phone) || empty($diaChiDay) ||
    empty($tinhTP) || empty($quanHuyen) || empty($phuongXa)) {
    header("Location: chitietdonhang.php?id=$orderID&error=" . urlencode("Vui long dien day du thong tin bat buoc!"));
    exit();
}

try {
    $sql = "UPDATE orders
            SET HoTen=?, Phone=?, Email=?, DiaChiDay=?, TinhTP=?, QuanHuyen=?, PhuongXa=?, GhiChu=?
            WHERE OrderID=? AND UserID=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $hoTen, $phone, $email,
        $diaChiDay, $tinhTP, $quanHuyen, $phuongXa, $ghiChu,
        $orderID, $userID
    ]);

    header("Location: chitietdonhang.php?id=$orderID&updated=1");
    exit();

} catch (PDOException $e) {
    header("Location: chitietdonhang.php?id=$orderID");
    exit();
}
?>