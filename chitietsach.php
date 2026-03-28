<?php
session_start();
require_once 'config.php';

$bookID = (int)($_GET['id'] ?? 0);

if ($bookID <= 0) {
    header("Location: index.php");
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM books WHERE BookID = ? AND trangthai = 1");
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
$maTheLoai = $book['TheLoai'] ?? '';
$tenTheLoai = $theLoaiMap[$maTheLoai] ?? 'Khác';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['Title']); ?> - BookStore</title>
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/chitietsach.css"> 
</head>
<body>

<?php include 'components/navbar.php'; ?>


<div class="breadcrumb">
    <a href="index.php"> Trang chủ</a>
    <span class="separator">›</span>
    <a href="index.php?theloai=<?php echo htmlspecialchars($maTheLoai); ?>" style="text-decoration: none; color: inherit;">
        <?php echo htmlspecialchars($tenTheLoai); ?>
    </a>
    <span class="separator">›</span>
    <span class="breadcrumb-current">
         <?php echo htmlspecialchars($book['Title']); ?>
    </span>
</div>


<div class="book-detail">

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


    <div class="book-info">
        <h1><?php echo htmlspecialchars($book['Title']); ?></h1>

        <p><b>Tác giả:</b> <?php echo htmlspecialchars($book['Author'] ?? 'Đang cập nhật'); ?></p>
        <p><b>Thể loại:</b> <?php echo htmlspecialchars($tenTheLoai); ?></p>
        

        <div class="price-box">
            <span class="price">
                <?php echo number_format($book['Price'], 0, ',', '.'); ?> đ
            </span>
        </div>

        <?php if ($book['Stock'] > 0): ?>
            <div class="quantity">
                <span>Số lượng:</span>
                <button onclick="changeQty(-1)">−</button>
                <input type="number" id="qty" value="1" min="1" max="<?php echo $book['Stock']; ?>">
                <button onclick="changeQty(1)">+</button>
            </div>
            
            <div class="actions">
                <form id="form-add-to-cart" action="xulygiohang.php" method="POST">
                    <input type="hidden" name="BookID" value="<?php echo $book['BookID']; ?>">
                    <input type="hidden" name="action" value="them">
                    <input type="hidden" name="qty" id="qty-them" value="1">
                    <button type="submit" class="cart-btn">Thêm vào giỏ hàng</button>
                </form>

                <form action="xulygiohang.php" method="POST">
                    <input type="hidden" name="BookID" value="<?php echo $book['BookID']; ?>">
                    <input type="hidden" name="action" value="muangay">
                    <input type="hidden" name="qty" id="qty-mua" value="1">
                    <button type="submit" class="buy-btn" onclick="syncQty('qty-mua')">Mua ngay</button>
                </form>
            </div>
        <?php else: ?>
            <p style="color: #e74c3c; font-size: 18px; font-weight: bold; margin-top: 20px;">Sản phẩm hiện đang tạm hết hàng.</p>
        <?php endif; ?>
    </div>
</div>


<div class="book-description">
    <h2> Thông tin chi tiết</h2>
    <table>
        <tr>
            <td>Tác giả</td>
            <td><?php echo htmlspecialchars($book['Author'] ?? 'Đang cập nhật'); ?></td>
        </tr>
        <tr>
            <td>Thể loại</td>
            <td><?php echo htmlspecialchars($tenTheLoai); ?></td>
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
    </table>


    <?php if (!empty($book['Description'])): ?>
    <div class="book-desc-text">
        <h2> Mô tả sách</h2>
        <p><?php echo htmlspecialchars($book['Description']); ?></p>
    </div>
    <?php endif; ?>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script src="assets/js/popup.js"></script>
<script src="assets/js/cart.js"></script>

<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    const max = parseInt(input.max) || 99;
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
    
    syncQty('qty-them');
    syncQty('qty-mua');
}

function syncQty(hiddenId) {
    const visible = document.getElementById('qty');
    const hidden = document.getElementById(hiddenId);
    if (visible && hidden) {
        hidden.value = visible.value;
    }
}
</script>

</body>
</html>