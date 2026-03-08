<?php
session_start(); // Bắt đầu phiên để có quyền xóa nó
session_unset(); // Giải phóng tất cả các biến session
session_destroy(); // Hủy bỏ hoàn toàn phiên làm việc

// Sau khi đăng xuất, tự động quay về trang chủ
header("Location: index.php");
exit();
?>