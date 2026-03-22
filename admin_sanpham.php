<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: dangnhap.php");
    exit();
}

require_once 'config.php';

// Lấy danh sách sách
try {
    $stmt  = $conn->query("SELECT * FROM books ORDER BY BookID DESC");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $books = [];
}

// Nếu đang sửa → lấy thông tin sách đó
$editBook = null;
if (isset($_GET['sua'])) {
    $editID = (int)$_GET['sua'];
    try {
        $stmt     = $conn->prepare("SELECT * FROM books WHERE BookID = ?");
        $stmt->execute([$editID]);
        $editBook = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $editBook = null;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm - Admin</title>
    <!-- CSS dùng chung cho admin -->
    <link rel="stylesheet" href="assets/css/admin.css">
    <!-- CSS riêng trang quản lý sản phẩm -->
    <link rel="stylesheet" href="assets/css/admin_sanpham.css">
</head>
<body>

<!-- NAVBAR ADMIN -->
<div class="admin-navbar">
    <div class="admin-navbar-left">
        <span class="admin-logo"> ADMIN</span>
        <span class="admin-user">
            Xin chào, 
            <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong>
        </span>
    </div>
    <ul class="admin-menu">
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_sanpham.php" class="active">Quản lý sản phẩm</a></li>
        <li><a href="admin_khachhang.php">Quản lý khách hàng</a></li>
        <li><a href="admin_donhang.php">Quản lý đơn hàng</a></li>
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="logout.php" class="btn-logout">Đăng xuất</a></li>
    </ul>
</div>

<div class="admin-content">
    <h1> Quản Lý Sản Phẩm</h1>
    <p class="admin-subtitle">Thêm, sửa, xoá sách trong hệ thống.</p>

    <!-- Thông báo -->
    <?php if (!empty($_GET['msg'])): ?>
        <?php
        $msgs = [
            'them_ok' => '✅ Thêm sách thành công!',
            'sua_ok'  => '✅ Cập nhật sách thành công!',
            'xoa_ok'  => '✅ Xoá sách thành công!',
            'loi'     => '❌ Có lỗi xảy ra, vui lòng thử lại!',
        ];
        $msgText  = $msgs[$_GET['msg']] ?? '';
        $msgClass = str_contains($_GET['msg'], 'ok') ? 'alert-success' : 'alert-error';
        ?>
        <div class="alert <?php echo $msgClass; ?>"><?php echo $msgText; ?></div>
    <?php endif; ?>

    <!-- FORM THÊM / SỬA -->
    <div class="form-box">
        <h2><?php echo $editBook ? ' Sửa sách' : ' Thêm sách mới'; ?></h2>

        <form action="xulysanpham.php" method="POST">
            <?php if ($editBook): ?>
                <input type="hidden" name="action" value="sua">
                <input type="hidden" name="bookID" value="<?php echo $editBook['BookID']; ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="them">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group full">
                    <label>Tên sách *</label>
                    <input type="text" name="title" required
                           value="<?php echo htmlspecialchars($editBook['Title'] ?? ''); ?>"
                           placeholder="Nhập tên sách...">
                </div>

                <div class="form-group">
                    <label>Tác giả *</label>
                    <input type="text" name="author" required
                           value="<?php echo htmlspecialchars($editBook['Author'] ?? ''); ?>"
                           placeholder="Nhập tên tác giả...">
                </div>

                <div class="form-group">
                    <label>Thể loại *</label>
                    <select name="theloai" required>
                        <option value="">-- Chọn thể loại --</option>
                        <?php
                        $theLoais = [
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
                        foreach ($theLoais as $val => $label) {
                            $selected = ($editBook['TheLoai'] ?? '') === $val ? 'selected' : '';
                            echo "<option value='{$val}' {$selected}>{$label}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Giá (VNĐ) *</label>
                    <input type="number" name="price" required min="0"
                           value="<?php echo $editBook['Price'] ?? ''; ?>"
                           placeholder="Ví dụ: 89000">
                </div>

                <div class="form-group">
                    <label>Tồn kho</label>
                    <input type="number" name="stock" min="0"
                           value="<?php echo $editBook['Stock'] ?? 0; ?>">
                </div>

                <div class="form-group">
                    <label>Tên file ảnh</label>
                    <input type="text" name="imageurl"
                           value="<?php echo htmlspecialchars($editBook['ImageURL'] ?? ''); ?>"
                           placeholder="Ví dụ: ten-sach.jpg">
                </div>

                <div class="form-group full">
                    <label>Mô tả sách</label>
                    <textarea name="description"
                              placeholder="Nhập mô tả ngắn về sách..."><?php echo htmlspecialchars($editBook['Description'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <?php echo $editBook ? ' Lưu thay đổi' : '➕ Thêm sách'; ?>
                </button>
                <?php if ($editBook): ?>
                    <a href="admin_sanpham.php" class="btn-cancel"> Huỷ</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- DANH SÁCH SÁCH -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên sách</th>
                <th>Tác giả</th>
                <th>Thể loại</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($books)): ?>
                <tr><td colspan="8" style="text-align:center; color:#aaa;">Chưa có sách nào</td></tr>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo $book['BookID']; ?></td>
                    <td>
                        <img class="img-preview"
                             src="assets/images/<?php echo htmlspecialchars($book['ImageURL'] ?? 'book-default.jpg'); ?>"
                             onerror="this.src='assets/images/book-default.jpg'">
                    </td>
                    <td><?php echo htmlspecialchars($book['Title']); ?></td>
                    <td><?php echo htmlspecialchars($book['Author']); ?></td>
                    <td><?php echo htmlspecialchars($book['TheLoai']); ?></td>
                    <td><?php echo number_format($book['Price'], 0, ',', '.'); ?> đ</td>
                    <td><?php echo $book['Stock']; ?></td>
                    <td>
                        <a href="admin_sanpham.php?sua=<?php echo $book['BookID']; ?>"
                           class="btn-edit"> Sửa</a>
                        <a href="xulysanpham.php?action=xoa&bookID=<?php echo $book['BookID']; ?>"
                           class="btn-delete"
                           onclick="return confirm('Xoá sách \'<?php echo htmlspecialchars($book['Title']); ?>\'?')">
                            Xoá
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>