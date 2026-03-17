<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'Config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName   = $_POST['name']        ?? '';
    $email      = $_POST['email']       ?? '';
    $birthDate  = $_POST['birthdate']   ?? '';
    $password   = $_POST['password']    ?? '';
    $rePassword = $_POST['re-password'] ?? '';
    $address    = $_POST['address']     ?? '';
    $phone      = $_POST['phone']       ?? '';

    if (empty($fullName) || empty($email) || empty($password)) {
        die("Vui lòng nhập đầy đủ thông tin bắt buộc!");
    }

    if ($password !== $rePassword) {
        die("Mật khẩu nhập lại không khớp!");
    }

    try {
        // SQL Server dùng []  
        $sql = "INSERT INTO Users (FullName, Email, BirthDate, `Password`, Address, Phone, `Role`)
                VALUES (?, ?, ?, ?, ?, ?, 'Customer')";

        $stmt = $conn->prepare($sql);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$fullName, $email, $birthDate, $hashedPassword, $address, $phone]);

        echo "<script>
            alert('Đăng ký thành công! Vui lòng đăng nhập.');
            window.location.href = 'Dangnhap.php';
        </script>";
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            echo "Lỗi: Email này đã tồn tại!";
        } else {
            echo "Lỗi hệ thống: " . $e->getMessage();
        }
    }
} else {
    echo "Vui lòng đăng ký từ trang <a href='Dangky.php'>Đăng ký</a>.";
}
?>