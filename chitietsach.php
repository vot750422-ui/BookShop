<?php
session_start();
require_once 'Config.php';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ✅ Xử lý thêm vào giỏ hàng
$thongBao = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $bookIDPost = (int)($_POST['BookID'] ?? 0);
    $soLuong    = (int)($_POST['soLuong'] ?? 1);
    if ($soLuong < 1) $soLuong = 1;

    if ($_POST['action'] === 'them' && $bookIDPost > 0) {
        if (isset($_SESSION['cart'][$bookIDPost])) {
            $_SESSION['cart'][$bookIDPost]['slg'] += $soLuong;
        } else {
            $_SESSION['cart'][$bookIDPost] = ['slg' => $soLuong];
        }
        $thongBao = 'them_ok';
    }

    // Mua ngay → thêm vào giỏ rồi chuyển sang thanh toán
    if ($_POST['action'] === 'mua_ngay' && $bookIDPost > 0) {
        if (isset($_SESSION['cart'][$bookIDPost])) {
            $_SESSION['cart'][$bookIDPost]['slg'] += $soLuong;
        } else {
            $_SESSION['cart'][$bookIDPost] = ['slg' => $soLuong];
        }
        header("Location: Thanhtoan.php");
        exit();
    }
}

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
    <link rel="stylesheet" href="assets/css/chitietsach.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php">Trang chủ</a>
    <span class="separator">›</span>
    <span class="breadcrumb-current">
        <?php echo htmlspecialchars($book['Title']); ?>
    </span>
</div>

<!-- ✅ Thông báo thêm vào giỏ thành công -->
<?php if ($thongBao === 'them_ok'): ?>
    <div class="alert-success-cart">
        ✅ Đã thêm <strong><?php echo htmlspecialchars($book['Title']); ?></strong> vào giỏ hàng!
        <a href="GioHang.php" style="margin-left:10px; color:#0f6624; font-weight:600;">
            Xem giỏ hàng →
        </a>
    </div>
<?php endif; ?>

<!-- Chi tiết sách -->
<div class="book-detail">

    <!-- LEFT: Ảnh -->
    <div class="book-gallery">
        <img class="main-img"
             src="<?php echo $imgSrc; ?>"
             onerror="this.src='assets/images/book-default.jpg'"
             alt="<?php echo htmlspecialchars($book['Title']); ?>">
        <div class="thumbs">
            <img src="<?php echo $imgSrc; ?>" onerror="this.src='assets/images/book-default.jpg'">
            <img src="<?php echo $imgSrc; ?>" onerror="this.src='assets/images/book-default.jpg'">
            <img src="<?php echo $imgSrc; ?>" onerror="this.src='assets/images/book-default.jpg'">
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

        <div class="price-box">
            <span class="price"><?php echo number_format($book['Price'], 0, ',', '.'); ?> đ</span>
        </div>

        <!-- ✅ 1 form dùng chung cho cả 2 nút -->
        <form method="POST" action="chitietsach.php?id=<?php echo $bookID; ?>">
            <input type="hidden" name="BookID" value="<?php echo $book['BookID']; ?>">

            <div class="quantity">
                <span>Số lượng:</span>
                <button type="button" onclick="changeQty(-1)">−</button>
                <input type="number" id="qty" name="soLuong"
                       value="1" min="1" max="<?php echo $book['Stock']; ?>">
                <button type="button" onclick="changeQty(1)">+</button>
            </div>

            <div class="actions">
                <button type="submit" name="action" value="them"
                        class="cart-btn"
                        <?php echo ($book['Stock'] <= 0) ? 'disabled style="opacity:0.5;cursor:not-allowed"' : ''; ?>>
                    🛒 Thêm vào giỏ hàng
                </button>
                <button type="submit" name="action" value="mua_ngay"
                        class="buy-btn"
                        <?php echo ($book['Stock'] <= 0) ? 'disabled style="opacity:0.5;cursor:not-allowed"' : ''; ?>>
                    ⚡ Mua ngay
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bảng thông tin chi tiết -->
<div class="book-description">
    <h2>📋 Thông tin chi tiết</h2>
    <table>
        <tr><td>Mã hàng</td><td><?php echo $book['BookID']; ?></td></tr>
        <tr><td>Tác giả</td><td><?php echo htmlspecialchars($book['Author'] ?? 'Đang cập nhật'); ?></td></tr>
        <tr><td>Thể loại</td><td><?php echo htmlspecialchars($book['TheLoai'] ?? 'Đang cập nhật'); ?></td></tr>
        <tr><td>Ngôn ngữ</td><td>Tiếng Việt</td></tr>
        <tr><td>Số trang</td><td>Đang cập nhật</td></tr>
        <tr><td>Hình thức</td><td>Bìa mềm</td></tr>
        <tr><td>Tình trạng kho</td><td><?php echo $book['Stock']; ?> cuốn</td></tr>
    </table>

    <?php if (!empty($book['Description'])): ?>
    <div class="book-desc-text">
        <h2>📖 Mô tả sách</h2>
        <p><?php echo htmlspecialchars($book['Description']); ?></p>
    </div>
    <?php endif; ?>
</div>

<?php include 'components/footer.html'; ?>

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