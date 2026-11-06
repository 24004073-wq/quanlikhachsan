<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';
$id = (int)$_GET['id'];
$deleteStmt = $conn->prepare("DELETE FROM datphong WHERE id = ?");
$deleteStmt->bind_param("i", $id);
$deleteStmt->execute();
ghiLichSu($conn, $_SESSION['admin']['id'], 'Xóa đơn', 'datphong', $id, "Xóa đơn #$id");
header("Location: quanlidatphong.php?msg=Đã xóa!");
exit();
?>