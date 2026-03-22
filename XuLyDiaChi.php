<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$userID   = $_SESSION['user_id'];
$action   = $_POST['action'] ?? '';
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'profile';

if ($action === 'add') {
    $name      = trim($_POST['receiver_name']  ?? '');
    $phone     = trim($_POST['receiver_phone'] ?? '');
    $diaChiDay = trim($_POST['detail_address'] ?? '');
    $phuongXa  = trim($_POST['phuong']         ?? '');
    $quanHuyen = trim($_POST['quan']           ?? '');
    $tinhTP    = trim($_POST['tinh']           ?? '');
    $isDef     = isset($_POST['is_default']) ? 1 : 0;

    if ($name && $phone && $diaChiDay && $phuongXa && $quanHuyen && $tinhTP) {
        try {
            if ($isDef === 1) {
                $conn->prepare("UPDATE useraddresses SET IsDefault = 0 WHERE UserID = ?")
                     ->execute([$userID]);
            }
            $stmt = $conn->prepare(
                "INSERT INTO useraddresses
                    (UserID, ReceiverName, ReceiverPhone, DiaChiDay, PhuongXa, QuanHuyen, TinhTP, IsDefault)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$userID, $name, $phone, $diaChiDay, $phuongXa, $quanHuyen, $tinhTP, $isDef]);
        } catch (PDOException $e) {}
    }

} elseif ($action === 'delete') {
    $addressID = (int)($_POST['address_id'] ?? 0);
    if ($addressID > 0) {
        try {
            $conn->prepare("DELETE FROM useraddresses WHERE AddressID = ? AND UserID = ?")
                 ->execute([$addressID, $userID]);
        } catch (PDOException $e) {}
    }
}

// Neu den tu trang thanh toan thi quay lai thanh toan
if ($redirect === 'thanhtoan') {
    header("Location: thanhtoan.php");
} else {
    header("Location: profile.php?tab=address");
}
exit();
?>