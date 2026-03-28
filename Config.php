<?php
$host     = "localhost";
$database = "bookshop";
$uid      = "root";
$pwd      = "";       
$charset  = 'utf8mb4';

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$database;charset=$charset",
        $uid,
        $pwd
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

// Khai báo mảng thể loại dùng chung cho toàn bộ dự án
$theLoaiMap = [
    'tieu-thuyet'     => 'Tiểu Thuyết',
    'truyen-ngan'     => 'Truyện Ngắn',
    'co-dien'         => 'Văn Học Cổ Điển',
    'kinh-di'         => 'Kinh Dị',
    'tam-ly-toi-pham' => 'Tâm Lý Học Tội Phạm',
    'ky-nang-song'    => 'Kỹ Năng Sống',
    'but-bi'          => 'Bút Bi',
    'but-chi'         => 'Bút Chì',
    'but-da-quang'    => 'Bút Dạ Quang',
    'vo-o-ly'         => 'Vở Ô Li',
    'so-tay'          => 'Sổ Tay',
    'giay-note'       => 'Giấy Note',
];
?>