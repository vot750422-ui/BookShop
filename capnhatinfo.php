<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $userID    = $_SESSION['user_id'];
    $fullname  = trim($_POST['fullname'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');

    try {
        $stmt = $conn->prepare("UPDATE users SET FullName = ?, Phone = ?, BirthDate = ? WHERE UserID = ?");
        $stmt->execute([$fullname, $phone, $birthdate, $userID]);

        // Cập nhật lại session để Navbar đổi tên ngay lập tức
        $_SESSION['user_name'] = $fullname;

        // Quay lại trang profile kèm thông báo thành công
        header("Location: profile.php?tab=info&msg=ok");
        exit();
    } catch (PDOException $e) {
        header("Location: profile.php?tab=info&msg=error");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}