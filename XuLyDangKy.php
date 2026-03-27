<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName   = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $birthDate  = trim($_POST['birthdate'] ?? '');
    $password   = $_POST['password'] ?? '';
    $rePassword = $_POST['re-password'] ?? '';
    $phone      = trim($_POST['phone'] ?? '');

    if (empty($fullName) || empty($email) || empty($password)) {
        header("Location: dangky.php?error=" . urlencode("Vui lòng nhập đầy đủ thông tin bắt buộc!"));
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: dangky.php?error=" . urlencode("Mật khẩu phải từ 6 ký tự trở lên!"));
        exit();
    }

    if ($password !== $rePassword) {
        header("Location: dangky.php?error=" . urlencode("Mật khẩu nhập lại không khớp!"));
        exit();
    }

    try {
        $checkEmail = $conn->prepare("SELECT Email FROM users WHERE Email = ?");
        $checkEmail->execute([$email]);
        if ($checkEmail->rowCount() > 0) {
            header("Location: dangky.php?error=" . urlencode("Email này đã tồn tại trong hệ thống!"));
            exit();
        }

        $sql  = "INSERT INTO users (FullName, Email, BirthDate, `Password`, Phone, `Role`) VALUES (?, ?, ?, ?, ?, 'Customer')";
        $stmt = $conn->prepare($sql);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$fullName, $email, $birthDate, $hashedPassword, $phone]);

        header("Location: dangnhap.php?success=" . urlencode("Đăng ký thành công! Vui lòng đăng nhập."));
        exit();

    } catch (PDOException $e) {
        header("Location: dangky.php?error=" . urlencode("Lỗi hệ thống!"));
        exit();
    }
} else {
    header("Location: dangky.php");
    exit();
}
?>