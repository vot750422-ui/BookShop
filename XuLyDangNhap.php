<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailInput = $_POST['email']    ?? '';
    $passInput  = $_POST['password'] ?? '';

    if (empty($emailInput) || empty($passInput)) {
        header("Location: dangnhap.php?error=Vui lòng nhập đầy đủ thông tin.");
        exit();
    }

    try {
        // Lấy thêm cột TrangThai từ cơ sở dữ liệu
        $sql  = "SELECT UserID, FullName, `Password`, `Role`, `TrangThai` FROM users WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$emailInput]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra xem user có tồn tại và mật khẩu có khớp không
        if ($user && password_verify($passInput, $user['Password'])) {
            
            // Nếu tài khoản đã bị khóa (xóa mềm), từ chối đăng nhập
            if (isset($user['TrangThai']) && $user['TrangThai'] == 0) {
                header("Location: dangnhap.php?error=Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.");
                exit();
            }

            // Đăng nhập thành công -> Lưu session
            $_SESSION['user_id']   = $user['UserID'];
            $_SESSION['user_name'] = $user['FullName'];
            $_SESSION['user_role'] = $user['Role'];

            // Điều hướng dựa trên vai trò động từ DB
            if ($user['Role'] === 'Admin') {
                header("Location: admin.php"); // Sửa lại thành admin.php cho đúng với tên file giao diện
            } else {
                header("Location: index.php");
            }
            exit();

        } else {
            header("Location: dangnhap.php?error=Thông tin đăng nhập không chính xác.");
            exit();
        }

    } catch (PDOException $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
} else {
    header("Location: dangnhap.php");
    exit();
}
?>