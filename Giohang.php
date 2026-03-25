<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['action']) && isset($_POST['BookID'])) {
    $BookID = (int)$_POST['BookID'];

    switch ($_POST['action']) {
        case 'increase':
            if (isset($_SESSION['cart'][$BookID])) {
                $_SESSION['cart'][$BookID]['slg']++;
            }
            break;
        case 'decrease':
            if (isset($_SESSION['cart'][$BookID])) {
                $_SESSION['cart'][$BookID]['slg']--;
                if ($_SESSION['cart'][$BookID]['slg'] <= 0) {
                    unset($_SESSION['cart'][$BookID]);
                }
            }
            break;
        case 'delete':
            unset($_SESSION['cart'][$BookID]);
            break;
        case 'xoa-het':
            $_SESSION['cart'] = [];
            break;
    }

    header("Location: giohang.php");
    exit();
}

$TongTien = 0;
$items    = [];

if (!empty($_SESSION['cart'])) {
    $ids  = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $stmt = $conn->query("SELECT * FROM books WHERE BookID IN ($ids)");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $slg       = $_SESSION['cart'][$row['BookID']]['slg'] ?? 1;
        $ThanhTien = $row['Price'] * $slg;
        $TongTien += $ThanhTien;

        $items[] = [
            'BookID'    => $row['BookID'],
            'Title'     => $row['Title'],
            'Author'    => $row['Author'],
            'Price'     => $row['Price'],
            'ImageURL'  => $row['ImageURL'],
            'slg'       => $slg,
            'ThanhTien' => $ThanhTien,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/giohang.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<main class="main-content">
    <h1> Giỏ Hàng</h1>

    <?php if (empty($items)): ?>
        <div class="empty-cart">
            <span class="empty-icon"></span>
            <p>Giỏ hàng trống!</p>
            <a href="index.php" class="btn-thanhtoan"> Tiếp tục mua sắm</a>
        </div>

    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Ảnh</th><th>Tên sách</th><th>Tác giả</th>
                    <th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <img src="assets/images/<?php echo htmlspecialchars($item['ImageURL'] ?? 'book-default.jpg'); ?>"
                             onerror="this.src='assets/images/book-default.jpg'" alt="book">
                    </td>
                    <td><?php echo htmlspecialchars($item['Title']); ?></td>
                    <td><?php echo htmlspecialchars($item['Author']); ?></td>
                    <td><?php echo number_format($item['Price'], 0, ',', '.'); ?> đ</td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="BookID" value="<?php echo $item['BookID']; ?>">
                            <div class="qty-box">
                                <button type="submit" name="action" value="decrease">−</button>
                                <span><?php echo $item['slg']; ?></span>
                                <button type="submit" name="action" value="increase">+</button>
                            </div>
                        </form>
                    </td>
                    <td class="thanh-tien"><?php echo number_format($item['ThanhTien'], 0, ',', '.'); ?> đ</td>
                    
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tong-tien-box">
            <p>Tổng tiền: <span><?php echo number_format($TongTien, 0, ',', '.'); ?> đ</span></p>
        </div>

        <div class="btn-group">
            <div>

                <form method="POST" style="display:inline;" id="form-xoa-het">
                    <input type="hidden" name="BookID" value="0">
                    <input type="hidden" name="action" value="xoa-het">
                    <button type="button" class="btn-xoa" style="padding:10px 20px;"
                            onclick="confirmXoaHet()">
                         Xoá tất cả
                    </button>
                </form>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="index.php" class="btn-tieptuc">← Tiếp tục mua sắm</a>
                <a href="thanhtoan.php" class="btn-thanhtoan">Thanh toán →</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script>
function confirmXoaHet() {
    showPopup('Xoá toàn bộ giỏ hàng?', 'warning');
    hienNut2('form-xoa-het');
}


function hienNut2(formId) {

    const closeBtn = document.getElementById('popup-close');
    closeBtn.style.display = 'none';

    const cu = document.getElementById('popup-confirm-group');
    if (cu) cu.remove();

    const btnGroup = document.createElement('div');
    btnGroup.id        = 'popup-confirm-group';
    btnGroup.className = 'popup-btn-group';
    btnGroup.innerHTML = `
        <button class="popup-btn-confirm" id="popup-yes"> Xác nhận</button>
        <button class="popup-btn-cancel"  id="popup-no"> Huỷ</button>
    `;
    closeBtn.parentNode.insertBefore(btnGroup, closeBtn);

    document.getElementById('popup-yes').onclick = function () {
        closePopup();
        setTimeout(() => document.getElementById(formId).submit(), 300);
    };

    document.getElementById('popup-no').onclick = function () {
        closePopup();
        setTimeout(() => {
            btnGroup.remove();
            closeBtn.style.display = 'block';
        }, 300);
    };
}
</script>

</body>
</html>