<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';
require('fpdf/fpdf.php');

$type = $_GET['type'] ?? 'datphong';
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'HOTEL - DANH SACH ' . strtoupper($type), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, 'Ngay xuat: ' . date('d/m/Y H:i'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(0, 119, 182);
$pdf->SetTextColor(255, 255, 255);

if ($type == 'datphong') {
    $pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(45, 10, 'Khach hang', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'SDT', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Phong', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Ngay nhan', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Ngay tra', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Tong tien', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Trang thai', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $r = $conn->query("SELECT d.*, k.hoten, k.sdt, p.tenphong FROM datphong d
                       JOIN khachhang k ON d.khachhang_id=k.id
                       JOIN phong p ON d.phong_id=p.id ORDER BY d.id DESC");
    while($row = $r->fetch_assoc()) {
        $pdf->Cell(15, 8, $row['id'], 1, 0, 'C');
        $pdf->Cell(45, 8, $row['hoten'], 1);
        $pdf->Cell(30, 8, $row['sdt'], 1, 0, 'C');
        $pdf->Cell(30, 8, $row['tenphong'], 1, 0, 'C');
        $pdf->Cell(30, 8, date('d/m/Y', strtotime($row['ngaynhan'])), 1, 0, 'C');
        $pdf->Cell(30, 8, date('d/m/Y', strtotime($row['ngaytra'])), 1, 0, 'C');
        $pdf->Cell(35, 8, number_format($row['tongtien'],0,',','.').' d', 1, 0, 'R');
        $pdf->Cell(40, 8, $row['trangthai'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(80, 10, 'Ten phong', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Loai', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Gia', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Trang thai', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $r = $conn->query("SELECT * FROM phong ORDER BY id");
    while($row = $r->fetch_assoc()) {
        $pdf->Cell(20, 8, $row['id'], 1, 0, 'C');
        $pdf->Cell(80, 8, $row['tenphong'], 1);
        $pdf->Cell(50, 8, $row['loaiphong'], 1, 0, 'C');
        $pdf->Cell(50, 8, number_format($row['gia'],0,',','.').' d', 1, 0, 'R');
        $pdf->Cell(40, 8, $row['trangthai'], 1, 1, 'C');
    }
}

$pdf->Output('D', "hotel_{$type}_" . date('Ymd') . ".pdf");
?>