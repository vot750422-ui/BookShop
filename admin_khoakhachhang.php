<?php
session_start();
require_once 'config.php';

// Kiểm traAdmin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: dangnhap.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    $newStatus = ($action === 'unlock') ? 1 : 0;

    try {
        $stmt = $conn->prepare("UPDATE users SET TrangThai = ? WHERE UserID = ?");
        $stmt->execute([$newStatus, $id]);
    } catch (PDOException $e) {
    }
}

header("Location: admin_khachhang.php");
exit();
?>