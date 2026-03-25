<?php
session_start();
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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dangnhap.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="login-wrapper">
    <form class="login-container" action="xulydangnhap.php" method="POST">
        <h2>Đăng Nhập</h2>

        <?php if (!empty($_GET['error'])): ?>
            <p style="color:red; text-align:center; margin-bottom:10px;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </p>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>

        <button type="submit" class="btn-dangnhap">Đăng nhập</button>

        <p><a href="dangky.php">Chưa có tài khoản? </a></p>
    </form>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>



</body>
</html>
