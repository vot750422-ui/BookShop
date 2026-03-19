<?php
session_start();
require_once 'Config.php';

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

    header("Location: GioHang.php");
    exit();
}

$TongTien = 0;
$items    = [];

if (!empty($_SESSION['cart'])) {
    $ids  = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $stmt = $conn->query("SELECT * FROM Books WHERE BookID IN ($ids)");

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
    <style>
        .cart-table { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.08); margin-top:20px; }
        .cart-table th { background:#2c1a0e; color:#f0e6d3; padding:13px 15px; text-align:left; font-size:14px; }
        .cart-table td { padding:12px 15px; border-bottom:1px solid #f0e6d3; font-size:14px; vertical-align:middle; }
        .cart-table tr:hover td { background:#fdf6ec; }
        .cart-table img { width:60px; height:75px; object-fit:cover; border-radius:4px; border:1px solid #eee; }
        .qty-box { display:flex; align-items:center; gap:8px; }
        .qty-box button { width:30px; height:30px; border:1px solid #c9a96e; background:white; color:#2c1a0e; font-size:16px; cursor:pointer; border-radius:4px; transition:all 0.2s; }
        .qty-box button:hover { background:#c9a96e; color:white; }
        .qty-box span { font-weight:bold; font-size:15px; min-width:30px; text-align:center; }
        .btn-xoa { background:#e74c3c; color:white; border:none; padding:6px 14px; border-radius:4px; cursor:pointer; font-size:13px; transition:background 0.2s; }
        .btn-xoa:hover { background:#c0392b; }
        .thanh-tien { color:#c9a96e; font-weight:bold; font-size:15px; }
        .tong-tien-box { text-align:right; margin-top:20px; padding:20px; background:white; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.08); }
        .tong-tien-box p { font-size:18px; font-weight:bold; color:#2c1a0e; margin-bottom:15px; }
        .tong-tien-box p span { color:#c9a96e; font-size:22px; }
        .btn-group { display:flex; justify-content:space-between; align-items:center; margin-top:20px; flex-wrap:wrap; gap:10px; }
        .btn-tieptuc { display:inline-block; background:white; color:#2c1a0e; padding:12px 25px; border-radius:6px; text-decoration:none; font-size:14px; font-weight:600; border:2px solid #c9a96e; transition:all 0.3s; }
        .btn-tieptuc:hover { background:#f0e6d3; }
        .btn-thanhtoan { display:inline-block; background:#2c1a0e; color:#f0e6d3; padding:12px 30px; border-radius:6px; text-decoration:none; font-size:15px; font-weight:600; transition:background 0.3s; }
        .btn-thanhtoan:hover { background:#c9a96e; }
        .empty-cart { text-align:center; padding:60px 0; color:#999; }
        .empty-cart .empty-icon { font-size:70px; display:block; margin-bottom:15px; }
        .empty-cart p { font-size:16px; margin-bottom:20px; }

        /* Popup 2 nút */
        .popup-btn-group { display:flex; gap:10px; justify-content:center; margin-top:5px; }
        .popup-btn-confirm { background:#e74c3c; color:white; border:none; padding:10px 25px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; }
        .popup-btn-confirm:hover { background:#c0392b; }
        .popup-btn-cancel { background:#eee; color:#555; border:none; padding:10px 25px; border-radius:6px; font-size:14px; cursor:pointer; }
        .popup-btn-cancel:hover { background:#ddd; }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<main class="main-content">
    <h1>🛒 Giỏ Hàng</h1>

    <?php if (empty($items)): ?>
        <div class="empty-cart">
            <span class="empty-icon">🛒</span>
            <p>Giỏ hàng của bạn đang trống!</p>
            <a href="index.php" class="btn-thanhtoan">← Tiếp tục mua sắm</a>
        </div>

    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Ảnh</th><th>Tên sách</th><th>Tác giả</th>
                    <th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th><th>Xoá</th>
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
                    <td>
                        <!-- ✅ Nút xoá dùng popup -->
                        <form method="POST" style="display:inline;"
                              id="form-xoa-<?php echo $item['BookID']; ?>">
                            <input type="hidden" name="BookID" value="<?php echo $item['BookID']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="button" class="btn-xoa"
                                    onclick="confirmXoa('form-xoa-<?php echo $item['BookID']; ?>', '<?php echo addslashes($item['Title']); ?>')">
                                🗑 Xoá
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tong-tien-box">
            <p>Tổng tiền: <span><?php echo number_format($TongTien, 0, ',', '.'); ?> đ</span></p>
        </div>

        <div class="btn-group">
            <div>
                <!-- ✅ Nút xoá tất cả dùng popup -->
                <form method="POST" style="display:inline;" id="form-xoa-het">
                    <input type="hidden" name="BookID" value="0">
                    <input type="hidden" name="action" value="xoa-het">
                    <button type="button" class="btn-xoa" style="padding:10px 20px;"
                            onclick="confirmXoaHet()">
                        🗑 Xoá tất cả
                    </button>
                </form>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="index.php" class="btn-tieptuc">← Tiếp tục mua sắm</a>
                <a href="Thanhtoan.php" class="btn-thanhtoan">Thanh toán →</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'components/footer.html'; ?>

<!-- ✅ Include alertpopup TRƯỚC script bên dưới -->
<?php include 'components/alertpopup.php'; ?>

<script>
// ===== POPUP XOÁ 1 SẢN PHẨM =====
function confirmXoa(formId, tenSach) {
    showPopup('Xoá "' + tenSach + '" khỏi giỏ hàng?', 'warning');
    hienNut2(formId);
}

// ===== POPUP XOÁ TOÀN BỘ =====
function confirmXoaHet() {
    showPopup('Xoá toàn bộ giỏ hàng?', 'warning');
    hienNut2('form-xoa-het');
}

// ===== HIỆN 2 NÚT XÁC NHẬN / HUỶ =====
function hienNut2(formId) {
    // Ẩn nút Đóng mặc định
    const closeBtn = document.getElementById('popup-close');
    closeBtn.style.display = 'none';

    // Xoá nhóm nút cũ nếu có
    const cu = document.getElementById('popup-confirm-group');
    if (cu) cu.remove();

    // Tạo nhóm 2 nút mới
    const btnGroup = document.createElement('div');
    btnGroup.id        = 'popup-confirm-group';
    btnGroup.className = 'popup-btn-group';
    btnGroup.innerHTML = `
        <button class="popup-btn-confirm" id="popup-yes">✅ Xác nhận</button>
        <button class="popup-btn-cancel"  id="popup-no">✖ Huỷ</button>
    `;
    closeBtn.parentNode.insertBefore(btnGroup, closeBtn);

    // Xác nhận → submit form
    document.getElementById('popup-yes').onclick = function () {
        closePopup();
        setTimeout(() => document.getElementById(formId).submit(), 300);
    };

    // Huỷ → đóng popup, khôi phục nút Đóng
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