<?php
$host     = "localhost";
$database = "bookshop";
$uid      = "root";
$pwd      = "";       
$charset = 'utf8mb4';
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