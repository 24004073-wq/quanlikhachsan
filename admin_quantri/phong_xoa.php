<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$id = (int)$_GET['id'];
$p = $conn->query("SELECT tenphong FROM phong WHERE id=$id")->fetch_assoc();
if ($p) {
    $conn->query("DELETE FROM phong WHERE id=$id");
    ghiLichSu($conn, $_SESSION['admin']['id'], 'Xóa phòng', 'phong', $id, "Xóa phòng {$p['tenphong']}");
}
header("Location: quanliphong.php?msg=Đã xóa phòng!");
exit();
?>