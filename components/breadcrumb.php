<?php
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

$theLoai    = $_GET['theloai'] ?? '';
$tenHienThi = $theLoaiMap[$theLoai] ?? '';
?>

<?php if (!empty($tenHienThi)): ?>
<div class="breadcrumb">
     <a href="index.php">Trang chủ</a>
    <span class="separator">›</span>
    <span class="breadcrumb-current"> <?php echo htmlspecialchars($tenHienThi); ?></span>
</div>
<?php endif; ?>
