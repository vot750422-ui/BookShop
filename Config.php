<?php
$serverName = "DESKTOP-5I99H1C";
$database   = "bookshop";
$uid        = "root";
$pwd        = "";

try {
    $conn = new PDO(
        "mysql:host=localhost;Database=$database;charset=utf8",
        $uid,
        $pwd
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>