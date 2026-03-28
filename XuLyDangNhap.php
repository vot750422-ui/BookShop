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
        $sql  = "SELECT UserID, FullName, `Password`, `Role`, `TrangThai` FROM users WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$emailInput]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($passInput, $user['Password'])) {
            

            if (isset($user['TrangThai']) && $user['TrangThai'] == 0) {
                header("Location: dangnhap.php?error=Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.");
                exit();
            }

            $_SESSION['user_id']   = $user['UserID'];
            $_SESSION['user_name'] = $user['FullName'];
            $_SESSION['user_role'] = $user['Role'];

            $msg = urlencode("Đăng nhập thành công!");

            if ($user['Role'] === 'Admin') {
                header("Location: admin.php?success=" . $msg); 
            } else {
                header("Location: index.php?success=" . $msg);
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