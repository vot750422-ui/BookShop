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
$maTheLoai = $book['TheLoai'] ?? '';
$tenTheLoai = $theLoaiMap[$maTheLoai] ?? 'Khác';
?>

<div class="breadcrumb">
    <a href="index.php">Trang chủ</a>
    <span class="separator">›</span>
    <a href="index.php?theloai=<?php echo htmlspecialchars($maTheLoai); ?>" style="text-decoration: none; color: inherit;">
        <?php echo htmlspecialchars($tenTheLoai); ?>
    </a>
    <span class="separator">›</span>
    <span class="breadcrumb-current">
         <?php echo htmlspecialchars($book['Title']); ?>
    </span>
</div>