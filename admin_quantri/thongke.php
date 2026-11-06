<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$thongke_thang = $conn->query("SELECT DATE_FORMAT(ngaydat, '%Y-%m') AS thang_key, DATE_FORMAT(ngaydat, '%m/%Y') AS thang, COUNT(*) AS so_don, SUM(tongtien) AS doanhthu FROM datphong GROUP BY thang_key, thang ORDER BY thang_key");
$doanhthu_thang = $thongke_thang->fetch_all(MYSQLI_ASSOC);
$doanhthu_max = 0;
foreach ($doanhthu_thang as $thang) {
    $doanhthu_max = max($doanhthu_max, (float)$thang['doanhthu']);
}
$thongke_loai  = $conn->query("SELECT p.loaiphong, COUNT(d.id) as so_don, SUM(d.tongtien) as doanhthu FROM datphong d JOIN phong p ON d.phong_id = p.id GROUP BY p.loaiphong");
$top_kh = $conn->query("SELECT k.hoten, k.sdt, COUNT(d.id) as so_don, SUM(d.tongtien) as tongtien FROM datphong d JOIN khachhang k ON d.khachhang_id = k.id GROUP BY k.id ORDER BY tongtien DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thống kê</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background: #f1f5f9;
        color: #0f172a;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    .admin-layout {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 260px;
        background: #0f172a;
        color: #fff;
        padding: 20px 0;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
    }

    .sidebar .brand {
        padding: 0 20px 20px;
        border-bottom: 1px solid #1e293b;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 800;
        font-size: 17px;
    }

    .sidebar .brand .logo-icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 18px;
    }

    .sidebar nav a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 20px;
        color: #cbd5e1;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }

    .sidebar nav a:hover,
    .sidebar nav a.active {
        background: #1e293b;
        color: #fff;
        border-left-color: #0077b6;
    }

    .sidebar nav a i {
        width: 20px;
        text-align: center;
        font-size: 15px;
    }

    .main-content {
        margin-left: 260px;
        flex: 1;
        padding: 24px;
    }

    .topbar {
        background: #fff;
        padding: 18px 24px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        flex-wrap: wrap;
        gap: 12px;
    }

    .topbar h1 {
        font-size: 22px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        font-family: inherit;
        text-decoration: none;
    }

    .btn-success {
        background: #10b981;
        color: #fff;
    }

    .chart-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .chart-box {
        background: #fff;
        padding: 24px;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .chart-box h3 {
        margin-bottom: 16px;
        font-size: 16px;
    }

    .revenue-chart {
        height: 260px;
        display: flex;
        align-items: stretch;
        gap: 12px;
        padding: 18px 8px 0;
        border-bottom: 1px solid #e2e8f0;
        overflow-x: auto;
    }

    .revenue-column {
        flex: 1 0 52px;
        min-width: 52px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }

    .revenue-value {
        color: #475569;
        font-size: 11px;
        text-align: center;
        white-space: nowrap;
    }

    .revenue-bar-area {
        width: 100%;
        height: 190px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        background: repeating-linear-gradient(to top, #f1f5f9 0, #f1f5f9 1px, transparent 1px, transparent 47px);
        border-radius: 6px 6px 0 0;
    }

    .revenue-bar {
        width: min(34px, 70%);
        min-height: 3px;
        background: linear-gradient(180deg, #00b4d8, #0077b6);
        border-radius: 6px 6px 0 0;
    }

    .revenue-month {
        color: #475569;
        font-size: 12px;
        white-space: nowrap;
    }

    .chart-empty {
        min-height: 230px;
        display: grid;
        place-items: center;
        color: #64748b;
        text-align: center;
    }

    .table-wrapper {
        background: #fff;
        border-radius: 14px;
        overflow-x: auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table thead {
        background: linear-gradient(135deg, #0077b6, #005f8a);
        color: #fff;
    }

    table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
    }

    table td {
        padding: 14px 16px;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    table tbody tr:hover {
        background: #f8fafc;
    }

    .bar-track {
        background: #f1f5f9;
        border-radius: 8px;
        height: 24px;
        overflow: hidden;
        margin-top: 4px;
    }

    .bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #0077b6, #00b4d8);
        border-radius: 8px;
        transition: width 0.5s;
    }

    .price {
        color: #0077b6;
        font-weight: 700;
    }

    @media (max-width:900px) {
        .sidebar {
            width: 70px;
        }

        .sidebar .brand span,
        .sidebar nav a span {
            display: none;
        }

        .main-content {
            margin-left: 70px;
        }

        .chart-row {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="topbar">
                <h1><i class="fas fa-chart-bar" style="color:#0077b6;"></i> Thống kê</h1>
                <a href="xuat_excel.php?type=thongke" class="btn btn-success"><i class="fas fa-file-excel"></i> Xuất
                    Excel</a>
            </div>
            <div class="chart-row">
                <div class="chart-box">
                    <h3>💰 Doanh thu theo tháng</h3>
                    <?php if ($doanhthu_thang): ?>
                    <div class="revenue-chart" role="img" aria-label="Biểu đồ doanh thu theo tháng">
                        <?php foreach ($doanhthu_thang as $thang): ?>
                        <?php $ty_le = $doanhthu_max > 0 ? ((float)$thang['doanhthu'] / $doanhthu_max) * 100 : 0; ?>
                        <div class="revenue-column">
                            <span class="revenue-value"><?php echo number_format($thang['doanhthu'] / 1000000, 1, ',', '.'); ?> tr</span>
                            <div class="revenue-bar-area">
                                <div class="revenue-bar" style="height:<?php echo max($ty_le, 2); ?>%"
                                    title="<?php echo htmlspecialchars($thang['thang'] . ': ' . number_format($thang['doanhthu'], 0, ',', '.') . ' đ'); ?>">
                                </div>
                            </div>
                            <span class="revenue-month"><?php echo htmlspecialchars($thang['thang']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="chart-empty">Chưa có dữ liệu doanh thu để hiển thị.</div>
                    <?php endif; ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Tháng</th>
                                <th>Số đơn</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($doanhthu_thang as $r): ?>
                            <tr>
                                <td><b><?php echo $r['thang']; ?></b></td>
                                <td><?php echo $r['so_don']; ?> đơn</td>
                                <td class="price"><?php echo number_format($r['doanhthu'],0,',','.'); ?> đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="chart-box">
                    <h3>🛏️ Doanh thu theo loại phòng</h3>
                    <?php 
                $max = 1; $loai_data = [];
                while($r=$thongke_loai->fetch_assoc()) {
                    $loai_data[] = $r;
                    if ($r['doanhthu'] > $max) $max = $r['doanhthu'];
                }
                foreach($loai_data as $r): 
                ?>
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
                            <span><b><?php echo $r['loaiphong']; ?></b> (<?php echo $r['so_don']; ?> đơn)</span>
                            <span class="price"><?php echo number_format($r['doanhthu'],0,',','.'); ?> đ</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:<?php echo ($r['doanhthu']/$max)*100; ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="chart-box">
                <h3>⭐ Top 5 khách hàng thân thiết</h3>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Hạng</th>
                                <th>Họ tên</th>
                                <th>SĐT</th>
                                <th>Số đơn</th>
                                <th>Tổng chi tiêu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; while($r=$top_kh->fetch_assoc()): ?>
                            <tr>
                                <td><b style="color:#f59e0b;">#<?php echo $i++; ?></b></td>
                                <td><b><?php echo htmlspecialchars($r['hoten']); ?></b></td>
                                <td><?php echo htmlspecialchars($r['sdt']); ?></td>
                                <td><?php echo $r['so_don']; ?> đơn</td>
                                <td class="price"><?php echo number_format($r['tongtien'],0,',','.'); ?> đ</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>