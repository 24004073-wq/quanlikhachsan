<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$type = $_GET['type'] ?? 'datphong';
$filename = "hotel_{$type}_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

if ($type == 'phong') {
    fputcsv($output, ['ID','Tên phòng','Loại','Giá','Số khách','Trạng thái']);
    $r = $conn->query("SELECT * FROM phong ORDER BY id");
    while($row = $r->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['tenphong'], $row['loaiphong'], $row['gia'], $row['sokhach'], $row['trangthai']]);
    }
} 
elseif ($type == 'khachhang') {
    fputcsv($output, ['ID','Họ tên','CCCD','SĐT','Email','Tên đăng nhập','Ngày tạo']);
    $r = $conn->query("SELECT * FROM khachhang ORDER BY id");
    while($row = $r->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['hoten'], $row['cccd'], $row['sdt'], $row['email'], $row['tendangnhap'], $row['ngaytao']]);
    }
} 
elseif ($type == 'thongke') {
    fputcsv($output, ['Tháng','Số đơn','Doanh thu']);
    $r = $conn->query("SELECT DATE_FORMAT(ngaydat,'%m/%Y') as thang, COUNT(*) as so_don, SUM(tongtien) as dt 
                       FROM datphong GROUP BY thang");
    while($row = $r->fetch_assoc()) {
        fputcsv($output, [$row['thang'], $row['so_don'], $row['dt']]);
    }
} 
else {
    fputcsv($output, ['ID','Khách hàng','SĐT','Phòng','Ngày nhận','Ngày trả','Số khách','Tổng tiền','Đã trả','Trạng thái','Ngày đặt']);
    $r = $conn->query("SELECT d.*, k.hoten, k.sdt, p.tenphong FROM datphong d
                       JOIN khachhang k ON d.khachhang_id=k.id
                       JOIN phong p ON d.phong_id=p.id
                       ORDER BY d.id DESC");
    while($row = $r->fetch_assoc()) {
        fputcsv($output, [
            $row['id'], $row['hoten'], $row['sdt'], $row['tenphong'],
            $row['ngaynhan'], $row['ngaytra'], $row['sokhach'],
            $row['tongtien'], $row['thanhtoan_truoc'], $row['trangthai'], $row['ngaydat']
        ]);
    }
}

fclose($output);
exit();
?>