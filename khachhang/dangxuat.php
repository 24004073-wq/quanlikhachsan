<?php
// 1. Khởi tạo session để biết đang đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Xóa toàn bộ các biến session (bao gồm thông tin user)
$_SESSION = array();

// 3. Hủy session
session_destroy();

// 4. Chuyển hướng người dùng về lại trang chủ
header("Location: trangchu.php");
exit();
?>