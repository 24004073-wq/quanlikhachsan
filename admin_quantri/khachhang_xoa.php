<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';
$id = (int)$_GET['id'];
$kh = $conn->query("SELECT hoten FROM khachhang WHERE id=$id")->fetch_assoc();
if ($kh) {
    $conn->query("DELETE FROM khachhang WHERE id=$id");
    ghiLichSu($conn, $_SESSION['admin']['id'], 'Xóa KH', 'khachhang', $id, "Xóa KH {$kh['hoten']}");
}
header("Location: quanlikhachhang.php?msg=Đã xóa!");
exit();
?>