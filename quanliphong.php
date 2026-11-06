<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

if (isset($_GET['xoa'])) {
    $id = (int)$_GET['xoa'];
    $stmt = $conn->prepare("SELECT tenphong FROM phong WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    if ($p) {
        $deleteStmt = $conn->prepare("DELETE FROM phong WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        $deleteStmt->execute();
        ghiLichSu($conn, $_SESSION['admin']['id'], 'Xóa phòng', 'phong', $id, "Xóa phòng {$p['tenphong']}");
        header("Location: quanliphong.php?msg=Đã xóa!");
        exit();
    }
}
$result = $conn->query("SELECT * FROM phong ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý phòng</title>
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

    .btn-primary {
        background: linear-gradient(135deg, #0077b6, #005f8a);
        color: #fff;
    }

    .btn-danger {
        background: #ef4444;
        color: #fff;
    }

    .btn-success {
        background: #10b981;
        color: #fff;
    }

    .btn-warning {
        background: #f59e0b;
        color: #fff;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }

    .alert {
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-left: 4px solid #10b981;
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
        padding: 14px 12px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    table td {
        padding: 14px 12px;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
    }

    table tbody tr:hover {
        background: #f8fafc;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
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

    .price {
        color: #0077b6;
        font-weight: 700;
    }

    .room-thumb {
        width: 60px;
        height: 45px;
        object-fit: cover;
        border-radius: 8px;
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
                <h1><i class="fas fa-bed" style="color:#0077b6;"></i> Quản lý phòng</h1>
                <div style="display:flex;gap:8px;">
                    <a href="xuat_excel.php?type=phong" class="btn btn-success"><i class="fas fa-file-excel"></i>
                        Excel</a>
                    <a href="phong_them.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm</a>
                </div>
            </div>
            <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên phòng</th>
                            <th>Loại</th>
                            <th>Giá / đêm</th>
                            <th>Số khách</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><b>#<?php echo $row['id']; ?></b></td>
                            <td><img src="../image/<?php echo htmlspecialchars($row['hinhanh']); ?>" class="room-thumb">
                            </td>
                            <td><b><?php echo htmlspecialchars($row['tenphong']); ?></b></td>
                            <td><?php echo htmlspecialchars($row['loaiphong']); ?></td>
                            <td class="price"><?php echo number_format($row['gia'],0,',','.'); ?> đ</td>
                            <td><?php echo $row['sokhach']; ?> khách</td>
                            <td>
                                <?php if($row['trangthai']=='Trống'): ?>
                                <span class="badge badge-success">Trống</span>
                                <?php elseif($row['trangthai']=='Đã đặt'): ?>
                                <span class="badge badge-danger">Đã đặt</span>
                                <?php else: ?>
                                <span class="badge badge-warning"><?php echo $row['trangthai']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="phong_sua.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i
                                        class="fas fa-edit"></i></a>
                                <a href="quanliphong.php?xoa=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Xóa?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>