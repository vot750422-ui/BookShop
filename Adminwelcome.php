<?php
session_start();

// Chỉ admin mới vào được trang này
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: Dangnhap.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng Admin - BookStore</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #f0e6d3;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .welcome-box {
            background: white;
            border-radius: 12px;
            padding: 50px 60px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border-top: 5px solid #c9a96e;
            max-width: 500px;
            width: 90%;
        }

        .welcome-box .icon {
            font-size: 60px;
            margin-bottom: 15px;
        }

        .welcome-box h2 {
            color: #2c1a0e;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .welcome-box p {
            color: #7b4f1e;
            margin-bottom: 35px;
            font-size: 15px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 14px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
            min-width: 180px;
        }

        .btn-admin {
            background: #2c1a0e;
            color: #f0e6d3;
            border: 2px solid #2c1a0e;
        }

        .btn-admin:hover {
            background: #c9a96e;
            border-color: #c9a96e;
            color: white;
        }

        .btn-home {
            background: white;
            color: #2c1a0e;
            border: 2px solid #c9a96e;
        }

        .btn-home:hover {
            background: #f0e6d3;
        }

        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 30px 0 20px;
        }

        .logout {
            font-size: 13px;
            color: #aaa;
        }

        .logout a {
            color: #e74c3c;
            text-decoration: none;
        }

        .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="welcome-box">
    <div class="icon">👑</div>
    <h2>Xin chào, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
    <p>Bạn đang đăng nhập với quyền <strong>Quản trị viên</strong>. Bạn muốn đi đến đâu?</p>

    <div class="btn-group">
        <a href="admin.php" class="btn btn-admin">
            ⚙️ Trang Quản Trị
        </a>
        <a href="index.php" class="btn btn-home">
            🏠 Trang Chủ
        </a>
    </div>

    <hr class="divider">
    <p class="logout">
        <a href="logout.php">🚪 Đăng xuất</a>
    </p>
</div>

</body>
</html>