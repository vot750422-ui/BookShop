<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dangky.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="dangky-wrapper">
    <form class="dangky-container" action="xulydangky.php" method="POST">
        <h2>Đăng Ký Tài Khoản</h2>
        <input type="text" name="name" placeholder="Họ tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="date" name="birthdate" placeholder="Ngày sinh">
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <input type="password" name="re-password" placeholder="Nhập lại mật khẩu" required>
        <input type="text" name="phone" placeholder="Số điện thoại" required>
        <button type="submit" class="btn-dangky">Đăng ký</button>
        <p><a href="dangnhap.php">Đã có tài khoản?</a></p>
    </form>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>
<script src="assets/js/popup.js"></script>
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const errorMsg = urlParams.get('error');
    if (errorMsg) {
        showPopup(errorMsg, 'error');
        window.history.replaceState(null, null, window.location.pathname);
    }
</script>

</body>
</html>