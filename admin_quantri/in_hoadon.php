<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$id = (int)($_GET['id'] ?? 0);
$don = $conn->query("SELECT d.*, k.hoten, k.sdt, k.cccd, k.email, p.tenphong, p.loaiphong, p.gia
                     FROM datphong d
                     JOIN khachhang k ON d.khachhang_id = k.id
                     JOIN phong p ON d.phong_id = p.id
                     WHERE d.id = $id")->fetch_assoc();

if (!$don) die("Không tìm thấy hóa đơn");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #<?php echo $id; ?></title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Times New Roman', serif;
    }

    body {
        padding: 40px;
        color: #000;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #0077b6;
        padding-bottom: 16px;
        margin-bottom: 24px;
    }

    .logo {
        font-size: 32px;
        font-weight: 900;
        color: #0077b6;
    }

    .logo span {
        display: block;
        font-size: 13px;
        color: #333;
        font-weight: 400;
    }

    h1 {
        text-align: center;
        margin: 24px 0;
        font-size: 24px;
        text-transform: uppercase;
    }

    .info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .info-box {
        background: #f8fafc;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #0077b6;
    }

    .info-box h3 {
        font-size: 14px;
        text-transform: uppercase;
        margin-bottom: 10px;
        color: #0077b6;
    }

    .info-box p {
        font-size: 14px;
        margin-bottom: 6px;
    }

    .info-box b {
        display: inline-block;
        min-width: 130px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    table th {
        background: #0077b6;
        color: #fff;
        padding: 12px;
        text-align: left;
        font-size: 14px;
    }

    table td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        font-size: 14px;
    }

    .total {
        text-align: right;
        font-size: 20px;
        font-weight: 700;
        color: #0077b6;
        margin: 24px 0;
        padding: 16px;
        background: #f0f9ff;
        border-radius: 8px;
    }

    .footer {
        display: flex;
        justify-content: space-between;
        margin-top: 60px;
    }

    .sign {
        text-align: center;
    }

    .sign p {
        margin-bottom: 60px;
        font-style: italic;
    }

    @media print {
        body {
            padding: 0;
        }

        .no-print {
            display: none;
        }
    }

    .btn-print {
        background: #0077b6;
        color: #fff;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <button class="btn-print no-print" onclick="window.print()">🖨️ In hóa đơn</button>

    <div class="header">
        <div class="logo">HOTEL<span>Hệ thống quản lý khách sạn</span></div>
        <div style="text-align:right;">
            <p><b>Mã HĐ:</b> HD<?php echo str_pad($id, 6, '0', STR_PAD_LEFT); ?></p>
            <p><b>Ngày in:</b> <?php echo date('d/m/Y H:i'); ?></p>
        </div>
    </div>

    <h1>Hóa đơn đặt phòng</h1>

    <div class="info">
        <div class="info-box">
            <h3>Thông tin khách hàng</h3>
            <p><b>Họ tên:</b> <?php echo htmlspecialchars($don['hoten']); ?></p>
            <p><b>CCCD:</b> <?php echo htmlspecialchars($don['cccd']); ?></p>
            <p><b>SĐT:</b> <?php echo htmlspecialchars($don['sdt']); ?></p>
            <p><b>Email:</b> <?php echo htmlspecialchars($don['email']); ?></p>
            <p><b>Địa chỉ:</b> <?php echo htmlspecialchars($don['diachi']); ?></p>
        </div>
        <div class="info-box">
            <h3>Thông tin đặt phòng</h3>
            <p><b>Ngày nhận:</b> <?php echo date('d/m/Y', strtotime($don['ngaynhan'])); ?></p>
            <p><b>Ngày trả:</b> <?php echo date('d/m/Y', strtotime($don['ngaytra'])); ?></p>
            <p><b>Số khách:</b> <?php echo $don['sokhach']; ?></p>
            <p><b>Trạng thái:</b> <?php echo $don['trangthai']; ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên phòng</th>
                <th>Loại</th>
                <th>Giá / đêm</th>
                <th>Số đêm</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $d1 = new DateTime($don['ngaynhan']); $d2 = new DateTime($don['ngaytra']);
            $sodem = max(1, $d1->diff($d2)->days);
            ?>
            <tr>
                <td>1</td>
                <td><?php echo htmlspecialchars($don['tenphong']); ?></td>
                <td><?php echo htmlspecialchars($don['loaiphong']); ?></td>
                <td><?php echo number_format($don['gia'],0,',','.'); ?> đ</td>
                <td><?php echo $sodem; ?></td>
                <td><b><?php echo number_format($don['tongtien'],0,',','.'); ?> đ</b></td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        <p>Tổng tiền: <span style="font-size:24px;"><?php echo number_format($don['tongtien'],0,',','.'); ?> đ</span>
        </p>
        <p style="font-size:14px;margin-top:8px;">Đã thanh toán trước:
            <?php echo number_format($don['thanhtoan_truoc'],0,',','.'); ?> đ</p>
        <p style="font-size:16px;margin-top:4px;">Còn lại:
            <?php echo number_format($don['tongtien']-$don['thanhtoan_truoc'],0,',','.'); ?> đ</p>
    </div>

    <div class="footer">
        <div class="sign">
            <p>Khách hàng</p><b><?php echo htmlspecialchars($don['hoten']); ?></b>
        </div>
        <div class="sign">
            <p>Nhân viên lập hóa đơn</p><b><?php echo htmlspecialchars($_SESSION['admin']['hoten']); ?></b>
        </div>
    </div>
</body>

</html>