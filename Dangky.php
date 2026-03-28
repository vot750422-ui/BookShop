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
    <form class="dangky-container" action="xulydangky.php" method="POST" onsubmit="return kiemTraDangKy(event)">
        <h2>Đăng Ký Tài Khoản</h2>
        <input type="text" name="name" placeholder="Họ tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="date" name="birthdate" placeholder="Ngày sinh">
        <input type="password" name="password" placeholder="Mật khẩu" required>

        <input type="tel" name="phone" id="sdt" required placeholder="Nhập số điện thoại..." oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10">
        
        <button type="submit" class="btn-dangky">Đăng ký</button>
        <p><a href="dangnhap.php">Đã có tài khoản?</a></p>
    </form>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>
<script src="assets/js/popup.js"></script>
</body>
</html>