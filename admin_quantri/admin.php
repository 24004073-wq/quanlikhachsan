<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$tong_phong = $conn->query("SELECT COUNT(*) as t FROM phong")->fetch_assoc()['t'];
$tong_kh    = $conn->query("SELECT COUNT(*) as t FROM khachhang")->fetch_assoc()['t'];
$tong_don   = $conn->query("SELECT COUNT(*) as t FROM datphong")->fetch_assoc()['t'];
$doanhthu   = $conn->query("SELECT COALESCE(SUM(thanhtoan_truoc),0) as t FROM datphong WHERE trangthai IN ('Đã xác nhận','Trả phòng')")->fetch_assoc()['t'];
$don_moi = $conn->query("SELECT d.*, k.hoten, p.tenphong FROM datphong d JOIN khachhang k ON d.khachhang_id = k.id JOIN phong p ON d.phong_id = p.id ORDER BY d.id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Admin HOTEL</title>
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
    }

    .topbar h1 {
        font-size: 22px;
        font-weight: 800;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #fff;
        padding: 24px;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #fff;
    }

    .stat-icon.blue {
        background: linear-gradient(135deg, #0077b6, #00b4d8);
    }

    .stat-icon.green {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-icon.orange {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-icon.purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .stat-info h3 {
        font-size: 24px;
        font-weight: 800;
    }

    .stat-info p {
        color: #64748b;
        font-size: 13px;
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
        padding: 16px;
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

    .badge {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
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
    }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="topbar">
                <h1><i class="fas fa-chart-line" style="color:#0077b6;"></i> Dashboard</h1>
                <div>Xin chào, <b><?php echo htmlspecialchars($_SESSION['admin']['hoten']); ?></b></div>
            </div>
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-bed"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $tong_phong; ?></h3>
                        <p>Tổng phòng</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $tong_kh; ?></h3>
                        <p>Khách hàng</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $tong_don; ?></h3>
                        <p>Đơn đặt phòng</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($doanhthu,0,',','.'); ?> đ</h3>
                        <p>Doanh thu</p>
                    </div>
                </div>
            </div>
            <h2 style="margin-bottom:16px;font-size:18px;"><i class="fas fa-clock" style="color:#0077b6;"></i> Đơn đặt
                phòng mới nhất</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Ngày nhận</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $don_moi->fetch_assoc()): 
                        $cls = 'badge-warning';
                        if (in_array($row['trangthai'],['Đã xác nhận','Trả phòng'])) $cls='badge-success';
                        elseif ($row['trangthai']=='Đã hủy') $cls='badge-danger';
                    ?>
                        <tr>
                            <td><b>#<?php echo $row['id']; ?></b></td>
                            <td><?php echo htmlspecialchars($row['hoten']); ?></td>
                            <td><?php echo htmlspecialchars($row['tenphong']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['ngaynhan'])); ?></td>
                            <td><b style="color:#0077b6;"><?php echo number_format($row['tongtien'],0,',','.'); ?> đ</b>
                            </td>
                            <td><span class="badge <?php echo $cls; ?>"><?php echo $row['trangthai']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>