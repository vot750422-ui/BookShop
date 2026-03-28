<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>BookStore</title>
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<main class="main-content">

    <?php
    $theLoai    = $_GET['theloai'] ?? '';
    $tenHienThi = $theLoaiMap[$theLoai] ?? '';

    echo $tenHienThi
        ? "<h1>" . htmlspecialchars(strtoupper($tenHienThi)) . "</h1>"
        : "<h1>SÁCH MỚI NHẤT</h1>";
    ?>

    <div class="book-grid">
        <?php
        try {
            if (!empty($theLoai)) {
                $stmt = $conn->prepare("SELECT * FROM books WHERE TheLoai = ?");
                $stmt->execute([$theLoai]);
            } else {
                $stmt = $conn->query("SELECT * FROM books");
            }

            $count = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
                $imgSrc = 'assets/images/' . ($row['ImageURL'] ?? 'book-default.jpg');
                echo "
                <div class='book-card'>
                    <img src='{$imgSrc}'
                         alt='" . htmlspecialchars($row['Title']) . "'
                         onerror=\"this.src='assets/images/book-default.jpg'\">
                    <h3>" . htmlspecialchars($row['Title']) . "</h3>
                    <p class='author'>" . htmlspecialchars($row['Author']) . "</p>
                    <p class='price'>" . number_format($row['Price'], 0, ',', '.') . " đ</p>

                    <!-- Overlay trượt lên khi hover -->
                    <a href='chitietsach.php?id={$row['BookID']}' class='book-overlay'>
                         Xem chi tiết sách
                    </a>
                </div>";
            }

            if ($count === 0) {
                echo "<p>Không có sách nào trong thể loại này.</p>";
            }

        } catch (Exception $e) {
            echo "<p>Đang chờ cập nhật ...</p>";
        }
        ?>
    </div>
</main>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>
<script src="assets/js/popup.js"></script>

</body>
</html>