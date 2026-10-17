<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';
$id = (int)$_GET['id'];
$conn->query("DELETE FROM datphong WHERE id=$id");
ghiLichSu($conn, $_SESSION['admin']['id'], 'Xóa đơn', 'datphong', $id, "Xóa đơn #$id");
header("Location: quanlidatphong.php?msg=Đã xóa!");
exit();
?>