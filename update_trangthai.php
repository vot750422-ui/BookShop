<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: dangnhap.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id     = (int)($_POST['order_id'] ?? 0);
    $status = trim($_POST['trangthai'] ?? '');

    $allowed = ['Chờ xác nhận', 'Đang giao', 'Đã giao', 'Da huy'];

    if ($id > 0 && in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET TrangThai = ? WHERE OrderID = ?");
        $stmt->execute([$status, $id]);
    }

    header("Location: admin_donhang.php?msg=ok");
    exit();
}

header("Location: admin_donhang.php");
exit();
?>
