<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID    = $_SESSION['user_id'];
    $fullName  = trim($_POST['fullname']  ?? '');
    $phone     = trim($_POST['Phone']     ?? '');
    $address   = trim($_POST['address']   ?? '');
    $birthDate = trim($_POST['birthdate'] ?? '');

    if (empty($fullName) || empty($phone)) {
        header("Location: profile.php?tab=info&error=" . urlencode("Vui lòng nhập đầy đủ thông tin!"));
        exit();
    }

    if (strlen($phone) < 10) {
        header("Location: profile.php?tab=info&error=" . urlencode("Số điện thoại không hợp lệ!"));
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE users SET FullName = ?, Phone = ?, Address = ?, BirthDate = ? WHERE UserID = ?");
        $stmt->execute([$fullName, $phone, $address, $birthDate, $userID]);

        header("Location: profile.php?tab=info&success=" . urlencode("Cập nhật thông tin thành công!"));
        exit();
    } catch (PDOException $e) {
        header("Location: profile.php?tab=info&error=" . urlencode("Lỗi hệ thống!"));
        exit();
    }
} else {
    header("Location: profile.php");
    exit();
}
?>
