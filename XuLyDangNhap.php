<?php
session_start(); // Bắt đầu phiên làm việc để ghi nhớ người dùng đã đăng nhập
require_once 'Config.php'; // Gọi file kết nối SQL Server của bạn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form Dangnhap.php gửi sang
    $emailInput = $_POST['email'] ?? '';
    $passInput  = $_POST['password'] ?? '';

    if (empty($emailInput) || empty($passInput)) {
        die("Vui lòng nhập đầy đủ Email và Mật khẩu!");
    }

    try {
        // 1. Tìm tài khoản trong bảng users theo Email
        // Lưu ý: Tên cột phải là FullName theo ảnh image_7dd623.png của bạn
        $sql = "SELECT UserID, FullName, [Password], [Role] FROM users WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$emailInput]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Kiểm tra tài khoản có tồn tại và mật khẩu có khớp không
        if ($user && $user['Password'] === $passInput) {
            // Đăng nhập thành công! Lưu thông tin vào Session
            $_SESSION['user_id']   = $user['UserID'];
            $_SESSION['user_name'] = $user['FullName'];
            $_SESSION['user_role'] = $user['Role'];

            echo "<h2>Đăng nhập thành công!</h2>";
            echo "Chào mừng <strong>" . $user['FullName'] . "</strong> quay trở lại.";
            // Sau này bạn có thể dùng header("Location: TrangChu.php"); để chuyển trang
            header("Location: index.php");
            exit(); // Dừng script sau khi chuyển trang
        } else {
            // Nếu không tìm thấy user hoặc mật khẩu sai
            echo "Sai Email hoặc Mật khẩu. <a href='Dangnhap.php'>Thử lại</a>";
        }

    } catch (PDOException $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
} else {
    echo "Truy cập không hợp lệ.";
}
?>