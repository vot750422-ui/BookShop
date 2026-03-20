<?php
session_start();
require_once 'Config.php';

$tukhoa = trim($_GET['tukhoa'] ?? '');
$books = [];

if ($tukhoa !== '') {
    try {
        $sql = "SELECT * FROM Books WHERE Title LIKE ? OR Author LIKE ? ORDER BY BookID DESC";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%" . $tukhoa . "%";
        $stmt->execute([$searchTerm, $searchTerm]);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Lỗi truy vấn: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css"> <!-- File CSS ta vừa tách -->
</head>
<body>

<?php include 'components/navbar.php'; ?>

<!-- Bỏ hết style inline, chỉ dùng class chuẩn của bạn -->
<main class="main-content">
    
    <!-- Dùng thẻ h1 giống hệt trang chủ để ăn CSS -->
    <?php if ($tukhoa === ''): ?>
        <h1>VUI LÒNG NHẬP TỪ KHÓA ĐỂ TÌM KIẾM</h1>
    <?php endif; ?>

    <div class="book-grid">
        <?php
        if (!empty($books)) {
            foreach ($books as $row) {
                // Sửa lại cho đúng key ImageURL giống với index.php
                $imgSrc = 'assets/images/' . ($row['ImageURL'] ?? 'book-default.jpg');
                echo "
                <div class='book-card'>
                    <img src='{$imgSrc}'
                         alt='" . htmlspecialchars($row['Title']) . "'
                         onerror=\"this.src='assets/images/book-default.jpg'\">
                    <h3>" . htmlspecialchars($row['Title']) . "</h3>
                    <p class='author'>" . htmlspecialchars($row['Author']) . "</p>
                    <p class='price'>" . number_format($row['Price'], 0, ',', '.') . " đ</p>

                    <a href='chitietsach.php?id={$row['BookID']}' class='book-overlay'>
                         Xem chi tiết sách
                    </a>
                </div>";
            }
        } else if ($tukhoa !== '') {
            echo "<p style='grid-column: 1 / -1; text-align: center; font-size: 16px; padding: 20px;'>Không tìm thấy quyển sách phù hợp.</p>";
        }
        ?>
    </div>

</main>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

</body>
</html>