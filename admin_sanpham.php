<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: dangnhap.php");
    exit();
}

require_once 'config.php';

try {
    $stmt  = $conn->query("SELECT * FROM books ORDER BY BookID DESC");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $books = [];
}

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
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/admin_sanpham.css">
</head>
<body>

<!-- NAVBAR -->
<div class="admin-navbar">
    <div class="admin-navbar-left">
        <span class="admin-logo"> ADMIN</span>
    </div>
    <ul class="admin-menu">
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_sanpham.php" class="active">Quản lý sản phẩm</a></li>
        <li><a href="admin_khachhang.php">Quản lý khách hàng</a></li>
        <li><a href="admin_donhang.php">Quản lý đơn hàng</a></li>
        <li><a href="index.php" target="_blank">Xem trang chủ</a></li>
        <li><a href="logout.php" class="btn-logout">Đăng xuất</a></li>
    </ul>
</div>

<div class="admin-content">
    <h1> Quản Lý Sản Phẩm</h1>
 
    <!-- Thông báo -->
    <?php if (!empty($_GET['msg'])): ?>
        <?php
        $msgs = [
            'them_ok'   => ' Thêm sách thành công',
            'sua_ok'    => ' Cập nhật sách thành công',
            'toggle_ok' => ' Thay đổi trạng thái sách thành công',
            'loi'       => ' Có lỗi xảy ra, vui lòng thử lại!',
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
                        // $theLoaiMap được lấy từ config.php
                        foreach ($theLoaiMap as $val => $label) {
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
                              placeholder="Nhập mô tả sách..."><?php echo htmlspecialchars($editBook['Description'] ?? ''); ?></textarea>
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
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th style="min-width: 140px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($books)): ?>
                <tr><td colspan="8" style="text-align:center; color:#aaa;">Chưa có sách</td></tr>
            <?php else: ?>
                <?php foreach ($books as $book): 
                    $trangThai = $book['TrangThai']; // Mặc định là 1 nếu NULL
                ?>
                <tr>
                    <td><?php echo $book['BookID']; ?></td>
                    <td>
                        <img class="img-preview"
                             src="assets/images/<?php echo htmlspecialchars($book['ImageURL'] ?? 'book-default.jpg'); ?>"
                             onerror="this.src='assets/images/book-default.jpg'">
                    </td>
                    <td><?php echo htmlspecialchars($book['Title']); ?></td>
                    <td><?php echo htmlspecialchars($book['Author']); ?></td>
                    <td><?php echo number_format($book['Price'], 0, ',', '.'); ?> đ</td>
                    <td><?php echo $book['Stock']; ?></td>
                    <td>
                        <?php if ($trangThai == 1): ?>
                            <span style="color: #2ecc71; font-weight: bold; background: #e8f8f5; padding: 4px 8px; border-radius: 4px; font-size: 13px;">Đang bán</span>
                        <?php else: ?>
                            <span style="color: #e74c3c; font-weight: bold; background: #fdedec; padding: 4px 8px; border-radius: 4px; font-size: 13px;">Đã ẩn</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="admin_sanpham.php?sua=<?php echo $book['BookID']; ?>"
                           class="btn-edit"> Sửa</a>
                        
                        <!-- Nút Ẩn/Hiện động dựa vào trạng thái -->
                        <?php if ($trangThai == 1): ?>
                            <a href="xulysanpham.php?action=toggle&bookID=<?php echo $book['BookID']; ?>"
                               class="btn-delete" style="background-color: #e67e22;"
                               onclick="return confirm('Bạn có chắc muốn ẨN sách \'<?php echo htmlspecialchars(addslashes($book['Title'])); ?>\' khỏi hệ thống không?')">
                                Ẩn
                            </a>
                        <?php else: ?>
                            <a href="xulysanpham.php?action=toggle&bookID=<?php echo $book['BookID']; ?>"
                               class="btn-delete" style="background-color: #3498db;"
                               onclick="return confirm('Bạn có chắc muốn HIỆN lại sách \'<?php echo htmlspecialchars(addslashes($book['Title'])); ?>\' không?')">
                                Hiện
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>