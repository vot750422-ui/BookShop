<?php
session_start();
require_once 'Config.php';

// Lấy BookID từ URL
$bookID = (int)($_GET['id'] ?? 0);

if ($bookID <= 0) {
    header("Location: index.php");
    exit();
}

// Lấy thông tin sách từ DB
try {
    $stmt = $conn->prepare("SELECT * FROM Books WHERE BookID = ?");
    $stmt->execute([$bookID]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    header("Location: index.php");
    exit();
}

$imgSrc = 'assets/images/' . ($book['ImageURL'] ?? 'book-default.jpg');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['Title']); ?> - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* ===== CHI TIẾT SÁCH ===== */
        .book-detail {
            display: flex;
            gap: 40px;
            padding: 40px 60px;
            flex-wrap: wrap;
            background: white;
        }

        /* ===== GALLERY ===== */
        .book-gallery {
            flex: 0 0 300px;
        }

        .main-img {
            width: 100%;
            height: 380px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .thumbs {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .thumbs img {
            width: 70px;
            height: 85px;
            object-fit: cover;
            border-radius: 4px;
            border: 2px solid #eee;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .thumbs img:hover {
            border-color: #c9a96e;
        }

        /* ===== THÔNG TIN SÁCH ===== */
        .book-info {
            flex: 1;
            min-width: 280px;
        }

        .book-info h1 {
            font-size: 22px;
            color: #2c1a0e;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .book-info p {
            margin-bottom: 10px;
            color: #555;
            font-size: 14px;
        }

        .book-info p b {
            color: #2c1a0e;
        }

        /* Giá */
        .price-box {
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .price-box .price {
            font-size: 26px;
            font-weight: bold;
            color: #c9a96e;
        }

        .price-box .old-price {
            font-size: 16px;
            color: #aaa;
            text-decoration: line-through;
        }

        /* Số lượng */
        .quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #2c1a0e;
        }

        .quantity button {
            width: 32px;
            height: 32px;
            border: 1px solid #c9a96e;
            background: white;
            color: #2c1a0e;
            font-size: 18px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .quantity button:hover {
            background: #c9a96e;
            color: white;
        }

        .quantity input {
            width: 50px;
            height: 32px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
        }

        /* Nút hành động */
        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cart-btn {
            flex: 1;
            padding: 13px;
            background: white;
            color: #2c1a0e;
            border: 2px solid #c9a96e;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .cart-btn:hover {
            background: #f0e6d3;
        }

        .buy-btn {
            flex: 1;
            padding: 13px;
            background: #2c1a0e;
            color: #f0e6d3;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .buy-btn:hover {
            background: #c9a96e;
        }

        /* ===== BẢNG CHI TIẾT ===== */
        .book-description {
            padding: 30px 60px;
            background: #fdf6ec;
            border-top: 2px solid #f0e6d3;
        }

        .book-description h2 {
            color: #2c1a0e;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .book-description table {
            width: 100%;
            max-width: 600px;
            border-collapse: collapse;
        }

        .book-description table tr {
            border-bottom: 1px solid #eee;
        }

        .book-description table td {
            padding: 10px 15px;
            font-size: 14px;
            color: #555;
        }

        .book-description table td:first-child {
            font-weight: 600;
            color: #2c1a0e;
            width: 180px;
            background: #f7efe0;
        }

        /* Mô tả sách */
        .book-desc-text {
            margin-top: 25px;
        }

        .book-desc-text h2 {
            color: #2c1a0e;
            margin-bottom: 12px;
            font-size: 18px;
        }

        .book-desc-text p {
            color: #555;
            line-height: 1.8;
            font-size: 14px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .book-detail { padding: 20px; }
            .book-description { padding: 20px; }
            .book-gallery { flex: 0 0 100%; }
        }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php"> Trang chủ</a>
    <span class="separator">›</span>
    <span class="breadcrumb-current">
         <?php echo htmlspecialchars($book['Title']); ?>
    </span>
</div>

<!-- Chi tiết sách -->
<div class="book-detail">

    <!-- LEFT: Ảnh -->
    <div class="book-gallery">
        <img class="main-img"
             src="<?php echo $imgSrc; ?>"
             onerror="this.src='assets/images/book-default.jpg'"
             alt="<?php echo htmlspecialchars($book['Title']); ?>">
        <div class="thumbs">
            <img src="<?php echo $imgSrc; ?>"
                 onerror="this.src='assets/images/book-default.jpg'">
            <img src="<?php echo $imgSrc; ?>"
                 onerror="this.src='assets/images/book-default.jpg'">
            <img src="<?php echo $imgSrc; ?>"
                 onerror="this.src='assets/images/book-default.jpg'">
        </div>
    </div>

    <!-- RIGHT: Thông tin -->
    <div class="book-info">
        <h1><?php echo htmlspecialchars($book['Title']); ?></h1>

        <p><b>Tác giả:</b> <?php echo htmlspecialchars($book['Author'] ?? 'Đang cập nhật'); ?></p>
        <p><b>Thể loại:</b> <?php echo htmlspecialchars($book['TheLoai'] ?? 'Đang cập nhật'); ?></p>
        <p><b>Hình thức:</b> Bìa mềm</p>
        <p><b>Tình trạng:</b>
            <?php echo ($book['Stock'] > 0)
                ? "<span style='color:green;font-weight:600;'>Còn hàng (" . $book['Stock'] . " cuốn)</span>"
                : "<span style='color:red;font-weight:600;'>Hết hàng</span>"; ?>
        </p>

        <!-- Giá -->
        <div class="price-box">
            <span class="price">
                <?php echo number_format($book['Price'], 0, ',', '.'); ?> đ
            </span>
        </div>

        <!-- Số lượng -->
        <div class="quantity">
            <span>Số lượng:</span>
            <button onclick="changeQty(-1)">−</button>
            <input type="number" id="qty" value="1" min="1" max="<?php echo $book['Stock']; ?>">
            <button onclick="changeQty(1)">+</button>
        </div>

        <!-- Nút hành động -->
        <div class="actions">
            <form action="XuLyGioHang.php" method="POST">
                <input type="hidden" name="bookID" value="<?php echo $book['BookID']; ?>">
                <input type="hidden" name="action" value="them">
                <button type="submit" class="cart-btn"> Thêm vào giỏ hàng</button>
            </form>
            <form action="XuLyGioHang.php" method="POST">
                <input type="hidden" name="bookID" value="<?php echo $book['BookID']; ?>">
                <input type="hidden" name="action" value="them">
                <button type="submit" class="buy-btn"
                    formaction="Thanhtoan.php"> Mua ngay</button>
            </form>
        </div>
    </div>
</div>

<!-- Bảng thông tin chi tiết -->
<div class="book-description">
    <h2> Thông tin chi tiết</h2>
    <table>
        <tr>
            <td>Mã hàng</td>
            <td><?php echo $book['BookID']; ?></td>
        </tr>
        <tr>
            <td>Tác giả</td>
            <td><?php echo htmlspecialchars($book['Author'] ?? 'Đang cập nhật'); ?></td>
        </tr>
        <tr>
            <td>Thể loại</td>
            <td><?php echo htmlspecialchars($book['TheLoai'] ?? 'Đang cập nhật'); ?></td>
        </tr>
        <tr>
            <td>Ngôn ngữ</td>
            <td>Tiếng Việt</td>
        </tr>
        <tr>
            <td>Số trang</td>
            <td>Đang cập nhật</td>
        </tr>
        <tr>
            <td>Hình thức</td>
            <td>Bìa mềm</td>
        </tr>
        <tr>
            <td>Tình trạng kho</td>
            <td><?php echo $book['Stock']; ?> cuốn</td>
        </tr>
    </table>

    <!-- Mô tả -->
    <?php if (!empty($book['Description'])): ?>
    <div class="book-desc-text">
        <h2> Mô tả sách</h2>
        <p><?php echo htmlspecialchars($book['Description']); ?></p>
    </div>
    <?php endif; ?>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>
<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    const max   = parseInt(input.max) || 99;
    let val = parseInt(input.value) + delta;
    if (val < 1)   val = 1;
    if (val > max) val = max;
    input.value = val;
}
</script>

</body>
</html>