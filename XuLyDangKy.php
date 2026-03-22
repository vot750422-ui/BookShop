<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName   = trim($_POST['name']        ?? '');
    $email      = trim($_POST['email']       ?? '');
    $birthDate  = trim($_POST['birthdate']   ?? '');
    $password   = $_POST['password']         ?? '';
    $rePassword = $_POST['re-password']      ?? '';
    $address    = trim($_POST['address']     ?? '');
    $phone      = trim($_POST['phone']       ?? '');

    if (empty($fullName) || empty($email) || empty($password)) {
        header("Location: dangky.php?error=" . urlencode("Vui lòng nhập đầy đủ thông tin bắt buộc!"));
        exit();
    }

    if ($password !== $rePassword) {
        header("Location: dangky.php?error=" . urlencode("Mật khẩu nhập lại không khớp!"));
        exit();
    }

    try {
        $sql  = "INSERT INTO users (FullName, Email, BirthDate, `Password`, Address, Phone, `Role`)
                 VALUES (?, ?, ?, ?, ?, ?, 'Customer')";
        $stmt = $conn->prepare($sql);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$fullName, $email, $birthDate, $hashedPassword, $address, $phone]);

        header("Location: dangnhap.php?success=" . urlencode("Đăng ký thành công! Vui lòng đăng nhập."));
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            header("Location: dangky.php?error=" . urlencode("Email này đã tồn tại trong hệ thống!"));
        } else {
            header("Location: dangky.php?error=" . urlencode("Lỗi hệ thống: " . $e->getMessage()));
        }
        exit();
    }
} else {
    header("Location: dangky.php");
    exit();
}
?>