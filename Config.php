<?php
$serverName = "DESKTOP-5I99H1C"; // Tên máy bạn
$database = "BookShop";
$uid = "Theoythick1";  
$pwd = "123456";  

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Kết nối thành công!"; 
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>