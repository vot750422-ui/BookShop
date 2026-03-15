<?php
session_start();
require_once 'Config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailInput = $_POST['email']    ?? '';
    $passInput  = $_POST['password'] ?? '';

    if (empty($emailInput) || empty($passInput)) {
        header("Location: Dangnhap.php?error=trong");
        exit();
    }

    // ✅ Kiểm tra cứng tài khoản Admin
    if ($emailInput === 'admin@bookstore.com' && $passInput === '123456') {
        $_SESSION['user_id']   = 0;
        $_SESSION['user_name'] = 'Admin';
        $_SESSION['user_role'] = 'Admin';
        header("Location: AdminWelcome.php");
        exit();
    }

    // Tài khoản thường → kiểm tra trong DB
    try {
        $sql  = "SELECT UserID, FullName, [Password], [Role] FROM Users WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$emailInput]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($passInput, $user['Password'])) {
            $_SESSION['user_id']   = $user['UserID'];
            $_SESSION['user_name'] = $user['FullName'];
            $_SESSION['user_role'] = $user['Role'];

            if ($user['Role'] === 'Admin') {
                header("Location: AdminWelcome.php");
            } else {
                header("Location: index.php");
            }
            exit();

        } else {
            header("Location: Dangnhap.php?error=sai_tai_khoan");
            exit();
        }

    } catch (PDOException $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
} else {
    header("Location: Dangnhap.php");
    exit();
}
?>
