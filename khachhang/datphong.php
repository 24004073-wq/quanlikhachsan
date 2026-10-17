<?php
session_start();
include 'ketnoi.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='dangnhap.php';</script>";
    exit();
}

$khachhang_id = $_SESSION['user']['id'];
$phong_id = (int)$_POST['phong_id'];
$ngaynhan = $_POST['ngaynhan'];
$ngaytra = $_POST['ngaytra'];
$sokhach = (int)$_POST['sokhach'];
$sodem_input = (int)($_POST['sodem'] ?? 0);
$songay_input = (int)($_POST['songay'] ?? 0);
$diachi = trim($_POST['diachi']);
$thanhtoan = (int)$_POST['thanhtoan'];
$ghichu = trim($_POST['ghichu']);
$gia_dem = (int)$_POST['gia'];
$gia_ngay = (int)$_POST['giangay'];

// ⭐ KIỂM TRA NGÀY HỢP LỆ
$d1 = new DateTime($ngaynhan);
$d2 = new DateTime($ngaytra);

if ($d1 >= $d2) {
    echo "<script>alert('LỖI: Ngày trả phải SAU ngày nhận!'); window.history.back();</script>";
    exit();
}

$sodem = $d1->diff($d2)->days;
if ($sodem < 1) $sodem = 1;

// ⭐ KIỂM TRA SỐ KHÁCH
$phong = $conn->query("SELECT sokhach FROM phong WHERE id=$phong_id")->fetch_assoc();
if ($sokhach < 1 || $sokhach > $phong['sokhach']) {
    echo "<script>alert('Số khách không hợp lệ! Tối đa " . $phong['sokhach'] . " khách.'); window.history.back();</script>";
    exit();
}

// ⭐ TÍNH TIỀN: NGÀY × giá ngày + ĐÊM × giá đêm
$soLuongDem = max($sodem, $sodem_input > 0 ? $sodem_input : 1);
$soLuongNgay = max($songay_input, 0);

$tongDem = $soLuongDem * $gia_dem;
$tongNgay = $soLuongNgay * $gia_ngay;
$tongtien = $tongDem + $tongNgay;

$tien_truoc = round($tongtien * $thanhtoan / 100);

$stmt = $conn->prepare("INSERT INTO datphong 
    (khachhang_id, phong_id, ngaynhan, ngaytra, sokhach, diachi, tongtien, thanhtoan_truoc, ghichu, trangthai) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Chờ xác nhận')");

$stmt->bind_param("iissisiss",
    $khachhang_id,
    $phong_id,
    $ngaynhan,
    $ngaytra,
    $sokhach,
    $diachi,
    $tongtien,
    $tien_truoc,
    $ghichu
);

if ($stmt->execute()) {
    // ✅ Đặt phòng thành công → về trang "Đặt phòng của tôi"
    // Admin sẽ là người thu tiền, khách không tự thanh toán QR nữa
    echo "<script>alert('Đặt phòng thành công! Vui lòng chờ nhân viên xác nhận và thu tiền.'); window.location.href='donhangcuatoi.php';</script>";
} else {
    echo "Lỗi: " . $conn->error;
}
?>