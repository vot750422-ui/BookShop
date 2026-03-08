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
    <title>Đăng nhập - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background-color: aliceblue;
            display: flex;
            flex-direction: column; /* Để Header và Footer nằm đúng vị trí */
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            flex: 1; /* Đẩy footer xuống dưới cùng */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
        }
        .khung {
            width: 450px; /* Chỉnh lại độ rộng cho cân đối hơn so với 700px */
            border: 1px solid #ccc;
            padding: 30px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .khung h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0f3a63;
        }
        .khung input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Quan trọng để input không bị tràn khung */
        }
        .btn-dn {
            width: 100%;
            background-color: orangered;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-dn:hover {
            background: #716f6f;
        }
        .khung p {
            text-align: center;
            margin-top: 15px;
        }
        .khung a {
            color: orangered;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div id="navbar"></div>

<div class="login-container">
    <form class="khung" action="XuLyDangNhap.php" method="POST">
        <h2>Đăng Nhập</h2>
        
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        
        <button type="submit" class="btn-dn">Đăng nhập</button>
        
        <p>Chưa có tài khoản? <a href="Dangky.php">Đăng ký ngay</a></p>
    </form>
</div>

<div id="footer"></div>

<script>
    // Nhúng Header và Footer bằng fetch như cấu trúc của Đăng
    fetch("components/navbar.php")
    .then(res => res.text())
    .then(data => {
        document.getElementById("navbar").innerHTML = data;
    });

    fetch("components/footer.html")
    .then(res => res.text())
    .then(data => {
        document.getElementById("footer").innerHTML = data;
    });
</script>

</body>
</html>