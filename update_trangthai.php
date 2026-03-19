<?php
require_once 'Config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['order_id'];
    $status = $_POST['trangthai'];

    $sql = "UPDATE Orders SET TrangThai = ? WHERE OrderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$status, $id]);

    header("Location: admin_donhang.php");
    exit();
}
?>