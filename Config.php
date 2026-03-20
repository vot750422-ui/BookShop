<?php
$host     = "localhost";
$database = "sakurabo1_db";
$uid      = "admin";
$pwd      = "Trong@6419423";        // WAMP mặc định để trống

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