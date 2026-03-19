<?php
session_start();
// Nếu đã đăng nhập thì không cần hiện trang này nữa, đẩy về trang chủ
if (isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - BookStore</title>
    <!-- CSS dùng chung toàn site -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- CSS riêng của trang Đăng Nhập -->
    <link rel="stylesheet" href="assets/css/dangnhap.css">
</head>
<body>

<!-- Navbar nhúng từ component -->
<?php include 'components/navbar.php'; ?>

<!-- Nội dung trang Đăng Nhập -->
<div class="login-wrapper">
    <form class="login-container" action="XuLyDangNhap.php" method="POST">
        <h2>Đăng Nhập</h2>

        <!-- Thông báo lỗi nếu có -->
        <?php if (!empty($_GET['error'])): ?>
            <p style="color:red; text-align:center; margin-bottom:10px;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </p>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>

        <button type="submit" class="btn-dangnhap">Đăng nhập</button>

        <p>Chưa có tài khoản? <a href="Dangky.php">Đăng ký ngay</a></p>
    </form>
</div>

<!-- Footer nhúng từ component -->
<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>



</body>
</html>
