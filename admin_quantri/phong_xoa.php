<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT tenphong FROM phong WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if ($p) {
    $deleteStmt = $conn->prepare("DELETE FROM phong WHERE id = ?");
    $deleteStmt->bind_param("i", $id);
    $deleteStmt->execute();
    ghiLichSu($conn, $_SESSION['admin']['id'], 'Xóa phòng', 'phong', $id, "Xóa phòng {$p['tenphong']}");
}
header("Location: quanliphong.php?msg=Đã xóa phòng!");
exit();
?>