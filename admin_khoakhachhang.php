<?php
session_start();
require_once 'Config.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: Dangnhap.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    // Nếu action là unlock thì set = 1, ngược lại set = 0
    $newStatus = ($action === 'unlock') ? 1 : 0;

    try {
        $stmt = $conn->prepare("UPDATE Users SET TrangThai = ? WHERE UserID = ?");
        $stmt->execute([$newStatus, $id]);
    } catch (PDOException $e) {
        // Log lỗi nếu cần
    }
}

// Xong việc thì quay lại trang quản lý khách hàng
header("Location: admin_khachhang.php");
exit();
?>