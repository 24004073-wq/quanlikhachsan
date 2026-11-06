<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT hoten FROM khachhang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$kh = $stmt->get_result()->fetch_assoc();
if ($kh) {
    $deleteStmt = $conn->prepare("DELETE FROM khachhang WHERE id = ?");
    $deleteStmt->bind_param("i", $id);
    $deleteStmt->execute();
    ghiLichSu($conn, $_SESSION['admin']['id'], 'Xóa KH', 'khachhang', $id, "Xóa KH {$kh['hoten']}");
}
header("Location: quanlikhachhang.php?msg=Đã xóa!");
exit();
?>