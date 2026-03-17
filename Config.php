<?php
$host     = "localhost";
$database = "BookShop";
$uid      = "root";
$pwd      = "";        // WAMP mặc định để trống

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8",
        $uid,
        $pwd
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>