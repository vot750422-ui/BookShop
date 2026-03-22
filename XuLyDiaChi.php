<?php
session_start();
require_once 'config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$userID = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// ==========================================
// 1. XỬ LÝ THÊM ĐỊA CHỈ MỚI
// ==========================================
if ($action === 'add') {
    $name    = trim($_POST['receiver_name'] ?? '');
    $phone   = trim($_POST['receiver_phone'] ?? '');
    
    // Lấy các phần của địa chỉ từ form
    $detail  = trim($_POST['detail_address'] ?? '');
    $tinh    = trim($_POST['tinh'] ?? '');
    $quan    = trim($_POST['quan'] ?? '');
    $phuong  = trim($_POST['phuong'] ?? '');
    
    // Nối thành 1 chuỗi hoàn chỉnh: Số nhà, Phường, Quận, Tỉnh
    $fullAddress = $detail . ', ' . $phuong . ', ' . $quan . ', ' . $tinh;
    
    $isDef   = isset($_POST['is_default']) ? 1 : 0;

    // Kiểm tra không được để trống các trường quan trọng
    if ($name !== '' && $phone !== '' && $detail !== '' && $tinh !== '') {
        try {
            // Nếu khách chọn "Đặt làm mặc định", ta đưa toàn bộ địa chỉ cũ về 0 (địa chỉ phụ)
            if ($isDef === 1) {
                $stmtUpdate = $conn->prepare("UPDATE useraddresses SET IsDefault = 0 WHERE UserID = ?");
                $stmtUpdate->execute([$userID]);
            }

            // Thêm địa chỉ mới vào DB
            $stmt = $conn->prepare("INSERT INTO useraddresses (UserID, ReceiverName, ReceiverPhone, DetailAddress, IsDefault) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userID, $name, $phone, $fullAddress, $isDef]);
        } catch (PDOException $e) {
            // Có thể thêm log lỗi ở đây nếu cần
        }
    }
} 

// ==========================================
// 2. XỬ LÝ XOÁ ĐỊA CHỈ
// ==========================================
elseif ($action === 'delete') {
    $addressID = (int)($_POST['address_id'] ?? 0);
    
    if ($addressID > 0) {
        try {
            // Cẩn thận: Kèm theo UserID để khách chỉ xoá được địa chỉ của chính mình
            $stmt = $conn->prepare("DELETE FROM useraddresses WHERE AddressID = ? AND UserID = ?");
            $stmt->execute([$addressID, $userID]);
        } catch (PDOException $e) {
            // Bỏ qua lỗi
        }
    }
}

// Xong việc thì đẩy về lại đúng tab Sổ địa chỉ
header("Location: profile.php?tab=address");
exit();
?>