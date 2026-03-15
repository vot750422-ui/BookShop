<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - BookStore</title>
    <!-- CSS dùng chung toàn site -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- CSS riêng của trang Đăng Ký -->
    <link rel="stylesheet" href="assets/css/dangky.css">
</head>
<body>

<!-- Navbar nhúng từ component -->
<?php include 'components/navbar.php'; ?>

<!-- Nội dung trang Đăng Ký -->
<div class="dangky-wrapper">
    <form class="dangky-container" action="XuLyDangKy.php" method="POST">
        <h2>Đăng Ký Tài Khoản</h2>

        <input type="text"     name="name"        placeholder="Họ tên"              required>
        <input type="email"    name="email"        placeholder="Email"               required>
        <input type="date"     name="birthdate"    placeholder="Ngày sinh">
        <input type="password" name="password"     placeholder="Mật khẩu"           required>
        <input type="password" name="re-password"  placeholder="Nhập lại mật khẩu" required>
        <input type="text"     name="address"      placeholder="Địa chỉ"            required>
        <input type="text"     name="phone"        placeholder="Số điện thoại"      required>

        <button type="submit" class="btn-dangky">Đăng ký</button>

        <p>Đã có tài khoản? <a href="Dangnhap.php">Đăng nhập</a></p>
    </form>
</div>

<!-- Footer nhúng từ component -->
<?php include 'components/footer.html'; ?>



</body>
</html>
