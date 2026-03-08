<?php
// Bật hiển thị lỗi để dễ dàng kiểm tra nếu có vấn đề kết nối
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'Config.php'; // Kết nối tới database qua file Config.php của bạn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sử dụng toán tử ?? để tránh lỗi "Undefined array key" nếu ô nhập liệu bị trống
    $fullName    = $_POST['name'] ?? '';
    $email       = $_POST['email'] ?? '';
    $birthDate   = $_POST['birthdate'] ?? '';
    $password    = $_POST['password'] ?? '';
    $rePassword  = $_POST['re-password'] ?? '';
    $address     = $_POST['address'] ?? '';
    $phone       = $_POST['phone'] ?? '';

    // 1. Kiểm tra các trường bắt buộc không được để trống
    if (empty($fullName) || empty($email) || empty($password)) {
        die("Vui lòng nhập đầy đủ các thông tin bắt buộc (Họ tên, Email, Mật khẩu).");
    }

    // 2. Kiểm tra mật khẩu nhập lại
    if ($password !== $rePassword) {
        die("Mật khẩu nhập lại không khớp!");
    }

    try {
        // 3. Câu lệnh SQL INSERT - Lưu ý dùng tên cột FullName và Phone cho đúng với Database của bạn
        $sql = "INSERT INTO users (FullName, Email, BirthDate, [Password], [Address], Phone, [Role]) 
                VALUES (?, ?, ?, ?, ?, ?, 'Customer')";
        
        $stmt = $conn->prepare($sql);
        
        // 4. Thực thi chèn dữ liệu
        $stmt->execute([$fullName, $email, $birthDate, $password, $address, $phone]);

        echo "<script>
    alert('Đăng ký thành công! Vui lòng đăng nhập để tiếp tục.');
    window.location.href = 'Dangnhap.php'; 
</script>";
exit();
        
    } catch (PDOException $e) {
        // Kiểm tra mã lỗi trùng Email (Unique Constraint)
        if ($e->getCode() == '23000') {
            echo "Lỗi: Email này đã tồn tại trong hệ thống!";
        } else {
            echo "Lỗi hệ thống: " . $e->getMessage();
        }
    }
} else {
    // Nếu truy cập trực tiếp file này mà không qua Form
    echo "Vui lòng đăng ký từ trang <a href='Dangky.php'>Đăng ký</a>.";
}
?>