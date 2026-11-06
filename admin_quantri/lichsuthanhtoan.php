<?php
session_start();
include '../khachhang/ketnoi.php';

if (file_exists('check_loginadmin.php')) {
    include 'check_loginadmin.php';
} elseif (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT l.*, d.tongtien, d.thanhtoan_truoc, k.hoten, k.sdt, p.tenphong, a.hoten AS ten_admin
        FROM lichsuthanhtoan l
        JOIN datphong d ON l.datphong_id = d.id
        JOIN khachhang k ON d.khachhang_id = k.id
        JOIN phong p ON d.phong_id = p.id
        LEFT JOIN admin a ON l.admin_id = a.id
        ORDER BY l.thoigian DESC";
$result = $conn->query($sql);

$tong_thu = $conn->query("SELECT SUM(sotien) as t FROM lichsuthanhtoan")->fetch_assoc()['t'] ?? 0;
$so_gd = $conn->query("SELECT COUNT(*) as t FROM lichsuthanhtoan")->fetch_assoc()['t'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Lịch sử thanh toán - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .stat-mini {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
        max-width: 600px;
    }

    .stat-mini .card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .stat-mini .card p {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .stat-mini .card h3 {
        font-size: 22px;
        font-weight: 800;
    }

    .stat-mini .card.green h3 {
        color: #10b981;
    }

    .stat-mini .card.blue h3 {
        color: #0077b6;
    }

    .ma-gd {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: #0077b6;
        background: #dbeafe;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
    }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="topbar">
                <h1>💰 Lịch sử thanh toán</h1>
                <a href="xuat_excel.php?type=lichsuthanhtoan" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </a>
            </div>

            <div class="stat-mini">
                <div class="card green">
                    <p><i class="fas fa-money-bill-wave"></i> Tổng tiền đã thu</p>
                    <h3><?php echo number_format($tong_thu,0,',','.'); ?> đ</h3>
                </div>
                <div class="card blue">
                    <p><i class="fas fa-receipt"></i> Số giao dịch</p>
                    <h3><?php echo $so_gd; ?> giao dịch</h3>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Mã giao dịch</th>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Số tiền thu</th>
                            <th>Phương thức</th>
                            <th>Admin thu</th>
                            <th>Thời gian</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;padding:30px;color:#64748b;">Chưa có lịch sử thu
                                tiền nào.</td>
                        </tr>
                        <?php else: ?>
                        <?php while($row = $result->fetch_assoc()): 
                        $ma_gd = 'GDTT' . str_pad($row['id'], 5, '0', STR_PAD_LEFT);
                        $ma_don = 'GD' . str_pad($row['datphong_id'], 5, '0', STR_PAD_LEFT);
                    ?>
                        <tr>
                            <td><span class="ma-gd"><?php echo $ma_gd; ?></span></td>
                            <td><b><?php echo $ma_don; ?></b></td>
                            <td><?php echo htmlspecialchars($row['hoten']); ?><br><small
                                    style="color:#64748b;"><?php echo htmlspecialchars($row['sdt']); ?></small></td>
                            <td><?php echo htmlspecialchars($row['tenphong']); ?></td>
                            <td style="color:#10b981;font-weight:700;">
                                +<?php echo number_format($row['sotien'],0,',','.'); ?> đ</td>
                            <td><span
                                    class="badge badge-info"><?php echo htmlspecialchars($row['phuongthuc']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row['ten_admin'] ?? 'N/A'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['thoigian'])); ?></td>
                            <td><?php echo htmlspecialchars($row['ghichu'] ?? '—'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>