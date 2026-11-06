<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$id = (int)($_GET['id'] ?? 0);
$tt = $conn->query("SELECT t.*, k.hoten, k.sdt, k.cccd, k.email, 
                    d.id as don_id, d.ngaynhan, d.ngaytra, d.tongtien, d.thanhtoan_truoc,
                    p.tenphong, p.loaiphong
                    FROM thanhtoan t
                    JOIN khachhang k ON t.khachhang_id = k.id
                    JOIN datphong d ON t.datphong_id = d.id
                    JOIN phong p ON d.phong_id = p.id
                    WHERE t.id = $id")->fetch_assoc();

if (!$tt) die("Không tìm thấy phiếu thu");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phiếu thu #<?php echo $id; ?></title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Times New Roman', serif;
    }

    body {
        padding: 30px;
        color: #000;
        background: #f1f5f9;
    }

    .phieu {
        background: #fff;
        max-width: 700px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 8px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #0077b6;
        padding-bottom: 16px;
        margin-bottom: 20px;
    }

    .logo {
        font-size: 28px;
        font-weight: 900;
        color: #0077b6;
    }

    .logo span {
        display: block;
        font-size: 12px;
        color: #333;
        font-weight: 400;
    }

    h1 {
        text-align: center;
        margin: 20px 0;
        font-size: 22px;
    }

    .info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-box {
        background: #f8fafc;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #0077b6;
    }

    .info-box h3 {
        font-size: 13px;
        text-transform: uppercase;
        margin-bottom: 10px;
        color: #0077b6;
    }

    .info-box p {
        font-size: 13px;
        margin-bottom: 6px;
    }

    .sotien {
        text-align: center;
        padding: 20px;
        background: #ecfdf5;
        border: 2px dashed #10b981;
        border-radius: 10px;
        margin: 20px 0;
    }

    .sotien span {
        display: block;
        font-size: 13px;
        color: #166534;
        margin-bottom: 6px;
    }

    .sotien b {
        font-size: 32px;
        color: #10b981;
    }

    .detail {
        background: #f8fafc;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .detail p {
        font-size: 13px;
        margin-bottom: 6px;
    }

    .footer {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
    }

    .sign {
        text-align: center;
    }

    .sign p {
        margin-bottom: 60px;
        font-style: italic;
        font-size: 13px;
    }

    .btn-print {
        display: block;
        margin: 0 auto 20px;
        padding: 12px 24px;
        background: #0077b6;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    @media print {
        body {
            background: #fff;
            padding: 0;
        }

        .btn-print {
            display: none;
        }

        .phieu {
            box-shadow: none;
        }
    }
    </style>
</head>

<body>
    <button class="btn-print" onclick="window.print()">🖨️ In phiếu thu</button>

    <div class="phieu">
        <div class="header">
            <div class="logo">HOTEL<span>Hệ thống quản lý khách sạn</span></div>
            <div style="text-align:right;font-size:13px;">
                <p><b>Mã phiếu:</b> PT<?php echo str_pad($id, 6, '0', STR_PAD_LEFT); ?></p>
                <p><b>Ngày in:</b> <?php echo date('d/m/Y H:i'); ?></p>
            </div>
        </div>

        <h1>PHIẾU THU TIỀN</h1>

        <div class="info">
            <div class="info-box">
                <h3>Khách hàng</h3>
                <p><b>Họ tên:</b> <?php echo htmlspecialchars($tt['hoten']); ?></p>
                <p><b>SĐT:</b> <?php echo htmlspecialchars($tt['sdt']); ?></p>
                <p><b>CCCD:</b> <?php echo htmlspecialchars($tt['cccd']); ?></p>
                <p><b>Email:</b> <?php echo htmlspecialchars($tt['email']); ?></p>
            </div>
            <div class="info-box">
                <h3>Đơn đặt phòng</h3>
                <p><b>Mã đơn:</b> #<?php echo $tt['don_id']; ?></p>
                <p><b>Phòng:</b> <?php echo htmlspecialchars($tt['tenphong']); ?></p>
                <p><b>Ngày nhận:</b> <?php echo date('d/m/Y', strtotime($tt['ngaynhan'])); ?></p>
                <p><b>Ngày trả:</b> <?php echo date('d/m/Y', strtotime($tt['ngaytra'])); ?></p>
            </div>
        </div>

        <div class="detail">
            <p><b>Phương thức:</b> <?php echo htmlspecialchars($tt['phuongthuc']); ?></p>
            <p><b>Ngày thu:</b> <?php echo date('d/m/Y H:i', strtotime($tt['ngaythanhtoan'])); ?></p>
            <p><b>Ghi chú:</b> <?php echo htmlspecialchars($tt['ghichu'] ?: 'Không có'); ?></p>
        </div>

        <div class="sotien">
            <span>SỐ TIỀN ĐÃ THU</span>
            <b><?php echo number_format($tt['sotien'],0,',','.'); ?> đ</b>
        </div>

        <div class="detail">
            <p><b>Tổng tiền đơn:</b> <?php echo number_format($tt['tongtien'],0,',','.'); ?> đ</p>
            <p><b>Đã thanh toán:</b> <?php echo number_format($tt['thanhtoan_truoc'],0,',','.'); ?> đ</p>
            <p><b>Còn lại:</b> <?php echo number_format($tt['tongtien'] - $tt['thanhtoan_truoc'],0,',','.'); ?> đ</p>
        </div>

        <div class="footer">
            <div class="sign">
                <p>Khách hàng</p><b><?php echo htmlspecialchars($tt['hoten']); ?></b>
            </div>
            <div class="sign">
                <p>Nhân viên thu tiền</p><b><?php echo htmlspecialchars($_SESSION['admin']['hoten']); ?></b>
            </div>
        </div>
    </div>
</body>

</html>