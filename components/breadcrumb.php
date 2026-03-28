<?php
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